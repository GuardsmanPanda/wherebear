import { css, html, LitElement, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Dialog } from "./dialog.lit-component"

interface Guess {
  distance_meters: number
  detailed_points: string
  map_marker_file_path: string
  rank: number
  rounded_points: string
  title: string
  user_country_cca2: string
  user_display_name: string
  user_flag_description: string
  user_flag_file_path: string
  user_id: string
  user_level: string
}

/**
 * Displays a button to toggle a ranking dialog for the results of a round.
 */
@customElement("lit-round-result-ranking-dialog-button")
export class RoundResultRankingDialogButton extends LitElement {
  @property({ type: Array }) guesses: Guess[] = []
  @property({ type: Boolean }) isSelected = false
  @property({ type: String }) userId!: string

  static styles = css`
    ${TailwindStyles}
  `

  private onDialogClosed(): void {
    this.isSelected = false
  }

  private switchDialogVisibility(e: CustomEvent): void {
    const dialogElement = this.shadowRoot?.querySelector("#dialog") as Dialog
    if (e.detail.isSelected) {
      dialogElement?.open()
      this.isSelected = true
    } else {
      dialogElement?.close()
      this.isSelected = false
    }
  }

  protected render() {
    return html`
      <lit-dialog id="dialog" label="Ranking" iconPath="/static/img/icon/podium.svg" modal closeOnBgClick @closed="${this.onDialogClosed}">
        <div slot="content" class="flex flex-col gap-2 min-w-64">
          ${this.guesses?.length === 0
            ? html`
                <template>
                  <p class="text-base text-gray-800">There are no players in this game.</p>
                </template>
              `
            : nothing}
          ${this.guesses?.map(
            (guess) => html`
              <lit-player-result-item
                countryCCA2="${guess.user_country_cca2}"
                detailedPoints="${guess.detailed_points}"
                distanceMeters="${guess.distance_meters}"
                flagFilePath="${guess.user_flag_file_path}"
                flagDescription="${guess.user_flag_description}"
                iconPath="${guess.map_marker_file_path}"
                level="${guess.user_level}"
                name="${guess.user_display_name}"
                rank="${guess.rank}"
                rankIconType="medal"
                rankSelected="${guess.user_id === this.userId ? guess.rank : ""}"
                roundedPoints="${guess.rounded_points}"
                userTitle="${guess.title}"
              >
              </lit-player-result-item>
            `,
          )}
        </div>
      </lit-dialog>

      <lit-button-square
        label="Ranking"
        imgPath="/static/img/icon/podium.svg"
        size="xl"
        bgColorClass="bg-gray-600"
        class="z-10"
        isSelectable="true"
        ?isSelected="${this.isSelected}"
        @clicked="${(e: CustomEvent) => this.switchDialogVisibility(e)}"
      >
      </lit-button-square>
    `
  }
}
