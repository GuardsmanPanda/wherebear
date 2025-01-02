import { html } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

import { ButtonBase } from "./button-base.lit-component"

/**
 * Represents a square button with customizable size, color, image, and label.
 */
@customElement("lit-button-square")
class ButtonSquare extends ButtonBase {
  @property({ type: String }) bgColorClass = "bg-iris-400"
  @property({ type: String }) imgPath!: string
  @property({ type: String }) label!: string
  @property({ type: String }) imgHeightClass?: string

  get buttonClasses(): { [key: string]: boolean } {
    return {
      [this.bgColorClass]: true,
      "h-[56px]": true,
      "aspect-square": true,
      "inner-shadow": !this.isSelected,
      "inner-shadow-selected": this.isSelected,
    }
  }

  get imageClasses(): { [key: string]: boolean } {
    let classes: { [key: string]: boolean } = {
      "h-[40px]": !this.imgHeightClass,
      "-top-[8px]": !this.isSelected,
      "group-active:-top-[6px]": true,
      "-top-[7px]": this.isSelected,
    }

    if (this.imgHeightClass) {
      classes[this.imgHeightClass] = true
    }

    return classes
  }

  get labelClasses(): { [key: string]: boolean } {
    return {
      "top-[33px]": !this.isSelected,
      "group-active:top-[35px]": true,
      "top-[34px]": this.isSelected,
    }
  }

  get backgroundOverlayClasses(): { [key: string]: boolean } {
    return {
      "opacity-0": !this.isSelected,
      "group-hover:opacity-15": this.hasMouseLeftAfterClicked,
      "group-active:opacity-30": !this.isSelected,
      "opacity-45": this.isSelected,
      "group-active:opacity-0": this.isSelected,
    }
  }

  protected render() {
    return html`
      <button
        class="flex flex-col justify-start items-center relative rounded-md 
          transition-all duration-100 border border-gray-700
          group ${classMap(this.buttonClasses)}"
        @click="${this.onClick}"
        @mouseenter="${this.onMouseEnter}"
        @mouseleave="${this.onMouseLeave}"
      >
        <div class="absolute inset-0 bg-black transition-opacity duration-100 pointer-events-none ${classMap(this.backgroundOverlayClasses)}"></div>
        <img src="${this.imgPath}" draggable="false" class="absolute transition-all duration-100 ${classMap(this.imageClasses)}" />
        <span
          class="absolute text-xs text-gray-50 text-stroke-2 text-stroke-gray-700 font-medium transition-all duration-100 ${classMap(
            this.labelClasses,
          )}"
          >${this.label}</span
        >
      </button>
    `
  }
}
