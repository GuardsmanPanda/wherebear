import { LitElement, css } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * Base class for buttons that provides basic functionality for handling button states such as
 * size and mouse interaction events.
 */
export class ButtonBase extends LitElement {
  static properties = {
    /** The icon position, 'left' or 'right'. */
    iconPosition: { type: String },

    /** Whether the button is disabled and can't be clicked. */
    isDisabled: { type: Boolean },

    /** Whether the button is full rounded.  */
    isPill: { type: Boolean },

    /** Determines if the button is selectable. */
    isSelectable: { type: Boolean },

    /** Keeps track of whether the button is currently selected. */
    isSelected: { type: Boolean },

    /** Sets the size of the button, corresponding to the available size keys: 'sm', 'md', 'lg', 'xl'. */
    size: { type: String },

    /** Tracks whether the mouse has left the button after it was clicked. Used for not applying hover css before the mouse has left the button. */
    hasMouseLeftAfterClicked: { type: Boolean, state: true },
  };

  static styles = [css`${TailwindStyles}`, css`
    .inner-shadow {
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6), inset 0 -2px 0 rgba(0, 0, 0, 0.6);
    }
    .inner-shadow:active {
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), inset 0 -1px 0 rgba(0, 0, 0, 0.6);
    }

    .inner-shadow-selected {
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6), inset 0 -2px 0 rgba(0, 0, 0, 0.6);
    }
    .inner-shadow-selected:active {
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6), inset 0 -1px 0 rgba(0, 0, 0, 0.6);
    }
  `];

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
    this.isSelectable = false;
    this.isSelected = false;
    this.isPill = false;
    this.size = 'sm';
    this.hasMouseLeftAfterClicked = true;
  }

  /** The height class corresponding to the current button size. */
  get heightClass() {
    return this.heightClasses[this.size];
  }

  /** Invoked whenever the element is updated. */
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

  /** Event handler for when the mouse enters the button. */
  onMouseEnter() {
    if (this.isSelected) {
      this.hasMouseLeftAfterClicked = true;
    }
  }

  /** Event handler for when the mouse leaves the button. */
  onMouseLeave() {
    this.hasMouseLeftAfterClicked = true;
  }

  /** Event handler for when the button is clicked. */
  onClick() {
    if (this.isSelectable) {
      this.isSelected = !this.isSelected;
    }

    this.hasMouseLeftAfterClicked = false;

    this.dispatchEvent(new CustomEvent('clicked', {
      detail: { isSelected: this.isSelected },
      bubbles: true,
      composed: true
    }));
  }
}
