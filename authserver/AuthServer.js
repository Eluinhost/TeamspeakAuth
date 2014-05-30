var mc = require('minecraft-protocol'),
    jQuery = require('jquery-deferred'),
    config = require('./config/config'),
    util = require('./lib/ServerUtil'),
    database = require('./database/AuthDatabase');

/**
 * Generate a code for the client and kick them
 * @param {Client} client the client to process
 */
function processClient(client) {
    var code = util.generateCode(10);

    jQuery
        .when(util.sleep(3000))
        .then(function() {
            return authDatabase.updateMinecraftAccountUUIDWithName(client.username, client.username);
        })
        .then(function(account){
            return authDatabase.addCodeForAccount(account, code);
        })
        .then(function() {
            client.end(
                JSON.stringify('Your code is ' + code + ', it will last for the next ' + config.parameters.minutesToLast + ' minutes.')
            );
        }).fail(function() {
            client.end(
                JSON.stringify('There was an error creating your code, try again later. If the problem persists please contact an administrator')
            );
        });
}

authDatabase = new database.AuthDatabase();

jQuery.when(authDatabase.init()).then(function() {

    var server = mc.createServer({
        'online-mode': true,
        encryption: true,
        host: config.minecraft.host,
        port: config.minecraft.port,
        motd: config.minecraft.motd,
        'max-players': -1
    });

    server.favicon = util.base64Image(__dirname + '/servericon.png');

    server.on('login', function (client) {
        processClient(client);
    });

    server.on('error', function (error) {
        console.log('Error:', error);
    });

    server.on('listening', function () {
        console.log('Server listening on port', server.socketServer.address().port);
    });
});