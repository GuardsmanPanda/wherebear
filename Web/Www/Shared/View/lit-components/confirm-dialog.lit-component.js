import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays a confirmation dialog with options to cancel or confirm an action.
 * Emits an event when the user selects either option.
 */
class ConfirmDialog extends LitElement {
  static properties = {
    label: { type: String },
    message: { type: String },
    cancelBtnText: { type: String },
    confirmBtnText: { type: String },
    confirmBtnBgColorClass: { type: String }
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.label = 'Confirm';
    this.message = 'Are you sure?';
    this.cancelBtnText = 'Cancel';
    this.confirmBtnText = 'Confirm';
    this.confirmBtnBgColorClass = 'bg-iris-400';
  }

  get litDialogElement() {
    return this.renderRoot.querySelector('lit-dialog');
  }

  close() {
    this.litDialogElement.close();
  }

  open() {
    this.litDialogElement.open();
  }

  cancel() {
    this.dispatchEvent(new CustomEvent('canceled', { bubbles: true, composed: true }));
    this.close();
  }

  confirm() {
    this.dispatchEvent(new CustomEvent('confirmed', { bubbles: true, composed: true }));
    this.close();
  }

  render() {
    return html`
     <lit-dialog
        label="${this.label}"
        modal
        closeOnBgClick
        x-on:closed="${this.cancel}">
        <div slot="content" class="flex flex-col max-w-80 gap-8 mx-2 my-4">
          <div class="flex justify-center items-center">
            <p class="text-gray-800">${this.message}</p>
          </div>
          <div class="flex justify-center items-center gap-8">
            <lit-button label="${this.cancelBtnText}" bgColorClass="bg-gray-400" class="min-w-20" @click="${this.close}"></lit-button>
            <lit-button label="${this.confirmBtnText}" bgColorClass="${this.confirmBtnBgColorClass}" class="min-w-20" @click="${this.confirm}"></lit-button>
          </div>
        </div>
      </lit-dialog>`;
  }
}

customElements.define('lit-confirm-dialog', ConfirmDialog);
