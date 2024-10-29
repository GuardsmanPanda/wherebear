import { LitElement, css, html, nothing } from 'lit';

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
        class="flex flex-col w-full h-full relative rounded pt-[0px] pb-[3px] border border-gray-700 bg-iris-400"
        style="box-shadow: inset 0 2px 1px rgba(255, 255, 255, 0.25), inset 0 -3px 1px rgba(0, 0, 0, 0.25);"
      >
        <div class="h-1 absolute -top-[5px] left-1/2 transform -translate-x-1/2 rounded-t-lg bg-iris-500 border-t border-iris-900" style="width: calc(100% - 16px);"></div>

        ${this.label
        ? html`
            <div 
              class="flex justify-center items-center absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 min-w-20 z-10 rounded px-2 border border-gray-700 bg-gray-600"
              style="box-shadow: inset 0 2px 1px rgba(255, 255, 255, 0.25), inset 0 -3px 1px rgba(0, 0, 0, 0.25);"
            >
              <span class="font-heading text-sm text-white font-bold text-stroke text-stroke-gray-700">${this.label}</span>
            </div>`
        : nothing
      }
        <slot></slot>
      </div>
    `;
  }
}

customElements.define('lit-panel', Panel);
