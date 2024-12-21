/**
 * Abstract class for creating a toast.
 */
export class ToastBase {
  /** @type {string} */
  #id;

  /**
   * @type {Object<string, Array<function>>}
   * An object that holds arrays of event handler functions for different event types.
   * Keys are event types (e.g., 'cancel'), and values are arrays of callback functions to be executed for those events.
   * @private
   */
  #eventHandlers = {};

  constructor() {
    this.#id = crypto.randomUUID();
  }

  get id() {
    return this.#id;
  }

  /**
   * Handles an event by executing all associated callbacks.
   * @param {string} eventType - The type of the event.
   * @protected
   */
  _handleEvent(eventType) {
    if (this.#eventHandlers[eventType]) {
      this.#eventHandlers[eventType].forEach(callback => callback());
    }
  }

  /**
   * Builds the HTML template for the toast.
   * Must be implemented by subclasses.
   * @returns {string} The HTML template for the toast.
   * @throws {Error} If the method is not implemented by a subclass.
   */
  buildTemplate() {
    throw new Error("buildTemplate method must be implemented by subclasses");
  }

  /**
   * Registers an event handler for a specific event type.
   * @param {string} eventType - The type of the event (e.g., 'cancel').
   * @param {function} callback - The callback function to be executed when the event occurs.
   */
  on(eventType, callback) {
    if (!this.#eventHandlers[eventType]) {
      this.#eventHandlers[eventType] = [];
    }
    this.#eventHandlers[eventType].push(callback);
  }
}