import { describe, expect, test } from 'bun:test'
import { assertIdle, downloadScript, endpoint, execute, labelList, loadSettings, parseProbe, platformFor, probeScript, runnerHealth, saveSettings, select, serviceScript, shell, type GitHubRunner, type LocalRunner, type Row, type Snapshot } from '../src/runnerwatch.ts'
import { mkdtemp, readFile, rm, stat } from 'node:fs/promises'
import { tmpdir } from 'node:os'
import { join } from 'node:path'
const local: LocalRunner = { scope: 'tana3d', name: 'bugsy', id: 33, directory: '/Users/sami/actions-runner-tana3d', unit: 'actions.runner.tana3d.bugsy', active: true, pid: 12, state: 'running' }
const github: GitHubRunner = { id: 33, name: 'bugsy', os: 'macOS', status: 'online', busy: false, labels: [] }
const row: Row = { selector: 'tana3d/bugsy', scope: 'tana3d', name: 'bugsy', id: 33, host: 'bugsy', status: 'idle', githubStatus: 'online', busy: false, service: local, labels: [] }
const data: Snapshot = { hosts: {}, runners: [row], errors: [] }
const encoded = (text: string) => Buffer.from(text).toString('base64')

describe('runner discovery and health', () => {
    test('BOM metadata and macOS service names parse without credentials', () => {
        const metadata = '\uFEFF' + JSON.stringify({ agentId: 33, agentName: 'bugsy', gitHubUrl: 'https://github.com/tana3d' })
        const packet = `RW_HOST\tDarwin\tarm64\t${encoded('/Users/sami')}\tsami\nRW_RUNNER\t${encoded(local.directory)}\t${encoded(local.unit)}\trunning\t12\t${encoded(metadata)}\n`
        const result = parseProbe(packet + packet.split('\n')[1] + '\n')
        expect(result.runners).toEqual([local]); expect(result.arch).toBe('arm64')
        expect(probeScript()).not.toContain('.credentials')
    })
    test('invalid SSH probes fail rather than report healthy', () => { expect(() => parseProbe('unauthorized')).toThrow('valid runnerwatch probe') })
    test('GitHub online alone is distinct from managed service health', () => {
        expect(runnerHealth(github, local)).toBe('idle')
        expect(runnerHealth({ ...github, busy: true }, local)).toBe('busy')
        expect(runnerHealth(github, { ...local, active: false })).toBe('stopped')
        expect(runnerHealth({ ...github, status: 'offline' }, local)).toBe('disconnected')
        expect(runnerHealth(github, null, false)).toBe('unreachable')
        expect(runnerHealth(undefined, local)).toBe('unverified')
    })
    test('native runner architecture never falsifies Intel capability', () => {
        expect(platformFor({ os: 'Darwin', arch: 'arm64' })).toBe('osx-arm64')
        expect(platformFor({ os: 'Linux', arch: 'x86_64' })).toBe('linux-x64')
        expect(() => platformFor({ os: 'Windows', arch: 'AMD64' })).toThrow('Unsupported')
    })
})
describe('lifecycle safety', () => {
    test('busy, unknown, offline or scope lookup failure blocks interruption', () => {
        assertIdle(row, data)
        for (const unsafe of [{ ...row, busy: true }, { ...row, busy: null }, { ...row, githubStatus: 'offline' }]) expect(() => assertIdle(unsafe, data)).toThrow()
        expect(() => assertIdle(row, { ...data, errors: [{ target: 'tana3d', message: 'API unavailable' }] })).toThrow()
        assertIdle({ ...row, busy: true }, data, true)
        assertIdle(row, { ...data, errors: [{ target: 'unrelated-org', message: 'API unavailable' }] })
    })
    test('ambiguous/unmanaged targets cannot control arbitrary services', () => {
        expect(select(data, 'tana3d/bugsy').service.directory).toBe(local.directory)
        expect(() => select({ ...data, runners: [row, row] }, row.selector)).toThrow('unique')
        expect(() => select({ ...data, runners: [{ ...row, service: null }] }, row.selector)).toThrow('not mapped')
    })
    test('OS dependency installation and SDK update require verified digest', () => {
        expect(() => downloadScript({ url: 'https://github.com/actions/runner/releases/download/v1/file.tar.gz', sha256: 'bad', version: '1' })).toThrow('checksum')
        const script = downloadScript({ url: 'https://github.com/actions/runner/releases/download/v1/file.tar.gz', sha256: 'a'.repeat(64), version: '1' })
        expect(script).toContain('Runner checksum mismatch'); expect(script).toContain('--proto')
    })
    test('service scripts address only the chosen SDK and preserve Linux stop success', () => {
        const script = serviceScript('/home/sami/runner', ['stop', 'start'], 'Linux', 'sami')
        expect(script).toContain('sudo -n ./svc.sh stop ||'); expect(script).toContain('inactive|failed')
        expect(script).not.toContain('killall'); expect(script).not.toContain('pkill')
        expect(() => serviceScript('/', ['stop'], 'Linux', 'sami')).toThrow()
        expect(() => serviceScript('/tmp/../home', ['stop'], 'Linux', 'sami')).toThrow()
        expect(() => serviceScript('/tmp/runner', ['delete-everything'], 'Linux', 'sami')).toThrow()
    })
    test('shell quoting preserves literal text without executing substitutions', async () => {
        const text = `something'$(printf DANGER)\nwith spaces`
        expect(await execute(['sh', '-c', `printf %s ${shell(text)}`])).toBe(text)
    })
    test('scope and custom label validation rejects command injection', () => {
        expect(endpoint('tana3d/studio')).toBe('repos/tana3d/studio/actions')
        expect(() => endpoint('org/../admin')).toThrow()
        expect(labelList('darwin-x64,studio,studio')).toEqual(['darwin-x64', 'studio'])
        expect(() => labelList('studio,$(malicious)')).toThrow()
    })
    test('config is atomic, private, and rejects SSH options', async () => {
        const directory = await mkdtemp(join(tmpdir(), 'runnerwatch-test-')), path = join(directory, 'config.json')
        try {
            expect(await loadSettings(path)).toEqual({ schema: 1, hosts: {}, scopes: [], registrations: {} })
            await saveSettings({ schema: 1, hosts: { bugsy: { ssh: 'sami@bugsy', directories: [] } }, scopes: ['tana3d'], registrations: {} }, path)
            expect((await stat(path)).mode & 0o777).toBe(0o600)
            expect((await loadSettings(path)).hosts.bugsy?.ssh).toBe('sami@bugsy')
            const contents = await readFile(path, 'utf8'); expect(contents).not.toContain('token')
            await expect(saveSettings({ schema: 1, hosts: { bad: { ssh: '-oProxyCommand=evil', directories: [] } }, scopes: [], registrations: {} }, path)).rejects.toThrow()
            expect(await readFile(path, 'utf8')).toBe(contents)
        } finally { await rm(directory, { recursive: true }) }
    })
})
describe('CLI contract', () => {
    test('help comes from commands, including lifecycle actions', async () => {
        const help = JSON.parse(await execute(['bun', 'src/runnerwatch.ts', '--help']))
        expect(help.commands.map((command: { name: string }) => command.name)).toContain('init')
        expect(help.commands.map((command: { name: string }) => command.name)).toContain('remove')
    })
    test('noninteractive init needs machine/scope flags', async () => {
        await expect(execute(['bun', 'src/runnerwatch.ts', '--config', '/tmp/runnerwatch-nonexistent-test.json', 'init'])).rejects.toThrow('Noninteractive setup')
    })
})
