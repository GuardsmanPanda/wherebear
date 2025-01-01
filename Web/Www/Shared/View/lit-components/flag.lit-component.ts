import { LitElement, css, html } from 'lit';
import { customElement, property } from 'lit/decorators.js';
import { classMap } from 'lit/directives/class-map.js';

// @ts-ignore
import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/**
 * Displays a flag image based on the provided CCA2 code.
 * 
 * The `maxHeightClass` property is a workaround to prevent the Nepal flag 
 * from overflowing its container until a better solution is found.
 * 
 * IMPORTANT: For compatibility with Firefox, either width or height must 
 * be set when using this component. This may be due to how Firefox handles 
 * the Shadow DOM. I have not checked on other browsers yet. 
 * 
 * Example:
 * <lit-flag cca2="FR" filePath="/static/flag/svg/FR.svg" description="France" class="w-full"></lit-flag>
 */
@customElement('lit-flag')
class Flag extends LitElement {
  @property({ type: String }) cca2!: string;
  @property({ type: String }) description!: string;
  @property({ type: String }) filePath!: string;
  @property({ type: String }) maxHeightClass?: string;
  @property({ type: String }) roundedClass?: string;

  static styles = css`${TailwindStyles}`;

  get imgClasses() {
    let classes: { [key: string]: boolean } = {
			'border': this.cca2 !== 'NP'
		};
			
		if (this.maxHeightClass) {
			classes[this.maxHeightClass] = true;
		}
		if (this.roundedClass) {
			classes[this.roundedClass] = true;
		}
		
		return classes;
  }

  protected render() {
    return html`
      <div class="flex items-center justify-center w-full h-full">
        <img 
          src="${this.filePath}"
          class="w-full h-full border-gray-700 ${classMap(this.imgClasses)}"
          alt="${this.description}"
          ${tooltip(this.description)} />
      </div>
    `;
  }
}
