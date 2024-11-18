import { LitElement, css, html, nothing } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays a very short text, one or two words reccommended, in a small rectangle.
 */
class Label extends LitElement {
  static properties = {
    label: { type: String },
    bgColorClass: { type: String },
    iconPath: { type: String },
    isPill: { type: Boolean },
    size: { type: String },
    widthClass: { type: String },
    type: { type: String }
  }

  static styles = css`${TailwindStyles}`;

  get classes() {
    return {
      [this.heightClass]: true,
      [this.bgColorClass]: this.bgColorClass,
      [this.widthClass]: this.widthClass,
      'justify-center': !this.iconPath,
      'pl-1': !this.iconPath,
      '-skew-x-6': !this.isPill && this.size !== 'xs',
      '-skew-x-12': !this.isPill && this.size === 'xs',
      'rounded': !this.isPill,
      'rounded-full': this.isPill,
      'bg-pistachio-500': this.type === 'success',
      'bg-poppy-500': this.type === 'error',
      'bg-gray-600': this.type === 'dark',
      'bg-iris-500': this.type === 'primary',
    }
  }

  get imgClasses() {
    return {
      'skew-x-6': !this.isPill && this.size !== 'xs',
      'skew-x-12': !this.isPill && this.size === 'xs',
    }
  }

  get labelClasses() {
    return {
      [this.textSizeClass]: true,
      'skew-x-6': !this.isPill && this.size !== 'xs',
      'skew-x-12': !this.isPill && this.size === 'xs',
    }
  }

  get heightClass() {
    switch (this.size) {
      case 'xs': return 'h-4';
      case 'sm': return 'h-6';
      default: return 'h-5'
    }
  }

  get textSizeClass() {
    switch (this.size) {
      case 'xs': return 'text-xs';
      case 'sm': return 'text-sm';
      default: return 'text-xs';
    }
  }

  render() {
    return html`
      <div class="flex items-center relative pr-1 border border-gray-700 ${classMap(this.classes)}">
        <div class="${this.iconPath ? 'flex' : 'hidden'} justify-center items-center w-5 h-4 relative bottom-[2px] left-[2px] mr-1">
        ${this.iconPath ? html`<img src="${this.iconPath}" class="max-h-4 max-h-5 object-contain ${classMap(this.imgClasses)}" />` : nothing}
        </div>
        <span class="font-heading font-semibold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}">${this.label}</span>
      </div>
    `;
  }
}

customElements.define('lit-label', Label);
