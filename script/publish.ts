#!/usr/bin/env bun

import { $ } from "bun"
import { Script } from "@opencode-ai/script"

const notes = [] as string[]

console.log("=== publishing ===\n")

if (!Script.preview) {
  // Try to get previous version from npm registry
  try {
    const previous = await fetch("https://registry.npmjs.org/woreda/latest")
      .then((res) => {
        if (!res.ok) return null
        return res.json()
      })
      .then((data: any) => data?.version)

    if (previous) {
      const log =
        await $`git log v${previous}..HEAD --oneline --format="%h %s" -- packages/opencode packages/sdk packages/plugin`.text()

      const commits = log
        .split("\n")
        .filter((line) => line && !line.match(/^\w+ (ignore:|test:|chore:|ci:)/i))
        .join("\n")

      if (commits) {
        notes.push(`Changes since ${previous}:`)
        notes.push(commits)
      }
    }
  } catch (e) {
    console.log("Could not fetch previous version from npm, skipping changelog")
  }

  if (notes.length === 0) {
    notes.push("Initial release of Woreda - Ollama-first AI coding agent")
  }

  console.log("---- Changelog ----")
  console.log(notes.join("\n"))
  console.log("-------------------")
}

const pkgjsons = await Array.fromAsync(
  new Bun.Glob("**/package.json").scan({
    absolute: true,
  }),
).then((arr) => arr.filter((x) => !x.includes("node_modules") && !x.includes("dist")))

for (const file of pkgjsons) {
  let pkg = await Bun.file(file).text()
  pkg = pkg.replaceAll(/"version": "[^"]+"/g, `"version": "${Script.version}"`)
  console.log("updated:", file)
  await Bun.file(file).write(pkg)
}

const extensionToml = new URL("../packages/extensions/zed/extension.toml", import.meta.url).pathname
let toml = await Bun.file(extensionToml).text()
toml = toml.replace(/^version = "[^"]+"/m, `version = "${Script.version}"`)
toml = toml.replaceAll(/releases\/download\/v[^/]+\//g, `releases/download/v${Script.version}/`)
console.log("updated:", extensionToml)
await Bun.file(extensionToml).write(toml)

await $`bun install`

console.log("\n=== opencode ===\n")
await import(`../packages/opencode/script/publish.ts`)

// Skip SDK and plugin publishing for Woreda
// console.log("\n=== sdk ===\n")
// await import(`../packages/sdk/js/script/publish.ts`)

// console.log("\n=== plugin ===\n")
// await import(`../packages/plugin/script/publish.ts`)

const dir = new URL("..", import.meta.url).pathname
process.chdir(dir)

if (!Script.preview) {
  await $`git commit -am "release: v${Script.version}"`
  await $`git tag v${Script.version}`
  await $`git fetch origin`
  await $`git cherry-pick HEAD..origin/dev`.nothrow()
  await $`git push origin HEAD --tags --no-verify --force-with-lease`
  await new Promise((resolve) => setTimeout(resolve, 5_000))
  await $`gh release create v${Script.version} --title "v${Script.version}" --notes ${notes.join("\n") ?? "No notable changes"} ./packages/opencode/dist/*.zip`
}
