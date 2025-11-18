# Woreda

**The Ollama-first AI coding agent built for the terminal.**

> Forked from OpenCode to focus exclusively on local Ollama models with tool support.

[![Build status](https://img.shields.io/github/actions/workflow/status/samifouad/woreda/test.yml?style=flat-square&branch=main)](https://github.com/samifouad/woreda/actions)

---

## Philosophy

Woreda is **100% local-first**. No cloud APIs, no billing, no authentication. Just you, your powerful machine, and open-source models running locally via Ollama.

**Why local-only?**
- **Privacy**: Your code never leaves your machine
- **Cost**: $0.00 per token, forever
- **Speed**: No network latency, just raw local inference
- **Control**: Full context window management, no provider limits
- **Focus**: Built for code, not chat

We target **Ollama models with tool support** - specifically optimized for coding tasks like qwen2.5-coder, deepseek-coder-v2, and llama3.2.

---

## Installation

### Prerequisites

1. **Install Ollama**: https://ollama.com/download
2. **Pull a tool-capable model**:
   ```bash
   ollama pull qwen2.5-coder        # Recommended: Best for code
   ollama pull deepseek-coder-v2    # Alternative: Great for reasoning
   ollama pull llama3.2             # Alternative: Fast and capable
   ```

### Install Woreda

```bash
# From npm (recommended)
npm install -g woreda
# or
bun install -g woreda

# From source
git clone https://github.com/samifouad/woreda.git
cd woreda
bun install
bun run build
bun link
```

---

## Supported Models

Woreda **only shows Ollama models with tool support**. These models can execute bash commands, edit files, and use the full agent toolkit.

**Recommended:**
- `qwen2.5-coder` - Best overall for code generation
- `deepseek-coder-v2` - Excellent reasoning capabilities
- `llama3.2` - Fast and lightweight (3B/1B variants available)

**Also supported:**
- `llama3.1` - Strong general capabilities
- `mistral-nemo` - Good balance of speed and capability
- `command-r` - Cohere's code model
- `firefunction-v2` - Fireworks' function-calling specialist

### Model Requirements

To appear in Woreda, a model must:
1. Be available in your local Ollama instance
2. Support tool/function calling
3. Match one of the known tool-capable model families

---

## Features

### Agent System

Switch between agents with the `Tab` key:

- **build** (default) - Full access agent for development work
- **plan** - Read-only agent for analysis and exploration
  - Denies file edits by default
  - Asks permission before running bash commands
  - Perfect for exploring unfamiliar codebases

### Tools

All the standard tools you need:
- `bash` - Execute shell commands (with tree-sitter safety parsing)
- `edit` - Intelligent file editing with diffs
- `read` - Smart file reading
- `write` - File creation
- `grep` - Pattern-based code search
- `glob` - File pattern matching
- `task` - Multi-step task execution
- `mcp` - Model Context Protocol support

### MCP Support

Woreda keeps **full MCP (Model Context Protocol) support**. Connect external tools and servers:

```jsonc
// .opencode/opencode.jsonc
{
  "mcp": {
    "servers": {
      "filesystem": {
        "command": "npx",
        "args": ["-y", "@modelcontextprotocol/server-filesystem", "/path/to/allowed/files"]
      }
    }
  }
}
```

### Context Window Management

One of Woreda's core focuses is **intelligent context window management** for local models:

- Auto-detects model context limits (32K, 64K, 128K)
- Smart message compaction
- Session snapshots and revert
- Optimized for tool-heavy workflows

---

## Configuration

Woreda uses the same config format as OpenCode for compatibility:

```jsonc
// .opencode/opencode.jsonc (or ~/.opencode/opencode.jsonc)
{
  "model": "ollama/qwen2.5-coder:latest",
  "provider": {
    "ollama": {
      "api": "http://localhost:11434/v1",  // Override if needed
      "models": {
        "custom-model:latest": {
          "name": "My Custom Model",
          "tool_call": true  // Mark as tool-capable
        }
      }
    }
  }
}
```

### Environment Variables

- `OLLAMA_HOST` - Override Ollama API location (default: `http://localhost:11434`)

---

## Usage

```bash
# Start TUI
woreda spawn

# Run a one-off command
woreda run "refactor this function to use async/await"

# Attach to running server
woreda tui attach
```

---

## Development

```bash
# Clone and setup
git clone https://github.com/samifouad/woreda.git
cd woreda
bun install

# Run in dev mode
bun dev

# Type checking
bun typecheck

# Tests
bun test

# Build
bun run build
```

---

## Roadmap

### Core Focus: Local Model Excellence
- [ ] Enhanced context window management
- [ ] Model-specific prompt optimization
- [ ] Advanced agent collaboration
- [ ] Session branching and merging
- [ ] Intelligent tool selection per model

### Ollama Integration
- [ ] Auto-pull missing models
- [ ] Model performance benchmarking
- [ ] Quantization recommendations
- [ ] `num_ctx` auto-tuning
- [ ] Model switching based on task

### Developer Experience
- [ ] Rich TUI improvements (inspired by neovim)
- [ ] Better error handling for local models
- [ ] Streaming performance optimization
- [ ] Offline-first architecture

---

## FAQ

### Why fork OpenCode?

OpenCode is excellent but deliberately multi-provider. It treats Ollama as a second-class citizen requiring manual JSON configuration. Woreda inverts this: **Ollama is the only citizen**, and we optimize everything around local model performance.

### Why Ollama only?

Focus breeds excellence. By targeting only Ollama, we can:
- Optimize context window management for local constraints
- Build features specific to local model capabilities
- Remove all authentication, billing, and cloud complexity
- Provide a superior local-first developer experience

### What about cloud models?

They're great! But they're also well-served by existing tools (including OpenCode, Claude Code, Cursor, etc.). Woreda carves out a niche: **local-only, privacy-first, cost-free coding**.

### Can I still use MCP?

Absolutely! MCP is provider-agnostic and works great with local models.

### Will you add cloud providers back?

No. That's the whole point. Use OpenCode if you want multi-provider support.

---

## Credits

Forked from [OpenCode](https://github.com/sst/opencode) by the SST team. Massive respect for their work building the foundation.

Changes:
- Ripped out all cloud providers (Anthropic, OpenAI, Google, AWS, Azure)
- Ripped out authentication system
- Focused exclusively on Ollama models with tool support
- TUI-only (removed web console and desktop app)
- Renamed to Woreda (Ethiopian for "district" - representing local community)

---

## License

MIT (inherited from OpenCode)
