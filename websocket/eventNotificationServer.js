var WebSocketServer = require("websocket").server;
var http = require("http");
var htmlEntity = require("html-entities");

var PORT = 3280;

// List of currently connected clients (users)
var clients = [];

// Create http server
var server = http.createServer();

server.listen(PORT, function() {
    console.log("Server is listening on PORT:" + PORT);
});

//Create the websocket server here
wsServer = new WebSocketServer({
    httpServer: server
});


/**
 * The Websocket server
 */
wsServer.on("request", function(request) {
    var connection = request.accept(null, request.origin);

    //Pass each connection instance to each client
    var index = clients.push(connection) - 1;
    console.log('Client', index, "connected");

    //Send message to all clients connected
    connection.on("message", function(message) {
        // console.log("message");
        var utf8Data = JSON.parse(message.utf8Data);

        if (message.type === 'utf8') {
            //Prepare the json data to be sent to all clients that are connected
            var obj = JSON.stringify({
                eventName: htmlEntity.encode(utf8Data.eventName),
                eventMessage: htmlEntity.encode(utf8Data.eventMessage)
            });

            //sent them to all the client
            for (let i = 0; i < clients.length; i++) {
                clients[i].sendUTF(obj);
            }
        }
    });

    //when connection clients is closed
    connection.on("close", function(connection) {
        clients.splice(index, 1);
        console.log("Client", index, "was disconnected");
    });

});