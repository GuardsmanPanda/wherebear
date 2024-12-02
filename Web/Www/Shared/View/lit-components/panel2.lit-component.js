import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * A flexible rectangular area for displaying dynamic injected content.
 */
class Panel extends LitElement {
  static properties = {
    label: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  render() {
    return html`
      <div 
        class="flex flex-col w-full h-full relative rounded"
        style="box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.15);"
      >
        <lit-panel-header2 label="${this.label}">
          <slot name="header-left" slot="left"></slot>
          <slot name="header-right" slot="right"></slot>
        </lit-panel-header2>
        <div
          class="rounded-b border border-t-0 border-gray-700 bg-iris-50"
          style="box-shadow: inset 0 2px 1px rgba(0, 0, 0, 0.25)">
          <slot></slot>
        </div>
      </div>
    `;
  }
}

customElements.define('lit-panel2', Panel);