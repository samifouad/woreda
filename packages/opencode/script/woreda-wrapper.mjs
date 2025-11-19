#!/usr/bin/env node

import { spawn } from "child_process"
import os from "os"
import path from "path"
import { fileURLToPath } from "url"
import { createRequire } from "module"

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const require = createRequire(import.meta.url)

function detectPlatformAndArch() {
  let platform
  switch (os.platform()) {
    case "darwin":
      platform = "darwin"
      break
    case "linux":
      platform = "linux"
      break
    case "win32":
      platform = "windows"
      break
    default:
      platform = os.platform()
      break
  }

  let arch
  switch (os.arch()) {
    case "x64":
      arch = "x64"
      break
    case "arm64":
      arch = "arm64"
      break
    default:
      arch = os.arch()
      break
  }

  return { platform, arch }
}

function findBinary() {
  const { platform, arch } = detectPlatformAndArch()
  const packageName = `woreda-${platform}-${arch}`
  const binaryName = platform === "windows" ? "woreda.exe" : "woreda"

  try {
    const packageJsonPath = require.resolve(`${packageName}/package.json`)
    const packageDir = path.dirname(packageJsonPath)
    return path.join(packageDir, "bin", binaryName)
  } catch (error) {
    console.error(`Could not find package ${packageName}: ${error.message}`)
    process.exit(1)
  }
}

const binaryPath = findBinary()
const proc = spawn(binaryPath, process.argv.slice(2), { stdio: "inherit" })

proc.on("exit", (code) => {
  process.exit(code ?? 1)
})
