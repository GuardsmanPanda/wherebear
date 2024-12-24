import { css, html, LitElement, nothing } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Represents a player's progress on an achievement.
 * This component displays the title, description, and completion status of an achievement.
 */
class AchievementProgress extends LitElement {
  static properties = {
    title: { type: String },
    description: { type: String },
    isCompleted: { type: Boolean },
    completedDate: { type: String },
    hasSteps: { type: Boolean },
    currentStep: { type: Number },
    totalSteps: { type: Number },
  };

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.isCompleted = false;
    this.completedDate = null;
    this.hasSteps = false;
    this.currentStep = null;
    this.totalSteps = null;
  }

  get convertedDate() {
    return new Date(this.completedDate).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  }

  render() {
    return html`
      <div class="flex flex-col min-h-24 relative border border-gray-200 rounded p-2 bg-gray-0">
        <span class="mr-8 font-medium text-base text-iris-600">${this.title}</span>
        <span class="mr-8 text-sm text-gray-700">${this.description}</span>
        
        ${!this.isCompleted && this.hasSteps ? html`
          <span class="w-full self-end text-right text-sm text-gray-600">${this.currentStep}/${this.totalSteps}</span>
          <lit-progress-bar innerBgColorClass="bg-pistachio-400" percentage="20"></lit-progress-bar>
        ` : nothing}

        ${this.isCompleted ? html`
          <img src="/static/img/icon/ribbon-medal-green.svg" class="absolute -top-1.5 right-1 h-12" draggable="false" />
          <span class="mt-auto w-full text-right text-xs text-gray-500">Completed on ${this.convertedDate}</span>
        ` : nothing}
      </div>
    `;
  }
}

customElements.define('lit-achievement-progress', AchievementProgress);