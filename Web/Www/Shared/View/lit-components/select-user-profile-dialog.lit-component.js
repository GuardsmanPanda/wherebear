import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';


class SelectUserProfileDialog extends LitElement {
  static properties = {
    displayName: { type: String },
    selectedCountryFlag: { type: String },
    errorMsg: { type: String, state: true },
    countryFlags: { type: Array, state: true },
    specialFlags: { type: Array, state: true },
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.countryFlags = [];
    this.specialFlags = [];
  }

  get litDialogElement() {
    return this.renderRoot.querySelector('lit-dialog');
  }

  async fetchFlags() {
    const response = await fetch(`/web-api/user/flags`);
    if (!response.ok) { throw new Error(response.status); }
    const data = await response.json();
    return data;
  }

  close() {
    this.litDialogElement.close();
  }

  async open() {
    try {
      const flags = await this.fetchFlags();
      this.countryFlags = flags.countries;
      this.specialFlags = flags.novelty_flags
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
    this.litDialogElement.open();
  }

  async selectFlag(flagEnum) {
    try {
      await fetch(`/web-api/user`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          user_flag_enum: flagEnum
        })
      });

      this.litDialogElement.close();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
  }

  async submit() {
    const displayName = this.renderRoot.querySelector('#display_name').value;
    const countryFlagSelected = this.renderRoot.querySelector('#country-flag-select').value;

    try {
      await fetch(`/web-api/user`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          display_name: displayName,
          country_cca2: countryFlagSelected,
        })
      });

      this.litDialogElement.close();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
  }

  async selectSpecialFlag(flagEnum) {
    try {
      await fetch(`/web-api/user`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          user_flag_enum: flagEnum
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
        label="Edit Name And Flag"
        modal
        closeOnBgClick
        x-on:closed="closeSelectUserProfileDialog()">
        <div slot="content" class="flex flex-col mx-2">
          ${this.errorMsg
        ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
        : html`
          <form class="flex flex-col" autocomplete="off">
            <label for="country-flag-select" class="text-gray-700 font-medium">Display Name</label>
            <input id="display_name" maxlength="32" value="${this.displayName}" />
      
            <label for="country-flag-select" class="mt-2 text-gray-700 font-medium">Country Flag</label>
            <select id="country-flag-select">
              ${this.countryFlags.map(flag => html`
                <option value="${flag.cca2}" ?selected="${flag.cca2 === this.selectedCountryFlag}">${flag.name}</option>
              `)}
            </select>
          </form>

          <span class="mt-2 text-gray-700 font-medium">Special Flags</span>
          <div class="flex">
            ${this.specialFlags.map(flag => html`
              <button class="px-1 hover:scale-110 transition-transform duration-75" tippy="${flag.description}" @click="${() => this.selectSpecialFlag(flag.enum)}">
                <img class="h-8" src="${flag.file_path}" alt="${flag.description}">
              </button>
            `)}
          </div>
          `}
        </div>
        <div slot="footer" class="flex justify-between">
          <lit-button label="CANCEL" bgColorClass="bg-gray-400" class="w-20" @click="${this.close}"></lit-button>
          <lit-button label="EDIT" bgColorClass="bg-iris-400" class="w-20" @click="${this.submit}"></lit-button>
        </div>
      </lit-dialog>`;
  }
}

customElements.define('lit-select-user-profile-dialog', SelectUserProfileDialog);
