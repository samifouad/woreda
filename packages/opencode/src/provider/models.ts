import { Global } from "../global"
import { Log } from "../util/log"
import path from "path"
import z from "zod"

export namespace ModelsDev {
  const log = Log.create({ service: "ollama.models" })
  const filepath = path.join(Global.Path.cache, "ollama-models.json")

  export const Model = z
    .object({
      id: z.string(),
      name: z.string(),
      release_date: z.string(),
      attachment: z.boolean(),
      reasoning: z.boolean(),
      temperature: z.boolean(),
      tool_call: z.boolean(),
      cost: z.object({
        input: z.number(),
        output: z.number(),
        cache_read: z.number().optional(),
        cache_write: z.number().optional(),
        context_over_200k: z
          .object({
            input: z.number(),
            output: z.number(),
            cache_read: z.number().optional(),
            cache_write: z.number().optional(),
          })
          .optional(),
      }),
      limit: z.object({
        context: z.number(),
        output: z.number(),
      }),
      modalities: z
        .object({
          input: z.array(z.enum(["text", "audio", "image", "video", "pdf"])),
          output: z.array(z.enum(["text", "audio", "image", "video", "pdf"])),
        })
        .optional(),
      experimental: z.boolean().optional(),
      status: z.enum(["alpha", "beta", "deprecated"]).optional(),
      options: z.record(z.string(), z.any()),
      headers: z.record(z.string(), z.string()).optional(),
      provider: z.object({ npm: z.string() }).optional(),
    })
    .meta({
      ref: "Model",
    })
  export type Model = z.infer<typeof Model>

  export const Provider = z
    .object({
      api: z.string().optional(),
      name: z.string(),
      env: z.array(z.string()),
      id: z.string(),
      npm: z.string().optional(),
      models: z.record(z.string(), Model),
    })
    .meta({
      ref: "Provider",
    })

  export type Provider = z.infer<typeof Provider>

  // Known Ollama models with tool support
  const TOOL_CAPABLE_MODELS = [
    "llama3.2",
    "llama3.1",
    "qwen2.5-coder",
    "mistral-nemo",
    "deepseek-coder-v2",
    "command-r",
    "firefunction-v2",
  ]

  interface OllamaModel {
    name: string
    modified_at: string
    size: number
    digest: string
    details?: {
      parameter_size?: string
      quantization_level?: string
    }
  }

  interface OllamaListResponse {
    models: OllamaModel[]
  }

  function modelSupportsTools(modelName: string): boolean {
    return TOOL_CAPABLE_MODELS.some((capable) => modelName.toLowerCase().includes(capable))
  }

  function parseModelInfo(ollamaModel: OllamaModel): Model {
    const name = ollamaModel.name
    const displayName = name.split(":")[0] // Remove tag
    const supportsTools = modelSupportsTools(name)

    // Estimate context window based on known models
    let contextWindow = 32768 // Default for most modern models
    if (name.includes("llama3.2")) contextWindow = 131072
    else if (name.includes("llama3.1")) contextWindow = 131072
    else if (name.includes("qwen2.5")) contextWindow = 131072
    else if (name.includes("deepseek")) contextWindow = 65536

    return {
      id: name,
      name: displayName,
      release_date: ollamaModel.modified_at,
      attachment: false,
      reasoning: false,
      temperature: true,
      tool_call: supportsTools,
      cost: {
        input: 0,
        output: 0,
        cache_read: 0,
        cache_write: 0,
      },
      limit: {
        context: contextWindow,
        output: 8192,
      },
      modalities: {
        input: ["text"],
        output: ["text"],
      },
      options: {},
    }
  }

  export async function get(): Promise<Record<string, Provider>> {
    const cached = await loadCache()
    if (cached) return cached

    const models = await fetchOllamaModels()
    const provider = buildProvider(models)
    await saveCache(provider)
    return provider
  }

  async function loadCache(): Promise<Record<string, Provider> | null> {
    const file = Bun.file(filepath)
    const exists = await file.exists()
    if (!exists) return null

    try {
      const result = await file.json()
      // Cache is valid for 5 minutes
      const cacheAge = Date.now() - (await file.lastModified)
      if (cacheAge < 5 * 60 * 1000) {
        return result as Record<string, Provider>
      }
    } catch (e) {
      log.error("Failed to load cache", { error: e })
    }
    return null
  }

  async function saveCache(provider: Record<string, Provider>) {
    const file = Bun.file(filepath)
    await Bun.write(file, JSON.stringify(provider, null, 2))
  }

  async function fetchOllamaModels(): Promise<OllamaModel[]> {
    const baseURLs = [
      process.env["OLLAMA_HOST"] ?? "http://localhost:11434",
      "http://127.0.0.1:11434",
    ]

    for (const baseURL of baseURLs) {
      try {
        log.info("Checking Ollama at", { baseURL })
        const response = await fetch(`${baseURL}/api/tags`, {
          signal: AbortSignal.timeout(3000),
        })

        if (response.ok) {
          const data = (await response.json()) as OllamaListResponse
          log.info("Found Ollama models", { count: data.models.length })
          return data.models
        }
      } catch (e) {
        log.debug("Ollama not available at", { baseURL, error: e })
      }
    }

    log.warn("Ollama not detected, returning default models")
    return []
  }

  function buildProvider(models: OllamaModel[]): Record<string, Provider> {
    const toolCapableModels = models
      .filter((m) => modelSupportsTools(m.name))
      .reduce(
        (acc, model) => {
          acc[model.name] = parseModelInfo(model)
          return acc
        },
        {} as Record<string, Model>,
      )

    // If no models found, provide recommended defaults
    const modelsToUse: Record<string, Model> =
      Object.keys(toolCapableModels).length > 0
        ? toolCapableModels
        : {
            "qwen2.5-coder:latest": {
              id: "qwen2.5-coder:latest",
              name: "Qwen 2.5 Coder",
              release_date: new Date().toISOString(),
              attachment: false,
              reasoning: false,
              temperature: true,
              tool_call: true,
              cost: { input: 0, output: 0, cache_read: 0, cache_write: 0 },
              limit: { context: 131072, output: 8192 },
              modalities: { input: ["text"], output: ["text"] },
              options: {},
            },
          }

    return {
      ollama: {
        id: "ollama",
        name: "Ollama (Local)",
        env: [],
        npm: "@ai-sdk/openai-compatible",
        api: process.env["OLLAMA_HOST"] ?? "http://localhost:11434/v1",
        models: modelsToUse,
      },
    }
  }

  export async function refresh() {
    log.info("Refreshing Ollama models")
    const models = await fetchOllamaModels()
    const provider = buildProvider(models)
    await saveCache(provider)
  }
}

// Refresh Ollama models every 5 minutes
setInterval(() => ModelsDev.refresh(), 5 * 60 * 1000).unref()
