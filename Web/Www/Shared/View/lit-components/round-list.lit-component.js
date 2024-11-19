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
    "404: Skill Issue",
    "A wild location hasn't appeared yet",
    "Access denied",
    "Alexander hasn't conquered this yet",
    "All your maps are belong to us",
    "Analogue coordinates in a digital wasteland",
    "API rate limit exceeded",
    "Area 52",
    "Async/await: Location loading",
    "Atlantis 2.0",
    "Bazel build rules undefined",
    "Boss level locked",
    "Buffer overflow: Too many locations",
    "buffering_location.exe",
    "Callback hell: Geographical edition",
    "Carmen Sandiego was here",
    "Challenger approaching... eventually",
    "Cheat code required",
    "Classified by MI6",
    "Colony DLC not installed",
    "Contains 100% organic technical debt",
    "Coming soon",
    "Companion cube storage facility",
    "Compilation failed: Terrain.class",
    "Compiling geographical data...",
    "Connection interrupted",
    "Content geoblocked",
    "Coordinates written on reclaimed wood",
    "Coordinates you've probably never heard of",
    "Country not available in your region",
    "Craft-coded geographical experience",
    "Ctrl+Alt+Defeat",
    "Currently building pyramids",
    "Data corrupted",
    "Debugging symbols not loaded",
    "Decentralized map protocol",
    "Deliberately inefficient coordinates",
    "Defeat optional boss first",
    "Dependency injection failed",
    "Do not pass GO",
    "Doctor Who's next destination",
    "Don't forget to save your progress!",
    "Don't look at me!",
    "Error 403: Forbidden Land",
    "Error 1492: Discovery pending",
    "Error: Keyboard cat took a break",
    "Ethically sourced map fragments",
    "Ethically sourced mystery zone",
    "Equip better gear to unlock",
    "Expansion pack coming soon",
    "Expected: Location, Actual: ¯\\_(ツ)_/¯",
    "Fast travel location unavailable",
    "Fermented geographical data",
    "Fog of war",
    "Foreign key to nowhere",
    "Frankenstein's backup lab",
    "Geography.dll is missing",
    "Geography.exe has stopped working",
    "Geographical cache invalidated",
    "git clone earth --shallow",
    "Git gud at finding me",
    "Gluten-free geographical data",
    "Gluten-free, open-source geography",
    "Gone with the bandwidth",
    "Guess who?",
    "Hand-crafted, organic location",
    "Hardcoded geographical constant",
    "Here be dragons",
    "Hidden dev test area",
    "Holodeck malfunction",
    "In a galaxy far, far away",
    "Intentionally low-resolution",
    "insert cartridge",
    "Insert coin to unlock",
    "Kernel panic: Map not loaded",
    "Level not found",
    "Level requirement not met",
    "Loading...",
    "Locally sourced mystery",
    "Location aged in small batches",
    "Location before feature bloat",
    "Location before you heard of it",
    "Location branch not merged",
    "Location in airplane mode",
    "Location in alpha testing",
    "Location is sus",
    "Location generator not yielding",
    "Location goes brrrrr",
    "Location lost in translation.translate()",
    "Location marked as TODO",
    "Location microservice offline",
    "Location.min.js not loaded",
    "Location preserved on vinyl",
    "Location ran away! (1% catch rate)",
    "Location requires 10 years experience",
    "Location requires root permissions",
    "Location roasted to perfection",
    "Location tokenization error",
    "Location too underground to be found",
    "Location wrapped in Optional<T>",
    "Location.exe has stopped working",
    "Location: 51% consensus pending",
    "Location: Lexical scope undefined",
    "Location: Resistance is fertile",
    "Location: Task failed successfully",
    "Makefile: Location not found",
    "Manually coded. No frameworks.",
    "Manually ground coordinates",
    "Map DLC required",
    "Map sold separately",
    "Matchmaking failed",
    "Memory leak: Map data corrupted",
    "Memory profiler: Leaking geography",
    "Merge conflict: Reality vs Map",
    "Minecraft chunk not generated",
    "Modern problems require modern confusion",
    "Mystery box",
    "NaN miles from nowhere",
    "Need higher perception stat",
    "Nice try, but no",
    "No backend for old maps",
    "No man's land",
    "Not enough mana to reveal",
    "Not the droids you're looking for",
    "Nothing to see here... yet",
    "NullPointerException: Location",
    "Object Object",
    "Overfitted geographical model",
    "Our princess is in another castle",
    "Out of service",
    "Pagefault in geographical memory",
    "Paradoxical coordinate generator",
    "Perfectly unknown, as all things should be",
    "Permission denied: Universe access",
    "PHP: Possibly Hidden Place",
    "ping: Request timed out",
    "Please insert Stanley Parable disk 2",
    "Portal testing chamber",
    "Pour-over geographical methodology",
    "Pre-cloud geographical research",
    "Pre-monetization coordinate experience",
    "Premium content",
    "Primary key not found",
    "Procedurally ungenerated",
    "Procedural generation failed",
    "Promise.reject(new Location())",
    "Proof of geographical existence",
    "Quantum location uncertainty",
    "Queue position: 2147483647",
    "Query limit exceeded",
    "Reality has encountered a problem",
    "Redacted",
    "RTFM: Read The Forgotten Map",
    "Runtime exception: World not found",
    "Satellite connection lost",
    "Segmentation fault in GPS",
    "SELECT location FROM world WHERE hidden",
    "Self-documenting location",
    "Shangri-La beta server",
    "Simulation boundaries reached",
    "Spoilers ahead!",
    "sudo make me a location",
    "sudo reveal_location",
    "Sustainably programmed coordinates",
    "SSH access denied",
    "Still waiting for Half-Life 3",
    "Peek-a-boo!",
    "rm -rf /location",
    "Secret warp zone",
    "Shhh...",
    "Still mapping this one...",
    "Sustainable geographical practices",
    "Syntax error in reality",
    "Terrain chunk loading",
    "The cake is a lie",
    "The map less traveled",
    "This isn't even my final location",
    "Throw new Error('Nice try')",
    "Top secret",
    "Too cool to be discovered",
    "Uncharted territory",
    "Undefined geographical variable",
    "Undefined is a valid location type",
    "Undefined is not a function of geography",
    "Under construction",
    "Unhandled geographical exception",
    "Unknown location type",
    "Unoptimized. Intentionally.",
    "Viewport blocked by Death Star",
    "Wait, that's illegal",
    "Webpack still bundling this location",
    "World seed not found",
    "World.prototype.hide()",
    "Works on my machine",
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
