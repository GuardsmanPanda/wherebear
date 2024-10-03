import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

class MapMarker extends LitElement {
  static properties = {
    distanceMeters: { type: Number },
    mapMarkerFilePath: { type: String },
    playerName: { type: String },
    rank: { type: Number },
  };

  static styles = [css`${TailwindStyles}`];

  constructor() {
    super();
    this.distanceMeters = null;
    this.mapMarkerFilePath = null;
    this.playerName = null;
    this.rank = null;
  }

  getDistanceAndUnit() {
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
    const distance = this.getDistanceAndUnit();

    return html` 
      <div class="flex flex-col items-center font-body">
        <div class="flex flex-col items-center">
          <div class="flex items-center relative top-[4px] z-20">
            <div 
              class="flex justify-center items-center w-[22px] h-[22px] relative top-[1px] left-[2px] z-20 p-1 rounded border border-black" 
              style="background-color: ${this.getRankColor()};">
              <span class="text-xs text-black font-medium">${this.rank}</span>
            </div>
            <div 
              class="flex items-center h-5 relative top-[1px] px-1 pt-0.5 rounded-r bg-gray-100 border border-black">
              <span class="text-xs text-black">${distance.value}${distance.unit}</span>
            </div>
          </div>
          <div class="flex justify-center items-center min-w-20 h-5 pl-2 pr-1 z-10 rounded bg-blue-300 border border-b-2 border-blue-800">
            <span class="font-heading text-xs text-black font-medium">${this.playerName}</span>
          </div>
        </div>
        <div class="w-3 h-10 absolute top-8 bg-gray-200 border border-black" style="box-shadow: inset 1px 0 2px rgba(0, 0, 0, 0.3);"></div>
        <div class="flex justify-center items-end w-16 h-12 relative top-[4px] overflow-visible">
          <img src="${this.mapMarkerFilePath}" class="z-10" style="height: auto; max-height: 48px;" />
        </div>
      </div>   
    `;
  }
}

customElements.define('lit-map-marker', MapMarker);
