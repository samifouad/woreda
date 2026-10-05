import { mkdir } from 'node:fs/promises'
const supported = ['darwin-arm64', 'darwin-x64', 'linux-x64', 'linux-arm64']
const wanted = process.argv.slice(2)
if (wanted.some(target => !supported.includes(target))) throw Error(`Targets: ${supported.join(', ')}`)
await mkdir('dist', { recursive: true })
for (const target of wanted.length ? wanted : supported) {
    const child = Bun.spawn(['bun', 'build', 'src/runnerwatch.ts', '--compile', `--target=bun-${target}`, '--outfile', `dist/runnerwatch-${target}`], { stdout: 'inherit', stderr: 'inherit' })
    if (await child.exited) process.exit(1)
}
