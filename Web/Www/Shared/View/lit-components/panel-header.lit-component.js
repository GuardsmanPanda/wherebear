import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * A flexible rectangular area for displaying dynamic injected content.
 */
class PanelHeader extends LitElement {
  static properties = {
    label: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  render() {
    return html`
      <div class="flex w-full h-8 justify-between items-center relative px-2 border-t border-b-4 border-gray-700 bg-gray-600">
        <div class="w-32 h-full absolute right-0 bg-gray-700" style="clip-path: polygon(6px 0, 100% 0, 100% 100%, 0 100%)"></div>
        <div class="flex items-center gap-2">
          <span class="font-heading font-semibold text-base text-gray-0">${this.label}</span>
          <slot name="left"></slot>
        </div>
        <div class="z-10 ">
          <slot name="right"></slot>
        </div>
      </div>
    `;
  }
}

customElements.define('lit-panel-header', PanelHeader);
