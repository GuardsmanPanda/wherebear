/**
 * Class representing a WebSocket client using Pusher.
 */
class WebSocketClient {
  /**
   * The singleton instance of WebSocketClient.
   * @type {WebSocketClient}
   * @private
   * @static
   */
  static #instance;

  #client;

  /**
   * Creates an instance of WebSocketClient.
   * @param {string} id - The Pusher app key.
   * @param {Object} config - Configuration options for the Pusher client.
   * @param {string} config.cluster - The Pusher cluster.
   * @param {string} config.wsHost - The WebSocket host.
   * @param {number} config.wsPort - The WebSocket port.
   * @param {number} config.wssPort - The WebSocket Secure (WSS) port.
   * @param {Array<string>} config.enabledTransports - The enabled transports for the Pusher client.
   * @private
   */
  constructor(id, config) {
    if (WebSocketClient.#instance) {
      throw new Error('Instance of WebSocketClient already exists. Use WebSocketClient.getInstance() to get the singleton instance.');
    }

    this.#client = new Pusher(id, {
      cluster: config.cluster,
      wsHost: config.wsHost,
      wsPort: config.wsPort,
      wssPort: config.wssPort,
      enabledTransports: config.enabledTransports
    });

    this.#client.bind('error', (err) => {
      console.error('Pusher error', err);
    });

    this.#client.bind('disconnected', (err) => {
      console.error('Pusher disconnected', err);
    });

    WebSocketClient.#instance = this;
  }

  /**
   * Initializes the singleton instance of WebSocketClient.
   * If the instance does not already exist, it creates a new one.
   * @returns {WebSocketClient} The singleton instance of WebSocketClient.
   */
  static init() {
    if (!WebSocketClient.#instance) {
      WebSocketClient.#instance = new WebSocketClient('6csm0edgczin2onq92lm', {
        cluster: 'eu',
        wsHost: 'socket.wherebear.fun',
        wsPort: 80,
        wssPort: 443,
        enabledTransports: ['ws', 'wss']
      });
    }
    return WebSocketClient.#instance;
  }

  /**
   * Subscribes to a channel with the given name.
   * @param {string} name - The name of the channel to subscribe to.
   * @returns {Pusher.Channel} The subscribed channel.
   */
  subscribeToChannel(name) {
    return this.#client.subscribe(name);
  }
}

window.WebSocketClient = WebSocketClient;