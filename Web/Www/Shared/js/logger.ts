/* eslint-disable no-console */

export class Logger {
  static #instance: Logger | null = null

  private constructor() {}

  private logError(...args: unknown[]) {
    console.error(...args)
  }

  private logInfo(...args: unknown[]) {
    console.info(...args)
  }

  static getInstance(): Logger {
    if (!Logger.#instance) {
      Logger.#instance = new Logger()
    }
    return Logger.#instance
  }

  static error(...args: unknown[]) {
    Logger.getInstance().logError(...args)
  }

  static info(...args: unknown[]) {
    Logger.getInstance().logInfo(...args)
  }
}
