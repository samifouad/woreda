#!/usr/bin/env bun
import { $ } from "bun"
import pkg from "../package.json"
import { Script } from "@opencode-ai/script"
import { fileURLToPath } from "url"

const dir = fileURLToPath(new URL("..", import.meta.url))
process.chdir(dir)

const { binaries } = await import("./build.ts")
{
  const name = `${pkg.name}-${process.platform}-${process.arch}`
  console.log(`smoke test: running dist/${name}/bin/woreda --version`)
  await $`./dist/${name}/bin/woreda --version`
}

await $`mkdir -p ./dist/${pkg.name}/bin`
await $`cp ./script/woreda-wrapper.mjs ./dist/${pkg.name}/bin/${pkg.name}`
await $`chmod +x ./dist/${pkg.name}/bin/${pkg.name}`

await Bun.file(`./dist/${pkg.name}/package.json`).write(
  JSON.stringify(
    {
      name: pkg.name + "-ai",
      version: Script.version,
      description: "Woreda - Ollama-first AI coding agent",
      repository: {
        type: "git",
        url: "https://github.com/samifouad/woreda",
      },
      homepage: "https://github.com/samifouad/woreda",
      bugs: {
        url: "https://github.com/samifouad/woreda/issues",
      },
      license: "MIT",
      bin: {
        [pkg.name]: `./bin/${pkg.name}`,
      },
      optionalDependencies: binaries,
    },
    null,
    2,
  ),
)
for (const [name] of Object.entries(binaries)) {
  try {
    process.chdir(`./dist/${name}`)
    if (process.platform !== "win32") {
      await $`chmod 755 -R .`
    }
    await $`bun publish --access public --tag ${Script.channel}`
  } finally {
    process.chdir(dir)
  }
}
await $`cd ./dist/${pkg.name} && bun publish --access public --tag ${Script.channel}`

if (!Script.preview) {
  const major = Script.version.split(".")[0]
  const majorTag = `latest-${major}`
  for (const [name] of Object.entries(binaries)) {
    await $`cd dist/${name} && npm dist-tag add ${name}@${Script.version} ${majorTag}`
  }
  await $`cd ./dist/${pkg.name} && npm dist-tag add ${pkg.name}-ai@${Script.version} ${majorTag}`
}

if (!Script.preview) {
  // Create zip files for GitHub release
  for (const key of Object.keys(binaries)) {
    await $`cd dist/${key}/bin && zip -r ../../${key}.zip *`
  }
  console.log("Created release zip files")
}
