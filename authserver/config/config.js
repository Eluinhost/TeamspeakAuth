var yaml = require('js-yaml'),
    fs   = require('fs');

var doc = yaml.safeLoad(fs.readFileSync(__dirname + '/../../config/config.yml', 'utf8'));

module.exports.minecraft = doc.parameters.minecraft;
module.exports.database = doc.parameters.database;
module.exports.parameters = doc.parameters;
