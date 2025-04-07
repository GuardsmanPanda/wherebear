import { LitElement, css, html, nothing } from "lit"
import { customElement, property, state } from "lit/decorators.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
// @ts-ignore
import { AppStyles } from "../../../../../public/static/dist/lit-app-css"
import { Logger } from "../../js/logger"
import { Dialog } from "./dialog.lit-component"

interface MapStyle {
  enum: string
  full_uri: string
  name: string
  short_name: string
  user_level_enum: number
}

interface LocationMarker {
  color: string
  enum: string
  imgPath: string
  type: "pin" | "cross"
}

interface LocationMarkerApi {
  border_color: "black" | "white"
  color: string
  enum: string
  file_path: string
  type: "cross" | "pin"
}

@customElement("lit-select-map-style-dialog")
export class SelectMapStyleDialog extends LitElement {
  @property({ type: String }) selectedLocationMarkerEnum!: string
  @property({ type: String }) selectedMapStyleEnum!: string
  @property({ type: Number }) userLevel!: number

  @state() mapStyles: MapStyle[] = []
  @state() isWhiteBorderSelected = false
  @state() size = "xs"

  static styles = css`
    ${TailwindStyles} ${AppStyles}
  `

  private locationMarkers: Map<string, LocationMarker[]> = new Map()

  get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector("lit-dialog")
  }

  get selectedMapStyle(): MapStyle | undefined {
    return this.mapStyles.find((n) => n.enum === this.selectedMapStyleEnum)
  }

  get selectedLocationMarker(): LocationMarker | undefined {
    for (const markers of this.locationMarkers.values()) {
      const marker = markers.find((marker) => marker.enum === this.selectedLocationMarkerEnum)
      if (marker) {
        return marker
      }
    }
    return undefined
  }

  private cancel() {
    this.dispatchEvent(new CustomEvent("canceled", { bubbles: true, composed: true }))
    this.litDialogElement?.close()
  }

  private async fetchUserMapLocationMarkers(): Promise<LocationMarkerApi[]> {
    const response = await fetch(`/web-api/user/map-location-markers`)
    if (!response.ok) {
      throw new Error(`Error: ${response.status}`)
    }
    return response.json()
  }

  private async fetchUserMapStyles(): Promise<MapStyle[]> {
    const response = await fetch(`/web-api/user/map-styles`)
    if (!response.ok) {
      throw new Error(`Error: ${response.status}`)
    }
    return response.json()
  }

  private getFormattedMapStyleFullUri(fullUri: string): string {
    return fullUri.replace("{x}", "1614").replace("{y}", "1016").replace("{z}", "11")
  }

  private getLocationMarkerImgPath(locationMarker: LocationMarker, isWhiteBorderSelected: boolean) {
    if (!isWhiteBorderSelected) return locationMarker.imgPath
    return locationMarker.imgPath.replace("black-border", "white-border")
  }

  private isMapStyleSelected(mapStyle: MapStyle): boolean {
    return mapStyle.enum === this.selectedMapStyleEnum
  }

  private isMapStyleUnlocked(mapStyle: MapStyle): boolean {
    return mapStyle.user_level_enum <= this.userLevel
  }

  private async submit(): Promise<void> {
    if (!this.selectedMapStyle) {
      Logger.error(`Could not submit because no map style is selected`)
      return
    }
    if (!this.selectedLocationMarker) {
      Logger.error(`Could not submit because no location marker is selected`)
      return
    }

    const selectedLocationMarkerEnum = this.isWhiteBorderSelected
      ? this.selectedLocationMarkerEnum.replace("BLACK_BORDER", "WHITE_BORDER")
      : this.selectedLocationMarkerEnum

    try {
      await fetch(`/web-api/user`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          map_style_enum: this.selectedMapStyle.enum,
          map_location_marker_enum: selectedLocationMarkerEnum,
        }),
      })
      this.dispatchEvent(
        new CustomEvent("submitted", {
          detail: {
            mapLocationMarkerEnum: selectedLocationMarkerEnum,
          },
          bubbles: true,
          composed: true,
        }),
      )
      this.litDialogElement?.close()
    } catch (err) {
      Logger.error(err)
    }
  }

  private selectMapStyle(mapStyle: MapStyle): void {
    this.selectedMapStyleEnum = mapStyle.enum
  }

  private selectLocationMarker(locationMarker: LocationMarker): void {
    this.selectedLocationMarkerEnum = locationMarker.enum
  }

  private updateSize = () => {
    const width = window.innerWidth

    if (width < 640) {
      this.size = "xs"
    } else if (width < 768) {
      this.size = "sm"
    } else if (width < 1024) {
      this.size = "md"
    } else if (width < 1280) {
      this.size = "lg"
    } else {
      this.size = "xl"
    }
  }

  connectedCallback(): void {
    super.connectedCallback()
    this.updateSize()
    window.addEventListener("resize", () => this.updateSize())
  }

  disconnectedCallback() {
    super.disconnectedCallback()
    window.removeEventListener("resize", () => this.updateSize())
  }

  protected firstUpdated(): void {
    if (this.selectedLocationMarkerEnum.includes("WHITE_BORDER")) {
      this.isWhiteBorderSelected = true
      this.selectedLocationMarkerEnum = this.selectedLocationMarkerEnum.replace("WHITE_BORDER", "BLACK_BORDER")
    }
  }

  async open(): Promise<void> {
    try {
      const [locationMarkersApi, mapStylesApi] = await Promise.all([this.fetchUserMapLocationMarkers(), this.fetchUserMapStyles()])

      this.mapStyles = mapStylesApi

      // Populate the locationMarkers map
      this.locationMarkers.clear()
      const typeOrder: Record<string, number> = {
        pin: 1,
        cross: 2,
      }
      const colorOrder: Record<string, number> = {
        blue: 1,
        green: 2,
        yellow: 3,
        orange: 4,
        red: 5,
        purple: 6,
      }
      const locationMarkers = locationMarkersApi
        .filter((n) => n.border_color === "black")
        .map((locationMarkerApi) => ({
          enum: locationMarkerApi.enum,
          imgPath: locationMarkerApi.file_path,
          type: locationMarkerApi.type,
          color: locationMarkerApi.color,
        }))
        .sort((a, b) => {
          if (typeOrder[a.type] !== typeOrder[b.type]) {
            return typeOrder[a.type] - typeOrder[b.type]
          }
          return (colorOrder[a.color] || Infinity) - (colorOrder[b.color] || Infinity)
        })
      locationMarkers.forEach((locationMarker) => {
        if (!this.locationMarkers.has(locationMarker.type)) {
          this.locationMarkers.set(locationMarker.type, [])
        }
        this.locationMarkers.get(locationMarker.type)?.push(locationMarker)
      })
    } catch (err) {
      Logger.error(err)
    }

    this.litDialogElement?.open()
  }

  protected render() {
    return html`
      <lit-dialog label="Map Style" iconPath="/static/img/icon/map-with-marker.svg" modal>
        <div id="content" slot="content" class="flex flex-col gap-2 h-full max-h-[calc(90vh-140px)] select-none">
          <div
            id="options"
            class="flex flex-col gap-2 sm:gap-4 w-full flex-1 overflow-y-auto p-2 sm:p-4 rounded-sm border border-gray-700 bg-gray-50"
          >
            <div class="flex items-center w-full h-6 px-2 border-b border-gray-700">
              <span class="font-heading font-semibold text-sm sm:text-base text-iris-800">Map Style</span>
            </div>
            <div class="grid grid-cols-2 min-[440px]:grid-cols-3 gap-2 sm:gap-4">
              ${this.mapStyles.map((mapStyle) => {
                if (this.isMapStyleUnlocked(mapStyle)) {
                  return html`<lit-button-checkbox
                    label="${mapStyle.short_name}"
                    size="${["xs"].includes(this.size) ? "sm" : "md"}"
                    class="w-full"
                    .isDisabled="${this.selectedMapStyle?.enum === mapStyle.enum}"
                    .isSelected="${this.isMapStyleSelected(mapStyle)}"
                    @clicked="${() => {
                      if (this.isMapStyleUnlocked(mapStyle)) {
                        this.selectMapStyle(mapStyle)
                      }
                    }}"
                  ></lit-button-checkbox>`
                }

                return html`
                    <div class="flex justify-center items-center gap-2 h-8 sm:h-10 rounded-sm border border-gray-700 bg-gray-400">
                      <img src="/static/img/icon/lock-gold.svg" class="w-4 sm:w-5" />
                        <span class="font-heading font-semibold text-sm sm:text-base text-gray-0 text-stroke-2 text-stroke-700">Level ${mapStyle.user_level_enum}</span>
                      </div>
                    </div>
                 `
              })}
            </div>

            <div class="flex flex-col gap-2">
              <div class="flex items-center w-full h-6 px-2 border-b border-gray-700">
                <span class="font-heading font-semibold text-sm sm:text-base text-iris-800">Location Marker</span>
              </div>

              ${Array.from(this.locationMarkers).map(
                ([, locationMarkers]) => html`
                  <div class="grid grid-cols-6 gap-2 sm:gap-4 max-w-96">
                    ${locationMarkers.map(
                      (locationMarker) => html`
                        <div
                          class="flex justify-center items-center w-10 sm:w-12 h-10 sm:h-12 rounded-sm ${this.selectedLocationMarkerEnum ===
                          locationMarker.enum
                            ? "bg-iris-400"
                            : "hover:bg-iris-200 cursor-pointer"}"
                          @click="${() => {
                            if (this.selectedLocationMarkerEnum === locationMarker.imgPath) return
                            this.selectLocationMarker(locationMarker)
                          }}"
                        >
                          <img src="${locationMarker.imgPath}" class="w-8 sm:w-10 h-8 sm:h-10" alt="${locationMarker.enum}" />
                        </div>
                      `,
                    )}
                  </div>
                `,
              )}

              <lit-checkbox
                label="White Border"
                size=${["xs"].includes(this.size) ? "xs" : "sm"}
                class="mt-2"
                .isSelected=${this.isWhiteBorderSelected}
                @toggled="${(e: CustomEvent) => {
                  this.isWhiteBorderSelected = e.detail.isSelected
                }}"
              ></lit-checkbox>
            </div>
          </div>

          <div id="preview" class="w-full h-32 sm:h-48 shrink-0 rounded-sm relative overflow-hidden border-4 border-gray-0">
            <div class="flex justify-center items-center w-20 h-8 rounded-br-sm absolute top-0 left-0 z-10 bg-gray-0">
              <span class="font-heading font-medium text-gray-700">Preview</span>
            </div>

            <div class="w-full relative bg-gray-600">
              ${this.selectedMapStyle && this.mapStyles[2]
                ? html`<img src="${this.getFormattedMapStyleFullUri(this.mapStyles[2].full_uri)}" class="opacity-0" draggable="false" />`
                : nothing}
              ${this.selectedMapStyle
                ? html`<img
                    src="${this.getFormattedMapStyleFullUri(this.selectedMapStyle.full_uri)}"
                    class="absolute top-0 left-1/2 transition -translate-x-1/2"
                    draggable="false"
                  />`
                : nothing}
            </div>
            ${this.selectedLocationMarker
              ? html`
                  <img
                    src="${this.getLocationMarkerImgPath(this.selectedLocationMarker, this.isWhiteBorderSelected)}"
                    class="absolute top-1/2 left-1/2 transition -translate-x-1/2 -translate-y-1/2 h-12"
                  />
                `
              : nothing}
          </div>
        </div>

        <div slot="footer" class="flex justify-end items-center gap-4">
          <lit-button
            label="CANCEL"
            bgColorClass="bg-gray-500"
            size="${["xs"].includes(this.size) ? "sm" : "sm"}"
            @clicked="${this.cancel}"
          ></lit-button>
          <lit-button label="CHANGE" size="${["xs"].includes(this.size) ? "sm" : "sm"}" @clicked="${this.submit}"></lit-button>
        </div>
      </lit-dialog>
    `
  }
}
