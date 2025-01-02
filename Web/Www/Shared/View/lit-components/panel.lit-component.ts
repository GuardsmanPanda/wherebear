import { LitElement, css, html } from 'lit';
import { customElement, property } from 'lit/decorators.js';

// @ts-ignore
import { AppStyles } from '../../../../../public/static/dist/lit-app-css';
// @ts-ignore
import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * A flexible container component that provides a structured layout with a header and content area.
 * This component can be used to encapsulate and display content within a visually distinct panel.
 */
@customElement('lit-panel')
class Panel extends LitElement {
  @property({ type: String }) label?: string

  static styles = css`${TailwindStyles} ${AppStyles}`;

  protected render() {
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
          class="rounded-b border border-t-0 border-gray-700 bg-iris-50 overflow-y-auto"
          style="box-shadow: inset 0 2px 1px rgba(0, 0, 0, 0.25)">
          <slot></slot>
        </div>
      </div>
    `;
  }
}