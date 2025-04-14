import { html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

import { ButtonBase } from "./button-base.lit-component"
import { Icon } from "./icon.lit-component"

/**
 * Defines the possible horizontal alignments for button content.
 * - `left`: aligns content to the left.
 * - `center`: centers content within the button.
 */
type ContentAlignment = "left" | "center"

/**
 * Represents predefined semantic color options for the button.
 * Can be used to apply consistent theming across the UI.
 */
type Color = "blue" | "red" | "green" | "yellow" | "gray"

/**
 * Represents a customizable button component with support for labels,
 * icons, colors, alignment, and optional ping notification.
 *
 * @example
 * ```html
 * <lit-button label="SUBMIT" color="green" size="md" icon="check_green"></lit-button>
 * ```
 */
@customElement("lit-button")
export class Button extends ButtonBase {
  /**
   * The text displayed on the button.
   *
   * @example 'SUBMIT'
   */
  @property({ type: String }) label?: string

  /**
   * Semantic color of the button. Determines styling based on
   * predefined themes (e.g., blue, red, green).
   * Ignored if `bgColorClass` is defined.
   * Default to `blue`.
   */
  @property({ type: String }) color: Color = "blue"

  /**
   * The name of the icon to display. Icon names should match those
   * accepted by the `Icon` component.
   *
   * @example 'cross'
   */
  @property({ type: String }) icon?: Icon

  /**
   * CSS class to set the button's background color.
   * Takes precedence over the `color` property if defined.
   *
   * @example 'bg-sky-500'
   */
  @property({ type: String }) bgColorClass?: string

  /**
   * Horizontal alignment of the button's content (icon and/or label).
   * Use `center` to center-align or `left` to left-align.
   * Default to `center`
   */
  @property({ type: String }) contentAlignment: ContentAlignment = "center"

  /**
   * Whether the button has fully rounded sides (pill-shaped).
   * When set to `true`, the button will have a rounded-full border radius.
   * Default to `false`.
   */
  @property({ type: Boolean }) pill = false

  /**
   * Whether to show a small red notification badge with a ping animation
   * at the top-right corner of the button.
   * Default to `false`.
   */
  @property({ type: Boolean }) ping = false

  /**
   * Disables the button, preventing interaction and applying
   * a dimmed visual style.
   * Default to `false`.
   */
  @property({ type: Boolean, reflect: true }) disabled = false

  static styles = [...ButtonBase.styles]

  get buttonClasses() {
    const bgColorClassMap: Record<Color, string> = {
      blue: "bg-iris-400",
      red: "bg-poppy-400",
      green: "bg-pistachio-400",
      yellow: "bg-yellow-400",
      gray: "bg-gray-500",
    }

    const bgColorClass = this.bgColorClass ?? bgColorClassMap[this.color] ?? "bg-iris-400"

    return {
      [this.heightClass]: true,
      [bgColorClass]: !this.disabled,
      "bg-gray-100": this.disabled,
      "px-2": true,
      "justify-start": this.contentAlignment === "left",
      "justify-center": this.contentAlignment !== "left",
      "aspect-square": !this.label,
      "rounded-sm": !this.pill && this.isSize("xs"),
      "rounded-md": !this.pill && !this.isSize("xs"),
      "rounded-full": this.pill,
      "inner-shadow": !this.isSelected && !this.disabled,
      "inner-shadow-active": !this.isSelected && !this.disabled && this.isSelectable,
      "inner-shadow-xs": !this.isSelected && this.isSize("xs"),
      "inner-shadow-sm": !this.isSelected && this.isSize("sm"),
      "inner-shadow-md": !this.isSelected && this.isSize("md"),
      "inner-shadow-lg": !this.isSelected && this.isSize("lg"),
      "inner-shadow-xl": !this.isSelected && this.isSize("xl"),
      "inner-shadow-selected": this.isSelected && !this.disabled,
      "cursor-pointer": !this.disabled,
      "cursor-not-allowed": this.disabled,
    }
  }

  get contentClasses() {
    return {
      "justify-start": this.contentAlignment === "left",
      "justify-center": this.contentAlignment !== "left",
      "gap-1": this.isSize("xs"),
      "gap-1.5": this.isSize("sm", "md"),
      "gap-2": this.isSize("lg", "xl"),
      "bottom-px": !this.isSelected && this.isSize("md", "lg", "xl"),
      "group-active:top-[1px]": !this.disabled,
      "top-[1px]": this.isSelected,
    }
  }

  get labelClasses() {
    return {
      hidden: !this.label,
      "text-base": this.isSize("xs", "sm"),
      "text-xl": this.isSize("md"),
      "text-2xl": this.isSize("lg"),
      "text-3xl": this.isSize("xl"),
      "text-shadow-[0_1px_0_rgb(25_28_37_/_1)]": this.isSize("xs"),
      "text-shadow-[0_2px_0_rgb(25_28_37_/_1)]": this.isSize("sm", "md"),
      "text-shadow-[0_3px_0_rgb(25_28_37_/_1)]": this.isSize("lg", "xl"),
    }
  }

  get iconClasses() {
    return {
      hidden: !this.icon,
      "h-4 w-auto": this.isSize("xs", "sm"),
      "h-5 w-auto": this.isSize("md"),
      "h-6 w-auto": this.isSize("lg"),
      "h-7 w-auto": this.isSize("xl"),
      "bottom-px": true,
    }
  }

  /**
   * Dynamic SVG icon styling, including size-dependent drop shadow
   * and stroke configuration for consistency across sizes.
   */
  get svgClasses() {
    return {
      "drop-shadow-[0_1px_0px_rgba(25_28_37_/_1)]": this.isSize("xs", "sm", "md"),
      "drop-shadow-[0_2px_0px_rgba(25_28_37_/_1)]": this.isSize("lg", "xl"),
    }
  }

  get backgroundOverlayClasses() {
    return {
      "opacity-25": this.disabled,
      "opacity-0": !this.isSelected,
      "group-hover:opacity-15": (this.isSelectable && this.hasMouseLeftAfterClicked) || (!this.isSelectable && !this.disabled),
      "group-active:opacity-30": !this.isSelected && this.isSelectable && !this.disabled,
      "opacity-30": this.isSelected,
      "group-active:opacity-0": this.isSelected,
      "rounded-md": !this.pill,
      "rounded-full": this.pill,
    }
  }

  get pingClasses() {
    return {
      "size-[12px]": this.isSize("xs"),
      "size-[14px]": this.isSize("sm"),
      "-top-[5px]": this.isSize("xs", "sm"),
      "right-[1px]": this.isSize("xs", "sm"),
      "size-[16px]": this.isSize("md"),
      "-top-[6px]": this.isSize("md"),
      "right-[2px]": this.isSize("md"),
      "size-[18px]": this.isSize("lg"),
      "-top-[7px]": this.isSize("lg"),
      "right-[3px]": this.isSize("lg"),
      "size-[20px]": this.isSize("xl"),
      "-top-[8px]": this.isSize("xl"),
      "right-[4px]": this.isSize("xl"),
    }
  }

  protected render() {
    return html`
      <button
        class="
          flex relative items-center w-full select-none
          transition-all duration-50 border border-gray-700
          group ${classMap(this.buttonClasses)}"
        .disabled=${this.disabled}
        @click=${this.disabled ? undefined : this.onClick}
        @mouseenter=${this.disabled ? undefined : this.onMouseEnter}
        @mouseleave=${this.disabled ? undefined : this.onMouseLeave}
      >
        ${this.ping
          ? html`<span class="absolute z-20 ${classMap(this.pingClasses)}">
              <span class="absolute size-full animate-ping rounded-full bg-poppy-400 opacity-75"></span>
              <span class="absolute size-full rounded-full border border-gray-700 bg-poppy-500"></span>
            </span>`
          : null}
        <div
          class="absolute inset-0 bg-black transition-opacity duration-50 pointer-events-none z-10 ${classMap(this.backgroundOverlayClasses)}"
        ></div>
        <div class="flex items-center relative ${classMap(this.contentClasses)}">
          <lit-icon
            name="${this.icon}"
            class="flex relative shrink-0 transition-all duration-50 ${classMap(this.iconClasses)}"
            .classes=${this.svgClasses}
          ></lit-icon>
          <span
            class="relative font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-900 transition-all duration-50 ${classMap(
              this.labelClasses,
            )}"
            >${this.label}</span
          >
        </div>
      </button>
    `
  }
}
