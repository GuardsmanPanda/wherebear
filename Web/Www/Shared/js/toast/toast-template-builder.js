/**
 * A builder to dynamically generate and customize toast notifications.
 * It provides methods to set a title, content, icon, and a close button with a callback.
 */
export class ToastTemplateBuilder {
  #closeButtonId = null
  #closeButtonCallback = null
  #title = ""
  #hasCloseButton = false
  #iconPath = ""
  #content = ""

  /**
   * Gets the CSS classes for the content div.
   * @returns {string} The CSS classes for the content div.
   */
  get contentClasses() {
    const baseClasses = "p-2 border-gray-700 bg-gray-50"

    if (!this.#title && !this.#hasCloseButton) {
      return `${baseClasses} ${this.#iconPath ? "rounded-r-sm border-t border-r border-b" : "rounded-sm border"}`
    }

    if (this.#title || this.#hasCloseButton) {
      return `${baseClasses} ${this.#iconPath ? "rounded-br-sm border-r border-b" : "rounded-b-sm border-r border-b border-l"}`
    }

    return baseClasses
  }

  /**
   * Observes DOM mutations to attach a click event to the close button when it's added to the DOM.
   * @private
   */
  #observeMutation() {
    const observer = new MutationObserver((mutationsList, observer) => {
      const closeButton = document.getElementById(this.#closeButtonId)
      if (closeButton) {
        closeButton.addEventListener("click", () => {
          if (this.#closeButtonCallback) {
            this.#closeButtonCallback()
          }
        })
        observer.disconnect() // Stop observing once the button is found and the event is attached
      }
    })

    // Observe the body for added child nodes (new DOM elements)
    observer.observe(document.body, { childList: true, subtree: true })
  }

  /**
   * Sets the title of the toast.
   * @param {string} title - The title of the toast.
   * @returns {ToastTemplateBuilder} The instance of the builder for chaining.
   */
  withTitle(title) {
    if (typeof title !== "string") {
      throw new Error("Title must be a string")
    }
    this.#title = title
    return this
  }

  /**
   * Adds a close button with a callback function.
   * @param {function} callback - The callback function to execute when the close button is clicked.
   * @returns {ToastTemplateBuilder} The instance of the builder for chaining.
   */
  withCloseButton(callback) {
    if (typeof callback !== "function") {
      throw new Error("Callback must be a function")
    }
    this.#hasCloseButton = true
    this.#closeButtonId = crypto.randomUUID()
    this.#closeButtonCallback = callback
    return this
  }

  /**
   * Sets the content of the toast.
   * @param {string} content - The content of the toast.
   * @returns {ToastTemplateBuilder} The instance of the builder for chaining.
   */
  withContent(content) {
    if (typeof content !== "string") {
      throw new Error("Content must be a string")
    }
    this.#content = content
    return this
  }

  /**
   * Sets the icon path of the toast.
   * @param {string} iconPath - The path to the icon.
   * @returns {ToastTemplateBuilder} The instance of the builder for chaining.
   */
  withIconPath(iconPath) {
    if (typeof iconPath !== "string") {
      throw new Error("Icon path must be a string")
    }
    this.#iconPath = iconPath
    return this
  }

  /**
   * Builds the toast template and returns it as an HTML string.
   * @returns {string} The HTML string of the toast template.
   */
  build() {
    this.#observeMutation()

    return `
      <div class="flex w-full rounded-sm" style="box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.15);">
        ${
          this.#iconPath
            ? `
        <div class="flex justify-center items-center flex-grow w-24 rounded-l-sm border border-r-0 border-gray-700 bg-gray-600">
          <img src="${this.#iconPath}" class="w-16" />
        </div>
        `
            : ""
        }
        <div class="flex flex-col w-full h-full rounded-r-sm">
          ${
            this.#title || this.#hasCloseButton
              ? `
            <div class="flex shrink-0 ${this.#title ? "justify-between" : "justify-end"} items-center gap-4 h-10 pl-2 pr-1 rounded-tr-sm border-t border-r ${!this.#iconPath ? "border-l rounded-tl-sm" : ""} ${!this.#content ? "border-b rounded-b-sm" : ""} border-gray-700 bg-gray-500">
              ${this.#title ? `<span class="font-heading font-semibold text-gray-50">${this.#title}</span>` : ""}
              ${
                this.#hasCloseButton
                  ? `
                <button id="${this.#closeButtonId}"
                  class="toast-close-btn flex justify-center items-center w-8 h-8 rounded-lg rounded-tr-xl bg-red-500 hover:bg-red-600 border border-b-2 active:border-b border-gray-700"
                  style="box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.3), inset 0 -1px 1px rgba(0, 0, 0, 0.3);">
                  <img src="/static/img/icon/cross.svg" width="20" height="20" draggable=false />
                </button>
              `
                  : ""
              }
            </div>
          `
              : ""
          }

          ${
            this.#content
              ? `
            <div class="${this.contentClasses}">
              ${this.#content}
            </div>
          `
              : ""
          }
        </div>
      </div>
    `
  }
}
