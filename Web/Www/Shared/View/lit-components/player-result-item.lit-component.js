import { LitElement, css, html } from 'lit';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';

/**
 * A game result item for a player in a list.
 * Displays the player's name, rank, score, and distance, along with relevant icons and styling.
 */
class PlayerResultItem extends LitElement {
  static properties = {
    distanceMeters: { type: Number },
    iconPath: { type: String },
    name: { type: String },
    points: { type: Number },
    rank: { type: Number },
    honorificTitle: { type: String },
    countryCCA2: { type: String },
    countryName: { type: String }
  }

  static styles = css`${TailwindStyles}`;

  get distanceClasses() {
    if (this.distanceWithUnit.unit === 'm') {
      if (this.distanceWithUnit.value < 100) {
        return 'bg-[#FDE047] text-gray-800'
      }
      return 'bg-[#FF8D1B] text-gray-800';
    }
    return `bg-gray-700 text-gray-50`;
  }

  /** Returns the player's distance and formats it as either meters (m) or kilometers (km). */
  get distanceWithUnit() {
    if (this.distanceMeters < 1000) {
      return {
        value: this.distanceMeters,
        unit: 'm'
      };
    }
    return {
      value: Math.round(this.distanceMeters / 1000),
      unit: 'km'
    }
  }

  /** Returns a template to display the player's rank. */
  getRankTemplate() {
    if (this.rank > 3) {
      return html`
      <div class="flex justify-center items-center w-8 h-8 rounded-full bg-gray-600">
        <span class="text-sm text-gray-50">${this.rank}</span>
      </div>
      `;
    }

    const rankIconPath1 = '/static/img/icon/cup-gold.svg';
    const rankIconPath2 = '/static/img/icon/cup-silver.svg';
    const rankIconPath3 = '/static/img/icon/cup-bronze.svg';

    return html`
      <div class="flex items-center">
        <img src="${this.rank === 1 ? rankIconPath1 : this.rank === 2 ? rankIconPath2 : rankIconPath3}" alt="" class="h-8" />
      </div>  
    `;
  }

  render() {
    return html`
      <div class="flex gap-2 items-center p-1 bg-iris-50 relative overflow-hidden rounded border border-gray-900 font-body">
        <div class="flex self-center h-full z-10 shrink-0">${this.getRankTemplate()}</div>
        <lit-player-profile-circular iconPath=${this.iconPath} flagPath="/static/flag/svg/${this.countryCCA2}.svg" countryCca2="${this.countryCCA2}" countryName="${this.countryName}"></lit-player-profile-circular>
        <div class="flex flex-col self-start relative bottom-[3px] gap-0 mr-4 pt-1 grow z-10 truncate">
          <div class="text-base text-gray-700 font-medium truncate">${this.name}</div>
          <div class="text-xs text-gray-700 truncate">${this.honorificTitle}</div>
        </div>
        <div class="flex flex-col gap-2 z-10 shrink-0">
          <div class="flex justify-center w-[72px] relative rounded bg-iris-500 border border-gray-800">
            <div class="w-5 aspect-auto absolute -top-[2px] left-0 transform -translate-x-1/2">
              <img src="/static/img/icon/star-gold.svg" />
            </div>
            <span class="text-xs text-gray-50 font-medium">${this.points}</span>
          </div>

          <div class="flex justify-center w-[72px] relative rounded border border-gray-800 ${this.distanceClasses}">
            <div class="w-5 aspect-auto absolute -top-[2px] left-0 transform -translate-x-1/2">
              
            </div>
            <span class="text-xs font-medium">${this.distanceWithUnit.value}${this.distanceWithUnit.unit}</span>
          </div>
        </div>
        <div class="slanted-edge absolute top-0 right-0 w-[106px] h-full bg-gray-600" style="clip-path: polygon(20px 0, 100% 0, 100% 100%, 0 100%); z-index: 0;"></div>
      </div>
    `;
  }


}

customElements.define('lit-player-result-item', PlayerResultItem);
