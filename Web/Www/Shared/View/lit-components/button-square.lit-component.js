import { css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { ButtonBase } from './button-base.lit-component';

class ButtonSquare extends ButtonBase {

  static properties = {
    ...ButtonBase.properties,
    label: { type: String },
    imgPath: { type: String }
  };

  static styles = [...ButtonBase.styles, css`
    .shadow-default {
      box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6), inset 0 -1px 1px rgba(0, 0, 0, 0.6);
    }

    .shadow-selected {
      box-shadow: inset 0 2px 1px rgba(255, 255, 255, 0.6), inset 0 -2px 1px rgba(0, 0, 0, 0.6);
    }
  `];

  widthClasses = {
    'xl': 'w-[56px]'
  };

  heightClasses = {
    'xl': 'h-[56px]'
  };

  constructor() {
    super();
    this.size = 'xl';
  }

  getWidthClass() {
    return this.widthClasses[this.size];
  }

  getHeightClass() {
    return this.heightClasses[this.size];
  }

  getActiveHeightClass() {
    return this.activeHeightClasses[this.size] || this.heightClasses['sm'];
  }

  getClasses() {
    return {
      [this.getWidthClass()]: true,
      [this.getHeightClass()]: true,
      // 'border-t': this.isSelected,
      // 'border-b': this.isSelected,
      'bg-gray-800': this.isSelected,
      // 'shadow-default': !this.isSelected,
      // 'shadow-selected': this.isSelected,
    }
  }

  render() {
    return html`
      <button
        class="flex flex-col justify-start items-center relative rounded-md bg-gray-700 active:bg-gray-800 border-[2px] border-b-[3px] active:border-t-2 active:border-b-2 border-gray-900 active:shadow-selected ${classMap(this.getClasses())}"
        @click="${this.onClick}" @mousedown="${this.onMouseDown}" @mouseup="${this.onMouseUp}"
      >
        <img src="${this.imgPath}" draggable="false" class="absolute -top-[8px] h-[40px]" />
        <span class="absolute top-[34px] text-xs text-white text-stroke-2 font-medium">${this.label}</span>
      </button>
    `;
  }
}

customElements.define('lit-button-square', ButtonSquare);
