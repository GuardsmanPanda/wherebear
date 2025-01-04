import { LitElement, css, html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

/**
 * A header component for a panel.
 * This component includes two slot areas ("left" and "right") for flexible content injection.
 */
@customElement("lit-panel-header2")
class PanelHeader extends LitElement {
  @property({ type: String }) label?: string
  @property({ type: Boolean }) noBorder = false
  @property({ type: Boolean }) noRounded = false

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    return {
      border: !this.noBorder,
      "rounded-t": !this.noRounded,
    }
  }

  protected render() {
    return html`
      <div class="flex w-full h-8 justify-between items-center relative px-2 border-gray-700 bg-iris-400 ${classMap(this.classes)}">
        <div class="w-40 h-full absolute left-0 bg-iris-600" style="clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 100%, 0 100%)"></div>
        <div class="flex items-center gap-2 z-10">
          <span class="font-heading font-semibold text-base text-gray-0">${this.label}</span>
          <slot name="left" class=""></slot>
        </div>
        <div class="z-10">
          <slot name="right"></slot>
        </div>
      </div>
    `
  }
}
