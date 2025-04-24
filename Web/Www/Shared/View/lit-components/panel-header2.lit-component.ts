import { LitElement, css, html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type Type = "blue" | "gray"

/**
 * A header component for a panel.
 * This component includes two slot areas ("left" and "right") for flexible content injection.
 */
@customElement("lit-panel-header2")
class PanelHeader extends LitElement {
  @property({ type: String }) label?: string
  @property({ type: Boolean }) noBorder = false
  @property({ type: Boolean }) noRounded = false
  @property({ type: String }) color: Type = "blue"

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    return {
      border: !this.noBorder,
      "rounded-t-sm": !this.noRounded,
      "from-iris-600": this.color === "blue",
      "to-iris-400": this.color === "blue",
      "from-gray-600": this.color === "gray",
      "to-gray-400": this.color === "gray",
    }
  }

  protected render() {
    return html`
      <div class="flex w-full h-8 justify-between items-center relative px-2 border-gray-700 bg-gradient-to-r ${classMap(this.classes)}">
        <div class="flex items-center gap-2 z-10">
          <span class="font-heading font-bold text-base text-gray-0 text-stroke-2 text-stroke-gray-700 text-shadow-[0_2px_0_rgb(25_28_37_/_1)]"
            >${this.label}</span
          >
          <slot name="left" class=""></slot>
        </div>
        <div class="z-10">
          <slot
            name="right"
            class="font-heading font-bold text-base text-gray-0 text-stroke-2 text-stroke-gray-700 text-shadow-[0_2px_0_rgb(25_28_37_/_1)]"
          ></slot>
        </div>
      </div>
    `
  }
}
