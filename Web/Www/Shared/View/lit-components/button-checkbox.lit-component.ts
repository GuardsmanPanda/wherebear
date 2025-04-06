import { css, html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

import { ButtonBase } from "./button-base.lit-component"

/**
 * Represents a selectable button with a checkbox.
 */
@customElement("lit-button-checkbox")
class ButtonCheckbox extends ButtonBase {
  @property({ type: String }) label!: string

  static styles = [
    ...ButtonBase.styles,
    css`
      .inner-shadow {
        box-shadow:
          inset 0 1px 1px rgba(255, 255, 255, 0.6),
          inset 0 -2px 0 rgba(0, 0, 0, 0.6);
      }
      .group:active .inner-shadow {
        box-shadow:
          inset 0 1px 0 rgba(255, 255, 255, 0.6),
          inset 0 -0 0 rgba(0, 0, 0, 0.6);
      }
      .inner-shadow-selected {
        box-shadow:
          inset 0 0 1px rgba(255, 255, 255, 0.6),
          inset 0 -1px 0 rgba(0, 0, 0, 0.6);
      }
      .group:active .inner-shadow-selected {
        box-shadow:
          inset 0 1px 1px rgba(255, 255, 255, 0.6),
          inset 0 0 0 rgba(0, 0, 0, 0.6);
      }
    `,
  ]

  get buttonClasses(): { [key: string]: boolean } {
    return {
      [this.heightClass]: true,
      "pointer-events-none": this.isDisabled,
    }
  }

  get leftPartClasses(): { [key: string]: boolean } {
    return {
      "w-10": this.size === "sm",
      "w-14": this.size === "md",
      "rounded-l-md": this.size !== "xs",
      "rounded-l-sm": this.size === "xs",
      "inner-shadow": !this.isSelected,
      "inner-shadow-selected": this.isSelected,
    }
  }

  get checkboxClasses(): { [key: string]: boolean } {
    return {
      "opacity-0": !this.isSelected,
      "opacity-100": this.isSelected,
      "h-4": this.size === "sm",
      "h-5": this.size === "md",
      "group-active:top-[2px]": true,
    }
  }

  get rightPartClasses(): { [key: string]: boolean } {
    return {
      "bg-gray-200": !this.isSelected,
      "bg-iris-400": this.isSelected,
      "rounded-r-md": this.size !== "xs",
      "rounded-r-sm": this.size === "xs",
      "inner-shadow": !this.isSelected,
      "inner-shadow-selected": this.isSelected,
    }
  }

  get labelClasses(): { [key: string]: boolean } {
    return {
      "text-[14px]": this.size === "xs",
      "text-sm": this.size === "sm",
      "text-base": this.size === "md",
      "text-lg": this.size === "lg",
      "text-xl": this.size === "xl",
      "group-active:top-[2px]": true,
    }
  }

  get backgroundOverlayClasses(): { [key: string]: boolean } {
    return {
      "group-hover:opacity-15": this.hasMouseLeftAfterClicked,
      "group-active:opacity-30": !this.isSelected,
      "group-active:opacity-0": this.isSelected,
      "rounded-md": this.size !== "xs",
      "rounded-sm": this.size === "xs",
    }
  }

  protected render() {
    return html`
      <button
        class="flex w-full relative transition-all duration-100 select-none cursor-pointer group ${classMap(this.buttonClasses)}"
        @click="${this.onClick}"
        @mouseenter="${this.onMouseEnter}"
        @mouseleave="${this.onMouseLeave}"
      >
        <div
          class="absolute inset-0 bg-black opacity-0 transition-opacity duration-100 pointer-events-none ${classMap(this.backgroundOverlayClasses)}"
        ></div>
        <div class="flex justify-center items-center h-full bg-gray-50 border border-gray-700 ${classMap(this.leftPartClasses)}">
          <img src="/static/img/icon/check-blue.svg" class="relative transition-opacity duration-100 ${classMap(this.checkboxClasses)}" />
        </div>
        <div class="flex justify-center items-center w-full h-full border border-l-0 border-gray-700 ${classMap(this.rightPartClasses)}">
          <span class="font-heading font-semibold text-stroke-2 text-stroke-gray-700 text-gray-50 relative ${classMap(this.labelClasses)}"
            >${this.label}</span
          >
        </div>
      </button>
    `
  }
}
