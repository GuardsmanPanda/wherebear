import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays an emblem icon with the user level in the center.
 */
class LevelEmblem extends LitElement {
  static properties = {
    level: { type: Number },
    size: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  get classes() {
    return {
      'w-6': this.size === 'xs',
      'w-8': this.size === 'sm',
      'w-10': this.size === 'md',
      'w-12': this.size === 'lg'
    }
  }

  get levelClasses() {
    return {
      'text-sm': this.size === 'xs',
      'text-lg': this.size === 'sm',
      'bottom-[2px]': this.size === 'sm',
      'text-xl': this.size === 'md',
      'bottom-[4px]': this.size === 'md',
      'text-2xl': this.size === 'lg'
    }
  }

  render() {
    return html`
      <div class="relative ${classMap(this.classes)}">
        <img src="/static/img/icon/emblem.svg" class="" draggable="false" />
        <span class="absolute inset-0 flex items-center justify-center text-white font-heading font-bold text-stroke-2 text-stroke-iris-900 select-none ${classMap(this.levelClasses)}">
          ${this.level}
        </span>
      </div>
    `;
  }

}

customElements.define('lit-level-emblem', LevelEmblem);