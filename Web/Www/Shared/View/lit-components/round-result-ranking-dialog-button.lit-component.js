import { css, html, LitElement, nothing } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays a button to toggle a ranking dialog for the results of a round.
 */
class RoundResultRankingDialogButton extends LitElement {
  static properties = {
    guesses: { type: Array },
    isSelected: { type: Boolean }
  };

  static styles = [
    css`${TailwindStyles}`
  ];

  constructor() {
    super();
    this.guesses = [];
    this.isSelected = false;
  }

  onDialogClosed() {
    this.isSelected = false;
  }

  switchDialogVisibility(e) {
    const dialogElement = this.shadowRoot.querySelector('#dialog');
    if (e.detail.isSelected) {
      dialogElement.open();
      this.isSelected = true;
    } else {
      dialogElement.close();
      this.isSelected = false;
    }
  }

  render() {
    return html`
      <lit-dialog
        id="dialog"
        label="Ranking"
        iconPath="/static/img/icon/podium.svg"
        @closed="${this.onDialogClosed}"
      >
        <div slot="content" class="flex flex-col gap-2 min-w-64">
          ${this.guesses?.length === 0 ? html`
          <template>
            <p class="text-base text-gray-800">There are no players in this game.</p>
          </template>
          ` : nothing} 
          
          ${this.guesses?.map(guess => html`
            <lit-player-result-item 
              countryCCA2="${guess.user_country_cca2}"
              detailedPoints="${guess.detailed_points}"
              distanceMeters="${guess.distance_meters}"
              flagFilePath="${guess.user_flag_file_path}"
              flagDescription="${guess.user_flag_description}"
              .title="${guess.title}"
              iconPath="${guess.map_marker_file_path}"
              level="${guess.user_level}"
              name="${guess.user_display_name}"
              rank="${guess.rank}"
              roundedPoints="${guess.rounded_points}">
            </lit-player-result-item>
          `)}
        </div>
      </lit-dialog>

      <lit-button-square label="Ranking" 
        imgPath="/static/img/icon/podium.svg"
        size="xl"
        bgColorClass="bg-gray-600"
        class="z-10"
        isSelectable="true"
        ?isSelected="${this.isSelected}"
        @clicked="${(e) => this.switchDialogVisibility(e)}">
      </lit-button-square>
    `;
  }
}

customElements.define('lit-round-result-ranking-dialog-button', RoundResultRankingDialogButton);
