import { LitElement, css, html, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"
import { styleMap } from "lit/directives/style-map.js"

// @ts-ignore
import { AppStyles } from "../../../../../public/static/dist/lit-app-css"
// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"

/**
 * Dialog component with options for customization like modal, content, and footer.
 */
@customElement("lit-dialog")
export class Dialog extends LitElement {
  private readonly DEFAULT_OFFSET_TOP_PX = 10
  private readonly DEFAULT_OFFSET_BOTTOM_PX = 108

  @property({ type: Boolean }) hideCloseButton = false
  @property({ type: String }) iconPath?: string
  @property({ type: Boolean }) closeOnBgClick = false
  @property({ type: Boolean }) modal = false
  @property({ type: String }) label?: string
  @property({ type: Number }) maxHeightPx?: number
  @property({ type: Boolean }) hasFooter = false
  @property({ type: Boolean }) isOpened = false
  @property({ type: Number }) screenHeightPx = window.innerHeight

  static styles = css`
    ${TailwindStyles} ${AppStyles}
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
  `

  private resizeTimeout: number | null = null

  get contentClasses() {
    return {
      "pb-0": !this.hasFooter,
    }
  }

  get contentStyles() {
    const footerHeightPx = this.hasFooter ? 48 : 0
    const availableHeight = window.innerHeight - (this.DEFAULT_OFFSET_TOP_PX + this.DEFAULT_OFFSET_BOTTOM_PX + footerHeightPx)

    return {
      "max-height":
        this.maxHeightPx && availableHeight > this.maxHeightPx
          ? `${this.maxHeightPx}px`
          : `calc(100vh - ${this.DEFAULT_OFFSET_TOP_PX + this.DEFAULT_OFFSET_BOTTOM_PX + footerHeightPx}px)`,
    }
  }

  get dialogClasses() {
    let classes = {}

    if (this.isOpened && !this.modal) {
      classes = {
        ...classes,
        fixed: true,
        "top-4": true,
        "bottom-4": true,
        "left-4": true,
        "right-4": true,
        flex: true,
        "justify-center": true,
        "items-center": true,
      }
    }
    return classes
  }

  private throttledResize() {
    if (this.resizeTimeout) return

    this.resizeTimeout = window.setTimeout(() => {
      this.handleResize()
      this.resizeTimeout = null
    }, 200)
  }

  private handleResize() {
    const newHeight = window.innerHeight

    if (this.screenHeightPx !== newHeight) {
      this.screenHeightPx = newHeight
    }
  }

  private getDialogElement(): HTMLDialogElement | null | undefined {
    return this.shadowRoot?.querySelector("dialog")
  }

  private handleBackgropClick(event: MouseEvent) {
    if (!this.closeOnBgClick) return

    if (event.target === this.getDialogElement()) {
      this.close()
    }
  }

  private handleClose() {
    const footerSlotEl = this.shadowRoot?.querySelector('slot[name="footer"]') as HTMLSlotElement
    if (footerSlotEl) {
      footerSlotEl.removeEventListener("slotchange", this.onSlotChangeHandler)
    }

    this.dispatchEvent(
      new CustomEvent("closed", {
        detail: {},
        bubbles: true,
        composed: true,
      }),
    )
  }

  private handleSlotChange() {
    const footerSlotEl = this.shadowRoot?.querySelector('slot[name="footer"]') as HTMLSlotElement
    this.hasFooter = footerSlotEl?.assignedElements().length > 0 || false
  }

  private onBackdropClickHandler: (event: MouseEvent) => void = this.handleBackgropClick.bind(this)
  private onCloseHandler: () => void = this.handleClose.bind(this)
  private onSlotChangeHandler: () => void = this.handleSlotChange.bind(this)

  connectedCallback() {
    super.connectedCallback()
    window.addEventListener("resize", () => this.throttledResize())

    const dialog = this.getDialogElement()
    if (dialog) {
      dialog.addEventListener("click", (event) => {
        if (!this.closeOnBgClick) return

        if (event.target === this.getDialogElement()) {
          this.close()
        }
      })

      dialog.addEventListener("close", () => {
        this.dispatchEvent(
          new CustomEvent("closed", {
            detail: {},
            bubbles: true,
            composed: true,
          }),
        )
      })
    }

    const footerSlotEl = this.shadowRoot?.querySelector('slot[name="footer"]') as HTMLSlotElement
    footerSlotEl?.addEventListener("slotchange", () => {
      this.hasFooter = footerSlotEl.assignedElements().length > 0
    })
  }

  disconnectedCallback() {
    super.disconnectedCallback()
    window.removeEventListener("resize", () => this.throttledResize())
  }

  firstUpdated() {
    const dialog = this.getDialogElement()
    if (dialog) {
      dialog.addEventListener("click", this.onBackdropClickHandler)
      dialog.addEventListener("close", this.onCloseHandler)
    }

    const footerSlotEl = this.shadowRoot?.querySelector('slot[name="footer"]') as HTMLSlotElement
    if (footerSlotEl) {
      footerSlotEl.addEventListener("slotchange", this.onSlotChangeHandler)
    }
  }

  open() {
    this.isOpened = true
    const dialog = this.getDialogElement()
    if (dialog) {
      if (this.modal) {
        dialog.showModal()
      } else {
        dialog.show()
      }
    }
  }

  close() {
    this.isOpened = false
    const dialog = this.getDialogElement()
    dialog?.close()
  }

  protected render() {
    return html`
      <dialog class="z-50 ${classMap(this.dialogClasses)}">
        <div class="flex flex-col w-full rounded-lg border border-b-2 border-gray-700">
          <div
            id="header"
            class="flex items-center h-12 rounded-t-lg bg-iris-500 border-b border-iris-600"
            style="box-shadow: inset 0 3px 1px rgba(255, 255, 255, 0.25), inset 0 -3px 1px rgba(0, 0, 0, 0.25);"
          >
            <div class="flex justify-center items-center w-full h-full relative">
              <div class="absolute bottom-2 left-2">
                ${this.iconPath ? html`<img src="${this.iconPath}" class="h-12" draggable="false" />` : nothing}
              </div>

              <div class="flex justify-center items-center">
                <span class="font-heading text-xl font-bold text-gray-0 uppercase text-stroke-2 text-stroke-gray-700 select-none">${this.label}</span>
              </div>

              ${this.hideCloseButton
                ? nothing
                : html`
                    <button
                      class="flex justify-center items-center absolute right-2 w-8 h-8 rounded-lg rounded-tr-xl bg-red-500 hover:bg-red-600 border border-b-2 active:border-b border-gray-700"
                      style="box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.3), inset 0 -1px 1px rgba(0, 0, 0, 0.3);"
                      @click="${this.close}"
                    >
                      <img src="/static/img/icon/cross.svg" width="20" height="20" draggable="false" />
                    </button>
                  `}
            </div>
          </div>

          <div class="p-2 overflow-y-auto bg-iris-300 ${classMap(this.contentClasses)}" style="${styleMap(this.contentStyles)}">
            <slot name="content"></slot>
          </div>

          <div
            id="footer"
            class="items-center rounded-b-lg ${this.hasFooter ? "h-12 p-2 border-t border-iris-600 bg-iris-500" : "h-2 bg-iris-300"}"
            style="box-shadow: inset 0 -3px 1px rgba(0, 0, 0, 0.25);"
          >
            <slot name="footer"></slot>
          </div>
        </div>
      </dialog>
    `
  }
}

declare global {
  interface HTMLElementTagNameMap {
    "lit-dialog": Dialog
  }
}
