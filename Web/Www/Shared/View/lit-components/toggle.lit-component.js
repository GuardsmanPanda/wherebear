import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Displays a toggle button.
 */
class Toggle extends LitElement {
  static properties = {
    isSelected: { type: Boolean },
    leftLabel: { type: String },
    rightLabel: { type: String },
    size: { type: String },
    /** Internal state property to track rendering. */
    hasRendered: { type: Boolean, state: true },
  }

  static styles = css`${TailwindStyles}`;

  /** Defines a mapping between size labels (e.g., 'sm', 'md') and their corresponding height classes in Tailwind CSS syntax. */
  heightClasses = {
    'xs': 'h-6',
    'sm': 'h-8',
    'md': 'h-10',
    'lg': 'h-12',
    'xl': 'h-14'
  };

  constructor() {
    super();
    this.hasRendered = false;

  }

  firstUpdated() {
    setTimeout(() => {
      this.hasRendered = true;
    }, 500);
  }

  /** The height class corresponding to the current toggle size. */
  get heightClass() {
    if (!this.size) return this.heightClasses['sm'];
    return this.heightClasses[this.size];
  }

  get classes() {
    return {
      [this.heightClass]: true
    }
  }

  get labelClasses() {
    return {
      'text-base': true
    }
  }

  get selectedOptionClasses() {
    return {
      [this.heightClass]: true,
      '-left-px': !this.isSelected,
      'left-[calc(50%+1px)]': this.isSelected,
      'transform': this.hasRendered,
      'duration-300': this.hasRendered
    }
  }

  toggleOption() {
    this.isSelected = !this.isSelected;

    this.dispatchEvent(new CustomEvent('clicked', {
      detail: { isSelected: this.isSelected },
      bubbles: true,
      composed: true
    }));
  }

  render() {
    return html`
      <div class="flex w-full relative overflow-hidden cursor-pointer rounded-md border border-gray-700 bg-iris-200 text-white ${classMap(this.classes)}" @click="${this.toggleOption}"
        style="box-shadow: inset 0 -3px 1px 0 rgba(0, 0, 0, 0.25)">
        <div 
          class="w-1/2 absolute -top-px -left-px z-10 rounded border border-gray-700 bg-iris-500 ${classMap(this.selectedOptionClasses)}"
          style="box-shadow: -2px 0 1px 0 rgba(0, 0, 0, 0.4), 2px 0 1px 0 rgba(0, 0, 0, 0.4), inset 0 -3px 1px 0 rgba(0, 0, 0, 0.40), inset 0 2px 1px 0 rgba(255, 255, 255, 0.25)"></div>
        <div class="flex justify-center items-center w-full z-10">
          <span class="font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}">${this.leftLabel}</span>
        </div>
        <div class="flex justify-center items-center w-full z-10">
          <span class="font-heading font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 ${classMap(this.labelClasses)}">${this.rightLabel}</span>
        </div>
      </div>
    `;
  }
}

customElements.define('lit-toggle', Toggle);
