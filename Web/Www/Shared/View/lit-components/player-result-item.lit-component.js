import { LitElement, css, html } from 'lit';
import { classMap } from 'lit/directives/class-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/**
 * A game result item for a player in a list.
 * Displays the player's name, rank, score, and distance, along with relevant icons and styling.
 */
class PlayerResultItem extends LitElement {
  static properties = {
    countryCca2: { type: String },
    detailedPoints: { type: String },
    distanceMeters: { type: Number },
    flagFilePath: { type: String },
    flagDescription: { type: String },
    iconPath: { type: String },
    level: { type: Number },
    name: { type: String },
    rank: { type: Number },
    rankSelected: { type: Number },
    roundedPoints: { type: Number },
    userTitle: { type: String },
  }

  static styles = css`${TailwindStyles}`;

  get classes() {
    return {
      'bg-rank-first-default': this.rankSelected === 1,
      'bg-rank-second-default': this.rankSelected === 2,
      'bg-rank-third-default': this.rankSelected === 3,
      'bg-honey-400': this.rankSelected > 3,
      'bg-gray-50': !this.rankSelected
    }
  }

  get distanceClasses() {
    if (this.distanceWithUnit.unit === 'm') {
      if (this.distanceWithUnit.value < 100) {
        return 'bg-[#FDE047] text-gray-800'
      }
      return 'bg-[#FF8D1B] text-gray-800';
    }
    return `bg-gray-700 text-gray-50`;
  }

  get rankClasses() {
    return {
      'bg-rank-first-dark': this.rankSelected === 1,
      'bg-rank-second-dark': this.rankSelected === 2,
      'bg-rank-third-dark': this.rankSelected === 3,
      'bg-honey-500': this.rankSelected > 3,
      'bg-iris-100': !this.rankSelected
    }
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

  get scoreOnlyTemplate() {
    return html`
      <div class="flex justify-between items-center w-14 sm:w-[72px] mr-4">
        <img src="/static/img/icon/star-gold.svg" class="w-5 sm:w-6 relative bottom-[2px]" />
        <div class="font-heading text-xl sm:text-2xl font-semibold text-[#F5D83A] text-stroke-2 text-stroke-gray-700" ${tooltip(this.detailedPoints)}>${this.roundedPoints}</div>
      </div>
    `;
  }

  get scoreAndDistanceTemplate() {
    return html`
      <div class="flex flex-col gap-2 sm:gap-1.5 p-2">
        <div class="flex justify-center items-center w-16 sm:w-20 h-4 sm:h-5 relative rounded bg-iris-500 border border-gray-700">
          <div class="w-5 sm:w-6 aspect-auto absolute -top-[4px] left-0 transform -translate-x-1/2">
            <img src="/static/img/icon/star-gold.svg" />
          </div>
          <span class="text-sm text-gray-50 font-medium" ${tooltip(this.detailedPoints)}>${this.roundedPoints}</span>
        </div>
          
        <div class="flex justify-center items-center w-16 sm:w-20 h-4 sm:h-5 relative rounded border border-gray-800 ${this.distanceClasses}">
          <span class="text-xs font-medium">${this.distanceWithUnit.value}${this.distanceWithUnit.unit}</span>
        </div>
      </div>
    `;
  }

  render() {
    return html`${this.bgColor}
      <div class="flex h-14 sm:h-16 relative rounded border border-gray-700 select-none ${classMap(this.classes)}">
        <div class="flex justify-center items-center w-14 sm:w-16 rounded-l shrink-0 border-r border-gray-300
        00 ${classMap(this.rankClasses)}">${this.getRankTemplate()}</div>

        <div class="hidden sm:flex justify-center items-center w-14 ml-2 shrink-0">
          <img src="${this.iconPath}" class="max-w-14 max-h-14" />
        </div>

        <div class="flex flex-col w-full pt-1 truncate">
          <div class="flex flex-col gap-0.5 w-full h-full px-2">
            <div class="flex items-center gap-1">
              <div class="flex flex-none items-center w-5 h-5">
                <lit-flag cca2="${this.countryCca2}" filePath="${this.flagFilePath}" description="${this.flagDescription}" roundedClass="rounded-sm" maxHeightClass="max-h-5" class="w-5"></lit-flag>
              </div>

              <div class="text-sm sm:text-base text-gray-700 font-medium truncate select-text">${this.name}</div>
            </div>
            <div class="flex items-center gap-1">
              <div class="flex justify-center items-center w-5 shrink-0">
                <img src="/static/img/icon/emblem.svg" class="h-5" />
                <span class="absolute text-white font-heading text-base font-bold text-stroke-2 text-stroke-iris-900">${this.level}</span>
              </div>
              <div class="text-xs sm:text-sm text-gray-700 truncate select-text">${this.userTitle}</div>
            </div>
          </div>
        </div>

        <div class="flex justify-end w-24 sm:w-32 rounded-r-sm shrink-0 bg-gray-600" style="clip-path: polygon(10px 0, 100% 0, 100% 100%, 0 100%);">
          ${this.distanceMeters ? this.scoreAndDistanceTemplate : this.scoreOnlyTemplate}
        </div>
      </div>
    `;
  }
}

customElements.define('lit-player-result-item', PlayerResultItem);
