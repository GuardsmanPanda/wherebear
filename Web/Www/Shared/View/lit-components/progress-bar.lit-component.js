import { html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { styleMap } from 'lit/directives/style-map.js';

import { Base } from './base.lit-component';

class ProgressBar extends Base {
  static properties = {

    /** 
     * The background color of the inner section of the progress bar.
     * If provided, it will override `innerBgColorCls`.
     * @example 
     * 'green'
     * '#ffffff'
    */
    innerBgColor: { type: String },

    /**
     * A CSS class used to style the background of the inner section of the progress bar.
    */
    innerBgColorCls: { type: String },

    /** Whether the CSS transitions are enabled. */
    isTransitionEnabled: { type: Boolean, state: true },

    /** 
     * The current progress of the progress bar as a percentage from 0 to 100.
    */
    percentage: { type: Number },

    /** 
     * Determines whether the sides of the progress bar are flat or rounded. 
     * Default: `false` (rounded sides).
     */
    sideFlated: { type: Boolean },

    /** 
     * Determines whether the sides of the progress bar should have a border.
     * Default: `false` (bordered sides).
     */
    sideUnbordered: { type: Boolean },

    /** 
     * The duration of the width transition in milliseconds. 
     * Controls how long the progress bar takes to adjust its width.
     */
    widthTransitionDurationMs: { type: Number },

    /**
     * Controls the visibility of the inner bar. This can be used to completely hide
     * the inner bar when the percentage is 0%, preventing the border from being displayed.
     * 
     * Removing the 'border' class when the percentage is 0% doesn't help because of the 
     * transition (the border is removed too soon).
     */
    showInnerBar: { type: Boolean, state: true }
  };


  constructor() {
    super();
    this.innerBgColor = '#fff';
    this.innerBgColorCls = null;
    this.isTransitionEnabled = false;
    this.sideFlated = false;
    this.percentage = null;
    this.showInnerBar = true;
    this.sideUnbordered = false;
    this.widthTransitionDurationMs = 1000;
  }

  firstUpdated() {
    setTimeout(() => {
      this.isTransitionEnabled = true;
      this.requestUpdate();
    }, 100);
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

  get outterBarCls() {
    return {
      'rounded': !this.sideFlated,
      'border-x': !this.sideUnbordered
    }
  }

  get innerBarCls() {
    return {
      'invisible': !this.showInnerBar,
      'border-r': this.percentage < 100,
      'rounded-r': this.percentage < 100,
      [this.innerBgColorCls]: !this.innerBgColor
    }
  }

  get innerBarStyles() {
    return {
      'backgroundColor': this.innerBgColor,
      'boxShadow': 'inset 0 -4px 1px rgba(0, 0, 0, 0.3)',
      'width': `${this.percentage}%`,
      'border-radius': this.sideFlated ? 'none' : '0.25rem',
      'transition': this.isTransitionEnabled ? `width ${this.widthTransitionDurationMs}ms linear, background-color 1000ms linear` : 'none'
    }
  }

  render() {
    return html`
      <div 
        class="flex w-full h-4 border-y bg-gray-200 border-gray-900 ${classMap(this.outterBarCls)}"
        style="box-shadow: inset 0 4px 1px rgb(0 0 0 / 0.3);">
        <div 
          class="border-l-0 border-gray-800 ${classMap(this.innerBarCls)}"
          style="${styleMap(this.innerBarStyles)}">
        </div>
      </div>
    `;
  }
}

customElements.define('lit-progress-bar', ProgressBar);
