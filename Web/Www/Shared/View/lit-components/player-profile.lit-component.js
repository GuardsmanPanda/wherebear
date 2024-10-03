import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class PlayerProfile extends LitElement {
  static properties = {
    iconPath: { type: String }
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();

  }

  render() {
    return html`
      <div class="flex justify-center items-center w-12 h-12 z-10 rounded-full bg-gray-100 border border-gray-800">
        <img class="h-12" src="${this.iconPath}" alt="" />  
      </div>
    `;
  }
}

customElements.define('lit-player-profile', PlayerProfile);
