import { css, html, LitElement, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Logger } from "../../js/logger"

/**
 * Represents a player's progress on an achievement.
 * This component displays the name, description, and completion status of an achievement.
 */
@customElement("lit-achievement-progress")
class AchievementProgress extends LitElement {
  @property({ type: String }) name!: string
  @property({ type: String }) description!: string
  @property({ type: Boolean }) isCompleted = false
  @property({ type: String }) completedDate?: string
  @property({ type: Boolean }) hasSteps = false
  @property({ type: Number }) currentStep?: number
  @property({ type: Number }) totalSteps?: number

  static styles = css`
    ${TailwindStyles}
  `

  /** Returns the completed date into a readable string format. */
  get convertedDate(): string {
    return this.completedDate
      ? new Date(this.completedDate).toLocaleDateString(undefined, {
          year: "numeric",
          month: "short",
          day: "numeric",
        })
      : ""
  }

  get progressPercentage(): number {
    if (!this.currentStep) {
      Logger.error(`The property 'currentStep' is missing`)
      return 0
    }
    if (!this.totalSteps) {
      Logger.error(`The property 'totalSteps' is missing`)
      return 0
    }
    return Math.floor((this.currentStep / this.totalSteps) * 100)
  }

  protected render() {
    return html`
      <div class="flex flex-col min-h-24 relative border border-gray-200 rounded p-2 bg-gray-0">
        <span class="mr-8 font-medium text-base text-iris-600">${this.name}</span>
        <span class="mr-8 text-sm text-gray-700">${this.description}</span>

        ${!this.isCompleted && this.hasSteps
          ? html`
              <span class="w-full self-end text-right text-sm text-gray-600">${this.currentStep}/${this.totalSteps}</span>
              <lit-progress-bar size="xs" innerBgColorClass="bg-pistachio-400" percentage="${this.progressPercentage}"> </lit-progress-bar>
            `
          : nothing}
        ${this.isCompleted
          ? html`
              <img src="/static/img/icon/ribbon-medal-green.svg" class="absolute -top-1.5 right-1 h-12" draggable="false" />
              <span class="mt-auto w-full text-right text-xs text-gray-500">Completed on ${this.convertedDate}</span>
            `
          : nothing}
      </div>
    `
  }
}

