# Runnerwatch

**GitHub Actions self-hosting.** Set up your own runners, see whether they're working, and manage them from your terminal.

## Get started

Download a standalone executable from [Releases](https://github.com/samifouad/runnerwatch/releases), make it executable, and put it on your PATH. It does not need Bun.

Install the [GitHub CLI](https://cli.github.com/), then:

```sh
gh auth login
runnerwatch init --human
```

The setup wizard asks which machine to connect to and which GitHub repository or organization it should work for. For example: `sami@bugsy` and `tana3d/studio`. It installs GitHub's runner, starts the service, and checks that GitHub sees it online.

Your machine needs to be reachable over SSH first. macOS and Linux runners are supported; Linux needs systemd and passwordless sudo. Your GitHub login needs permission to manage the selected runners.

## See what's happening

Show each runner and whether it's idle, busy or stopped:

```sh
runnerwatch status --human
```

Keep a live dashboard open:

```sh
runnerwatch watch
```

Check for problems:

```sh
runnerwatch doctor --human
```

## Add a machine that already has runners

```sh
runnerwatch hosts add bugsy --ssh sami@bugsy
runnerwatch status --human
```

Existing installations in `~/actions-runner*` are discovered automatically. Adding a machine here does not create a new runner.

## Add another runner

Use the wizard again:

```sh
runnerwatch add --human
```

Or tell it exactly what to set up. This adds a machine called `builder`, then registers it for one repository:

```sh
runnerwatch hosts add builder --ssh sami@builder
runnerwatch add --host builder --scope tana3d/studio --name builder --labels studio --install-dependencies
```

## Inspect a job or restart a runner

```sh
runnerwatch jobs tana3d/studio --human
runnerwatch logs tana3d/studio/builder --lines 50
runnerwatch restart tana3d/studio/builder --human
```

Copy the runner name from `runnerwatch status` when using logs, restart, update or removal. Repository runners use `owner/repo/runner`; organization runners use `org/runner`. Runnerwatch refuses to interrupt a busy runner by default.

## Update, repair or remove

```sh
runnerwatch update tana3d/studio/builder --human
runnerwatch repair tana3d/studio/builder --human
runnerwatch remove tana3d/studio/builder --yes
```

Removal unregisters the runner and removes its service, keeping its files. Add `--purge` to delete an installation created by Runnerwatch too.

## More details

Output is JSON by default; add `--human` for readable status. The detailed [reference](docs/reference.md) covers access groups, labels, configuration, credentials, custom folders and recovery.

To develop locally:

```sh
bun install --frozen-lockfile
bun run typecheck
bun test
bun run build
```

The CLI lives in one TypeScript file, `src/runnerwatch.ts`. Standalone releases include the runtime. No dashboard server or separate database is needed.
