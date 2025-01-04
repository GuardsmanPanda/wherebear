import { LitElement, css, html, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type Size = "xs" | "sm" | "md"
type Type = "success" | "error" | "dark" | "primary"

/**
 * Displays a very short text in a small rectangle.
 */
@customElement("lit-label")
export class Label extends LitElement {
  @property({ type: String }) label!: string
  @property({ type: String }) bgColorClass?: string
  @property({ type: String }) iconPath?: string
  @property({ type: Boolean }) isPill = false
  @property({ type: String }) size: Size = "sm"
  @property({ type: String }) widthClass?: string
  @property({ type: String }) type?: Type

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    return {
      [this.heightClass]: true,
      [this.bgColorClass || ""]: !!this.bgColorClass,
      [this.widthClass || ""]: !!this.widthClass,
      "justify-center": !this.iconPath,
      "pl-2": !this.iconPath,
      "-skew-x-6": !this.isPill && this.size !== "xs",
      "-skew-x-12": !this.isPill && this.size === "xs",
      rounded: !this.isPill,
      "rounded-full": this.isPill,
      "bg-pistachio-500": this.type === "success",
      "bg-poppy-500": this.type === "error",
      "bg-gray-600": this.type === "dark",
      "bg-iris-500": this.type === "primary",
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

  get imgClasses() {
    return {
      "skew-x-6": !this.isPill && this.size !== "xs",
      "skew-x-12": !this.isPill && this.size === "xs",
      "bottom-1": this.size === "xs" || this.size === "sm",
      "left-0.5": this.size === "xs",
      "left-1": this.size === "sm",
    }
  }

  get iconBgClasses() {
    const classes: Record<string, boolean> = {}

    classes["flex"] = !!this.iconPath
    classes["hidden"] = !this.iconPath

    if (this.size === "xs") {
      classes["w-5"] = true
      classes["h-5"] = true
      classes["mr-[8px]"] = true
    } else if (this.size === "sm") {
      classes["w-6"] = true
      classes["h-6"] = true
      classes["mr-2"] = true
    }

    return classes
  }

  get labelClasses() {
    return {
      [this.textSizeClass]: true,
      "skew-x-6": !this.isPill && this.size !== "xs",
      "skew-x-12": !this.isPill && this.size === "xs",
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

  protected render() {
    return html`
      <div class="flex items-center relative pr-2 border border-gray-700 ${classMap(this.classes)}">
        <div class="justify-center items-center ${classMap(this.iconBgClasses)}">
          ${this.iconPath
            ? html`<img src="${this.iconPath}" class="object-contain relative ${classMap(this.imgClasses)}" draggable="false" />`
            : nothing}
        </div>
        <span class="font-heading font-semibold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}"> ${this.label} </span>
      </div>
    `
  }
}
