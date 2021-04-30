var WebSocketServer = require("websocket").server;
var http = require("http");
var htmlEntity = require("html-entities");
var uniqueId = require('uniqid');
var mysql = require('mysql');
var PORT = 3281;

//Database connection
var db = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "telixnet"
});

// connect to database
db.connect();

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

    var connection_id;
    var from;

    //Pass each connection instance to each client
    var index = clients.push(connection) - 1;
    console.log('Client', index, "connected");

    //Send message to all clients connected
    connection.on("message", function(message) {
        // console.log("message");
        var utf8Data = JSON.parse(message.utf8Data);

        if (message.type === 'utf8') {
            if (utf8Data.type == "info") {
                connection_id = "connection__" + uniqueId();
                from = utf8Data.data.from;

                clients.push({
                    "connection": connection,
                    "connection_id": connection_id,
                    "from": from,
                    "user_id": utf8Data.data.user_id,
                });

                console.log("Connection", {
                    "connection_id": connection_id,
                    "from": from,
                    "user_id": utf8Data.data.user_id,
                });
            } else if (utf8Data.type == "chatMessage") {
                retrieveLatestChatMessage();
            }
        }
    });

    //when connection clients is closed
    connection.on("close", function(connection) {});


    /**
     * Retrieve the latest message
     */
    function retrieveLatestChatMessage() {
        var statement = `
            SELECT requests.from, requests.user_id, requests.reply, requests.created_at, clients.name
            FROM requests
            LEFT JOIN clients ON requests.from = clients.id
            ORDER BY created_at DESC
            LIMIT 1
        `;

        // Query from the database
        db.query(statement, (error, results) => {
            if (error) console.log(error);

            if (results) {
                // Broadcast the messages to all users in the same room
                console.log(results);
                clients.forEach(item => {
                    if (from == item.from) {
                        item.connection.sendUTF(
                            JSON.stringify({
                                type: "chatMessage",
                                data: {
                                    from: results[0]["from"],
                                    user_id: results[0]["user_id"],
                                    name: htmlEntity.encode(results[0]["name"]),
                                    message: htmlEntity.encode(
                                        results[0]["reply"]
                                    ),
                                    created_at: results[0]["created_at"]
                                }
                            })
                        );
                    }
                });
            }
        });
    }
});