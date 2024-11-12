import { html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { ButtonBase } from './button-base.lit-component';

/**
 * Represents a button with customizable size, color, icon, and label.
 */
class Button extends ButtonBase {
  static properties = {
    ...ButtonBase.properties,

    /** 
     * Background color class for the button. 
     * @example 'bg-blue-400'
     */
    bgColorClass: { type: String },

    /** Alignment of the content: accepts 'left' or 'center'. */
    contentAlignment: { type: String },

    /** The path to the image to display in the button. */
    imgPath: { type: String },

    /** The label text to display on the button. */
    label: { type: String },

    /** 
     * Text color class for the label. 
     * @example 'text-gray-800'
     */
    labelColorClass: { type: String },

    /** Whether the label should be converted to lowercase. */
    lowercased: { type: Boolean }
  };

  static styles = [...ButtonBase.styles];

  constructor() {
    super();
    this.size = 'sm';
    this.bgColorClass = 'bg-iris-400';
    this.labelColorClass = 'text-gray-50';
  }

  /** The dynamic CSS classes for the button. */
  get buttonClasses() {
    let paddingClass = 'px-1';

    if (this.label) {
      if (this.size === 'xs') {
        paddingClass = 'px-1'
      } else if (this.size === 'sm') {
        paddingClass = 'px-3'
      } else {
        paddingClass = 'px-4'
      }
    }

    return {
      [this.heightClass]: true,
      [this.bgColorClass]: true,
      [paddingClass]: true,
      'justify-start': this.contentAlignment === 'left',
      'justify-center': this.contentAlignment !== 'left',
      'aspect-square': !this.label,
      'rounded-md': !this.isPill && this.size !== 'xs',
      'rounded': !this.isPill && this.size === 'xs',
      'rounded-full': this.isPill,
      'inner-shadow': !this.isSelected,
      'inner-shadow-selected': this.isSelected,
    }
  }

  /** The dynamic CSS classes for the content inside the button. */
  get contentClasses() {
    return {
      'justify-start': this.contentAlignment === 'left',
      'justify-center': this.contentAlignment !== 'left',
      'gap-1': this.size === 'xs' || this.size === 'sm' || this.size === 'md',
      'gap-2': this.size === 'lg' || this.size === 'xl',
      'group-active:top-[2px]': true,
      'top-[1px]': this.isSelected
    };
  }


  /** The dynamic CSS classes for the icon inside the button. */
  get imageClasses() {
    return {
      'hidden': !this.imgPath,
      'h-6': this.size === 'xs' || this.size === 'sm' || this.size === 'md',
      'h-7': this.size === 'lg',
      'h-8': this.size === 'xl',
      'bottom-[2px]': true
    }
  }

  /** The dynamic CSS classes for the label inside the button. */
  get labelClasses() {
    return {
      [this.labelColorClass]: this.labelColorClass,
      'hidden': !this.label,
      'bottom-[1px]': this.size !== 'xs',
      'text-[14px]': this.size === 'xs',
      'text-sm': this.size === 'sm',
      'text-base': this.size === 'md',
      'text-lg': this.size === 'lg',
      'text-xl': this.size === 'xl',
      'uppercase': !this.lowercased
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
          flex relative items-center w-full
          transition-all duration-100 border border-gray-700
          group ${classMap(this.buttonClasses)}"
        @click="${this.onClick}"
        @mouseenter="${this.onMouseEnter}"
        @mouseleave="${this.onMouseLeave}"
      >
        <div class="absolute inset-0 bg-black transition-opacity duration-100 pointer-events-none ${classMap(this.backgroundOverlayClasses)}"></div>
        <div class="flex items-center relative ${classMap(this.contentClasses)}">
          <img src="${this.imgPath}" draggable="false" class="relative transition-all duration-100 ${classMap(this.imageClasses)}" />
          <span class="relative font-heading text-stroke-2 text-stroke-gray-700 font-semibold transition-all duration-100 ${classMap(this.labelClasses)}">${this.label}</span>
        </div>
      </button>
    `;
  }
}

customElements.define('lit-button', Button);