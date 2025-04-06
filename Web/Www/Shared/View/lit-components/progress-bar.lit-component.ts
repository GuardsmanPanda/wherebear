import { LitElement, css, html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"
import { styleMap } from "lit/directives/style-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { tooltip } from "./tippy.lit-directive"

type Size = "xs" | "sm"

/**
 * A custom progress bar component that visually represents a progress value
 * as a filled section of a bar.
 */
@customElement("lit-progress-bar")
class ProgressBar extends LitElement {
  /**
   * The background color of the inner section of the progress bar.
   * Overrides `innerBgColorClass` if provided.
   * @example
   * 'green'
   * '#ffffff'
   */
  @property({ type: String }) innerBgColor?: string

  /**
   * A CSS class used to style the background of the inner section of the progress bar.
   * @example 'bg-green-300'
   */
  @property({ type: String }) innerBgColorClass?: string

  /** Whether the CSS transitions are enabled. */
  @property({ type: Boolean }) isTransitionEnabled = false

  /** The current progress of the progress bar as a percentage (0 to 100). */
  @property({ type: Number }) percentage: number = 0

  /** Whether the percentage is displayed as tooltip. */
  @property({ type: Boolean }) showPercentageTooltip = false

  /**
   * Determines whether the sides of the progress bar are flat or rounded.
   * Default is `false` (rounded sides).
   */
  @property({ type: Boolean }) sideFlated = false

  /**
   * Determines whether the sides of the progress bar should have a border.
   * Default is `false` (bordered sides).
   */
  @property({ type: Boolean }) sideUnbordered = false

  @property({ type: String }) size: Size = "sm"

  /**
   * The duration of the width transition in milliseconds.
   * Controls how long it takes for the progress bar to adjust its width.
   */
  @property({ type: Number }) widthTransitionDurationMs: number = 1000

  /**
   * Controls the visibility of the inner bar.
   * Useful for completely hiding the inner bar when the percentage is 0%,
   * preventing the border from being displayed.
   *
   * Note: Removing the 'border' class when the percentage is 0% doesn't help
   * due to the transition (the border is removed too soon).
   */
  @property({ type: Boolean }) showInnerBar = false

  static styles = css`
    ${TailwindStyles}
  `

  get outterBarClasses() {
    return {
      "h-3": this.size === "xs",
      "h-4": this.size !== "xs",
      "rounded-sm": !this.sideFlated,
      "border-x": !this.sideUnbordered,
    }
  }

  get innerBarClasses() {
    return {
      invisible: !this.showInnerBar,
      "border-r": this.percentage < 100,
      "rounded-r-sm": this.percentage < 100,
      [this.innerBgColorClass ?? ""]: !!this.innerBgColorClass,
    }
  }

  get innerBarStyles() {
    return {
      backgroundColor: this.innerBgColor ?? "",
      boxShadow: "inset 0 -4px 1px rgba(0, 0, 0, 0.25)",
      width: `${this.percentage}%`,
      "border-radius": this.sideFlated ? "none" : "0.25rem",
      transition: this.isTransitionEnabled ? `width ${this.widthTransitionDurationMs}ms linear, background-color 1000ms linear` : "none",
    }
  }

  get roundedPercentage() {
    return this.percentage < 1 ? 1 : Math.floor(this.percentage)
  }

  firstUpdated() {
    setTimeout(() => {
      this.isTransitionEnabled = true
      this.requestUpdate()
    }, 150)
  }

  updated(changedProperties: Map<string | number | symbol, unknown>) {
    if (this.percentage <= 0) {
      const oldPercentage = changedProperties.get("percentage") as number
      if (oldPercentage > 0) {
        setTimeout(() => {
          this.showInnerBar = false
        }, this.widthTransitionDurationMs)
      }
    } else {
      this.showInnerBar = true
    }
  }

  protected render() {
    return html`
      <div
        class="flex w-full border-y bg-gray-500 border-gray-700 ${classMap(this.outterBarClasses)}"
        style="box-shadow: inset 0 4px 1px rgb(0 0 0 / 0.3);"
        ${this.showPercentageTooltip ? tooltip(`${this.roundedPercentage}%`) : ""}
      >
        <div class="border-l-0 border-gray-700 ${classMap(this.innerBarClasses)}" style="${styleMap(this.innerBarStyles)}"></div>
      </div>
    `
  }
}
