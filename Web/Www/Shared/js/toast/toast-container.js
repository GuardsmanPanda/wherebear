/**
 * Class representing a container for toasts.
 * Manages the creation, display, and removal of toast notifications within a specified HTML container element.
 */
class ToastContainer {
  static DEFAULT_ANCHOR = 'bottom-right';
  static DEFAULT_CONTAINER_MARGIN_PX = 4;
  static DEFAULT_SLIDE_DURATION_MS = 1000;
  static DEFAULT_TOAST_GAP_PX = 8;
  static VALID_ANCHORS = ['top-right', 'top-left', 'bottom-right', 'bottom-left'];

  #anchor;
  #containerHtmlElement;
  #containerMarginPx;
  #isQueuePaused;
  #isQueueProcessing = false;

  /**
   * @type {Array<{id: string, element: HTMLElement}>} The toasts currently in the container.
   */
  #toasts = [];
  #toastGapPx;
  #toastQueue = [];
  #toastClasses;

  /**
   * Creates an instance of ToastContainer.
   * @param {HTMLElement} containerHtmlElement - The HTML element that will contain the toasts.
   * @param {Object} opts - Options for configuring the toast container.
   * @param {string} [opts.anchor='bottom-right'] - The anchor position for the toasts.
   * @param {number} [opts.toastGapPx=ToastContainer.DEFAULT_TOAST_GAP_PX] - The gap between toasts in pixels.
   * @param {number} [opts.containerMarginPx=ToastContainer.DEFAULT_CONTAINER_MARGIN_PX] - The margin of the container in pixels.
   * @param {Array<string>} [opts.toastClasses] - The classes to apply to the toast elements.
   */
  constructor(containerHtmlElement, opts) {
    this.#isQueuePaused = true;

    containerHtmlElement.style.position = 'fixed';
    this.#containerHtmlElement = containerHtmlElement;

    if (opts?.anchor && !ToastContainer.VALID_ANCHORS.includes(opts.anchor)) {
      throw new Error(`Invalid anchor value. Valid options are: ${ToastContainer.VALID_ANCHORS.join(', ')}`);
    }

    this.#anchor = opts?.anchor ?? ToastContainer.DEFAULT_ANCHOR;
    this.#toastGapPx = opts?.toastGapPx ?? ToastContainer.DEFAULT_TOAST_GAP_PX;
    this.#containerMarginPx = opts?.containerMarginPx ?? ToastContainer.DEFAULT_CONTAINER_MARGIN_PX;
    this.#toastClasses = opts?.toastClasses ?? [];

    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState !== 'visible') {
        this.#pauseQueue();
      } else {
        this.#resumeQueue();
      }
    });

    if (document.visibilityState === 'visible') {
      this.#resumeQueue();
    }
  }

  /**
   * Creates a toast element from the provided template.
   * @param {string} template - The HTML template for the toast.
   * @returns {HTMLElement} The created toast element.
   * @private
   */
  #createToastElement(template) {
    // Creater the wrapper element for the toast
    const toastWrapperEl = document.createElement('div');
    if (this.#toastClasses.length > 0) {
      toastWrapperEl.classList.add(...this.#toastClasses);
    }

    // Set up the base styles
    Object.assign(toastWrapperEl.style, {
      position: 'absolute',
      zIndex: '100',
      paddingLeft: `${this.#containerMarginPx}px`,
      paddingRight: `${this.#containerMarginPx}px`
    });

    // Set alignment based on the anchor
    if (this.#anchor.includes('top')) {
      toastWrapperEl.style.top = `${this.#containerMarginPx}px`;
    } else if (this.#anchor.includes('bottom')) {
      toastWrapperEl.style.bottom = `${this.#containerMarginPx}px`;
    }
    if (this.#anchor.includes('right')) {
      toastWrapperEl.style.right = `0px`;
    } else if (this.#anchor.includes('left')) {
      toastWrapperEl.style.left = `0px`;
    }

    // Create the inner element for the toast content
    const toastContentElement = document.createElement('div');
    toastContentElement.style.width = '100%';
    toastContentElement.innerHTML = template;

    toastWrapperEl.appendChild(toastContentElement);

    return toastWrapperEl;
  }

  /**
   * Pauses the processing of the toast queue.
   * Prevents new toasts from being processed until the queue is resumed.
   * @private
   */
  #pauseQueue() {
    this.#isQueuePaused = true;
  }

  /**
   * Processes the toast queue.
   * If the queue is paused or already processing, it does nothing.
   * @private
   * @async
   */
  async #processToastQueue() {
    if (this.#isQueueProcessing) return;
    if (this.#isQueuePaused) return;

    this.#isQueueProcessing = true;

    while (this.#toastQueue.length > 0) {
      const nextToast = this.#toastQueue.shift();
      await nextToast();
      await new Promise(resolve => setTimeout(resolve, 1000));
    }

    this.#isQueueProcessing = false;
  }

  /**
   * Resumes the processing of the toast queue.
   * If the queue was paused, it will start processing toasts again.
   * @private
   */
  #resumeQueue() {
    this.#isQueuePaused = false;
    this.#processToastQueue();
  }

  /**
   * Slides the toasts in the specified direction by the specified offset.
   * @param {Array<Object>} toasts - The toasts to slide.
   * @param {string} direction - The direction to slide ('up' or 'down').
   * @param {number} offsetPx - The offset in pixels.
   * @private
   */
  #slideToasts(toasts, direction, offsetPx) {
    toasts.forEach((toast) => {
      const toastWrapperEl = toast.element;
      const currentTransform = getComputedStyle(toastWrapperEl).transform;
      const currentTranslateY = currentTransform.includes('matrix')
        ? parseFloat(currentTransform.split(', ')[5]) || 0
        : 0;

      const newTranslateY = direction === 'up'
        ? currentTranslateY - offsetPx
        : currentTranslateY + offsetPx;

      toastWrapperEl.style.transition = `transform ${ToastContainer.DEFAULT_SLIDE_DURATION_MS}ms ease`;
      toastWrapperEl.style.transform = `translateY(${newTranslateY}px)`;
    });
  }

  /**
   * Toggles the clickability of the provided toasts.
   * @param {Array<Object>} toasts - An array of toast objects.
   * @param {boolean} isClickable - A boolean indicating whether the toasts should be clickable (true) or not (false).
   * @private
   */
  #toggleToastsClickability(toasts, isClickable) {
    toasts.forEach((toast) => {
      toast.element.style.pointerEvents = isClickable ? 'auto' : 'none';
    });
  }


  /**
   * Adds a toast to the container.
   * @param {Object} toast - The toast to add.
   * @param {Object} opts - Options for configuring the toast.
   * @param {number} [opts.durationSec] - The duration in seconds for which the toast should be displayed.
   */
  addToast(toast, opts) {
    const toastWrapperEl = this.#createToastElement(toast.buildTemplate());

    this.#toastQueue.push(() => {
      this.#containerHtmlElement.appendChild(toastWrapperEl);

      // Needs to be set after appending to the DOM or offsetHeight will be 0
      toastWrapperEl.style.transform = `translateY(${this.#anchor.includes('top') ? '-' : ''}${toastWrapperEl.offsetHeight + this.#toastGapPx}px)`;

      this.#toasts.push({
        id: toast.id,
        element: toastWrapperEl
      });

      this.#toggleToastsClickability(this.#toasts, false);
      setTimeout(() => {
        this.#toggleToastsClickability(this.#toasts, true);
      }, ToastContainer.DEFAULT_SLIDE_DURATION_MS);

      setTimeout(() => {
        this.#slideToasts(
          this.#toasts,
          this.#anchor.includes('bottom') ? 'up' : 'down',
          toastWrapperEl.offsetHeight + this.#toastGapPx
        );
      }, 10);

      if (opts?.durationSec) {
        setTimeout(() => {
          this.removeToast(toast);
        }, opts.durationSec * 1000);
      }
    });
    this.#processToastQueue();

  }

  /**
   * Removes a toast from the container.
   * @param {Object} toast - The toast to remove.
   */
  removeToast(toast) {
    const toastWithElement = this.#toasts.find(n => n.id === toast.id);
    if (!toastWithElement) return;

    const fadeOutDurationMs = 300;
    toastWithElement.element.style.transition = `opacity ${fadeOutDurationMs}ms ease`;
    toastWithElement.element.style.opacity = 0;

    setTimeout(() => {
      this.#containerHtmlElement.removeChild(toastWithElement.element);
    }, fadeOutDurationMs);

    setTimeout(() => {
      const removedToastIndex = this.#toasts.indexOf(toastWithElement);
      const toastsBefore = this.#toasts.filter((n, index) => index < removedToastIndex);

      this.#slideToasts(
        toastsBefore,
        this.#anchor.includes('bottom') ? 'down' : 'up',
        toastWithElement.element.offsetHeight + this.#toastGapPx
      );

      this.#toasts = this.#toasts.filter(n => n.id !== toast.id);
    }, 100);
  }
}
window.ToastContainer = ToastContainer;