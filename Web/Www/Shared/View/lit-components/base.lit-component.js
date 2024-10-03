import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

export class Base extends LitElement {

  static styles = [css`${TailwindStyles}`];

  constructor() {
    super();
  }

  render() {
    return html`
      base
    `;
  }
}

