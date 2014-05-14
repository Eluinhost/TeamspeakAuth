var mc = require('minecraft-protocol');
var yaml = require('yaml');
fs   = require('fs');

var host = '0.0.0.0';
var post = 25565;
var motd = '§eAuth Server';

try {
    var doc = yaml.safeLoad(fs.readFileSync('./../config/config.yml', 'utf8'));
    var parameters = doc.parameters.minecraft;
    host = parameters.host;
    port = parameters.port;
    motd = parameters.motd;
} catch (e) {
    console.log(e);
    return;
}

var server = mc.createServer({
    'online-mode': true,
    encryption: true,
    host: host,
    port: port,
    motd: motd
});

server.on('login', function(client) {
    //TODO generate code, insert into database send player the code
    client.write('kick_disconnect', {
        reason: 'Kick message'
    });
});