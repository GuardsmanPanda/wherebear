import { LitElement, css, html, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

type Size = "xs" | "sm" | "md"
type Color = "blue" | "green" | "red" | "orange" | "gray"

/**
 * Displays a very short text in a small rectangle.
 */
@customElement("lit-label")
export class Label extends LitElement {
  @property({ type: String }) label!: string
  @property({ type: String }) iconPath?: string
  @property({ type: Boolean }) isPill = false
  @property({ type: String }) size: Size = "sm"
  @property({ type: String }) widthClass?: string
  @property({ type: String }) color: Color = "blue"

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    return {
      [this.heightClass]: true,
      [this.widthClass || ""]: !!this.widthClass,
      "justify-center": !this.iconPath,
      "pl-2": !this.iconPath,
      "-skew-x-6": !this.isPill && this.size !== "xs",
      "-skew-x-12": !this.isPill && this.size === "xs",
      "rounded-sm": !this.isPill,
      "rounded-full": this.isPill,
      "bg-iris-400": this.color === "blue",
      "bg-poppy-400": this.color === "red",
      "bg-pistachio-400": this.color === "green",
      "bg-orange-400": this.color === "orange",
      "bg-gray-500": this.color === "gray",
      "bg-gradient-to-b": this.size !== "xs",
      "from-poppy-400": this.color === "red",
      "to-poppy-500": this.color === "red",
      "from-iris-400": this.color === "blue",
      "to-iris-500": this.color === "blue",
      "from-pistachio-400": this.color === "green",
      "to-pistachio-500": this.color === "green",
      "from-orange-400": this.color === "orange",
      "to-orange-500": this.color === "orange",
      "from-gray-500": this.color === "gray",
      "to-gray-600": this.color === "gray",
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
      [this.textWeightClass]: true,
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

  get textWeightClass() {
    switch (this.size) {
      case "xs":
        return "font-extrabold"
      case "sm":
        return "font-bold"
      default:
        return "font-bold"
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
        <span class="font-heading font-extrabold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}"> ${this.label} </span>
      </div>
    `
  }
}
