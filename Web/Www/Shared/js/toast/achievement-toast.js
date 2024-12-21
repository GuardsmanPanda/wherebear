import { ToastBase } from './toast-base';
import { ToastTemplateBuilder } from './toast-template-builder';

class AchievementToast extends ToastBase {
  #name;
  #conditions;
  #percentage;

  /**
   * Creates an instance of AchievementToast.
   * @param {string} name - The name of the achievement.
   * @param {string} conditions - The conditions to unlock the achievement.
   * @param {number} percentage - The percentage of players who have unlocked the achievement.
   */
  constructor(name, conditions, percentage) {
    super();
    this.#name = name;
    this.#conditions = conditions;
    this.#percentage = percentage;
  }

  buildTemplate() {
    return new ToastTemplateBuilder()
      .withTitle('ACHIEVEMENT UNLOCKED')
      .withCloseButton(() => this._handleEvent('cancel'))
      .withIconPath('/static/img/icon/medal-blue.svg')
      .withContent(`
        <div class="flex flex-col">
          <span class="leading-none font-heading font-bold text-lg text-iris-600">${this.#name}</span>
          <span class="font-body text-base font-regular text-gray-800">${this.#conditions}</span>
          ${this.#percentage ?
          `<span class="mt-2 text-sm text-gray-600">${this.#percentage}% of players have this achievement.</span>`
          : ''}
        </div>
      `)
      .build();
  }
}
window.AchievementToast = AchievementToast;