import { LitElement, css, html, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Icon } from "./icon.lit-component"

/**
 * Represents the predefined size options for the label.
 * Defines the height and other related styles like font size and padding.
 */
type Size = "xs" | "sm" | "md"

/**
 * Represents predefined semantic color options for the button.
 * Can be used to apply consistent theming across the UI.
 */
type Color = "blue" | "green" | "yellow" | "orange" | "red" | "gray"

/**
 * A compact label component for displaying short text, optionally with a leading icon.
 * Commonly used to show tags, statuses, or metrics in a concise visual format.
 *
 * @example
 * ```html
 * <lit-label label="132 pts" color="gray" size="md" icon="star"></lit-label>
 * ```
 */
@customElement("lit-label")
export class Label extends LitElement {
  /**
   * The text displayed on the label.
   *
   * @example '132 pts'
   */
  @property({ type: String }) label!: string

  /**
   * Semantic color of the label. Determines styling based on
   * predefined themes (e.g., blue, green, red).
   * Ignored if `bgColorClass` is defined.
   * Default to `blue`.
   */
  @property({ type: String }) color: Color = "blue"

  @property({ type: String }) size: Size = "sm"

  /**
   * The name of the icon to display. Icon names should match those
   * accepted by the `Icon` component.
   *
   * @example 'cross'
   */
  @property({ type: String }) icon?: Icon

  /**
   * CSS class to set the label's background color.
   * Takes precedence over the `color` property if defined.
   *
   * @example 'bg-sky-500'
   */
  @property({ type: String }) bgColorClass?: string

  /**
   * Whether the label has fully rounded sides (pill-shaped).
   * When set to `true`, the label will have a rounded-full border radius.
   * Default to `false`.
   */
  @property({ type: Boolean }) pill = false

  static styles = css`
    ${TailwindStyles}
  `

  get classes() {
    const bgColorClassMap: Record<Color, string> = {
      blue: "bg-iris-400",
      red: "bg-poppy-400",
      green: "bg-pistachio-400",
      yellow: "bg-yellow-400",
      orange: "bg-orange-400",
      gray: "bg-gray-500",
    }

    const bgColorClass = this.bgColorClass ?? bgColorClassMap[this.color] ?? "bg-iris-400"

    return {
      "h-4": this.isSize("xs"),
      "h-6": this.isSize("sm"),
      "h-8": this.isSize("md"),
      [bgColorClass]: true,
      "-skew-x-12": !this.pill,
      "rounded-sm": !this.pill,
      "rounded-full": this.pill,
      "px-1": this.isSize("xs"),
      "px-2": this.isSize("sm"),
      "px-3": this.isSize("md"),
    }
  }

  get _iconClasses() {
    return {
      "h-6": this.isSize("xs"),
      "h-8": this.isSize("sm"),
      "h-[44px]": this.isSize("md"),
      "skew-x-12": !this.pill,
    }
  }

  /**
   * Creates a fake left padding for the icon to reserve space in layout.
   * Since the icon is absolutely positioned, it doesn’t affect sibling or parent layout.
   * This ensures that margin or padding on the label doesn't let the icon overlap nearby content.
   */
  get iconBackgroundClasses() {
    return {
      "w-3": this.icon !== undefined && this.isSize("xs"),
      "w-4": this.icon !== undefined && this.isSize("sm"),
      "w-[22px]": this.icon !== undefined && this.isSize("md"),
    }
  }

  get labelClasses() {
    return {
      "text-sm": this.isSize("xs"),
      "text-base": this.isSize("sm"),
      "text-[18px]": this.isSize("md"),
      "font-semibold": this.isSize("xs"),
      "font-bold": this.isSize("sm", "md"),
      "text-shadow-[0_2px_0_rgb(51_56_71_/_1)]": this.isSize("sm", "md"),
      "skew-x-12": !this.pill,
      "pl-3": this.icon !== undefined && this.isSize("xs"),
      "pl-4": this.icon !== undefined && this.isSize("sm"),
      "pl-[22px]": this.icon !== undefined && this.isSize("md"),
    }
  }

  /**
   * Checks if the button's current size matches any of the provided sizes.
   *
   * @param sizes - One or more size values to compare against the current size.
   * @returns True if the current size matches any of the provided sizes.
   *
   * @example
   * this.isSize("sm", "md") // true if size is "sm" or "md"
   */
  isSize(...sizes: Size[]) {
    return sizes.includes(this.size)
  }

  protected render() {
    return html`
      <div class="flex w-full">
        <div class="${classMap(this.iconBackgroundClasses)}"></div>
        <div class="flex justify-center items-center w-full relative border border-gray-700 ${classMap(this.classes)}">
          ${this.icon
            ? html`<lit-icon
                name="${this.icon}"
                class="flex absolute left-0 -translate-x-1/2 top-1/2 -translate-y-1/2 z-10 ${classMap(this._iconClasses)}"
              ></lit-icon>`
            : nothing}
          <span class="font-heading text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}"> ${this.label} </span>
        </div>
      </div>
    `
  }
}
