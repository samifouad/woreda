# Woreda Development TODO

## Current Status (v5.0.3-dev)

### ✅ Fixed
- Provider initialization working with `ollama-ai-provider-v2@1.5.4`
- Model discovery from local Ollama instance
- Basic chat functionality
- TUI launches and connects to models

### 🐛 Known Issues

#### Tool Calling Not Working
**Status**: In Progress
**Priority**: High
**Affects**: All Ollama models

**Symptoms:**
- Models output tool calls as raw JSON text instead of executing them
- Example output: `{"name": "web_search", "arguments": {"query": "height of Mount Everest"}}`
- Expected: Tool should execute and return results

**Investigation:**
- Tools ARE being passed correctly to `streamText()` (verified in `src/session/prompt.ts:570`)
- `ollama-ai-provider-v2` should support Ollama's `<tool_call>` XML format
- Model generates JSON instead of using structured tool call API

**Reproduction:**
```bash
bun run dev
# Enter: "how tall is mount everest?"
# Observe: Raw JSON output instead of web search execution
```

**Files Involved:**
- `packages/opencode/src/provider/models.ts` - Model configuration
- `packages/opencode/src/provider/provider.ts` - SDK initialization
- `packages/opencode/src/session/prompt.ts` - Tool passing to streamText
- `packages/opencode/src/session/processor.ts` - Tool call processing

**Next Steps:**
1. Verify ollama-ai-provider-v2 configuration
2. Check if model ID format matters (`qwen2.5-coder:7b` vs `qwen2.5-coder`)
3. Test with known working model (llama3.2)
4. Check if we need to set `compatibility: 'strict'` option
5. Investigate if Ollama API version matters
6. Look into provider middleware or transform layer

**Related Commits:**
- `c2897e27` - Added missing @ai-sdk/openai-compatible dependency
- `65a79704` - Switched to ollama-ai-provider-v2 for tool calling support

---

## Roadmap

### High Priority
- [ ] Fix tool calling (see above)
- [ ] Test with multiple Ollama models
- [ ] Verify all tools work (bash, edit, read, write, etc.)
- [ ] Publish v5.0.3 once tool calling works

### Medium Priority
- [ ] Update branding from "opencode" to "woreda" in TUI
- [ ] Update system prompts to reference "Woreda"
- [ ] Comprehensive testing with different model sizes
- [ ] Document model-specific quirks and formatting

### Low Priority
- [ ] Model certification system (track which models work well)
- [ ] Per-model prompt optimization
- [ ] Benchmark tool calling performance

---

## Development Notes

### Provider Architecture
- Uses AI SDK with pluggable providers
- `ollama-ai-provider-v2` wraps Ollama's native API
- Provider initialized in `src/provider/provider.ts:getSDK()`
- Different providers may use different patterns:
  - OpenAI-compatible: `createOpenAI()`
  - Ollama v2: `createOllama()`

### Tool System
- Tools defined in `src/tool/registry.ts`
- Passed to AI SDK via `streamText({ tools })`
- Executed via processor in `src/session/processor.ts`
- Tool call format varies by provider (JSON vs XML)

### Testing
```bash
# Type check
bun typecheck

# Run dev mode
bun run dev

# Build
bun run build
```
