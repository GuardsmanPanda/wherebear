import { LitElement, css, html, nothing } from 'lit';
import { customElement, property, state } from 'lit/decorators.js';

// @ts-ignore
import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { Dialog } from './dialog.lit-component';

type GamePublicStatusEnum = 'PRIVATE' | 'PUBLIC' | 'GOOGLE';
type GameType = 'normal' | 'templated';

@customElement('lit-edit-game-settings-dialog')
class EditGameSettingsDialog extends LitElement {
  @property({ type: String }) gameId!: string;
  @property({ type: String }) gamePublicStatusEnum!: GamePublicStatusEnum;
  @property({ type: String }) gameType!: GameType;
  @property({ type: Boolean }) isBob = false;
  @property({ type: Number }) roundCount!: number;
  @property({ type: Number }) roundDurationSec!: number;
  @property({ type: Number }) roundResultDurationSec!: number;
  
  @state() errorMsg = '';

  static styles = css`${TailwindStyles}`;

  private get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector('lit-dialog');
  }

  private async submit() {
    const roundCount = (this.renderRoot.querySelector('#round-count') as HTMLInputElement)?.value;
    const roundDurationSec = (this.renderRoot.querySelector('#round-duration-sec') as HTMLInputElement)?.value;
    const roundResultDurationSec = (this.renderRoot.querySelector('#round-result-duration-sec') as HTMLInputElement)?.value;
    const gamePublicStatusEnum = (this.renderRoot.querySelector('input[name="game-public-status-enum"]:checked') as HTMLInputElement)?.value;

    try {
      await fetch(`/web-api/game/${this.gameId}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          number_of_rounds: Number(roundCount),
          round_duration_seconds: Number(roundDurationSec),
          round_result_duration_seconds: Number(roundResultDurationSec),
          game_public_status_enum: gamePublicStatusEnum,
        }),
      });

      this.close();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
  }

  close() {
    this.litDialogElement?.close();
  }

  open() {
    this.litDialogElement?.open();
  }
  
  protected render() {
    return html`
      <lit-dialog
        label="Game Settings"
        modal
        x-on:closed="closeEditGameSettingsDialog()">
        <div slot="content" class="flex flex-col mx-2">
          ${this.errorMsg
            ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
            : html`
              <form class="flex flex-col" autocomplete="off">
                <label for="round-count" class="text-gray-700 font-medium">Number of Rounds</label>
                <input id="round-count" required min="1" max="40" .value="${this.roundCount}" ?disabled="${this.gameType === 'templated'}" />

                <label for="round-duration-sec" class="mt-2 text-gray-700 font-medium">Round Duration Seconds</label>
                <input id="round-duration-sec" required min="1" max="40" .value="${this.roundDurationSec}" />

                <label for="round-result-duration-sec" class="mt-2 text-gray-700 font-medium">Round Result Duration Seconds</label>
                <input id="round-result-duration-sec" required min="1" max="40" .value="${this.roundResultDurationSec}" />

                <fieldset class="flex gap-3 px-3 border border-gray-400 pb-3 mt-2">
                  <legend class="px-1.5">Public Status</legend>
                  <label class="font-bold">Public
                    <input type="radio" name="game-public-status-enum" value="PUBLIC" class="ml-1" ?checked="${this.gamePublicStatusEnum === 'PUBLIC'}">
                  </label>
                  ${this.isBob ? html`
                    <label class="font-bold">Google
                      <input type="radio" name="game-public-status-enum" value="GOOGLE" class="ml-1" ?checked="${this.gamePublicStatusEnum === 'GOOGLE'}">
                    </label>` : nothing}
                  <label class="font-bold">Private
                    <input type="radio" name="game-public-status-enum" value="PRIVATE" class="ml-1" ?checked="${this.gamePublicStatusEnum === 'PRIVATE'}">
                  </label>
                </fieldset>
              </form>`}
        </div>
        <div slot="footer" class="flex justify-between">
          <lit-button label="CANCEL" bgColorClass="bg-gray-400" class="w-20" @click="${this.close}"></lit-button>
          <lit-button label="EDIT" bgColorClass="bg-iris-400" class="w-20" @click="${this.submit}"></lit-button>
        </div>
      </lit-dialog>`;
  }
}
