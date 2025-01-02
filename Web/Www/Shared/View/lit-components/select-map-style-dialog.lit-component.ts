import { LitElement, css, html } from "lit"
import { customElement, property, state } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Dialog } from "./dialog.lit-component"

/**
 * Interface for the map style object.
 */
interface MapStyle {
  enum: string
  name: string
  full_uri: string
  user_level_enum: number
}

@customElement("lit-select-map-style-dialog")
export class SelectMapStyleDialog extends LitElement {
  @property({ type: String }) selectedMapStyleEnum!: string
  @property({ type: Number }) userLevel!: number

  @state() mapStyles: MapStyle[] = []

  static styles = css`
    ${TailwindStyles}
  `

  get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector("lit-dialog")
  }

  private async fetchUserMapStyles(): Promise<MapStyle[]> {
    const response = await fetch(`/web-api/user/map-styles`)
    if (!response.ok) {
      throw new Error(`Error: ${response.status}`)
    }
    const data: MapStyle[] = await response.json()
    return data
  }

  private getFormattedMapStyleFullUri(fullUri: string): string {
    return fullUri.replace("{x}", "1614").replace("{y}", "1016").replace("{z}", "11")
  }

  private getMapClasses(mapStyle: MapStyle): Record<string, boolean> {
    return {
      "border-pistachio-400": this.isMapStyleUnlocked(mapStyle) && this.isMapStyleSelected(mapStyle),
      "border-gray-50": this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
      "hover:border-gray-0": this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
      "border-gray-700": !this.isMapStyleUnlocked(mapStyle),
      "cursor-pointer": this.isMapStyleUnlocked(mapStyle),
      "bg-black": mapStyle.enum !== "OSM",
      "bg-gray-600": mapStyle.enum === "OSM",
    }
  }

  private getImgClasses(mapStyle: MapStyle): Record<string, boolean> {
    return {
      "opacity-30": !this.isMapStyleUnlocked(mapStyle),
      "opacity-90": this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
      "group-hover:opacity-100": this.isMapStyleUnlocked(mapStyle) && !this.isMapStyleSelected(mapStyle),
    }
  }

  private getMapStyleNameBackroundClasses(mapStyle: MapStyle): Record<string, boolean> {
    return {
      "bg-pistachio-400": this.isMapStyleSelected(mapStyle),
      "bg-gray-50": !this.isMapStyleSelected(mapStyle),
      "group-hover:bg-gray-0": !this.isMapStyleSelected(mapStyle),
    }
  }

  private isMapStyleSelected(mapStyle: MapStyle): boolean {
    return mapStyle.enum === this.selectedMapStyleEnum
  }

  private isMapStyleUnlocked(mapStyle: MapStyle): boolean {
    return mapStyle.user_level_enum <= this.userLevel
  }

  private async selectMapStyle(mapStyleEnum: string): Promise<void> {
    try {
      await fetch(`/web-api/user`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          map_style_enum: mapStyleEnum,
        }),
      })

      setTimeout(() => {
        this.litDialogElement?.close()
      }, 500)
    } catch (err) {
      console.error(err)
    }
  }

  closeSelectMapStyleDialog() {
    this.litDialogElement?.close()
  }

  async open(): Promise<void> {
    try {
      this.mapStyles = await this.fetchUserMapStyles()
    } catch (err) {
      console.error(err)
    }
    this.litDialogElement?.open()
  }

  protected render() {
    return html`
      <lit-dialog label="Select Map Style" modal @closed="${() => this.closeSelectMapStyleDialog()}">
        <div slot="content" class="select-none">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            ${this.mapStyles.map(
              (mapStyle) => html`
                <div class="rounded border ${this.isMapStyleSelected(mapStyle) ? "border-green-700" : ""}">
                  <div
                    class="flex flex-col w-full relative rounded border-4 group transition-all duration-200 ${classMap(this.getMapClasses(mapStyle))}"
                    @click="${() => {
                      if (this.isMapStyleUnlocked(mapStyle)) this.selectMapStyle(mapStyle.enum)
                    }}"
                  >
                    ${this.isMapStyleUnlocked(mapStyle)
                      ? html` <div
                            class="absolute top-0 left-0 z-10 h-6 rounded-br bg-gray-0 pl-1 pr-2 transition-all duration-200 ${classMap(
                              this.getMapStyleNameBackroundClasses(mapStyle),
                            )}"
                          >
                            <span class="relative bottom-0.5 font-heading font-semibold text-lg text-gray-800">${mapStyle.name}</span>
                          </div>

                          <div class="${this.isMapStyleSelected(mapStyle) ? "" : "hidden group-hover:block"}">
                            <div
                              class="absolute top-1 right-1 z-10 w-5 h-1.5 ${this.isMapStyleSelected(mapStyle) ? "bg-pistachio-400" : "bg-gray-0"}"
                            ></div>
                            <div
                              class="absolute top-1 right-1 z-10 w-1.5 h-5 ${this.isMapStyleSelected(mapStyle) ? "bg-pistachio-400" : "bg-gray-0"}"
                            ></div>
                            <div
                              class="absolute bottom-1 left-1 z-10 w-5 h-1.5 ${this.isMapStyleSelected(mapStyle) ? "bg-pistachio-400" : "bg-gray-0"}"
                            ></div>
                            <div
                              class="absolute bottom-1 left-1 z-10 w-1.5 h-5 ${this.isMapStyleSelected(mapStyle) ? "bg-pistachio-400" : "bg-gray-0"}"
                            ></div>
                            <div
                              class="absolute bottom-1 right-1 z-10 w-5 h-1.5 ${this.isMapStyleSelected(mapStyle) ? "bg-pistachio-400" : "bg-gray-0"}"
                            ></div>
                            <div
                              class="absolute bottom-1 right-1 z-10 w-1.5 h-5 ${this.isMapStyleSelected(mapStyle) ? "bg-pistachio-400" : "bg-gray-0"}"
                            ></div>
                          </div>`
                      : html` <div class="flex flex-col justify-center items-center gap-2 absolute inset-0 z-50">
                          <span class="font-heading font-semibold text-xl text-gray-100">${mapStyle.name}</span>
                          <img src="/static/img/icon/lock.svg" class="w-6" />
                          <span class="font-heading font-semibold text-xl text-gray-100">Level ${mapStyle.user_level_enum}</span>
                        </div>`}

                    <img
                      src="${this.getFormattedMapStyleFullUri(mapStyle.full_uri)}"
                      class="h-32 w-96 object-none transition-opacity duration-200 ${classMap(this.getImgClasses(mapStyle))}"
                      draggable="false"
                    />
                  </div>
                </div>
              `,
            )}
          </div>
        </div>
      </lit-dialog>
    `
  }
}
