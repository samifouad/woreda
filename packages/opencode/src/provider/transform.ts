import type { ModelMessage } from "ai"
import type { JSONSchema } from "zod/v4/core"

export namespace ProviderTransform {
  function normalizeMessages(msgs: ModelMessage[], _providerID: string, modelID: string): ModelMessage[] {
    // Mistral models need special tool call ID handling
    if (modelID.toLowerCase().includes("mistral")) {
      const result: ModelMessage[] = []
      for (let i = 0; i < msgs.length; i++) {
        const msg = msgs[i]
        const nextMsg = msgs[i + 1]

        if ((msg.role === "assistant" || msg.role === "tool") && Array.isArray(msg.content)) {
          msg.content = msg.content.map((part) => {
            if ((part.type === "tool-call" || part.type === "tool-result") && "toolCallId" in part) {
              // Mistral requires alphanumeric tool call IDs with exactly 9 characters
              const normalizedId = part.toolCallId
                .replace(/[^a-zA-Z0-9]/g, "") // Remove non-alphanumeric characters
                .substring(0, 9) // Take first 9 characters
                .padEnd(9, "0") // Pad with zeros if less than 9 characters

              return {
                ...part,
                toolCallId: normalizedId,
              }
            }
            return part
          })
        }

        result.push(msg)

        // Fix message sequence: tool messages cannot be followed by user messages
        if (msg.role === "tool" && nextMsg?.role === "user") {
          result.push({
            role: "assistant",
            content: [
              {
                type: "text",
                text: "Done.",
              },
            ],
          })
        }
      }
      return result
    }

    return msgs
  }

  export function message(msgs: ModelMessage[], providerID: string, modelID: string) {
    return normalizeMessages(msgs, providerID, modelID)
  }

  export function temperature(_providerID: string, modelID: string) {
    const lower = modelID.toLowerCase()
    // Qwen models work better with slightly higher temperature
    if (lower.includes("qwen")) return 0.55
    // DeepSeek and Llama work well with low temperature for code
    if (lower.includes("deepseek") || lower.includes("llama")) return 0.1
    return 0
  }

  export function topP(_providerID: string, modelID: string) {
    // Qwen models benefit from top_p = 1
    if (modelID.toLowerCase().includes("qwen")) return 1
    return undefined
  }

  export function options(
    _providerID: string,
    _modelID: string,
    _npm: string,
    _sessionID: string,
  ): Record<string, any> | undefined {
    // Ollama doesn't need special options
    return {}
  }

  export function providerOptions(_npm: string | undefined, providerID: string, options: { [x: string]: any }) {
    // For Ollama with openai-compatible, use provider ID
    return {
      [providerID]: options,
    }
  }

  export function maxOutputTokens(
    _npm: string,
    _options: Record<string, any>,
    modelLimit: number,
    globalLimit: number,
  ): number {
    // Simple: use the minimum of model limit and global limit
    return Math.min(modelLimit || globalLimit, globalLimit)
  }

  export function schema(_providerID: string, _modelID: string, schema: JSONSchema.BaseSchema) {
    return schema
  }
}
