# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Woreda is an Ollama-first AI coding agent built for the terminal. It's a fork of OpenCode with all cloud providers removed, focusing exclusively on local Ollama models with tool support. The project is privacy-first (100% local), cost-free ($0.00 per token), and optimized for local inference with no network latency.

## Development Commands

### Building
```bash
# Build the project
bun run build

# Build specific package
bun run build --single  # Build for current platform only

# Development mode
bun dev                  # Run from packages/opencode with browser conditions
```

### Testing
```bash
# Run all tests
bun test

# Run tests for specific package
cd packages/opencode && bun test

# Type checking
bun typecheck            # Uses turbo to check all packages
```

### Running Woreda
```bash
# Start TUI (Terminal UI)
woreda spawn

# Run one-off command
woreda run "your prompt here"

# Attach to running server
woreda tui attach

# List available models
woreda models

# Refresh Ollama models
woreda ollama refresh
```

## Architecture

### Monorepo Structure

This is a **Bun + Turbo monorepo** with workspaces in `packages/`:
- **packages/opencode** - Main CLI and agent implementation (binary: `woreda`)
- **packages/ui** - SolidJS UI components for rendering
- **packages/script** - Build scripts and utilities
- **packages/plugin** - Plugin system
- **packages/sdk/js** - JavaScript SDK
- **packages/slack** - Slack integration (legacy from OpenCode)
- **packages/desktop** - Desktop app (removed in Woreda)
- **packages/web** - Web console (removed in Woreda)
- **packages/function** - Serverless functions (legacy)

The main codebase is in `packages/opencode/src/`.

### Core Systems

#### Provider System (`src/provider/`)
- **models.ts** - Ollama model discovery and management
  - Fetches models from `http://localhost:11434/api/tags`
  - Filters for tool-capable models only (qwen2.5-coder, deepseek-coder-v2, llama3.2, etc.)
  - Caches model list for 5 minutes in `~/.cache/opencode/ollama-models.json`
  - Auto-detects context windows: 32K, 64K, 128K, or 131K based on model
- **provider.ts** - Provider abstraction using Vercel AI SDK
  - Only loads `@ai-sdk/openai-compatible` for Ollama
  - No cloud provider SDKs (Anthropic/OpenAI/Google removed)

#### Agent System (`src/agent/`)
- **Two built-in agents:**
  - **build** - Full access agent (default) - allows edits and bash commands
  - **plan** - Read-only agent - denies edits, asks permission for bash (except safe read commands)
- Agents have configurable permissions for `edit`, `bash`, `webfetch`, `doom_loop`, `external_directory`
- Agent switching via Tab key in TUI

#### Session System (`src/session/`)
- **index.ts** - Session lifecycle and state management
- **message-v2.ts** - Message handling (user/assistant/tool)
- **compaction.ts** - Context window management (critical for local models)
  - Auto-compacts messages when approaching context limit
  - Preserves recent messages and system prompts
- **prompt.ts** - System prompt construction
- **revert.ts** - Session snapshot/revert functionality

#### Tool System (`src/tool/`)
All tools follow a common pattern: `{tool}.ts` (implementation) + `{tool}.txt` (system prompt).

Core tools:
- **bash.ts** - Execute shell commands (tree-sitter safety parsing)
- **edit.ts** - File editing with precision diffs
- **read.ts** - Smart file reading (supports images, PDFs, notebooks)
- **write.ts** - File creation
- **grep.ts** - Pattern-based code search (wraps ripgrep)
- **glob.ts** - File pattern matching
- **task.ts** - Multi-step task execution (spawns subagents)
- **todo.ts** - Task list management

MCP tools automatically discovered from configured MCP servers.

#### Configuration (`src/config/`)
- Loads from multiple sources (merged in order):
  1. Global config: `~/.opencode/opencode.jsonc`
  2. Project configs (bottom-up): `.opencode/opencode.jsonc` or `opencode.json`
  3. Environment: `OPENCODE_CONFIG` file path
  4. Environment: `OPENCODE_CONFIG_CONTENT` JSON string
- Supports JSONC (JSON with comments)
- Config includes: model selection, agent definitions, permissions, MCP servers, plugins

Example config:
```jsonc
{
  "model": "ollama/qwen2.5-coder:latest",
  "provider": {
    "ollama": {
      "api": "http://localhost:11434/v1"
    }
  },
  "mcp": {
    "servers": {
      "filesystem": {
        "command": "npx",
        "args": ["-y", "@modelcontextprotocol/server-filesystem", "/path/to/files"]
      }
    }
  }
}
```

#### CLI Commands (`src/cli/cmd/`)
Main entry point: `src/index.ts` (yargs-based CLI)

Commands:
- `spawn` - Start TUI session
- `tui attach` - Attach to running session
- `run <prompt>` - One-off command execution
- `models` - List available Ollama models
- `ollama refresh` - Refresh Ollama model cache
- `export` - Export session
- `import` - Import session
- `agent` - Agent management
- `mcp` - MCP server management
- `stats` - Session statistics

### Key Differences from OpenCode

Woreda has **removed** from the OpenCode fork:
1. All cloud providers (Anthropic, OpenAI, Google, AWS Bedrock, Azure)
2. Authentication system (no API keys needed)
3. Web console UI
4. Desktop Electron app
5. Billing/usage tracking for cloud APIs
6. Multi-provider model selection UI

Woreda **keeps** from OpenCode:
1. Full MCP (Model Context Protocol) support
2. Complete tool system
3. Agent framework
4. Context window management (critical for local models)
5. TUI interface
6. Session management and snapshotting

### Model Requirements

To appear in Woreda's model list, an Ollama model must:
1. Be available locally via `ollama list`
2. Match a known tool-capable model family (see `TOOL_CAPABLE_MODELS` in `src/provider/models.ts`)

Known tool-capable models:
- qwen2.5-coder
- deepseek-coder-v2
- llama3.2, llama3.1
- mistral-nemo
- command-r
- firefunction-v2

### Context Window Strategy

Woreda optimizes for local models with limited context:
- Auto-detects context limits (32K-131K)
- Aggressively compacts message history when near limit
- Preserves system prompts and recent tool calls
- Session snapshots allow reverting to earlier states

## Important Implementation Notes

### Tool Implementation Pattern
When adding/modifying tools:
1. Create `{tool}.ts` with zod schema and execute function
2. Create `{tool}.txt` with system prompt for the model
3. Register in `src/tool/registry.ts`

### Ollama API Integration
- Uses OpenAI-compatible endpoint: `http://localhost:11434/v1`
- Model IDs must include tags: `qwen2.5-coder:latest` not `qwen2.5-coder`
- Tool calls use standard OpenAI format (handled by `@ai-sdk/openai-compatible`)

### Permission System
Agents check permissions via `Config.Permission` (deny/allow/ask):
- **edit** - File modification permission
- **bash** - Shell command execution (supports glob patterns like `git *`)
- **webfetch** - HTTP requests
- **doom_loop** - Detect and prevent infinite loops
- **external_directory** - Access files outside project

### Binary Distribution
Build creates platform-specific binaries:
- Uses Bun's `--compile` flag
- Targets: darwin-arm64, darwin-x64, linux-arm64, linux-x64, win32-x64
- Includes native dependencies: tree-sitter, parcel-watcher
- Entry point: `packages/opencode/bin/opencode` (shell wrapper)

## Debugging

- Logs written to `~/.cache/opencode/logs/`
- Use `--print-logs` flag to see logs in stderr
- Use `--log-level DEBUG|INFO|WARN|ERROR`
- `woreda debug` commands for inspecting state

## Package Manager

This project uses **Bun** exclusively:
- Lock file: `bun.lockb`
- Package manager version: `1.3.2` (specified in package.json)
- Never use npm/yarn/pnpm - always use `bun install`, `bun run`, `bun test`
