import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';


class SelectMapStyleDialog extends LitElement {
  static properties = {
    errorMsg: { type: String, state: true },
    mapStyles: { type: Array, state: true },
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.mapStyles = [];
  }

  get litDialogElement() {
    return this.renderRoot.querySelector('lit-dialog');
  }

  async fetchUserMapStyles() {
    const response = await fetch(`/web-api/user/map-styles`);
    if (!response.ok) { throw new Error(response.status); }
    const data = await response.json();
    return data;
  }

  async open() {
    try {
      this.mapStyles = await this.fetchUserMapStyles();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
    this.litDialogElement.open();
  }

  replacePlaceholdersInUri(fullUri) {
    return fullUri.replace('{x}', 1614).replace('{y}', 1016).replace('{z}', 11);
  }

  async selectMapStyle(mapStyleEnum) {
    try {
      await fetch(`/web-api/user`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          map_style_enum: mapStyleEnum
        })
      });

      this.litDialogElement.close();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
  }

  render() {
    return html`
      <lit-dialog
        label="Select Map Style"
        x-on:closed="closeSelectMapStyleDialog()">
        <div slot="content" class="mx-2">
        ${this.errorMsg
        ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
        : html`<div class="grid">
          ${this.mapStyles.map(mapStyle => html`
          <button class="px-0.5 py-1 hover:scale-105 transition-transform duration-75" @click="${() => this.selectMapStyle(mapStyle.enum)}">
            <span class="text-xl font-medium">${mapStyle.name}</span>
            <img class="h-24 w-96 object-none" src="${this.replacePlaceholdersInUri(mapStyle.full_uri)}">
          </button>
        `)}
        </div>
        ` }
        </div>
      </lit-dialog>`;
  }
}

customElements.define('lit-select-map-style-dialog', SelectMapStyleDialog);
