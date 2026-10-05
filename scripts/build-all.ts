import { mkdir } from 'node:fs/promises'
const supported = ['darwin-arm64', 'darwin-x64', 'linux-x64', 'linux-arm64']
const wanted = process.argv.slice(2)
if (wanted.some(target => !supported.includes(target))) throw Error(`Targets: ${supported.join(', ')}`)
await mkdir('dist', { recursive: true })
for (const target of wanted.length ? wanted : supported) {
    const child = Bun.spawn(['bun', 'build', 'src/woreda.ts', '--compile', `--target=bun-${target}`, '--outfile', `dist/woreda-${target}`], { stdout: 'inherit', stderr: 'inherit' })
    if (await child.exited) process.exit(1)
    // Cross-compilation appends the JS payload; repair the Mach-O ad hoc signature.
    if (target.startsWith('darwin') && process.platform === 'darwin') {
        const sign = Bun.spawn(['codesign', '--force', '--sign', '-', `dist/woreda-${target}`], { stdout: 'inherit', stderr: 'inherit' })
        if (await sign.exited) process.exit(1)
        const verify = Bun.spawn(['codesign', '--verify', '--strict', `dist/woreda-${target}`], { stdout: 'inherit', stderr: 'inherit' })
        if (await verify.exited) process.exit(1)
    }
}
