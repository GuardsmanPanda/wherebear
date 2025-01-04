import { css, html, LitElement } from "lit"
import { customElement, property } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

/**
 * Represents a checkbox with a label.
 */
@customElement("lit-checkbox")
class Checkbox extends LitElement {
  @property({ type: String }) label!: string
  @property({ type: Boolean }) isSelected = false

  private static styles = css`
    ${TailwindStyles}
  `
  toggle() {
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
        <div class="flex justify-center items-center w-4 h-4 rounded-sm border border-gray-700 bg-gray-0">
          <img src="/static/img/icon/check-blue.svg" class="w-4 transition-opacity duration-100 ${this.isSelected ? "opacity-100" : "opacity-0"}" />
        </div>
        <span class="text-sm text-gray-700">${this.label}</span>
      </div>
    `
  }
}
