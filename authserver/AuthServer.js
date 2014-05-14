var mc = require('minecraft-protocol');
var yaml = require('js-yaml');
var fs   = require('fs');
var chance = require('chance').Chance();
var mysql = require('mysql');
var jQuery = require('jquery-deferred');
var mime = require('mime');
var util = require('util');

var minecraft, database;

try {
    var doc = yaml.safeLoad(fs.readFileSync(__dirname + '/../config/config.yml', 'utf8'));
    minecraft = doc.parameters.minecraft;
    database = doc.parameters.database;
} catch (e) {
    console.log(e);
    return;
}

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
        host     : database.host,
        user     : database.username,
        password : database.password,
        port     : database.port,
        database : database.database
    });

    connection.connect(function(err) {
        if (err) {
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
    kickClientWithMessage(client, 'Your code is ' + code + ', it will last for the next 15 minutes.');
}

/**
 * Kick the client with the given message
 * @param client the client to kick
 * @param message the message to send them
 */
function kickClientWithMessage(client, message) {
    client.write(0x40, {
        reason: JSON.stringify(message)
    });
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
        'INSERT INTO ??(username, code, created_time) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE code=?,created_time=NOW()',
        [
            database.minecraft_table,
            username,
            code,
            code
        ],
        function(err, results) {
            if (err) {
                deferred.reject(err);
            }
            deferred.resolve();
        }
    );
    return deferred.promise();
}

/**
 * Generate a code for the client and kick them
 * @param client the client to process
 */
function processClient(client) {
    var code = chance.hash({length: 10, casing: 'upper'});

    jQuery.when(getConnection())
        .then(function (connection) {
            return addCodeToDatabase(connection, client.username, code);
        }).then(function() {
            console.log('User: ' + client.username + ", Code: " + code);
            kickClientWithCode(client, code);
        }).fail(function(err) {
            console.log('Database connection error: ' + err);
            kickClientWithMessage(client, 'There was a problem with the database, please try again later.');
        });
}

var server = mc.createServer({
    'online-mode': true,
    encryption: true,
    host: minecraft.host,
    port: minecraft.port,
    motd: minecraft.motd
});

server.favicon = base64Image(__dirname + '/servericon.png');

server.on('login', function(client) {
    processClient(client);
});