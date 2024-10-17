import { LitElement, css, html, nothing } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { styleMap } from 'lit/directives/style-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

class CountryUsedList extends LitElement {
  static properties = {
    /** An array of country objects representing the list of countries used in the game. */
    countries: { type: Array },

    /** The number of the current round being played in the game. */
    currentRound: { type: Number },

    /** The total number of rounds available in the game. */
    totalRoundCount: { type: Number },

    /** The number of the round that is currently selected (arrow visible). */
    selectedRound: { type: Number },
  };

  static styles = css`${TailwindStyles}`;

  /** Array of tooltips to be shown when the country hasn't been revealed yet. */
  unplayedCountryTooltips = [
    "404: Country not found",
    "Access denied",
    "Coming soon",
    "Country not available in your region",
    "Don't look at me!",
    "Guess who?",
    "Loading...",
    "Mystery box",
    "No man's land",
    "Out of service",
    "Redacted",
    "Peek-a-boo!",
    "Shhh...",
    "Still mapping this one...",
    "Top Secret",
    "Uncharted territory",
    "Under construction",
  ];


  constructor() {
    super();
    this.countries = [];
    this.currentRound = null;
    this.totalRoundCount = null;
    this.selectedRound = null;
  }

  /**
   * Returns the appropriate rank icon (gold, silver, bronze) based on the user's rank.
   * @param {Number} userRank - The rank of the user.
   */
  getUserRankIcon(userRank) {
    switch (userRank) {
      case 1:
        return 'ribbon-gold copy';
      case 2:
        return 'ribbon-silver copy';
      case 3:
        return 'ribbon-bronze copy';
      default:
        return null;
    }
  }

  /**
   * Dynamically generates the styles for the country icon based on whether it's a placeholder or an actual country.
   * @param {Boolean} isPlaceHolder - Whether the icon is a placeholder.
   * @param {String} cca2 - The country's code (if it's not a placeholder).
   */
  getCountryIconStyles(isPlaceHolder, cca2) {
    if (isPlaceHolder) {
      return {
        'box-shadow': 'inset 0 -4px 1px rgb(0 0 0 / 0.3)'
      };
    }
    return {
      'background-image': `url('/static/flag/svg/${cca2}.svg')`,
      'background-size': 'cover',
      'background-position': 'center',
      'box-shadow': 'inset 0 -4px 1px rgb(0 0 0 / 0.3)'
    };
  }

  /**
   * Generates the HTML template for displaying a country icon.
   * @param {Object} args - Parameters including `isPlaceHolder`, `cca2`, `userRank` and `isSelected`.
   */
  getCountryIconTemplate(args) {
    return html`
      <div
        class="flex flex-col w-[40px] h-[28px] rounded bg-gray-50 border border-gray-700 relative cursor-default"
        style="${styleMap(this.getCountryIconStyles(args.isPlaceHolder, args.cca2))}">
    
        ${args.userRank <= 3 ? html`<img class="w-[22px] absolute -top-[3px] -right-[3px]" src="/static/img/icon/${this.getUserRankIcon(args.userRank)}.svg">` : nothing}
            
        ${args.isPlaceHolder
        ? html`
          <div class="flex justify-center items-center h-[24px] relative bottom-[2px] ${classMap({ 'rounded': args.isSelected })}}">
            <span class="text-xl font-medium text-gray-600">?</span>
          </div>`
        : ''}
      </div>
    `;
  }

  /** Randomly selects one of the tooltips for an unplayed country. */
  getRandomTooltipForUnplayedCountry() {
    return this.unplayedCountryTooltips[Math.floor(Math.random() * this.unplayedCountryTooltips.length)];
  }

  render() {
    const countryTemplates = [];
    for (let i = 0; i < this.totalRoundCount; i++) {
      const country = this.countries[i];
      const isSelected = this.selectedRound - 1 === i;
      countryTemplates.push(html`
        <div class="relative">
          ${isSelected
          ? html`<img src="/static/img/icon/arrow.svg" class="absolute -top-[20px] left-[5px] z-10 w-[30px]" style="filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.6));">`
          : nothing}
          ${i < this.countries.length
          ? html`
            <div ${tooltip(country.name)}>
              ${this.getCountryIconTemplate({ isPlaceHolder: false, cca2: country.cca2, userRank: country.user_rank, isCountryMatch: country.country_match, isCountrySubdivisionMatch: country.country_subdivision_match })}
            </div>`
          : html`
            <div ${tooltip(this.getRandomTooltipForUnplayedCountry())}>
              ${this.getCountryIconTemplate({ isPlaceHolder: true, isSelected })}
            </div>`}
        </div>
      `);
    }

    return html`
      <div class="flex flex-wrap w-full gap-1 justify-center p-2">
        ${countryTemplates}
      </div>
    `;
  }
}

customElements.define('lit-country-used-list', CountryUsedList);
