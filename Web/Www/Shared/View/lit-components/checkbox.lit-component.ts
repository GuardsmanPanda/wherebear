import { css, html, LitElement } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type Size = "xs" | "sm"

/**
 * Represents a checkbox with a label.
 */
@customElement("lit-checkbox")
class Checkbox extends LitElement {
  @property({ type: String }) label!: string
  @property({ type: Boolean }) isSelected = false
  @property({ type: String }) size: Size = "sm"

  private static styles = css`
    ${TailwindStyles}
  `

  private get checkboxClasses() {
    return {
      "w-4": this.size === "xs",
      "h-4": this.size === "xs",
      "w-5": this.size === "sm",
      "h-5": this.size === "sm",
    }
  }

  private get labelClasses() {
    return {
      "text-sm": this.size === "xs",
      "text-base": this.size === "sm",
    }
  }

  private toggle() {
    this.isSelected = !this.isSelected

    this.dispatchEvent(
      new CustomEvent("toggled", {
        detail: { isSelected: this.isSelected },
        bubbles: true,
        composed: true,
      }),
    )
  }

  protected render() {
    return html`
      <div class="flex items-center gap-2 cursor-pointer" @click="${this.toggle}">
        <div class="flex justify-center items-center w-4 h-4 rounded-sm border border-gray-700 bg-gray-0 ${classMap(this.checkboxClasses)}">
          <img src="/static/img/icon/check-blue.svg" class="w-4 transition-opacity duration-100 ${this.isSelected ? "opacity-100" : "opacity-0"}" />
        </div>
        <span class="text-gray-700 ${classMap(this.labelClasses)}">${this.label}</span>
      </div>
    `
  }
}
