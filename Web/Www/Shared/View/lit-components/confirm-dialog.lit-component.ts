import { LitElement, css, html } from 'lit';
import { customElement, property } from 'lit/decorators.js';

// @ts-ignore
import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { Dialog } from './dialog.lit-component';

/**
 * Displays a confirmation dialog with options to cancel or confirm an action.
 * Emits an event when the user selects either option.
 */
@customElement('lit-confirm-dialog')
class ConfirmDialog extends LitElement {
  @property({ type: String }) label = 'Confirm';
  @property({ type: String }) message = 'Are you sure?';
  @property({ type: String }) cancelBtnText = 'Cancel';
  @property({ type: String }) confirmBtnText = 'Confirm';
  @property({ type: String }) confirmBtnBgColorClass = 'bg-iris-400';

  static styles = css`${TailwindStyles}`;

  get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector('lit-dialog');
  }

  close() {
    this.litDialogElement?.close();
  }

  open() {
    this.litDialogElement?.open();
  }

  cancel() {
    this.dispatchEvent(new CustomEvent('canceled', { bubbles: true, composed: true }));
    this.close();
  }

  confirm() {
    this.dispatchEvent(new CustomEvent('confirmed', { bubbles: true, composed: true }));
    this.close();
  }

  protected render() {
    return html`
     <lit-dialog
        .label="${this.label}"
        modal
        @closed="${this.cancel}">
        <div slot="content" class="flex flex-col max-w-80 gap-8 mx-2 my-4">
          <div class="flex justify-center items-center">
            <p class="text-gray-800">${this.message}</p>
          </div>
          <div class="flex justify-center items-center gap-8">
            <lit-button 
              .label="${this.cancelBtnText}" 
              .bgColorClass="bg-gray-400" 
              class="min-w-20" 
              @click="${this.close}">
            </lit-button>
            <lit-button 
              .label="${this.confirmBtnText}" 
              .bgColorClass="${this.confirmBtnBgColorClass}" 
              class="min-w-20" 
              @click="${this.confirm}">
            </lit-button>
          </div>
        </div>
      </lit-dialog>`;
  }
}
