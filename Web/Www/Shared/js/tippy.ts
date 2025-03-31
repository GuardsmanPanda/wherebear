import tippy from "tippy.js"

/**
 * The Tippy class provides a wrapper around the Tippy.js library for initializing tooltips.
 */
export class Tippy {
  /**
   * Initializes Tippy.js with default settings and applies tooltips to elements with `data-tippy-content`.
   */
  static init() {
    tippy.setDefaultProps({
      delay: [250, 150],
      hideOnClick: true,
    })
    tippy("[data-tippy-content]")
  }
}
