import { afterAll, beforeAll, describe, expect, test } from 'bun:test'
import { mkdtemp, rm } from 'node:fs/promises'
import { tmpdir } from 'node:os'
import { join } from 'node:path'
import { humanText, logo, saveSettings } from '../src/woreda.ts'

let directory: string, config: string, empty: string
beforeAll(async () => {
    directory = await mkdtemp(join(tmpdir(), 'woreda-output-'))
    config = join(directory, 'config.json'); empty = join(directory, 'empty.json')
    await saveSettings({ schema: 1, hosts: { builder: { ssh: 'sami@builder', directories: [] } }, scopes: [], registrations: {} }, config)
    await saveSettings({ schema: 1, hosts: {}, scopes: [], registrations: {} }, empty)
})
afterAll(async () => { await rm(directory, { recursive: true }) })
const plain = (text: string) => text.replace(/\x1b\[[0-?]*[ -/]*[@-~]/g, '').replaceAll('\r', '')
async function cli(args: string[], terminal = false) {
    const command = ['bun', 'src/woreda.ts', ...args]
    const env: Record<string, string | undefined> = { ...process.env, FORCE_COLOR: '3', TERM: 'xterm-256color' }
    delete env.NO_COLOR
    if (terminal) {
        const chunks: Buffer[] = []
        const child = Bun.spawn(command, { env, terminal: { cols: 120, rows: 60, data(_terminal, chunk) { chunks.push(Buffer.from(chunk)) } } })
        try {
            const code = await child.exited
            await Bun.sleep(10)
            const stdout = Buffer.concat(chunks).toString()
            return { code, stdout, stderr: stdout }
        } finally { child.terminal?.close() }
    }
    const child = Bun.spawn(command, { stdin: 'ignore', stdout: 'pipe', stderr: 'pipe', env })
    const [stdout, stderr, code] = await Promise.all([new Response(child.stdout).text(), new Response(child.stderr).text(), child.exited])
    return { code, stdout, stderr }
}

describe('output in a real terminal and pipe', () => {
    test('terminal results are indented and coloured; pipes stay compact even with FORCE_COLOR', async () => {
        const args = ['--config', config, 'hosts', 'list']
        const tty = await cli(args, true), pipe = await cli(args)
        expect(tty.code).toBe(0); expect(tty.stdout).toContain('\x1b[')
        expect(plain(tty.stdout)).toContain('\n  "builder":')
        expect(pipe.stdout).not.toContain('\x1b')
        expect(pipe.stdout.trim().split('\n')).toHaveLength(1)
        expect(JSON.parse(pipe.stdout).builder.ssh).toBe('sami@builder')
        expect(JSON.parse(plain(tty.stdout))).toEqual(JSON.parse(pipe.stdout))
    })
    test('--nopretty gives a single uncoloured JSON line in a terminal, including help', async () => {
        for (const args of [['--config', config, 'hosts', 'list'], ['--help']]) {
            const result = await cli(['--nopretty', ...args], true)
            expect(result.code).toBe(0); expect(result.stdout).not.toContain('\x1b')
            expect(plain(result.stdout).trim().split('\n')).toHaveLength(1)
            expect(() => JSON.parse(plain(result.stdout))).not.toThrow()
        }
    })
    test('help gets the yellow block banner only in a terminal; piped help remains indented JSON', async () => {
        const tty = await cli(['--help'], true), pipe = await cli(['--help'])
        expect(plain(tty.stdout)).toContain(logo(false))
        expect(tty.stdout).toContain('\x1b[38;2;255;255;85m')
        expect(pipe.stdout).not.toContain('█'); expect(pipe.stdout).not.toContain('\x1b')
        expect(pipe.stdout).toContain('\n  "name":'); expect(JSON.parse(pipe.stdout).name).toBe('woreda')
        const explicit = await cli(['--json', '--help'], true)
        expect(explicit.stdout).not.toContain('█'); expect(JSON.parse(plain(explicit.stdout)).name).toBe('woreda')
    })
    test('bare --human and command help are readable text with successful exit', async () => {
        for (const args of [['--human'], ['--human', 'hosts', '--help']]) {
            const result = await cli(args)
            expect(result.code).toBe(0); expect(result.stdout).toContain('Usage: woreda')
            expect(result.stdout).not.toContain('"usage"')
        }
        const tty = await cli(['--human'], true)
        expect(plain(tty.stdout)).toContain(logo(false)); expect(plain(tty.stdout)).toContain('Usage: woreda')
    })
    test('--human renders host details as text and --json overrides it', async () => {
        const human = await cli(['--human', '--config', config, 'hosts', 'list'])
        expect(human.stdout).toContain('Builder:\n  Ssh: sami@builder')
        expect(human.stdout).not.toContain('{')
        const json = await cli(['--human', '--json', '--config', config, 'hosts', 'list'])
        expect(JSON.parse(json.stdout).builder.ssh).toBe('sami@builder')
        expect(humanText([{ name: 'Build', status: 'completed', url: 'https://github.com/example' }])).toContain('NAME')
    })
    test('errors follow stderr terminal formatting and human/json precedence', async () => {
        const tty = await cli(['jobs', 'not-a-repo'], true), pipe = await cli(['jobs', 'not-a-repo'])
        expect(tty.code).toBe(1); expect(tty.stderr).toContain('\x1b')
        expect(JSON.parse(plain(tty.stderr)).error.code).toBe('operation_failed')
        expect(pipe.stdout).toBe(''); expect(pipe.stderr.trim().split('\n')).toHaveLength(1)
        for (const args of [['--human', '--json', 'unknown-command'], ['--human', '--json', 'jobs', 'not-a-repo']]) {
            const result = await cli(args)
            expect(result.code).toBe(1); expect(JSON.parse(result.stderr).error).toBeDefined()
        }
        const human = await cli(['--human', 'jobs', 'not-a-repo'])
        expect(human.stderr).toContain('Jobs needs owner/repository.'); expect(human.stderr).not.toContain('{')
    })
    test('watch JSON is one compact line per snapshot, even in a terminal', async () => {
        const result = await cli(['--config', empty, '--json', 'watch', '--once'], true)
        expect(result.code).toBe(0); expect(result.stdout).not.toContain('\x1b')
        expect(plain(result.stdout).trim().split('\n')).toHaveLength(1)
        expect(JSON.parse(plain(result.stdout)).runners).toEqual([])
    })
})
