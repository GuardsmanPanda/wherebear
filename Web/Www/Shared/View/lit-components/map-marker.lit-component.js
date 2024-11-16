import { css, html, LitElement, nothing } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/** 
 * A custom map marker used to indicate the position of a player on a map.
 * Displays player rank, name, and distance, with custom styling for rank-based colors.
 */
class MapMarker extends LitElement {
  static properties = {
    /** Distance of the player from the reference point, in meters. */
    distanceMeters: { type: Number },
    /** File path to the image used for the map marker icon. */
    iconFilePath: { type: String },
    playerName: { type: String },
    rank: { type: Number },
  };

  static styles = [css`${TailwindStyles}`];

  constructor() {
    super();
    this.distanceMeters = null;
    this.iconFilePath = null;
    this.playerName = null;
    this.rank = null;
  }

  get nameSignClasses() {
    return {
      'bg-rank-first-default': this.rank === 1,
      'bg-rank-second-default': this.rank === 2,
      'bg-rank-third-default': this.rank === 3,
      'bg-gray-50': this.rank > 3
    }
  }
  /**
   * Returns the distance value and appropriate unit (meters or kilometers).
   * Converts values greater than 1000 meters into kilometers.
   */
  getDistanceWithUnit() {
    const meters = this.distanceMeters;

    if (meters < 1000) {
      return {
        value: Math.round(meters),
        unit: 'm',
      };
    } else {
      return {
        value: Math.round(meters / 1000),
        unit: 'km',
      };
    }
  }

  render() {
    let distance = null;
    if (this.distanceMeters) {
      distance = this.getDistanceWithUnit();
    }

    return html`
      <div class="flex flex-col items-center font-body">
        <div class="flex flex-col items-center">
          <div class="flex items-center relative top-[3px] z-20">
            ${this.rank ? html`
              <div class="flex justify-center items-center w-[24px] h-[24px] relative top-[1px] left-[2px] z-20 p-1 rounded border border-gray-700 bg-gray-50">
                <div class="absolute left-0 w-full h-full bg-gray-600" style="clip-path: polygon(0 0, 40% 0, 0 40%)"></div>
                <span class="text-sm text-gray-800 font-medium">${this.rank}</span>
              </div>
            ` : nothing}
            
            ${distance ? html`
              <div
                class="flex justify-center items-center h-5 relative top-[1px] px-1 pt-0.5 rounded-r bg-gray-600 border border-gray-700">
                <span class="relative bottom-[1px] text-xs font-medium text-gray-0">${distance.value}${distance.unit}</span>
              </div>
            ` : nothing}
          </div>
          ${this.playerName ? html`
            <div class="flex justify-center items-center min-w-28 max-w-36 h-6 px-2 z-10 relative rounded border border-b-2 border-gray-700 cursor-auto ${classMap(this.nameSignClasses)}">
              <div class="absolute left-0 w-2 h-full bg-black opacity-10" style="clip-path: polygon(0 0, 100% 0, 50% 100%, 0 100%)"></div>
              <div class="absolute right-0 w-2 h-full bg-black opacity-10" style="clip-path: polygon(50% 0, 100% 0, 100% 100%, 0 100%)"></div>
              <div class="w-3 h-10 absolute top-[22px] bg-gray-100 border border-gray-700" style="box-shadow: inset 1px 0 2px rgba(0, 0, 0, 0.3);"></div>
              <span class="relative top-[1px] font-heading font-medium text-base text-gray-800 truncate" ${tooltip(this.playerName)}>${this.playerName}</span>
            </div>
          ` : nothing}
        </div>
        <div class="flex justify-center items-end w-16 h-12 relative top-[4px] overflow-visible">
          <img src="${this.iconFilePath}" class="z-10" style="height: auto; max-height: 48px;" />
        </div>
      </div>   
    `;
  }
}

customElements.define('lit-map-marker', MapMarker);
