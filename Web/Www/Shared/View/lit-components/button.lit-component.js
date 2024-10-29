import { html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { ButtonBase } from './button-base.lit-component';

/**
 * Represents a button with customizable size, color, icon, and label.
 */
class Button extends ButtonBase {
  static properties = {
    ...ButtonBase.properties,

    /** Background color class for the button (e.g., 'bg-blue-400'). */
    bgColorClass: { type: String },

    /** The path to the image to display in the button. */
    imgPath: { type: String },

    /** The label text to display on the button. */
    label: { type: String },
  };

  static styles = [...ButtonBase.styles];

  constructor() {
    super();
    this.size = 'sm';
    this.bgColorClass = 'bg-iris-400';
  }

  /** The dynamic CSS classes for the button. */
  get buttonClasses() {
    return {
      [this.heightClass]: true,
      [this.bgColorClass]: true,
      'px-3': this.label && this.size === 'sm',
      'px-4': this.label && this.size !== 'sm',
      'aspect-square': !this.label,
      'rounded-md': !this.isPill,
      'rounded-full': this.isPill,
      'inner-shadow': !this.isSelected,
      'inner-shadow-selected': this.isSelected,
    }
  }

  /** The dynamic CSS classes for the content inside the button. */
  get contentClasses() {
    return {
      'gap-1': this.size === 'sm' || this.size === 'md',
      'gap-2': this.size === 'lg' || this.size === 'xl',
      'group-active:top-[2px]': true,
      'top-[1px]': this.isSelected
    };
  }


  /** The dynamic CSS classes for the icon inside the button. */
  get imageClasses() {
    return {
      'hidden': !this.imgPath,
      'h-5': this.size === 'sm',
      'h-6': this.size === 'md',
      'h-7': this.size === 'lg',
      'h-8': this.size === 'xl',
      'bottom-[2px]': true
    }
  }

  /** The dynamic CSS classes for the label inside the button. */
  get labelClasses() {
    return {
      'hidden': !this.label,
      'bottom-[1px]': true,
      'text-sm': this.size === 'sm',
      'text-base': this.size === 'md',
      'text-lg': this.size === 'lg',
      'text-xl': this.size === 'xl',
    }
  }

  /** The dynamic CSS classes for the background overlay inside the button. */
  get backgroundOverlayClasses() {
    return {
      'opacity-0': !this.isSelected,
      'group-hover:opacity-15': this.hasMouseLeftAfterClicked,
      'group-active:opacity-30': !this.isSelected,
      'opacity-45': this.isSelected,
      'group-active:opacity-0': this.isSelected,
      'rounded-md': !this.isPill,
      'rounded-full': this.isPill
    }
  }

  render() {
    return html`
      <button
        class="
          flex relative justify-center items-center w-full
          transition-all duration-100 border border-gray-950
          group ${classMap(this.buttonClasses)}"
        @click="${this.onClick}"
        @mouseenter="${this.onMouseEnter}"
        @mouseleave="${this.onMouseLeave}"
      >
        <div class="absolute inset-0 bg-black transition-opacity duration-100 pointer-events-none ${classMap(this.backgroundOverlayClasses)}"></div>
        <div class="flex justify-center items-center relative ${classMap(this.contentClasses)}">
          <img src="${this.imgPath}" draggable="false" class="relative transition-all duration-100 ${classMap(this.imageClasses)}" />
          <span class="relative font-body text-gray-50 text-stroke-2 text-stroke-gray-950 font-medium transition-all duration-100 uppercase ${classMap(this.labelClasses)}">${this.label}</span>
        </div>
      </button>
    `;
  }
}

customElements.define('lit-button', Button);