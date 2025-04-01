import { LitElement, css, html, nothing } from "lit"
import { customElement, property } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"
import { styleMap } from "lit/directives/style-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { tooltip } from "./tippy.lit-directive"

interface Round {
  /** The country code (ISO 3166-1 alpha-2) for the round's country. */
  country_cca2: string

  /** The name of the country associated with the round. */
  country_name: string

  /** The user's rank for the round (1 for gold, 2 for silver, 3 for bronze, etc.). */
  user_rank: number | null

  /** Whether the country match for the round matches the user's guess. */
  country_match_user_guess: boolean

  /** Whether the country subdivision match for the round matches the user's guess. */
  country_subdivision_match_user_guess: boolean

  /** Whether the round is a placeholder (e.g., a round that hasn't been revealed yet). */
  isPlaceHolder: boolean
}

/**
 * Represents a list of rounds in a game.
 * Displays each round as an icon and supports clickable rounds to trigger a selection event.
 */
@customElement("lit-round-list")
class RoundList extends LitElement {
  @property({ type: Array }) rounds: Round[] = []
  @property({ type: Boolean }) roundClickable = false
  @property({ type: Number }) selectedRoundNumber?: number
  @property({ type: Number }) totalRoundCount!: number

  static styles = css`
    ${TailwindStyles}
  `

  /** Array of tooltips to be shown when the round hasn't been revealed yet. */
  unplayedRoundTooltips: string[] = [
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
  ]

  /**
   * Dynamically generates the styles for the round icon based on whether it's an already played country.
   * @param isPlaceHolder - Whether the icon is a placeholder.
   * @param cca2 - The country's code (if it's not a placeholder).
   */
  private getRoundIconStyles(isPlaceHolder: boolean, cca2: string) {
    let styles: Record<string, string> = {
      "box-shadow": "inset 0 -4px 1px rgb(0 0 0 / 0.3)",
    }

    if (!isPlaceHolder) {
      styles = {
        ...styles,
        "background-image": `url('/static/flag/svg/${cca2}.svg')`,
        "background-size": cca2 === "NP" ? "contain" : "cover",
        "background-position": "left",
        "background-repeat": "no-repeat",
      }
    }

    return styles
  }

  /** Generates the HTML template for displaying a round icon. */
  private getRoundIconTemplate(args: { isPlaceHolder: boolean; countryCca2: string; userRank: number | null; isSelected: boolean }) {
    return html`
      <div
        class="flex flex-col w-[40px] h-[28px] rounded bg-gray-50 border border-gray-700 relative z-30"
        style="${styleMap(this.getRoundIconStyles(args.isPlaceHolder, args.countryCca2))}"
      >
        ${args.userRank && args.userRank >= 1 && args.userRank <= 3
          ? html`<img class="w-[22px] absolute -top-[3px] -right-[3px]" src="/static/img/icon/${this.getUserRankIcon(args.userRank)}.svg" />`
          : nothing}
        ${args.isPlaceHolder
          ? html` <div class="flex justify-center items-center h-[24px] relative bottom-[2px] ${classMap({ rounded: args.isSelected })}}">
              <span class="text-xl font-medium text-gray-600">?</span>
            </div>`
          : ""}
      </div>
    `
  }

  /** Randomly selects one of the tooltips for an unplayed round. */
  private getRandomTooltipForUnplayedRound() {
    return this.unplayedRoundTooltips[Math.floor(Math.random() * this.unplayedRoundTooltips.length)]
  }

  private getSelectedRoundBackgroundClasses(isSelected: boolean) {
    return {
      hidden: !isSelected,
      "group-hover:block": !isSelected,
      "group-hover:bg-iris-400": !isSelected,
      "group-hover:h-[calc(100%+1px)]": !isSelected,
      "group-hover:z-10": !isSelected,
      "bg-iris-500": isSelected,
      "h-[calc(100%+4px)]": isSelected,
      "rounded-t-md": isSelected,
      "z-20": isSelected,
    }
  }

  private getSelectedRoundBackgroundStyles(isSelected: boolean) {
    return {
      "box-shadow": isSelected ? "inset 0 2px 1px rgba(255, 255, 255, 0.6)" : "none",
    }
  }

  /** Returns the appropriate rank icon (gold, silver, bronze) based on the user's rank. */
  private getUserRankIcon(userRank: number) {
    switch (userRank) {
      case 1:
        return "ribbon-gold"
      case 2:
        return "ribbon-silver"
      case 3:
        return "ribbon-bronze"
      default:
        return null
    }
  }

  private selectRound(roundNumber: number) {
    if (!this.roundClickable) return
    if (this.selectedRoundNumber === roundNumber) return

    this.dispatchEvent(
      new CustomEvent("clicked", {
        detail: { roundNumber },
        bubbles: true,
        composed: true,
      }),
    )
  }

  protected render() {
    const roundTemplates = []
    for (let i = 0; i < (this.totalRoundCount ?? 0); i++) {
      const round = this.rounds[i]
      const roundNumber = i + 1
      const isSelected = this.selectedRoundNumber === roundNumber
      roundTemplates.push(html`
        <div
          class="group relative h-full px-[5px] py-2 ${this.roundClickable ? "cursor-pointer" : ""}"
          @click="${() => {
            // Round is undefined if it's a not played yet round (placeholder)
            if (round) {
              this.selectRound(roundNumber)
            }
          }}"
        >
          ${!this.roundClickable && isSelected
            ? html`<img src="/static/img/icon/arrow-down.svg" class="absolute -top-[14px] left-[10px] z-40 w-[30px]" />`
            : nothing}
          ${this.roundClickable
            ? html` <div
                class="absolute bottom-0 -left-[2px] w-[calc(100%+4px)] border border-b-0 border-gray-700 ${classMap(
                  this.getSelectedRoundBackgroundClasses(isSelected),
                )}"
                style="${styleMap(this.getSelectedRoundBackgroundStyles(isSelected))}"
              ></div>`
            : nothing}
          ${i < this.rounds.length
            ? html` <div ${tooltip(round.country_name)}>
                ${this.getRoundIconTemplate({
                  countryCca2: round.country_cca2,
                  isPlaceHolder: false,
                  isSelected,
                  userRank: round.user_rank,
                })}
              </div>`
            : html` <div ${tooltip(this.getRandomTooltipForUnplayedRound())}>
                ${this.getRoundIconTemplate({
                  isPlaceHolder: true,
                  isSelected,
                  countryCca2: "",
                  userRank: null,
                })}
              </div>`}
        </div>
      `)
    }

    return html` <div class="flex flex-wrap w-full justify-center items-center px-0 select-none">${roundTemplates}</div> `
  }
}
