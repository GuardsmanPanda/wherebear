import { css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { ButtonBase } from './button-base.lit-component';

/**
 * Represents a square button with customizable size, color, image, and label.
 */
class ButtonSquare extends ButtonBase {
  static properties = {
    ...ButtonBase.properties,
    /** Background color class for the button (e.g., 'bg-blue-400'). */
    bgColorClass: { type: String },
    /** The path to the image to display in the button. */
    imgPath: { type: String },
    /** The label text to display on the button. */
    label: { type: String },
    imgHeightClass: { type: String }
  };

  static styles = [...ButtonBase.styles, css``];

  /** Maps button sizes (e.g., 'xl') to corresponding height classes in Tailwind CSS. */
  buttonHeightClasses = {
    'xl': 'h-[56px]'
  };

  constructor() {
    super();
    this.size = 'xl';
    this.bgColorClass = 'bg-iris-400';
  }

  /** The CSS class for the button's width based on the current size. */
  get buttonWidthClass() {
    return this.buttonWidthClasses[this.size];
  }

  /** The CSS class for the button's height based on the current size. */
  get buttonHeightClass() {
    return this.buttonHeightClasses[this.size];
  }

  /** The dynamic CSS classes for the button. */
  get buttonClasses() {
    return {
      [this.buttonHeightClass]: true,
      [this.bgColorClass]: true,
      'aspect-square': true,
      'inner-shadow': !this.isSelected,
      'inner-shadow-selected': this.isSelected
    }
  }

  /** The dynamic CSS classes for the image inside the button. */
  get imageClasses() {
    return {
      'h-[40px]': !this.imgHeightClass,
      [this.imgHeightClass]: this.imgHeightClass,
      '-top-[8px]': !this.isSelected,
      'group-active:-top-[6px]': true,
      '-top-[7px]': this.isSelected,
    }
  }

  /** The dynamic CSS classes for the label inside the button. */
  get labelClasses() {
    return {
      'top-[33px]': !this.isSelected,
      'group-active:top-[35px]': true,
      'top-[34px]': this.isSelected,

    }
  }

  /** The dynamic CSS classes for the background overlay inside the button. */
  get backgroundOverlayClasses() {
    return {
      'opacity-0': !this.isSelected,
      'group-hover:opacity-15': this.hasMouseLeftAfterClicked,
      'group-active:opacity-30': !this.isSelected,
      'opacity-45': this.isSelected,
      'group-active:opacity-0': this.isSelected
    }
  }

  render() {
    return html`
      <button
        class="
          flex flex-col justify-start items-center relative rounded-md 
          transition-all duration-100 border border-gray-700
          group ${classMap(this.buttonClasses)}"
        @click="${this.onClick}"
        @mouseenter="${this.onMouseEnter}"
        @mouseleave="${this.onMouseLeave}"
      >
        <div class="absolute inset-0 bg-black transition-opacity duration-100 pointer-events-none ${classMap(this.backgroundOverlayClasses)}"></div>
        <img src="${this.imgPath}" draggable="false" class="absolute transition-all duration-100 ${classMap(this.imageClasses)}" />
        <span class="absolute text-xs text-gray-50 text-stroke-2 text-stroke-gray-700 font-medium transition-all duration-100 ${classMap(this.labelClasses)}">${this.label}</span>
      </button>
    `;
  }
}

customElements.define('lit-button-square', ButtonSquare);