var yaml = require('js-yaml'),
    fs   = require('fs');

try {
    var doc = yaml.safeLoad(fs.readFileSync(__dirname + '/../../config/config.yml', 'utf8'));
    var minecraft = doc.parameters.minecraft;
    var database = doc.parameters.database;
    var minutes = doc.parameters.minutesToLast;
} catch (e) {
    console.log(e);
    return;
}

module.exports = {
    database: database,
    minecraft: minecraft,
    minutes: minutes
};