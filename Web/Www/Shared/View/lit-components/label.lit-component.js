import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays a very short text, one or two words reccommended, in a small rectangle.
 */
class Label extends LitElement {
  static properties = {
    label: { type: String },
    bgColorClass: { type: String },
    isPill: { type: Boolean },
    size: { type: String },
    widthClass: { type: String }
  }

  static styles = css`${TailwindStyles}`;

  get classes() {
    return {
      [this.heightClass]: true,
      [this.bgColorClass]: this.bgColorClass,
      [this.widthClass]: this.widthClass,
      '-skew-x-6': !this.isPill,
      'rounded': !this.isPill,
      'rounded-full': this.isPill
    }
  }

  get labelClasses() {
    return {
      [this.textSizeClass]: true,
      'skew-x-6': !this.isPill
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
      <div class="flex justify-center items-center px-2 border border-gray-700 ${classMap(this.classes)}">
        <span class="font-heading font-semibold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}">${this.label}</span>
      </div>
    `;
  }
}

customElements.define('lit-label', Label);
