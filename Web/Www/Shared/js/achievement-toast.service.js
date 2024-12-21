/**
 * Class representing a service for managing achievement notifications.
 */
class AchievementToastService {
  /**
   * @type {AchievementToastService}
   * @private
   * @static
   */
  static #instance;

  /**
   * @type {Pusher.Channel}
   * @private
   */
  #webSocketChannel;

  /**
   * @type {ToastContainer}
   * @private
   */
  #toastContainer;

  /**
   * @type {string}
   * @private
   */
  #userId;

  /**
   * @type {WebSocketClient}
   * @private
   */
  #webSocketClient;

  /**
   * Creates an instance of AchievementToastService.
   * @param {WebSocketClient} webSocketClient - The WebSocket client instance.
   * @param {string} userId - The user ID.
   * @param {ToastContainer} toastContainer - The toast container instance.
   */
  constructor(webSocketClient, userId, toastContainer) {
    this.#toastContainer = toastContainer;
    this.#userId = userId;
    this.#webSocketClient = webSocketClient;

    this.#subscribeToWebSocketChannel();
    this.#listenToAchievementCompleted();
  }

  /**
   * Initializes the singleton instance of AchievementToastService.
   * @param {WebSocketClient} webSocketClient - The WebSocket client instance.
   * @param {string} userId - The user ID.
   * @param {ToastContainer} toastContainer - The toast container instance.
   * @returns {AchievementToastService} The singleton instance of AchievementToastService.
   */
  static init(webSocketClient, userId, toastContainer) {
    if (!AchievementToastService.#instance) {
      AchievementToastService.#instance = new AchievementToastService(webSocketClient, userId, toastContainer);
    }
    return AchievementToastService.#instance;
  }

  /**
   * Creates a WebSocket channel for the user.
   * @private
   */
  #subscribeToWebSocketChannel() {
    this.#webSocketChannel = this.#webSocketClient.subscribeToChannel(`user.${this.#userId}`);
  }

  /**
   * Listens for achievement completion events on the WebSocket channel.
   * @private
   */
  #listenToAchievementCompleted() {
    this.#webSocketChannel.bind('achievement.complete', (data) => {
      this.#handleAchievementComplete(data);
    });
  }

  /**
   * Handles the achievement completion event by showing a toast notification.
   * @param {Object} data - The event data.
   * @param {string} data.title - The title of the achievement.
   * @param {string} data.description - The description of the achievement.
   * @private
   */
  #handleAchievementComplete(data) {
    const toast = new AchievementToast(data.title, data.description);
    toast.on('cancel', () => {
      this.#toastContainer.removeToast(toast);
    });
    this.#toastContainer.addToast(toast);
  }
}
window.AchievementToastService = AchievementToastService;