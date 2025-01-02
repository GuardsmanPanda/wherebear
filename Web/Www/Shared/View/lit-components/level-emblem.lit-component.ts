import { LitElement, css, html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type Size = "xs" | "sm" | "md" | "lg"

/**
 * Displays an emblem icon with the user level in the center.
 */
@customElement("lit-level-emblem")
class LevelEmblem extends LitElement {
  @property({ type: Number }) level!: number
  @property({ type: String }) size: Size = "sm"

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    return {
      "w-6": this.size === "xs",
      "w-8": this.size === "sm",
      "w-10": this.size === "md",
      "w-12": this.size === "lg",
    }
  }

  get levelClasses() {
    return {
      "text-sm": this.size === "xs",
      "text-lg": this.size === "sm",
      "bottom-[2px]": this.size === "sm",
      "text-xl": this.size === "md",
      "bottom-[4px]": this.size === "md",
      "text-2xl": this.size === "lg",
    }
  }

  protected render() {
    return html`
      <div class="relative ${classMap(this.classes)}">
        <img src="/static/img/icon/emblem.svg" class="" draggable="false" />
        <span
          class="absolute inset-0 flex items-center justify-center text-white font-heading font-bold text-stroke-2 text-stroke-iris-900 select-none ${classMap(
            this.levelClasses,
          )}"
        >
          ${this.level}
        </span>
      </div>
    `
  }
}
