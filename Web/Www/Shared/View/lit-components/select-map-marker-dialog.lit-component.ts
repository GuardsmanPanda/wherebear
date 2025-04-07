import { LitElement, css, html } from "lit"
import { customElement, state } from "lit/decorators.js"
import { classMap } from "lit/directives/class-map.js"

// @ts-ignore
import { TailwindStyles } from "../../../../../public/static/dist/lit-tailwind-css"
import { Dialog } from "./dialog.lit-component"

interface MapMarker {
  enum: string
  file_path: string
  grouping?: string
  map_anchor: "center" | "bottom"
}

@customElement("lit-select-map-marker-dialog")
export class SelectMapMarkerDialog extends LitElement {
  @state() errorMsg?: string
  @state() mapMarkers: MapMarker[] = []

  static styles = css`
    ${TailwindStyles}
  `

  get litDialogElement(): Dialog | null {
    return this.renderRoot.querySelector("lit-dialog")
  }

  private async fetchUserMapMarkers(): Promise<MapMarker[]> {
    const response = await fetch(`/web-api/user/map-markers`)
    if (!response.ok) {
      throw new Error(`Error: ${response.status}`)
    }
    const data: MapMarker[] = await response.json()
    return data
  }

  private groupMapMarkersByGrouping(): Record<string, MapMarker[]> {
    return this.mapMarkers.reduce(
      (groups, mapMarker) => {
        const group = mapMarker.grouping || "Miscellaneous"
        if (!groups[group]) {
          groups[group] = []
        }
        groups[group].push(mapMarker)
        return groups
      },
      {} as Record<string, MapMarker[]>,
    )
  }

  private async selectMapMarker(mapMarkerEnum: string): Promise<void> {
    try {
      await fetch(`/web-api/user`, {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          map_marker_enum: mapMarkerEnum,
        }),
      })

      this.litDialogElement?.close()
    } catch (err) {
      this.errorMsg = "Something went wrong"
    }
  }

  async open(): Promise<void> {
    try {
      this.mapMarkers = await this.fetchUserMapMarkers()
    } catch (err) {
      this.errorMsg = "Something went wrong"
    }
    this.litDialogElement?.open()
  }

  protected render() {
    return html`
      <lit-dialog label="Select Map Marker" modal @closed="${() => this.closeSelectMapMarkerDialog()}">
        <div slot="content" class="mx-2">
          ${this.errorMsg
            ? html`<div class="text-poppy-700">${this.errorMsg}</div>`
            : Object.entries(this.groupMapMarkersByGrouping()).map(
                ([group, mapMarkers]) => html`
                  <div class="flex flex-col gap-2 group">
                    <h3 class="font-heading font-semibold text-xl text-gray-700 border-b-2 border-gray-700">${group}</h3>
                    <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-9 gap-2 mb-4">
                      ${mapMarkers.map(
                        (mapMarker) => html`
                          <div
                            class="flex justify-center max-w-14 max-h-14 ${classMap({
                              "items-center": mapMarker.map_anchor === "center",
                              "items-end": mapMarker.map_anchor === "bottom",
                            })}"
                          >
                            <img
                              src="${mapMarker.file_path}"
                              draggable="false"
                              class="max-w-full max-h-full object-contain drop-shadow-sm hover:scale-110 transition-transform duration-75 cursor-pointer"
                              @click="${() => this.selectMapMarker(mapMarker.enum)}"
                            />
                          </div>
                        `,
                      )}
                    </div>
                  </div>
                `,
              )}
        </div>
      </lit-dialog>
    `
  }

  // Method to close the dialog (optional: may be used for cleanup)
  closeSelectMapMarkerDialog() {
    this.litDialogElement?.close()
  }
}
