import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays the player's profile image inside a circular frame with a border.
 * Optionally, a flag can be displayed alongside the image.
 */
class PlayerProfileCircular extends LitElement {
  static properties = {
    countryCca2: { type: String },
    countryName: { type: String },
    flagPath: { type: String },
    iconPath: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  get classes() {
    return {
      'border': this.countryCca2 !== 'NP'
    }
  }

  render() {
    return html`
      <div class="flex justify-center items-center w-12 h-12 relative z-10 rounded-full bg-gray-0 border border-gray-700">
        <img src="${this.iconPath}" alt="Player's icon" class="h-12 rounded-sm" />  
        <img src="${this.flagPath}" alt="Flag of ${this.countryName}" title="${this.countryName}" class="h-4 absolute z-50 -bottom-[1px] -right-[1px] rounded-sm border-gray-700 ${classMap(this.classes)}" />
      </div>
    `;
  }
}

customElements.define('lit-player-profile-circular', PlayerProfileCircular);
