import { LitElement, css, html } from "lit"
import { customElement, property, state } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type ToggleSize = "xs" | "sm" | "md" | "lg" | "xl"

interface ToggleClickedEventDetail {
  isSelected: boolean
}

@customElement("lit-toggle")
export class Toggle extends LitElement {
  @property({ type: Boolean }) isSelected = false
  @property({ type: String }) leftLabel!: string
  @property({ type: String }) rightLabel!: string
  @property({ type: String }) size: ToggleSize = "sm"

  @state() hasRendered = false

  static styles = css`
    ${TailwindStyles}
  `

  private heightClasses = {
    xs: "h-6",
    sm: "h-8",
    md: "h-10",
    lg: "h-12",
    xl: "h-14",
  }

  firstUpdated() {
    setTimeout(() => {
      this.hasRendered = true
    }, 500)
  }

  private get heightClass(): string {
    return this.heightClasses[this.size] || this.heightClasses["sm"]
  }

  private get classes() {
    return {
      [this.heightClass]: true,
    }
  }

  private get labelClasses() {
    return {
      "text-base": true,
    }
  }

  private get selectedOptionClasses() {
    return {
      [this.heightClass]: true,
      "-left-px": !this.isSelected,
      "left-[calc(50%+1px)]": this.isSelected,
      transform: this.hasRendered,
      "duration-300": this.hasRendered,
    }
  }

  private toggleOption(): void {
    this.isSelected = !this.isSelected

    this.dispatchEvent(
      new CustomEvent<ToggleClickedEventDetail>("clicked", {
        detail: { isSelected: this.isSelected },
        bubbles: true,
        composed: true,
      }),
    )
  }

  protected render() {
    return html`
      <div
        class="flex w-full relative overflow-hidden cursor-pointer rounded-md border border-gray-700 bg-iris-200 text-white ${classMap(this.classes)}"
        @click="${this.toggleOption}"
        style="box-shadow: inset 0 -3px 1px 0 rgba(0, 0, 0, 0.25)"
      >
        <div
          class="w-1/2 absolute -top-px -left-px z-10 rounded-sm border border-gray-700 bg-iris-500 ${classMap(this.selectedOptionClasses)}"
          style="box-shadow: -2px 0 1px 0 rgba(0, 0, 0, 0.4), 2px 0 1px 0 rgba(0, 0, 0, 0.4), inset 0 -3px 1px 0 rgba(0, 0, 0, 0.40), inset 0 2px 1px 0 rgba(255, 255, 255, 0.25)"
        ></div>
        <div class="flex justify-center items-center w-full z-10">
          <span class="font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}">${this.leftLabel}</span>
        </div>
        <div class="flex justify-center items-center w-full z-10">
          <span class="font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}">${this.rightLabel}</span>
        </div>
      </div>
    `
  }
}
