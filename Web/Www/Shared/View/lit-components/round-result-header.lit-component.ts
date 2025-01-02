import { LitElement, PropertyValues, css, html, nothing } from "lit"
import { customElement, property, state } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { tooltip } from "./tippy.lit-directive"

interface Guess {
  rank: number
  detailedPoints: string
  countryMatch: boolean
  countrySubdivisionMatch: boolean
  roundedPoints: number
  distanceMeters: number
  countryCca2: string
  flagFilePath: string
  countryName: string
}

/**
 * The header for a round result, displays:
 * - The name of the country.
 * - The name of the country's subdivision.
 * - The user's rank and score based on their guess.
 */
@customElement("lit-round-result-header")
class RoundResultHeader extends LitElement {
  @property({ type: String }) countryCca2!: string
  @property({ type: String }) countryName!: string
  @property({ type: String }) countrySubdivisionName!: string
  @property({ type: Object }) userGuess?: Guess

  @state() texture = ""
  @state() textureIndex = 0

  static styles = css`
    ${TailwindStyles}
  `

  private textures = [
    "3px-tile",
    "45-degree-fabric-dark",
    "45-degree-fabric-light",
    "60-lines",
    "absurdity",
    "ag-square",
    "always-grey",
    "arabesque",
    "arches",
    "argyle",
    "asfalt-dark",
    "asfalt-light",
    "assault",
    "axiom-pattern",
    "az-subtle",
    "back-pattern",
    "basketball",
    "batthern",
    "bedge-grunge",
    "beige-paper",
    "billie-holiday",
    "binding-dark",
    "binding-light",
    "black-felt",
    "black-linen",
    "black-linen-2",
    "black-lozenge",
    "black-mamba",
    "black-orchid",
    "black-paper",
    "black-scales",
    "black-thread",
    "black-thread-light",
    "black-twill",
    "blizzard",
    "blu-stripes",
    "bo-play",
    "brick-wall",
    "brick-wall-dark",
    "bright-squares",
    "brilliant",
    "broken-noise",
    "brushed-alum",
    "brushed-alum-dark",
    "buried",
    "candyhole",
    "carbon-fibre",
    "carbon-fibre-big",
    "carbon-fibre-v2",
    "cardboard",
    "cardboard-flat",
    "cartographer",
    "checkered-light-emboss",
    "checkered-pattern",
    "church",
    "circles",
    "classy-fabric",
    "clean-gray-paper",
    "clean-textile",
    "climpek",
    "cloth-alike",
    "concrete-wall",
    "concrete-wall-2",
    "concrete-wall-3",
    "connected",
    "corrugation",
    "cream-dust",
    "cream-paper",
    "cream-pixels",
    "crisp-paper-ruffles",
    "crissxcross",
    "cross-scratches",
    "cross-stripes",
    "crossword",
    "cubes",
    "cutcube",
    "dark-brick-wall",
    "dark-circles",
    "dark-denim",
    "dark-denim-3",
    "dark-dot",
    "dark-dotted-2",
    "dark-exa",
    "dark-fish-skin",
    "dark-geometric",
    "dark-leather",
    "dark-matter",
    "dark-mosaic",
    "dark-stripes",
    "dark-stripes-light",
    "dark-tire",
    "dark-wall",
    "dark-wood",
    "darth-stripe",
    "debut-dark",
    "debut-light",
    "diagmonds",
    "diagmonds-light",
    "diagonal-noise",
    "diagonal-striped-brick",
    "diagonal-waves",
    "diagonales-decalees",
    "diamond-eyes",
    "diamond-upholstery",
    "diamonds-are-forever",
    "dimension",
    "dirty-old-black-shirt",
    "dotnoise-light-grey",
    "double-lined",
    "dust",
    "ecailles",
    "egg-shell",
    "elastoplast",
    "elegant-grid",
    "embossed-paper",
    "escheresque",
    "escheresque-dark",
    "exclusive-paper",
    "fabric-plaid",
    "fabric-1-dark",
    "fabric-1-light",
    "fabric-of-squares",
    "fake-brick",
    "fake-luxury",
    "fancy-deboss",
    "farmer",
    "felt",
    "first-aid-kit",
    "flower-trail",
    "flowers",
    "foggy-birds",
    "food",
    "football-no-lines",
    "french-stucco",
    "fresh-snow",
    "gold-scale",
    "gplay",
    "gradient-squares",
    "graphcoders-lil-fiber",
    "graphy-dark",
    "graphy",
    "gravel",
    "gray-floral",
    "gray-lines",
    "gray-sand",
    "green-cup",
    "green-dust-and-scratches",
    "green-fibers",
    "green-gobbler",
    "grey-jean",
    "grey-sandbag",
    "grey-washed-wall",
    "greyzz",
    "grid",
    "grid-me",
    "grid-noise",
    "grilled-noise",
    "groovepaper",
    "grunge-wall",
    "gun-metal",
    "handmade-paper",
    "hexabump",
    "hexellence",
    "hixs-evolution",
    "hoffman",
    "honey-im-subtle",
    "ice-age",
    "inflicted",
    "inspiration-geometry",
    "iron-grip",
    "kinda-jean",
    "knitted-netting",
    "knitted-sweater",
    "kuji",
    "large-leather",
    "leather",
    "light-aluminum",
    "light-gray",
    "light-grey-floral-motif",
    "light-honeycomb",
    "light-honeycomb-dark",
    "light-mesh",
    "light-paper-fibers",
    "light-sketch",
    "light-toast",
    "light-wool",
    "lined-paper",
    "lined-paper-2",
    "little-knobs",
    "little-pluses",
    "little-triangles",
    "low-contrast-linen",
    "lyonnette",
    "maze-black",
    "maze-white",
    "mbossed",
    "medic-packaging-foil",
    "merely-cubed",
    "micro-carbon",
    "mirrored-squares",
    "mocha-grunge",
    "mooning",
    "moulin",
    "my-little-plaid-dark",
    "my-little-plaid",
    "nami",
    "nasty-fabric",
    "natural-paper",
    "navy",
    "nice-snow",
    "nistri",
    "noise-lines",
    "noise-pattern-with-subtle-cross-lines",
    "noisy",
    "noisy-grid",
    "noisy-net",
    "norwegian-rose",
    "notebook",
    "notebook-dark",
    "office",
    "old-husks",
    "old-map",
    "old-mathematics",
    "old-moon",
    "old-wall",
    "otis-redding",
    "outlets",
    "p1",
    "p2",
    "p4",
    "p5",
    "p6",
    "padded",
    "padded-light",
    "paper",
    "paper-1",
    "paper-2",
    "paper-3",
    "paper-fibers",
    "paven",
    "perforated-white-leather",
    "pineapple-cut",
    "pinstripe-dark",
    "pinstripe-light",
    "pinstriped-suit",
    "pixel-weave",
    "polaroid",
    "polonez-pattern",
    "polyester-lite",
    "pool-table",
    "project-paper",
    "ps-neutral",
    "psychedelic",
    "purty-wood",
    "pw-pattern",
    "pyramid",
    "quilt",
    "random-grey-variations",
    "ravenna",
    "real-carbon-fibre",
    "rebel",
    "redox-01",
    "redox-02",
    "reticular-tissue",
    "retina-dust",
    "retina-wood",
    "retro-intro",
    "rice-paper",
    "rice-paper-2",
    "rice-paper-3",
    "robots",
    "rocky-wall",
    "rough-cloth",
    "rough-cloth-light",
    "rough-diagonal",
    "rubber-grip",
    "sandpaper",
    "satin-weave",
    "scribble-light",
    "shattered",
    "shattered-dark",
    "shine-caro",
    "shine-dotted",
    "shley-tree-1",
    "shley-tree-2",
    "silver-scales",
    "simple-dashed",
    "simple-horizontal-light",
    "skeletal-weave",
    "skewed-print",
    "skin-side-up",
    "skulls",
    "slash-it",
    "small-crackle-bright",
    "small-crosses",
    "smooth-wall-dark",
    "smooth-wall-light",
    "sneaker-mesh-fabric",
    "snow",
    "soft-circle-scales",
    "soft-kill",
    "soft-pad",
    "soft-wallpaper",
    "solid",
    "sos",
    "sprinkles",
    "squairy",
    "squared-metal",
    "squares",
    "stacked-circles",
    "stardust",
    "starring",
    "stitched-wool",
    "strange-bullseyes",
    "straws",
    "stressed-linen",
    "stucco",
    "subtle-carbon",
    "subtle-dark-vertical",
    "subtle-dots",
    "subtle-freckles",
    "subtle-grey",
    "subtle-grunge",
    "subtle-stripes",
    "subtle-surface",
    "subtle-white-feathers",
    "subtle-zebra-3d",
    "subtlenet",
    "swirl",
    "tactile-noise-dark",
    "tactile-noise-light",
    "tapestry",
    "tasky",
    "tex2res1",
    "tex2res2",
    "tex2res3",
    "tex2res4",
    "tex2res5",
    "textured-paper",
    "textured-stripes",
    "texturetastic-gray",
    "ticks",
    "tileable-wood",
    "tileable-wood-colored",
    "tiny-grid",
    "translucent-fibres",
    "transparent-square-tiles",
    "tree-bark",
    "triangles",
    "triangles-2",
    "triangular",
    "tweed",
    "twinkle-twinkle",
    "txture",
    "type",
    "use-your-illusion",
    "vaio",
    "vertical-cloth",
    "vichy",
    "vintage-speckles",
    "wall-4-light",
    "washi",
    "wave-grid",
    "wavecut",
    "weave",
    "wet-snow",
    "white-bed-sheet",
    "white-brick-wall",
    "white-brushed",
    "white-carbon",
    "white-carbonfiber",
    "white-diamond",
    "white-diamond-dark",
    "white-leather",
    "white-linen",
    "white-paperboard",
    "white-plaster",
    "white-sand",
    "white-texture",
    "white-tiles",
    "white-wall",
    "white-wall-2",
    "white-wall-3",
    "white-wall-3-2",
    "white-wave",
    "whitey",
    "wide-rectangles",
    "wild-flowers",
    "wild-oliva",
    "wine-cork",
    "wood",
    "wood-pattern",
    "worn-dots",
    "woven",
    "woven-light",
    "xv",
    "zig-zag",
  ]

  get rankOrdinalSuffix(): string {
    if (!this.userGuess) return ""
    return this.userGuess.rank === 1 ? "st" : this.userGuess.rank === 2 ? "nd" : this.userGuess.rank === 3 ? "rd" : "th"
  }

  firstUpdated(_changedProperties: PropertyValues): void {
    this.handleKeydown = this.handleKeydown.bind(this)
  }

  get distanceAndUnit() {
    if (this.userGuess && this.userGuess.distanceMeters < 1000) {
      return {
        value: Math.round(this.userGuess.distanceMeters),
        unit: "m",
      }
    } else if (this.userGuess) {
      return {
        value: Math.round(this.userGuess.distanceMeters / 1000),
        unit: "km",
      }
    }
    return { value: 0, unit: "m" }
  }

  get distanceClasses() {
    let mlClass = "ml-0"

    if (this.distanceAndUnit.unit === "km") {
      const distanceCharactersCount = this.distanceAndUnit.value.toString().length
      if (distanceCharactersCount > 3) {
        mlClass = "ml-3"
      } else if (distanceCharactersCount > 2) {
        mlClass = "ml-2"
      }
    }

    return classMap({
      [mlClass]: true,
    })
  }

  private getCls() {
    const rankGapMap: Record<number, string> = {
      1: "gap-0",
      2: "gap-1",
      3: "gap-0.5",
    }

    return classMap({
      [rankGapMap[this.userGuess?.rank || 0] || "gap-1"]: true,
    })
  }

  private handleKeydown(event: KeyboardEvent): void {
    return // Disable the texture switch, remove it for dev
    switch (event.code) {
      case "ArrowRight":
        this.nextTexture()
        break
      case "ArrowLeft":
        this.previousTexture()
        break
      default:
        break
    }
  }

  private nextTexture(): void {
    if (this.textureIndex === this.textures.length - 1) {
      this.textureIndex = 0
    } else {
      this.textureIndex++
    }
    this.texture = this.textures[this.textureIndex]
    console.log(this.texture)
  }

  private previousTexture(): void {
    if (this.textureIndex === 0) {
      this.textureIndex = this.textures.length - 1
    } else {
      this.textureIndex--
    }
    this.texture = this.textures[this.textureIndex]
    console.log(this.texture)
  }

  connectedCallback(): void {
    super.connectedCallback()
    window.addEventListener("keydown", this.handleKeydown)
  }

  disconnectedCallback(): void {
    window.removeEventListener("keydown", this.handleKeydown)
    super.disconnectedCallback()
  }

  protected render() {
    return this.userGuess && Object.keys(this.userGuess).length > 0
      ? html`
          <div
            class="flex justify-between items-start relative z-20 bg-iris-500 border-b-2 border-gray-700"
            style="background-image: url('https://www.transparenttextures.com/patterns/${this.texture}.png');"
          >
            <div class="flex gap-2 w-full relative pr-[122px]">
              <img
                src="/static/flag/wavy/${this.countryCca2.toLowerCase()}.png"
                class="h-16 absolute top-2 left-2 z-10 drop-shadow"
                alt="Flag of ${this.countryName}"
              />
              <div class="flex flex-col gap-1 py-2 pl-[100px]">
                <div class="text-3xl text-gray-0 font-medium text-stroke-2 text-stroke-gray-800 leading-none">${this.countryName}</div>
                <div class="text-lg text-gray-950 font-medium">${this.countrySubdivisionName}</div>
              </div>
            </div>

            ${this.userGuess.rank
              ? html`
                  <div class="absolute top-0 right-2 z-10">
                    <img class="relative bottom-[7px]" src="/static/img/ui/ribbon-emblem.png" />

                    <div class="flex flex-col items-center w-[114px] absolute top-[8px]">
                      <div class="flex items-end ${this.getCls()}">
                        <span class="text-5xl font-bold text-gray-0 text-stroke-2 text-stroke-gray-700 z-10">${this.userGuess.rank}</span>
                        <span
                          class="relative bottom-[1px] text-xl font-medium text-gray-0 text-stroke-2 text-stroke-gray-700 ${this.userGuess.rank === 1
                            ? "relative right-1"
                            : ""}"
                          >${this.rankOrdinalSuffix}</span
                        >
                      </div>

                      <div class="relative left-1.5 mt-1">
                        <div class="flex justify-center w-[72px] relative rounded border border-gray-700 bg-iris-500">
                          <div class="w-6 aspect-auto absolute -top-[4px] left-0 transform -translate-x-1/2">
                            <img src="/static/img/icon/star-gold.svg" />
                          </div>
                          <span class="text-xs text-gray-0 font-medium" ${tooltip(this.userGuess.detailedPoints)}
                            >${this.userGuess.roundedPoints}</span
                          >
                        </div>

                        <div class="flex justify-center items-center mt-3 rounded border border-gray-700 bg-iris-500">
                          ${this.userGuess.countryMatch || this.userGuess.countrySubdivisionMatch
                            ? html` <img
                                src="/static/img/icon/clover-${this.userGuess.countrySubdivisionMatch ? "gold" : "green"}.svg"
                                class="h-7 absolute left-0 transform -translate-x-1/2"
                              />`
                            : html`
                                <lit-flag
                                  cca2="${this.userGuess.countryCca2}"
                                  filePath="${this.userGuess.flagFilePath}"
                                  description="${this.userGuess.countryName}"
                                  roundedClass="rounded-sm"
                                  class="h-5 absolute ${this.userGuess.countryCca2 === "NP" ? "left-2" : "left-0"} transform -translate-x-1/2"
                                >
                                </lit-flag>
                              `}
                          <span class="text-xs text-gray-50 font-medium ${this.distanceClasses}"
                            >${this.distanceAndUnit.value}<span class="text-gray-100">${this.distanceAndUnit.unit}</span></span
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                `
              : nothing}
          </div>
        `
      : nothing
  }
}
