#!/usr/bin/env bun

/**
 * Simple version bump and tag script for Woreda
 *
 * Usage:
 *   ./script/version.ts patch  # 0.1.0 -> 0.1.1
 *   ./script/version.ts minor  # 0.1.0 -> 0.2.0
 *   ./script/version.ts major  # 0.1.0 -> 1.0.0
 *   ./script/version.ts 1.5.0  # specific version
 */

import { $ } from "bun"
import path from "path"

const arg = process.argv[2]

if (!arg) {
  console.error("Usage: ./script/version.ts [major|minor|patch|X.Y.Z]")
  process.exit(1)
}

// Get current version
const pkg = await Bun.file("./packages/opencode/package.json").json()
const current = pkg.version

let newVersion: string

if (arg.match(/^\d+\.\d+\.\d+$/)) {
  // Specific version provided
  newVersion = arg
} else {
  // Bump type provided
  const [major, minor, patch] = current.split(".").map(Number)

  switch (arg) {
    case "major":
      newVersion = `${major + 1}.0.0`
      break
    case "minor":
      newVersion = `${major}.${minor + 1}.0`
      break
    case "patch":
      newVersion = `${major}.${minor}.${patch + 1}`
      break
    default:
      console.error(`Invalid bump type: ${arg}`)
      console.error("Use: major, minor, patch, or X.Y.Z")
      process.exit(1)
  }
}

console.log(`Bumping version: ${current} → ${newVersion}`)

// Update all package.json files
const pkgjsons = await Array.fromAsync(
  new Bun.Glob("**/package.json").scan({ absolute: true })
).then((arr) => arr.filter((x) =>
  !x.includes("node_modules") &&
  !x.includes("dist") &&
  !x.includes(".next")
))

for (const file of pkgjsons) {
  let content = await Bun.file(file).text()
  content = content.replace(
    /"version": "[^"]+"/g,
    `"version": "${newVersion}"`
  )
  await Bun.file(file).write(content)
  console.log(`✓ Updated ${path.relative(process.cwd(), file)}`)
}

// Update lock file
await $`bun install`

// Show git status
console.log("\nGit status:")
await $`git status --short`

console.log(`\n✅ Version bumped to ${newVersion}`)
console.log("\nNext steps:")
console.log(`  git add .`)
console.log(`  git commit -m "chore: bump version to ${newVersion}"`)
console.log(`  git tag v${newVersion}`)
console.log(`  git push origin main --tags`)
console.log(`\nThis will automatically publish to npm! 🚀`)
