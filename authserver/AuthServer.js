var mc = require('minecraft-protocol');

var server = mc.createServer({
    'online-mode': true,   // optional
    encryption: true,      // optional
    host: '0.0.0.0',       // optional
    port: 25565           // optional
});

server.on('login', function(client) {

});