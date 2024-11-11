import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { styleMap } from 'lit/directives/style-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/**
 * A custom progress bar component that visually represents a progress value
 * as a filled section of a bar.
 */
class ProgressBar extends LitElement {
  static properties = {
    /** 
     * The background color of the inner section of the progress bar.
     * Overrides `innerBgColorClass` if provided.
     * @example 
     * 'green'
     * '#ffffff'
     */
    innerBgColor: { type: String },

    /**
     * A CSS class used to style the background of the inner section of the progress bar.
     * @example 'bg-green-300'
     */
    innerBgColorClass: { type: String },

    /** Whether the CSS transitions are enabled. */
    isTransitionEnabled: { type: Boolean, state: true },

    /** The current progress of the progress bar as a percentage (0 to 100). */
    percentage: { type: Number },

    /** Whether the percentage is displayed as tooltip. */
    showPercentageTooltip: { type: Boolean },

    /** 
     * Determines whether the sides of the progress bar are flat or rounded. 
     * Default is `false` (rounded sides).
     */
    sideFlated: { type: Boolean },

    /** 
     * Determines whether the sides of the progress bar should have a border.
     * Default is `false` (bordered sides).
     */
    sideUnbordered: { type: Boolean },

    /** 
     * The duration of the width transition in milliseconds. 
     * Controls how long it takes for the progress bar to adjust its width.
     */
    widthTransitionDurationMs: { type: Number },

    /**
     * Controls the visibility of the inner bar. 
     * Useful for completely hiding the inner bar when the percentage is 0%,
     * preventing the border from being displayed.
     * 
     * Note: Removing the 'border' class when the percentage is 0% doesn't help
     * due to the transition (the border is removed too soon).
     */
    showInnerBar: { type: Boolean, state: true }
  };

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.innerBgColor = null;
    this.innerBgColorClass = null;
    this.isTransitionEnabled = false;
    this.sideFlated = false;
    this.percentage = null;
    this.showInnerBar = true;
    this.showPercentageTooltip = false;
    this.sideUnbordered = false;
    this.widthTransitionDurationMs = 1000;
  }


  firstUpdated() {
    setTimeout(() => {
      this.isTransitionEnabled = true;
      this.requestUpdate();
    }, 150);
  }

  updated(changedProperties) {
    // If the pass drops to 0%, hide the inner bar after a delay equals to 
    // the transition duration.
    if (this.percentage <= 0) {
      const oldPercentage = (changedProperties.get('percentage'));
      if (oldPercentage > 0) {
        setTimeout(() => {
          this.showInnerBar = false;
        }, this.widthTransitionDurationMs);
      }
    } else {
      this.showInnerBar = true;
    }
  }

  get outterBarClasses() {
    return {
      'rounded': !this.sideFlated,
      'border-x': !this.sideUnbordered
    }
  }

  get innerBarClasses() {
    return {
      'invisible': !this.showInnerBar,
      'border-r': this.percentage < 100,
      'rounded-r': this.percentage < 100,
      [this.innerBgColorClass]: !this.innerBgColor
    }
  }

  get innerBarStyles() {
    return {
      'backgroundColor': this.innerBgColor,
      'boxShadow': 'inset 0 -4px 1px rgba(0, 0, 0, 0.25)',
      'width': `${this.percentage}%`,
      'border-radius': this.sideFlated ? 'none' : '0.25rem',
      'transition': this.isTransitionEnabled ? `width ${this.widthTransitionDurationMs}ms linear, background-color 1000ms linear` : 'none'
    }
  }

  get roundedPercentage() {
    return this.percentage < 1 ? 1 : Math.floor(this.percentage);
  }

  render() {
    return html`
      <div 
        class="flex w-full h-4 border-y bg-gray-500 border-gray-700 ${classMap(this.outterBarClasses)}"
        style="box-shadow: inset 0 4px 1px rgb(0 0 0 / 0.3);"
        ${this.showPercentageTooltip ? tooltip(`${this.roundedPercentage}%`) : ''}>
        <div
          class="border-l-0 border-gray-700 ${classMap(this.innerBarClasses)}"
          style="${styleMap(this.innerBarStyles)}">
        </div>
      </div>
    `;
  }
}

customElements.define('lit-progress-bar', ProgressBar);
