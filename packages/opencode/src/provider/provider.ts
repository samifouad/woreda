import z from "zod"
import path from "path"
import { Config } from "../config/config"
import { mergeDeep, sortBy } from "remeda"
import { NoSuchModelError, type LanguageModel, type Provider as SDK } from "ai"
import { Log } from "../util/log"
import { ModelsDev } from "./models"
import { NamedError } from "../util/error"
import { Instance } from "../project/instance"
import { Global } from "../global"
import { iife } from "@/util/iife"

export namespace Provider {
  const log = Log.create({ service: "provider" })

  type CustomLoader = (provider: ModelsDev.Provider) => Promise<{
    autoload: boolean
    getModel?: (sdk: any, modelID: string, options?: Record<string, any>) => Promise<any>
    options?: Record<string, any>
  }>

  type Source = "env" | "config" | "custom"

  const CUSTOM_LOADERS: Record<string, CustomLoader> = {
    async ollama(input) {
      return {
        autoload: true,
        options: {
          baseURL: input.api ?? process.env["OLLAMA_HOST"] ?? "http://localhost:11434/v1",
          apiKey: "ollama", // Required by SDK but not used
          includeUsage: true,
        },
      }
    },
  }

  const state = Instance.state(async () => {
    using _ = log.time("state")
    const config = await Config.get()
    const database = await ModelsDev.get()

    const providers: {
      [providerID: string]: {
        source: Source
        info: ModelsDev.Provider
        getModel?: (sdk: any, modelID: string, options?: Record<string, any>) => Promise<any>
        options: Record<string, any>
      }
    } = {}
    const models = new Map<
      string,
      {
        providerID: string
        modelID: string
        info: ModelsDev.Model
        language: LanguageModel
        npm?: string
      }
    >()
    const sdk = new Map<number, SDK>()
    // Maps `${provider}/${key}` to the provider’s actual model ID for custom aliases.
    const realIdByKey = new Map<string, string>()

    log.info("init")

    function mergeProvider(
      id: string,
      options: Record<string, any>,
      source: Source,
      getModel?: (sdk: any, modelID: string, options?: Record<string, any>) => Promise<any>,
    ) {
      const provider = providers[id]
      if (!provider) {
        const info = database[id]
        if (!info) return
        if (info.api && !options["baseURL"]) options["baseURL"] = info.api
        providers[id] = {
          source,
          info,
          options,
          getModel,
        }
        return
      }
      provider.options = mergeDeep(provider.options, options)
      provider.source = source
      provider.getModel = getModel ?? provider.getModel
    }

    const configProviders = Object.entries(config.provider ?? {})

    for (const [providerID, provider] of configProviders) {
      const existing = database[providerID]
      const parsed: ModelsDev.Provider = {
        id: providerID,
        npm: provider.npm ?? existing?.npm,
        name: provider.name ?? existing?.name ?? providerID,
        env: provider.env ?? existing?.env ?? [],
        api: provider.api ?? existing?.api,
        models: existing?.models ?? {},
      }

      for (const [modelID, model] of Object.entries(provider.models ?? {})) {
        const existing = parsed.models[model.id ?? modelID]
        const name = iife(() => {
          if (model.name) return model.name
          if (model.id && model.id !== modelID) return modelID
          return existing?.name ?? modelID
        })
        const parsedModel: ModelsDev.Model = {
          id: modelID,
          name,
          release_date: model.release_date ?? existing?.release_date,
          attachment: model.attachment ?? existing?.attachment ?? false,
          reasoning: model.reasoning ?? existing?.reasoning ?? false,
          temperature: model.temperature ?? existing?.temperature ?? false,
          tool_call: model.tool_call ?? existing?.tool_call ?? true,
          cost:
            !model.cost && !existing?.cost
              ? {
                  input: 0,
                  output: 0,
                  cache_read: 0,
                  cache_write: 0,
                }
              : {
                  cache_read: 0,
                  cache_write: 0,
                  ...existing?.cost,
                  ...model.cost,
                },
          options: {
            ...existing?.options,
            ...model.options,
          },
          limit: model.limit ??
            existing?.limit ?? {
              context: 0,
              output: 0,
            },
          modalities: model.modalities ??
            existing?.modalities ?? {
              input: ["text"],
              output: ["text"],
            },
          headers: model.headers,
          provider: model.provider ?? existing?.provider,
        }
        if (model.id && model.id !== modelID) {
          realIdByKey.set(`${providerID}/${modelID}`, model.id)
        }
        parsed.models[modelID] = parsedModel
      }
      database[providerID] = parsed
    }

    const disabled = await Config.get().then((cfg) => new Set(cfg.disabled_providers ?? []))

    // Load Ollama provider (always autoload)
    for (const [providerID, fn] of Object.entries(CUSTOM_LOADERS)) {
      if (disabled.has(providerID)) continue
      const providerInfo = database[providerID]
      if (!providerInfo) continue
      const result = await fn(providerInfo)
      if (result && result.autoload) {
        mergeProvider(providerID, result.options ?? {}, "custom", result.getModel)
      }
    }

    // Allow config overrides
    for (const [providerID, provider] of configProviders) {
      mergeProvider(providerID, provider.options ?? {}, "config")
    }

    for (const [providerID, provider] of Object.entries(providers)) {
      const filteredModels = Object.fromEntries(
        Object.entries(provider.info.models).filter(
          ([, model]) =>
            // Only show models with tool support
            model.tool_call && model.status !== "deprecated",
        ),
      )
      provider.info.models = filteredModels

      if (Object.keys(provider.info.models).length === 0) {
        delete providers[providerID]
        continue
      }
      log.info("found ollama provider", { providerID, modelCount: Object.keys(filteredModels).length })
    }

    return {
      models,
      providers,
      sdk,
      realIdByKey,
    }
  })

  export async function list() {
    return state().then((state) => state.providers)
  }

  async function getSDK(provider: ModelsDev.Provider, model: ModelsDev.Model) {
    return (async () => {
      using _ = log.time("getSDK", {
        providerID: provider.id,
      })
      const s = await state()
      const pkg = model.provider?.npm ?? provider.npm ?? provider.id
      const options = { ...s.providers[provider.id]?.options }
      if (pkg.includes("@ai-sdk/openai-compatible") && options["includeUsage"] === undefined) {
        options["includeUsage"] = true
      }
      const key = Bun.hash.xxHash32(JSON.stringify({ pkg, options }))
      const existing = s.sdk.get(key)
      if (existing) return existing

      // For Ollama, we use the openai-compatible SDK which should already be in node_modules
      const installedPath = pkg
      const mod = await import(installedPath)
      if (options["timeout"] !== undefined && options["timeout"] !== null) {
        // Preserve custom fetch if it exists, wrap it with timeout logic
        const customFetch = options["fetch"]
        options["fetch"] = async (input: any, init?: BunFetchRequestInit) => {
          const { signal, ...rest } = init ?? {}

          const signals: AbortSignal[] = []
          if (signal) signals.push(signal)
          if (options["timeout"] !== false) signals.push(AbortSignal.timeout(options["timeout"]))

          const combined = signals.length > 1 ? AbortSignal.any(signals) : signals[0]

          const fetchFn = customFetch ?? fetch
          return fetchFn(input, {
            ...rest,
            signal: combined,
            // @ts-ignore see here: https://github.com/oven-sh/bun/issues/16682
            timeout: false,
          })
        }
      }
      const fn = mod[Object.keys(mod).find((key) => key.startsWith("create"))!]
      const loaded = fn({
        name: provider.id,
        ...options,
      })
      s.sdk.set(key, loaded)
      return loaded as SDK
    })().catch((e) => {
      throw new InitError({ providerID: provider.id }, { cause: e })
    })
  }

  export async function getProvider(providerID: string) {
    return state().then((s) => s.providers[providerID])
  }

  export async function getModel(providerID: string, modelID: string) {
    const key = `${providerID}/${modelID}`
    const s = await state()
    if (s.models.has(key)) return s.models.get(key)!

    log.info("getModel", {
      providerID,
      modelID,
    })

    const provider = s.providers[providerID]
    if (!provider) throw new ModelNotFoundError({ providerID, modelID })
    const info = provider.info.models[modelID]
    if (!info) throw new ModelNotFoundError({ providerID, modelID })
    const sdk = await getSDK(provider.info, info)

    try {
      const keyReal = `${providerID}/${modelID}`
      const realID = s.realIdByKey.get(keyReal) ?? info.id
      const language = provider.getModel
        ? await provider.getModel(sdk, realID, provider.options)
        : sdk.languageModel(realID)
      log.info("found", { providerID, modelID })
      s.models.set(key, {
        providerID,
        modelID,
        info,
        language,
        npm: info.provider?.npm ?? provider.info.npm,
      })
      return {
        modelID,
        providerID,
        info,
        language,
        npm: info.provider?.npm ?? provider.info.npm,
      }
    } catch (e) {
      if (e instanceof NoSuchModelError)
        throw new ModelNotFoundError(
          {
            modelID: modelID,
            providerID,
          },
          { cause: e },
        )
      throw e
    }
  }

  export async function getSmallModel(providerID: string) {
    const cfg = await Config.get()

    if (cfg.small_model) {
      const parsed = parseModel(cfg.small_model)
      return getModel(parsed.providerID, parsed.modelID)
    }

    const provider = await state().then((state) => state.providers[providerID])
    if (!provider) return

    // Prefer faster, smaller models for quick tasks
    const priority = ["qwen2.5-coder:1.5b", "qwen2.5-coder:3b", "llama3.2:3b", "llama3.2:1b"]

    for (const item of priority) {
      for (const model of Object.keys(provider.info.models)) {
        if (model.includes(item)) return getModel(providerID, model)
      }
    }

    // Fallback to first available model
    const firstModel = Object.keys(provider.info.models)[0]
    if (firstModel) return getModel(providerID, firstModel)
  }

  const priority = ["qwen2.5-coder", "deepseek-coder-v2", "llama3.2", "llama3.1", "mistral-nemo"]
  export function sort(models: ModelsDev.Model[]) {
    return sortBy(
      models,
      [(model) => priority.findIndex((filter) => model.id.includes(filter)), "desc"],
      [(model) => (model.id.includes("latest") ? 0 : 1), "asc"],
      [(model) => model.id, "desc"],
    )
  }

  export async function defaultModel() {
    const cfg = await Config.get()
    if (cfg.model) return parseModel(cfg.model)

    // this will be adjusted when migration to opentui is complete,
    // for now we just read the tui state toml file directly
    //
    // NOTE: cannot just import file as toml without cleaning due to lack of
    // support for date/time references in Bun toml parser: https://github.com/oven-sh/bun/issues/22426
    const lastused = await Bun.file(path.join(Global.Path.state, "tui"))
      .text()
      .then((text) => {
        // remove the date/time references since Bun toml parser doesn't support yet
        const cleaned = text
          .split("\n")
          .filter((line) => !line.trim().startsWith("last_used ="))
          .join("\n")
        const state = Bun.TOML.parse(cleaned) as {
          recently_used_models?: {
            provider_id: string
            model_id: string
          }[]
        }
        const [model] = state?.recently_used_models ?? []
        if (model) {
          return {
            providerID: model.provider_id,
            modelID: model.model_id,
          }
        }
      })
      .catch((error) => {
        log.error("failed to find last used model", {
          error,
        })
        return undefined
      })

    if (lastused) return lastused

    const provider = await list()
      .then((val) => Object.values(val))
      .then((x) => x.find((p) => !cfg.provider || Object.keys(cfg.provider).includes(p.info.id)))
    if (!provider) throw new Error("no providers found")
    const [model] = sort(Object.values(provider.info.models))
    if (!model) throw new Error("no models found")
    return {
      providerID: provider.info.id,
      modelID: model.id,
    }
  }

  export function parseModel(model: string) {
    const [providerID, ...rest] = model.split("/")
    return {
      providerID: providerID,
      modelID: rest.join("/"),
    }
  }

  export const ModelNotFoundError = NamedError.create(
    "ProviderModelNotFoundError",
    z.object({
      providerID: z.string(),
      modelID: z.string(),
    }),
  )

  export const InitError = NamedError.create(
    "ProviderInitError",
    z.object({
      providerID: z.string(),
    }),
  )
}
