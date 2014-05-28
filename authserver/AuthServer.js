var mc = require('minecraft-protocol');
var fs   = require('fs');
var chance = require('chance').Chance();
var mysql = require('mysql');
var jQuery = require('jquery-deferred');
var mime = require('mime');
var util = require('util');
var config = require('./config/config');

function base64Image(src) {
    var data = fs.readFileSync(src).toString("base64");
    return util.format("data:%s;base64,%s", mime.lookup(src), data);
}

/**
 * @returns Deferred that resolves to the connection if successful
 */
function getConnection() {
    var deferred = new jQuery.Deferred();

    var connection = mysql.createConnection({
        host     : config.database.host,
        user     : config.database.username,
        password : config.database.password,
        port     : config.database.port,
        database : config.database.database
    });

    connection.connect(function(err) {
        if (err != null) {
            deferred.reject(err);
        }
        deferred.resolve(connection);
    });

    return deferred.promise();
}

/**
 * Kick the client with the given code
 * @param client the client to kick
 * @param code the code to send
 */
function kickClientWithCode(client, code) {
    kickClientWithMessage(client, 'Your code is ' + code + ', it will last for the next ' + config.minutes + ' minutes.');
}

/**
 * Kick the client with the given message
 * @param client the client to kick
 * @param message the message to send them
 */
function kickClientWithMessage(client, message) {
    client.end(JSON.stringify(message));
}

/**
 * Add the code and username to the database
 * @param connection the connection to use
 * @param username the username to add for
 * @param code the code to apply
 * @returns Deferred the promise that resolves on success
 */
function addCodeToDatabase(connection, username, code) {
    var deferred = new jQuery.Deferred();
    connection.query(
        'INSERT INTO ??(uuid, code, created_time) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE code=?,created_time=NOW()',
        [
            config.database.minecraft_table,
            username,
            code,
            code
        ],
        function(err, results) {
            if (err != null) {
                deferred.reject(err);
            }
            deferred.resolve();
        }
    );
    return deferred.promise();
}

/**
 * Returns a promise that resolves after the given duration
 * @param duration the ms to sleep for
 * @returns Deferred the promise that will resolve
 */
function sleep(duration) {
    var deferred = new jQuery.Deferred();
    setTimeout(function() {
        deferred.resolve();
    }, duration);
    return deferred.promise();
}

/**
 * Generate a code for the client and kick them
 * @param client the client to process
 */
function processClient(client) {
    var code = chance.hash({length: 10, casing: 'upper'});

    var dbConnection;
    jQuery.when(getConnection())
        .then(function (connection) {
            dbConnection = connection;
            return addCodeToDatabase(dbConnection, client.username, code);
        })
        .then(function(){
            dbConnection.end();
            return sleep(3000)
        })
        .then(function() {
            console.log('User: ' + client.username + ", Code: " + code);
            kickClientWithCode(client, code);
        })
        .fail(function(err) {
            if(dbConnection != null) {
                dbConnection.end();
            }
            console.log('Database connection error: ' + err);
            kickClientWithMessage(client, 'There was a problem with the database, please try again later.');
        });
}

var server = mc.createServer({
    'online-mode': true,
    encryption: true,
    host: config.minecraft.host,
    port: config.minecraft.port,
    motd: config.minecraft.motd,
    'max-players': -1
});

server.favicon = base64Image(__dirname + '/servericon.png');

server.on('login', function(client) {
    processClient(client);
});

server.on('error', function(error) {
    console.log('Error:', error);
});

server.on('listening', function() {
    console.log('Server listening on port', server.socketServer.address().port);
});