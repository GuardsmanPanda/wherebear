import { LitElement, css, html, nothing } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

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

  /**
   * Get the color associated with the player's rank.
   * Gold for 1st, Silver for 2nd, Bronze for 3rd, and white for others.
   */
  getRankColor() {
    switch (this.rank) {
      case 1:
        return '#FDE047'; // Gold
      case 2:
        return '#D4E1EB'; // Silver
      case 3:
        return '#F6B981'; // Bronze
      default:
        return '#ffffff';
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
              <div 
                class="flex justify-center items-center w-[22px] h-[22px] relative top-[1px] left-[2px] z-20 p-1 rounded border border-gray-700" 
                style="background-color: ${this.getRankColor()};">
                <span class="text-xs text-gray-900 font-medium">${this.rank}</span>
              </div>
            ` : nothing}
            
            ${distance ? html`
              <div
                class="flex items-center h-5 relative top-[1px] px-1 pt-0.5 rounded-r bg-gray-50 border border-gray-700">
                <span class="relative bottom-[1px] text-xs text-gray-900">${distance.value}${distance.unit}</span>
              </div>
            ` : nothing}
          </div>
          ${this.playerName ? html`
            <div class="flex justify-center items-center min-w-20 h-5 pl-2 pr-1 z-10 relative rounded bg-iris-200 border border-b-2 border-iris-600">
              <div class="w-3 h-10 absolute top-[18px] bg-gray-100 border border-gray-700" style="box-shadow: inset 1px 0 2px rgba(0, 0, 0, 0.3);"></div>
              <span class="relative top-[1px] font-heading text-xs text-gray-900 font-medium">${this.playerName}</span>
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
