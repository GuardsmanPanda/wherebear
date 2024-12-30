import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';


class SelectMapMarkerDialog extends LitElement {
  static properties = {
    errorMsg: { type: String, state: true },
    mapMarkers: { type: Array, state: true },
  }

  static styles = css`${TailwindStyles}`;

  constructor() {
    super();
    this.mapMarkers = [];
  }

  get litDialogElement() {
    return this.renderRoot.querySelector('lit-dialog');
  }

  async fetchUserMapMarkers() {
    const response = await fetch(`/web-api/user/map-markers`);
    if (!response.ok) { throw new Error(response.status); }
    const data = await response.json();
    return data;
  }

  async open() {
    try {
      this.mapMarkers = await this.fetchUserMapMarkers();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
    this.litDialogElement.open();
  }

  groupMapMarkersByGrouping() {
    return this.mapMarkers.reduce((groups, mapMarker) => {
      const group = mapMarker.grouping || 'Miscellaneous';
      if (!groups[group]) {
        groups[group] = [];
      }
      groups[group].push(mapMarker);
      return groups;
    }, {});
  }

  async selectMapMarker(mapMarkerEnum) {
    try {
      await fetch(`/web-api/user`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          map_marker_enum: mapMarkerEnum
        })
      });

      this.litDialogElement.close();
    } catch (err) {
      this.errorMsg = `Something went wrong`;
    }
  }

  render() {
    return html`
     <lit-dialog
        label="Select Map Marker"
        modal
        x-on:closed="closeSelectMapMarkerDialog()">
        <div slot="content" class="mx-2">
        ${this.errorMsg
        ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
        : Object.entries(this.groupMapMarkersByGrouping()).map(([group, mapMarkers]) => html`
          <div class="flex flex-col gap-2 group">
            <h3 class="font-heading font-semibold text-xl text-gray-700 border-b-2 border-gray-700">${group}</h3>
            <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-9 gap-2 mb-4">
              ${mapMarkers.map((mapMarker) => html`
                <div class="flex justify-center max-w-14 max-h-14 ${classMap({ 'items-center': mapMarker.map_anchor === 'center', 'items-end': mapMarker.map_anchor === 'bottom' })}">
                  <img src="${mapMarker.file_path}" 
                    draggable="false"
                    class="max-w-full max-h-full object-contain drop-shadow hover:scale-110 transition-transform duration-75 cursor-pointer"
                    @click="${() => this.selectMapMarker(mapMarker.enum)}"
                  />
                </div>
              `)}
            </div>
          </div>
        `)}
        </div>
      </lit-dialog>`;
  }
}

customElements.define('lit-select-map-marker-dialog', SelectMapMarkerDialog);
