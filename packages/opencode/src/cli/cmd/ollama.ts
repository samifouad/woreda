import { cmd } from "./cmd"
import * as prompts from "@clack/prompts"
import { UI } from "../ui"
import { ModelsDev } from "../../provider/models"

export const OllamaCommand = cmd({
  command: "ollama",
  describe: "Ollama utilities and model management",
  builder: (yargs) =>
    yargs
      .command(OllamaStatusCommand)
      .command(OllamaRecommendCommand)
      .demandCommand(),
  async handler() {},
})

export const OllamaStatusCommand = cmd({
  command: "status",
  describe: "Check Ollama connection and available models",
  async handler() {
    UI.empty()
    prompts.intro("Ollama Status")

    const baseURLs = [
      process.env["OLLAMA_HOST"] ?? "http://localhost:11434",
      "http://127.0.0.1:11434",
    ]

    let connected = false
    for (const baseURL of baseURLs) {
      try {
        const response = await fetch(`${baseURL}/api/tags`, {
          signal: AbortSignal.timeout(3000),
        })

        if (response.ok) {
          prompts.log.success(`Connected to Ollama at ${baseURL}`)
          const data = (await response.json()) as { models: any[] }

          if (data.models.length === 0) {
            prompts.log.warn("No models found!")
            prompts.log.info("Pull a model: ollama pull qwen2.5-coder")
          } else {
            prompts.log.info(`Found ${data.models.length} model(s):`)

            // Get tool-capable models
            const toolCapableModels = [
              "llama3.2", "llama3.1", "qwen2.5-coder", "mistral-nemo",
              "deepseek-coder-v2", "command-r", "firefunction-v2",
            ]

            for (const model of data.models) {
              const hasTools = toolCapableModels.some(capable =>
                model.name.toLowerCase().includes(capable)
              )
              const icon = hasTools ? "✓" : "✗"
              const hint = hasTools ? "(tool support)" : "(no tools)"
              prompts.log.step(`${icon} ${model.name} ${UI.Style.TEXT_DIM}${hint}`)
            }
          }
          connected = true
          break
        }
      } catch (e) {
        // Try next URL
      }
    }

    if (!connected) {
      prompts.log.error("Could not connect to Ollama")
      prompts.log.info("Make sure Ollama is running: ollama serve")
      prompts.log.info("Install Ollama: https://ollama.com/download")
    }

    prompts.outro("Done")
  },
})

export const OllamaRecommendCommand = cmd({
  command: "recommend",
  describe: "Get model recommendations for Woreda",
  async handler() {
    UI.empty()
    prompts.intro("Recommended Models for Woreda")

    prompts.log.message("Woreda works best with tool-capable models:")
    prompts.log.step("")
    prompts.log.step("🥇 qwen2.5-coder")
    prompts.log.message("   Best for code generation, 128K context")
    prompts.log.message("   ollama pull qwen2.5-coder")
    prompts.log.step("")
    prompts.log.step("🥈 deepseek-coder-v2")
    prompts.log.message("   Excellent reasoning, 64K context")
    prompts.log.message("   ollama pull deepseek-coder-v2")
    prompts.log.step("")
    prompts.log.step("🥉 llama3.2")
    prompts.log.message("   Fast and capable, 128K context")
    prompts.log.message("   ollama pull llama3.2")
    prompts.log.step("")
    prompts.log.info("Smaller models for quick tasks:")
    prompts.log.message("   ollama pull qwen2.5-coder:3b   # 3B parameters")
    prompts.log.message("   ollama pull llama3.2:3b        # 3B parameters")

    prompts.outro("Pull a model and start coding!")
  },
})
