import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { styleMap } from 'lit/directives/style-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class Dialog extends LitElement {
  DEFAULT_OFFSET_TOP_PX = 10;
  /** 48 for the header, 10 for the margin and 50 that comes from I don't know where. */
  DEFAULT_OFFSET_BOTTOM_PX = 108;

  static properties = {
    /** Hides the close button if set to true. */
    hideCloseButton: { type: Boolean },

    /** Path to the icon image to be displayed in the dialog header. */
    iconPath: { type: String },

    /** Label to be displayed as the dialog title. */
    label: { type: String },

    /** Maximum height of the content area in pixels. */
    maxHeightPx: { type: Number },

    /** Top offset for the dialog in pixels */
    offsetTopPx: { type: Number },

    /** Bottom offset for the dialog in pixels */
    offsetBottomPx: { type: Number },

    /** Tracks whether the dialog has a footer. */
    hasFooter: { type: Boolean, state: true },

    /** The height of the screen in pixels. */
    screenHeightPx: { type: Number, state: true }
  }

  static styles = css`${TailwindStyles}
    dialog {
      background: transparent;
      border: none;
      box-shadow: none;
      overflow: visible;
      padding: 0;
    }

    dialog::backdrop {
      background-color: rgba(0, 0, 0, 0.4);
    }
  `;

  resizeTimeout = null;

  constructor() {
    super();
    this.hideCloseButton = false;
    this.iconPath = null;
    this.label = '';
    this.maxHeightPx = null;
    this.offsetTopPx = null;
    this.offsetBottomPx = null;
    this.hasFooter = false;
    this.screenHeightPx = window.innerHeight;
  }

  /** The dynamic CSS classes for the content section. */
  get contentClasses() {
    return {
      'pb-0': !this.hasFooter
    };
  }

  get contentStyles() {
    const footerHeightPx = this.hasFooter ? 48 : 0;
    const availableHeight = window.innerHeight - (this.DEFAULT_OFFSET_TOP_PX + this.DEFAULT_OFFSET_BOTTOM_PX + footerHeightPx);

    return {
      'max-height': this.maxHeightPx && availableHeight > this.maxHeightPx
        ? `${this.maxHeightPx}px`
        : `calc(100vh - ${this.DEFAULT_OFFSET_TOP_PX + this.DEFAULT_OFFSET_BOTTOM_PX + footerHeightPx}px)`
    };
  }

  connectedCallback() {
    super.connectedCallback();
    window.addEventListener('resize', () => {
      this.throttledResize()
    });
  }

  disconnectedCallback() {
    super.disconnectedCallback();
    window.removeEventListener('resize', () => {
      this.throttledResize()
    });
  }

  throttledResize() {
    if (this.resizeTimeout) return;

    this.resizeTimeout = setTimeout(() => {
      this.handleResize();
      this.resizeTimeout = null;
    }, 200);
  }

  handleResize() {
    const newHeight = window.innerHeight;

    if (this.screenHeightPx !== newHeight) {
      this.screenHeightPx = newHeight;
    }
  }

  /** Invoked when the element is first updated. */
  firstUpdated() {
    const dialog = this.shadowRoot.querySelector('dialog');
    dialog.addEventListener('click', (event) => {
      this.onBackdropClick(event)
    });
    dialog.addEventListener('close', () => this.onDialogClose());

    const footerSlotEl = this.shadowRoot.querySelector('slot[name="footer"]');
    footerSlotEl.addEventListener('slotchange', () => {
      this.hasFooter = footerSlotEl.assignedElements().length > 0;
    });
  }

  onBackdropClick(event) {
    const dialog = this.shadowRoot.querySelector('dialog');
    if (event.target === dialog) {
      this.close();
    }
  }

  onDialogClose() {
    this.emitCloseEvent();
  }

  emitCloseEvent() {
    this.dispatchEvent(new CustomEvent('closed', {
      detail: {},
      bubbles: true,
      composed: true
    }));
  }

  open() {
    this.shadowRoot.querySelector('dialog').showModal();
  }

  close() {
    this.shadowRoot.querySelector('dialog').close();
    this.emitCloseEvent();
  }

  render() {
    return html`
      <dialog class="z-10">
        <div class="flex flex-col w-full rounded-lg border border-b-2 border-gray-700">

          <div id="header" class="flex items-center h-12 rounded-t-lg bg-iris-400 border-b border-iris-600" style="box-shadow: inset 0 3px 1px rgba(255, 255, 255, 0.25), inset 0 -3px 1px rgba(0, 0, 0, 0.25);">
            <div class="flex w-full">
              <div class="flex-grow basis-1/3 relative shrink-0">
                <div class="absolute bottom-0 left-1">${this.iconPath ? html`<img src="${this.iconPath}" class="h-12" />` : nothing}</div>
              </div>
              <div class="flex justify-center items-center flex-grow basis-1/3">
                <span class="font-heading text-2xl font-bold text-gray-0 uppercase text-stroke-2 text-stroke-gray-700">${this.label}</span>
              </div>
              <div class="flex justify-end flex-grow basis-1/3 pr-1">
                ${this.hideCloseButton ? nothing : html`
                  <button
                    class="flex justify-center items-center relative w-8 h-8 rounded-lg rounded-tr-xl bg-red-500 hover:bg-red-600 border border-b-2 active:border-b border-gray-700"
                    style="box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.3), inset 0 -1px 1px rgba(0, 0, 0, 0.3);"
                    @click="${this.close}">
                    <img src="/static/img/icon/cross.svg" width="20" height="20" draggable=false />
                  </button>
                `}
              </div>
            </div>
          </div>

          <div class="p-2 overflow-y-auto bg-iris-300 ${classMap(this.contentClasses)}" style="${styleMap(this.contentStyles)}">
            <slot name="content"></slot>
          </div>
          
          <div id="footer" class="items-center rounded-b-lg ${this.hasFooter ? 'h-12 p-2 border-t border-iris-600 bg-iris-400' : 'h-2 bg-iris-300'}" style="box-shadow: inset 0 -3px 1px rgba(0, 0, 0, 0.25);">
            <slot name="footer"></slot>
          </div>
        </div>
      </dialog>
    `;
  }
}

customElements.define('lit-dialog', Dialog);
