import { LitElement, css, html } from "lit"
import { customElement, property, state } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Dialog } from "./dialog.lit-component"

interface Flag {
  cca2: string
  name: string
  description: string
  file_path: string
  enum: string
}

@customElement("lit-select-user-profile-dialog")
export class SelectUserProfileDialog extends LitElement {
  @property({ type: String }) displayName!: string
  @property({ type: String }) selectedCountryFlag!: string

  @state() errorMsg = ""
  @state() countryFlags: Flag[] = []
  @state() specialFlags: Flag[] = []

  static styles = css`
    ${TailwindStyles}
  `

  get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector("lit-dialog")
  }

  private async fetchFlags(): Promise<{ countries: Flag[]; novelty_flags: Flag[] }> {
    const response = await fetch(`/web-api/user/flags`)
    if (!response.ok) {
      throw new Error(`Error: ${response.status}`)
    }
    const data = await response.json()
    return data
  }

  private async selectFlag(flagEnum: string): Promise<void> {
    try {
      await fetch(`/web-api/user`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          user_flag_enum: flagEnum,
        }),
      })

      this.litDialogElement?.close()
    } catch (err) {
      this.errorMsg = "Something went wrong"
    }
  }

  private async submit(): Promise<void> {
    const displayName = (this.renderRoot.querySelector("#display_name") as HTMLInputElement).value
    const countryFlagSelected = (this.renderRoot.querySelector("#country-flag-select") as HTMLSelectElement).value

    try {
      await fetch(`/web-api/user`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          display_name: displayName,
          country_cca2: countryFlagSelected,
        }),
      })

      this.litDialogElement?.close()
    } catch (err) {
      this.errorMsg = "Something went wrong"
    }
  }

  private async selectSpecialFlag(flagEnum: string): Promise<void> {
    try {
      await fetch(`/web-api/user`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          user_flag_enum: flagEnum,
        }),
      })

      this.litDialogElement?.close()
    } catch (err) {
      this.errorMsg = "Something went wrong"
    }
  }

  close(): void {
    this.litDialogElement?.close()
  }

  async open(): Promise<void> {
    try {
      const flags = await this.fetchFlags()
      this.countryFlags = flags.countries
      this.specialFlags = flags.novelty_flags
    } catch (err) {
      this.errorMsg = "Something went wrong"
    }
    this.litDialogElement?.open()
  }

  protected render() {
    return html`
      <lit-dialog label="Edit Name And Flag" modal @closed="${() => this.close()}">
        <div slot="content" class="flex flex-col mx-2">
          ${this.errorMsg
            ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
            : html`
                <form class="flex flex-col" autocomplete="off">
                  <label for="country-flag-select" class="text-gray-700 font-medium">Display Name</label>
                  <input id="display_name" maxlength="32" value="${this.displayName}" />

                  <label for="country-flag-select" class="mt-2 text-gray-700 font-medium">Country Flag</label>
                  <select id="country-flag-select">
                    ${this.countryFlags.map(
                      (flag: Flag) => html`
                        <option value="${flag.cca2}" ?selected="${flag.cca2 === this.selectedCountryFlag}">${flag.name}</option>
                      `,
                    )}
                  </select>
                </form>

                <span class="mt-2 text-gray-700 font-medium">Special Flags</span>
                <div class="flex">
                  ${this.specialFlags.map(
                    (flag: Flag) => html`
                      <button
                        class="px-1 hover:scale-110 transition-transform duration-75"
                        tippy="${flag.description}"
                        @click="${() => this.selectSpecialFlag(flag.enum)}"
                      >
                        <img class="h-8" src="${flag.file_path}" alt="${flag.description}" />
                      </button>
                    `,
                  )}
                </div>
              `}
        </div>
        <div slot="footer" class="flex justify-between">
          <lit-button label="CANCEL" bgColorClass="bg-gray-400" class="w-20" @click="${this.close}"></lit-button>
          <lit-button label="EDIT" bgColorClass="bg-iris-400" class="w-20" @click="${this.submit}"></lit-button>
        </div>
      </lit-dialog>
    `
  }
}
