import { LitElement, css, html, nothing } from 'lit';
import { classMap } from 'lit/directives/class-map.js';
import { styleMap } from 'lit/directives/style-map.js';

import { TailwindStyles } from '../../../../../public/static/dist/lit-tailwind-css';
import { tooltip } from './tippy.lit-directive';

/**
 * Represents a list of rounds in a game.
 * Displays each round as an icon and supports clickable rounds to trigger a selection event.
 */
class RoundList extends LitElement {
  static properties = {
    /** The number of the current round being played in the game. */
    currentRound: { type: Number },

    /** An array of round objects representing the list of all rounds in the game. */
    rounds: { type: Array },

    /** Whether the rounds are clickable. */
    roundClickable: { type: Boolean },

    /** The total number of rounds available in the game. */
    totalRoundCount: { type: Number },

    /** The number of the round that is currently selected (arrow visible). */
    selectedRound: { type: Number },
  };

  static styles = css`${TailwindStyles}`;

  /** Array of tooltips to be shown when the round hasn't been revealed yet. */
  unplayedRoundTooltips = [
    "404: Country not found",
    "A wild location hasn't appeared yet",
    "Access denied",
    "Alexander hasn't conquered this yet",
    "All your maps are belong to us",
    "Area 52",
    "Atlantis 2.0",
    "buffering_location.exe",
    "Carmen Sandiego was here",
    "Challenger approaching... eventually",
    "Classified by MI6",
    "Colony DLC not installed",
    "Coming soon",
    "Companion cube storage facility",
    "Content geoblocked",
    "Country not available in your region",
    "Currently building pyramids",
    "Data corrupted",
    "Defeat optional boss first",
    "Do not pass GO",
    "Doctor Who's next destination",
    "Don't forget to save your progress!",
    "Don't look at me!",
    "Error 403: Forbidden Land",
    "Error 1492: Discovery pending",
    "Error: Keyboard cat took a break",
    "Equip better gear to unlock",
    "Expansion pack coming soon",
    "Fast travel location unavailable",
    "Geography.dll is missing",
    "Geography.exe has stopped working",
    "Gone with the bandwidth",
    "Guess who?",
    "Here be dragons",
    "In a galaxy far, far away",
    "Insert coin to unlock",
    "Level requirement not met",
    "Loading...",
    "Location in airplane mode",
    "Location is sus",
    "Location goes brrrrr",
    "Location ran away! (1% catch rate)",
    "Map DLC required",
    "Map sold separately",
    "Minecraft chunk not generated",
    "Mystery box",
    "Need higher perception stat",
    "No man's land",
    "Not enough mana to reveal",
    "Not the droids you're looking for",
    "Nothing to see here... yet",
    "Object Object",
    "Our princess is in another castle",
    "Out of service",
    "Please insert Stanley Parable disk 2",
    "Portal testing chamber",
    "Premium content",
    "Quantum location uncertainty",
    "Queue position: 2147483647",
    "Query limit exceeded",
    "Redacted",
    "Satellite connection lost",
    "Shangri-La beta server",
    "Simulation boundaries reached",
    "Spoilers ahead!",
    "Still waiting for Half-Life 3",
    "Peek-a-boo!",
    "Shhh...",
    "Still mapping this one...",
    "The cake is a lie",
    "This isn't even my final location",
    "Top secret",
    "Uncharted territory",
    "Under construction",
    "Unknown location type",
    "Viewport blocked by Death Star",
    "X marks... somewhere else",
    "Yoda's retirement home",
    "You must construct additional pylons",
  ];

  constructor() {
    super();
    this.rounds = [];
    this.roundClickable = false;
    this.currentRound = null;
    this.totalRoundCount = null;
    this.selectedRound = null;
  }

  get roundIconTemplateClasses() {
    return {
      'cursor-pointer': this.roundClickable,
      'w-[40px]': true,
      'h-[28px]': true,
    }
  }

  /**
   * Returns the appropriate rank icon (gold, silver, bronze) based on the user's rank.
   * @param {Number} userRank - The rank of the user.
   */
  getUserRankIcon(userRank) {
    switch (userRank) {
      case 1:
        return 'ribbon-gold';
      case 2:
        return 'ribbon-silver';
      case 3:
        return 'ribbon-bronze';
      default:
        return null;
    }
  }

  /**
   * Dynamically generates the styles for the round icon based on whether it's an already played country.
   * @param {Boolean} isPlaceHolder - Whether the icon is a placeholder.
   * @param {String} cca2 - The country's code (if it's not a placeholder).
   */
  getRoundIconStyles(isPlaceHolder, cca2) {
    if (isPlaceHolder) {
      return {
        'box-shadow': 'inset 0 -4px 1px rgb(0 0 0 / 0.3)'
      };
    }
    return {
      'background-image': `url('/static/flag/svg/${cca2}.svg')`,
      'background-size': cca2 === 'NP' ? 'contain' : 'cover',
      'background-position': 'left',
      'background-repeat': 'no-repeat',
      'box-shadow': 'inset 0 -4px 1px rgb(0 0 0 / 0.3)'
    };
  }

  /**
   * Generates the HTML template for displaying a round icon.
   * @param {Object} args - Parameters including `isPlaceHolder`, `cca2`, `userRank` and `isSelected`.
   */
  getRoundIconTemplate(args) {
    return html`
      <div
        class="flex flex-col rounded bg-gray-50 border border-gray-700 relative ${classMap(this.roundIconTemplateClasses)}"
        style="${styleMap(this.getRoundIconStyles(args.isPlaceHolder, args.countryCca2))}"
        @click="${() => this.selectRound(args.countryCca2)}">

        ${args.userRank != null && args.userRank <= 3 ? html`<img class="w-[22px] absolute -top-[3px] -right-[3px]" src="/static/img/icon/${this.getUserRankIcon(args.userRank)}.svg">` : nothing}
            
        ${args.isPlaceHolder
        ? html`
          <div class="flex justify-center items-center h-[24px] relative bottom-[2px] ${classMap({ 'rounded': args.isSelected })}}">
            <span class="text-xl font-medium text-gray-600">?</span>
          </div>`
        : ''}
      </div>
    `;
  }

  /** Randomly selects one of the tooltips for an unplayed round. */
  getRandomTooltipForUnplayedRound() {
    return this.unplayedRoundTooltips[Math.floor(Math.random() * this.unplayedRoundTooltips.length)];
  }

  selectRound(cca2) {
    if (!this.roundClickable) return;

    this.dispatchEvent(new CustomEvent('clicked', {
      detail: { countryCca2: cca2 },
      bubbles: true,
      composed: true
    }));
  }

  render() {
    const roundTemplates = [];
    for (let i = 0; i < this.totalRoundCount; i++) {
      const round = this.rounds[i];
      const isSelected = this.selectedRound - 1 === i;
      roundTemplates.push(html`
        <div class="relative">
          ${isSelected
          ? html`<img src="/static/img/icon/arrow-down.svg" class="absolute -top-[20px] left-[5px] z-10 w-[30px]" style="filter: drop-shadow(0 2px 2px rgba(0, 0, 0, 0.6));">`
          : nothing}
          ${i < this.rounds.length
          ? html`
            <div ${tooltip(round.country_name)}>
              ${this.getRoundIconTemplate({ isPlaceHolder: false, countryCca2: round.country_cca2, userRank: round.user_rank, isCountryMatch: round.country_match_user_guess, isCountrySubdivisionMatch: round.country_subdivision_match_user_guess })}
            </div>`
          : html`
            <div ${tooltip(this.getRandomTooltipForUnplayedRound())}>
              ${this.getRoundIconTemplate({ isPlaceHolder: true, isSelected })}
            </div>`}
        </div>
      `);
    }

    return html`
      <div class="flex flex-wrap w-full gap-1 justify-center p-2 select-none">
        ${roundTemplates}
      </div>
    `;
  }
}

customElements.define('lit-round-list', RoundList);
