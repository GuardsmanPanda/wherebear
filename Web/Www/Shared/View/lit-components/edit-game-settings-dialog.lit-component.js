import { LitElement, css, html, nothing } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class EditGameSettingsDialog extends LitElement {
  static properties = {
    errorMsg: { type: String },
    gameId: { type: String },
    gamePublicStatusEnum: { type: String },
    gameType: { type: String },
    isBob: { type: Boolean },
    roundCount: { type: Number },
    roundDurationSec: { type: Number },
    roundResultDurationSec: { type: Number },
  }

  static styles = css`${TailwindStyles}`;

  get litDialogElement() {
    return this.renderRoot.querySelector('lit-dialog');
  }

  close() {
    this.litDialogElement.close();
  }

  open() {
    this.litDialogElement.open();
  }

  async submit() {
    const roundCount = this.renderRoot.querySelector('#round-count')?.value;
    const roundDurationSec = this.renderRoot.querySelector('#round-duration-sec').value;
    const roundResultDurationSec = this.renderRoot.querySelector('#round-result-duration-sec').value;
    const gamePublicStatusEnum = this.renderRoot.querySelector('input[name="game-public-status-enum"]:checked').value;

    try {
      await fetch(`/web-api/game/${this.gameId}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          number_of_rounds: roundCount,
          round_duration_seconds: roundDurationSec,
          round_result_duration_seconds: roundResultDurationSec,
          game_public_status_enum: gamePublicStatusEnum
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
        label="Game Settings"
        x-on:closed="closeEditGameSettingsDialog()">
        <div slot="content" class="flex flex-col mx-2">
          ${this.errorMsg
        ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
        : html`
          <form class="flex flex-col" autocomplete="off">
            <label for="round-count" class="text-gray-700 font-medium">Number of Rounds</label>
            <input id="round-count" required min="1" max="40" value="${this.roundCount}" ?disabled="${this.gameType === 'templated'}"  />

            <label for="round-duration-sec" class="mt-2 text-gray-700 font-medium">Round Duration Seconds</label>
            <input id="round-duration-sec" required min="1" max="40" value="${this.roundDurationSec}" />

            <label for="round-result-duration-sec" class="mt-2 text-gray-700 font-medium">Round Result Duration Seconds</label>
            <input id="round-result-duration-sec" required min="1" max="40" value="${this.roundResultDurationSec}" />

            <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
              <legend class="px-1.5">Public Status</legend>
              <label class="font-bold">Public
                <input type="radio" name="game-public-status-enum" value="PUBLIC" class="ml-1" ?checked="${this.gamePublicStatusEnum === 'PUBLIC'}">
              </label>
              
              ${this.isBob ? html`
              <label class="font-bold">Google
                <input type="radio" name="game-public-status-enum" value="GOOGLE" class="ml-1" ?checked="${this.gamePublicStatusEnum === 'GOOGLE'}">
              </label>
              ` : nothing}
              
              <label class="font-bold">Private
                <input type="radio" name="game-public-status-enum" value="PRIVATE" class="ml-1" ?checked="${this.gamePublicStatusEnum === 'PRIVATE'}">
              </label>
            </fieldset>
          </form>
          `}
        </div>
        <div slot="footer" class="flex justify-between">
          <lit-button label="CANCEL" bgColorClass="bg-gray-400" class="w-20" @click="${this.close}"></lit-button>
          <lit-button label="EDIT" bgColorClass="bg-iris-400" class="w-20" @click="${this.submit}"></lit-button>
        </div>
      </lit-dialog>`;
  }
}

customElements.define('lit-edit-game-settings-dialog', EditGameSettingsDialog);
