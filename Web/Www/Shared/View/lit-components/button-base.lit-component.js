import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

export class ButtonBase extends LitElement {
  heightClasses = {
    'sm': 'h-[32px]',
    'md': 'h-[40px]',
    'lg': 'h-[48px]',
    'xl': 'h-[56px]'
  };

  static properties = {
    size: { type: String },
    state: { type: String },
    isActive: { type: Boolean, state: true },
    isSelected: { type: Boolean, state: false }
  };

  static styles = [css`${TailwindStyles}`];

  constructor() {
    super();
    this.size = 'sm';
    this.isActive = false;
    this.isSelected = false;
  }

  updated() {
    this.assertSize();
  }

  /**
   * Ensures that the current size is valid by checking if it exists in the defined heightClasses object.
   * Throws an error if the size is invalid.
   */
  assertSize() {
    if (!this.heightClasses.hasOwnProperty(this.size)) {
      throw new Error(`Invalid size: '${this.size}'. Allowed values are: ${Object.keys(this.heightClasses).map(n => `'${n}'`).join(', ')}.`);
    }
  }

  getHeightClass() {
    return this.heightClasses[this.size];
  }

  onClick() {
    this.isSelected = !this.isSelected;

    this.dispatchEvent(new CustomEvent('clicked', {
      detail: { isSelected: this.isSelected },
      bubbles: true,
      composed: true
    }));
  }

  onMouseDown() {
    this.isActive = true;
  }

  onMouseUp() {
    this.isActive = false;
  }

  render() {
    return html`
      base button
    `;
  }
}
