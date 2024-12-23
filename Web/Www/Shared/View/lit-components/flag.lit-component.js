import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/**
 * Displays a flag image based on the provided CCA2 code.
 * 
 * The `maxHeightClass` property is a workaround to prevent the Nepal flag 
 * from overflowing its container until a better solution is found.
 * 
 * IMPORTANT: For compatibility with Firefox, either width or height must 
 * be set when using this component. This may be due to how Firefox handles 
 * the Shadow DOM. I have not checked on other browsers yet. 
 * 
 * Example:
 * <lit-flag cca2="FR" filePath="/static/flag/svg/FR.svg" description="France" class="w-full"></lit-flag>
 */
class Flag extends LitElement {
  static properties = {
    cca2: { type: String },
    description: { type: String },
    filePath: { type: String },
    maxHeightClass: { type: String },
    roundedClass: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  get imgClasses() {
    return {
      [this.roundedClass]: this.roundedClass,
      [this.maxHeightClass]: this.maxHeightClass,
      'border': this.cca2 !== 'NP'
    }
  }

  render() {
    return html`
      <div class="flex items-center justify-center w-full h-full">
        <img 
          src="${this.filePath}"
          class="w-full h-full border-gray-700 ${classMap(this.imgClasses)}"
          alt="${this.description}"
          ${tooltip(this.description)} />
      </div>
    `;
  }
}

customElements.define('lit-flag', Flag);
