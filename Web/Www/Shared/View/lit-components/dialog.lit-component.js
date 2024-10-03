import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class Dialog extends LitElement {
  static properties = {
    hideCloseButton: { type: Boolean },
    iconPath: { type: String },
    label: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.hideCloseButton = false;
    this.iconPath = null;
    this.label = 'Dialog';
  }

  close() {
    this.dispatchEvent(new CustomEvent('closed', {
      detail: {},
      bubbles: true,
      composed: true
    }));
  }

  render() {
    return html`
      <div class="flex flex-col w-80 rounded-lg border border-gray-800">
        <div class="flex items-center h-10 rounded-t-lg bg-[#99B5FF] border-b border-gray-800">
          <div class="flex w-full">
            <div class="flex-grow basis-1/3 relative">
              <div class="absolute bottom-0 left-1">${this.iconPath ? html`<img src="${this.iconPath}" class="h-10 w-auto" />` : nothing}</div>
            </div>
            <div class="flex justify-center items-center flex-grow basis-1/3">
              <span class="font-heading text-2xl font-bold text-white uppercase text-stroke-2">${this.label}</span>
            </div>
            <div class="flex justify-end flex-grow basis-1/3 pr-1">
              ${this.hideCloseButton ? empty : html`
                <button
                  class="flex justify-center items-center relative w-8 h-8 rounded-lg rounded-tr-xl bg-red-500 border border-b-2 border-gray-800"
                  style="box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.5), inset 0 -1px 1px rgba(0, 0, 0, 0.3);"
                  @click="${this.close}">
                  <img src="/static/img/icon/cross.svg" width="20" height="20" />
                </button>
              `}
            </div>
          </div>
        </div>
        <div class="rounded-b-lg bg-[#5774BC]">
          <slot></slot>
        </div>
      </div>
    `;
  }

}

customElements.define('lit-dialog', Dialog);