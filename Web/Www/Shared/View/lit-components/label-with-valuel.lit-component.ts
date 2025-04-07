import { LitElement, css, html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type Size = "xs" | "sm" | "md"
type Type = "success" | "error" | "dark" | "primary"

/**
 * Displays a very short text in a small rectangle associated with a value.
 */
@customElement("lit-label-with-value")
export class LabelWithValue extends LitElement {
  @property({ type: String }) label!: string
  @property({ type: String }) value!: string
  @property({ type: String }) bgColorClass?: string
  @property({ type: String }) size: Size = "sm"
  @property({ type: String }) widthClass?: string
  @property({ type: String }) type?: Type = "primary"

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    return {
      [this.heightClass]: true,
      [this.textSizeClass]: true,
      [this.bgColorClass || ""]: !!this.bgColorClass,
      [this.widthClass || ""]: !!this.widthClass,
    }
  }

  get heightClass() {
    switch (this.size) {
      case "xs":
        return "h-4"
      case "sm":
        return "h-6"
      case "md":
        return "h-8"
      default:
        return "h-4"
    }
  }

  get textSizeClass() {
    switch (this.size) {
      case "xs":
        return "text-xs"
      case "sm":
        return "text-sm"
      case "md":
        return "text-base"
      default:
        return "text-xs"
    }
  }

  get labelClasses() {
    return {
      //   "w-12": this.size === "xs",
      //   "w-16": this.size === "sm",
    }
  }

  get valueClasses() {
    return {
      "w-10": this.size === "xs" || this.size === "sm",
      "w-12": this.size === "md",
      "bg-pistachio-500": this.type === "success",
      "bg-poppy-500": this.type === "error",
      "bg-gray-600": this.type === "dark",
      "bg-iris-500": this.type === "primary",
    }
  }

  protected render() {
    return html`
      <div class="flex ${classMap(this.classes)}">
        <div
          class="flex w-full justify-center items-center border border-r-0 border-gray-700 rounded-l-sm bg-gray-0 -mr-px ${classMap(
            this.labelClasses,
          )}"
        >
          <span class="font-heading font-semibold text-gray-800">${this.label}</span>
        </div>
        <div class="flex justify-center items-center border border-l-0 border-gray-700 rounded-r-sm ${classMap(this.valueClasses)}">
          <span class="font-heading font-semibold text-gray-0 text-stroke-2 text-stroke-gray-700">${this.value}</span>
        </div>
      </div>
    `
  }
}
