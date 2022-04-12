/**
 * Initialize a websocket client
 */
function clientSocket(config = {}) {
    let route = config.route || "127.0.0.1";
    let port = config.port || "3280";
    window.WevSocket = window.WebSocket || window.MozSocket;
    return new WebSocket("ws://" + route + ":" + port);
}