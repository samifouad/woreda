import { cmd } from "./cmd"
import * as prompts from "@clack/prompts"
import { UI } from "../ui"

// Woreda uses Ollama locally - no authentication needed!
export const AuthCommand = cmd({
  command: "auth",
  describe: "authentication (not needed for local Ollama)",
  async handler() {
    UI.empty()
    prompts.intro("Woreda - Local Ollama Only")
    prompts.log.info("Woreda uses Ollama running locally on your machine.")
    prompts.log.info("No API keys or authentication required!")
    prompts.log.step("Make sure Ollama is running: ollama serve")
    prompts.log.step("Pull a tool-capable model: ollama pull qwen2.5-coder")
    prompts.log.step("Other recommended models: llama3.2, deepseek-coder-v2, mistral-nemo")
    prompts.outro("Ready to code!")
  },
})
