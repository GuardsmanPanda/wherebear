import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class SelectMapStyleDialog extends LitElement {
  static properties = {
    selectedMapStyleEnum: { type: String },
    userLevel: { type: Number },
    mapStyles: { type: Array, state: true },
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.mapStyles = [];
  }

  get litDialogElement() {
    return this.renderRoot.querySelector('lit-dialog');
  }

  async fetchUserMapStyles() {
    const response = await fetch(`/web-api/user/map-styles`);
    if (!response.ok) { throw new Error(response.status); }
    const data = await response.json();
    return data;
  }

  getFormattedMapStyleFullUri(fullUri) {
    return fullUri.replace('{x}', 1614).replace('{y}', 1016).replace('{z}', 11);
  }

  getMapClasses(mapStyle) {
    return {
      'border-pistachio-400': this.isMapStyleUnlocked(mapStyle) && this.isMapStyleSelected(mapStyle),
      'border-gray-50': this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
      'hover:border-gray-0': this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
      'border-gray-700': !this.isMapStyleUnlocked(mapStyle),
      'cursor-pointer': this.isMapStyleUnlocked(mapStyle),
      'bg-black': mapStyle.enum !== 'OSM',
      'bg-gray-600': mapStyle.enum === 'OSM'
    }
  }

  getImgClasses(mapStyle) {
    return {
      'opacity-30': !this.isMapStyleUnlocked(mapStyle),
      'opacity-90': this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
      'group-hover:opacity-100': this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
    }
  }

  getMapStyleNameBackroundClasses(mapStyle) {
    return {
      'bg-pistachio-400': this.isMapStyleSelected(mapStyle),
      'bg-gray-50': !this.isMapStyleSelected(mapStyle),
      'group-hover:bg-gray-0': !this.isMapStyleSelected(mapStyle),
    }
  }

  isMapStyleSelected(mapStyle) {
    return mapStyle.enum === this.selectedMapStyleEnum;
  }

  isMapStyleUnlocked(mapStyle) {
    return mapStyle.user_level_enum <= this.userLevel;
  }

  async open() {
    try {
      this.mapStyles = await this.fetchUserMapStyles();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
    this.litDialogElement.open();
  }


  async selectMapStyle(mapStyleEnum) {
    try {
      await fetch(`/web-api/user`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          map_style_enum: mapStyleEnum
        })
      });

      setTimeout(() => {
        this.litDialogElement.close();
      }, 500);
    } catch (err) {
      console.error(err);
    }
  }

  render() {
    return html`
      <lit-dialog
        label="Select Map Style"
        x-on:closed="closeSelectMapStyleDialog()">
        <div slot="content" class="select-none">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
          ${this.mapStyles.map(mapStyle => html`
            <div class="rounded border ${this.isMapStyleSelected(mapStyle) ? 'border-green-700' : ''}">
            <div 
              class="flex flex-col w-full relative rounded border-4 group transition-all duration-200 ${classMap(this.getMapClasses(mapStyle))}"
              @click="${() => { if (this.isMapStyleUnlocked(mapStyle)) this.selectMapStyle(mapStyle.enum) }}"
            >
              ${this.isMapStyleUnlocked(mapStyle)
        ? html`
              <div class="absolute top-0 left-0 z-10 h-6 rounded-br bg-gray-0 pl-1 pr-2 transition-all duration-200 ${classMap(this.getMapStyleNameBackroundClasses(mapStyle))}">
                <span class="relative bottom-0.5 font-heading font-semibold text-lg text-gray-800">${mapStyle.name}</span>
              </div>
              
              <div class="${this.isMapStyleSelected(mapStyle) ? '' : 'hidden group-hover:block'}">
                <div class="absolute top-1 right-1 z-10 w-5 h-1.5 ${this.isMapStyleSelected(mapStyle) ? 'bg-pistachio-400' : 'bg-gray-0'}"></div>
                <div class="absolute top-1 right-1 z-10 w-1.5 h-5 ${this.isMapStyleSelected(mapStyle) ? 'bg-pistachio-400' : 'bg-gray-0'}"></div>
                <div class="absolute bottom-1 left-1 z-10 w-5 h-1.5 ${this.isMapStyleSelected(mapStyle) ? 'bg-pistachio-400' : 'bg-gray-0'}"></div>
                <div class="absolute bottom-1 left-1 z-10 w-1.5 h-5 ${this.isMapStyleSelected(mapStyle) ? 'bg-pistachio-400' : 'bg-gray-0'}"></div>
                <div class="absolute bottom-1 right-1 z-10 w-5 h-1.5 ${this.isMapStyleSelected(mapStyle) ? 'bg-pistachio-400' : 'bg-gray-0'}"></div>
                <div class="absolute bottom-1 right-1 z-10 w-1.5 h-5 ${this.isMapStyleSelected(mapStyle) ? 'bg-pistachio-400' : 'bg-gray-0'}"></div>
              </div>`


        : html`
              <div class="flex flex-col justify-center items-center gap-2 absolute inset-0 z-50">
                <span class="font-heading font-semibold text-xl text-gray-100">${mapStyle.name}</span>
                <img src="/static/img/icon/lock.svg" class="w-6" />
                <span class="font-heading font-semibold text-xl text-gray-100">Level ${mapStyle.user_level_enum}</span>
              </div>`}
              
              <img src="${this.getFormattedMapStyleFullUri(mapStyle.full_uri)}" class="h-32 w-96 object-none transition-opacity duration-200 ${classMap(this.getImgClasses(mapStyle))}" draggable="false" />
            </div>            
          `)}
          </div>
          </div>
        </div>
      </lit-dialog>`;
  }
}

customElements.define('lit-select-map-style-dialog', SelectMapStyleDialog);