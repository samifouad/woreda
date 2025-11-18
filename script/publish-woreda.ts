#!/usr/bin/env bun

import { $ } from "bun"
import path from "path"

// Woreda simplified publish script
// No cloud APIs, no binaries, just npm!

const notes = [] as string[]

console.log("=== Publishing Woreda ===\n")

// Get version from env or package.json
const BUMP = process.env["WOREDA_BUMP"] || "patch"
const VERSION_OVERRIDE = process.env["WOREDA_VERSION"]
const CHANNEL = process.env["WOREDA_CHANNEL"] || "latest"
const PREVIEW = process.env["WOREDA_PREVIEW"] === "true"

// Calculate new version
let version: string
if (VERSION_OVERRIDE) {
  version = VERSION_OVERRIDE
} else {
  const pkg = await Bun.file("./packages/opencode/package.json").json()
  const current = pkg.version
  const [major, minor, patch] = current.split(".").map(Number)

  switch (BUMP) {
    case "major":
      version = `${major + 1}.0.0`
      break
    case "minor":
      version = `${major}.${minor + 1}.0`
      break
    case "patch":
    default:
      version = `${major}.${minor}.${patch + 1}`
      break
  }
}

console.log(`Version: ${version}`)
console.log(`Channel: ${CHANNEL}`)
console.log(`Preview: ${PREVIEW}\n`)

if (!PREVIEW) {
  // Get changelog since last version
  try {
    const previous = await fetch("https://registry.npmjs.org/woreda/latest")
      .then((res) => res.ok ? res.json() : null)
      .then((data: any) => data?.version || "0.1.0")

    console.log(`Generating changelog since v${previous}...\n`)

    const log = await $`git log v${previous}..HEAD --oneline --format="%h %s"`.text()

    const commits = log
      .split("\n")
      .filter((line) => line && !line.match(/^\w+ (chore:|ci:|test:)/i))
      .map(line => `- ${line.substring(8)}`) // Remove commit hash

    notes.push(...commits.slice(0, 20)) // Limit to 20 most recent

    if (notes.length > 0) {
      console.log("---- Changelog ----")
      console.log(notes.join("\n"))
      console.log("-------------------\n")
    }
  } catch (e) {
    console.log("Could not fetch previous version, skipping changelog")
  }
}

// Update all package.json files with new version
const pkgjsons = await Array.fromAsync(
  new Bun.Glob("**/package.json").scan({ absolute: true }),
).then((arr) => arr.filter((x) =>
  !x.includes("node_modules") &&
  !x.includes("dist") &&
  !x.includes(".next")
))

for (const file of pkgjsons) {
  let pkg = await Bun.file(file).text()
  pkg = pkg.replaceAll(/"version": "[^"]+"/g, `"version": "${version}"`)
  console.log("updated:", file)
  await Bun.file(file).write(pkg)
}

// Install dependencies with new versions
await $`bun install`

console.log("\n=== Building Woreda ===\n")
await $`cd packages/opencode && bun run build`

console.log("\n=== Publishing to npm ===\n")
const pkgPath = path.resolve("./packages/opencode")
process.chdir(pkgPath)

if (!PREVIEW) {
  await $`bun publish --access public --tag ${CHANNEL}`
  console.log(`\n✅ Published woreda@${version} to npm!\n`)
} else {
  console.log("Preview mode - skipping actual publish")
}

// Go back to root
process.chdir(path.resolve(__dirname, ".."))

if (!PREVIEW) {
  // Commit and tag
  await $`git add .`
  await $`git commit -m "release: v${version}"`
  await $`git tag v${version}`

  console.log("\n=== Pushing to GitHub ===\n")
  await $`git push origin main --tags --no-verify`

  // Wait for tag to propagate
  await new Promise((resolve) => setTimeout(resolve, 3000))

  // Create GitHub release
  const releaseNotes = notes.length > 0
    ? notes.join("\n")
    : "No notable changes"

  console.log("\n=== Creating GitHub Release ===\n")
  await $`gh release create v${version} --title "v${version}" --notes ${releaseNotes}`

  console.log(`\n🎉 Woreda v${version} released!\n`)
  console.log(`npm: https://www.npmjs.com/package/woreda`)
  console.log(`GitHub: https://github.com/samifouad/woreda/releases/tag/v${version}`)
}
