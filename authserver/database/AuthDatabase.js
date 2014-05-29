var Sequelize = require('sequelize'),
    config = require('./../config/config'),
    sequelize = new Sequelize(config.database.database, config.database.username, config.database.password, {
        dialect: 'mysql',
        host: config.database.host,
        port: config.database.port
    }),
    jQuery = require('jquery-deferred');

module.exports.MinecraftCode = sequelize.import(__dirname + '/models/MinecraftCode');
module.exports.MinecraftAccount = sequelize.import(__dirname + '/models/MinecraftAccount');
module.exports.TeamspeakCode = sequelize.import(__dirname + '/models/TeamspeakCode');
module.exports.TeamspeakAccount = sequelize.import(__dirname + '/models/TeamspeakAccount');
module.exports.Authentication = sequelize.import(__dirname + '/models/Authentication');

module.exports.MinecraftCode.belongsTo(module.exports.MinecraftAccount, {as: 'Account'});
module.exports.MinecraftAccount.hasOne(module.exports.MinecraftCode, {as: 'CurrentCode'});
module.exports.TeamspeakCode.belongsTo(module.exports.TeamspeakAccount, {as: 'Account'});
module.exports.TeamspeakAccount.hasOne(module.exports.TeamspeakCode, {as: 'CurrentCode'});
module.exports.MinecraftAccount.hasMany(module.exports.Authentication, {as: 'Authentications'});
module.exports.TeamspeakAccount.hasMany(module.exports.Authentication, {as: 'Authentications'});
module.exports.Authentication.belongsTo(module.exports.MinecraftAccount, {as: 'MinecraftAccount'});
module.exports.Authentication.belongsTo(module.exports.TeamspeakAccount, {as: 'TeamspeakAccount'});

module.exports.AuthDatabase = function() {};

/**
 * Initialize the database
 * @returns {Deferred} A promise that resolve on completion or rejects on failure
 */
module.exports.AuthDatabase.prototype.init = function() {
    var deferred = new jQuery.Deferred();
    sequelize.authenticate().complete(function(err) {
        if(!!err) {
            console.log('Unable to connect to the database: ', err);
            return;
        }
        var migrator = sequelize.getMigrator({
            path: __dirname + '/migrations'
        });
        migrator.migrate().success(function() {
            deferred.resolve();
        }).fail(function() {
            deferred.reject();
        });
    });
    return deferred.promise();
};

/**
 * Update the account with the given UUID with the given name, if it doesn't exist it creates it
 * @param {String} uuid the UUID to udpate
 * @param {String} name the latest name
 * @returns {Deferred} A promise that resolves on complete or fails on error (shouldn't happen)
 */
module.exports.AuthDatabase.prototype.updateMinecraftAccountUUIDWithName = function(uuid, name) {
    var deferred = jQuery.Deferred();
    //try to find the account with the given ID or create it if it doesn't exist
    module.exports.MinecraftAccount.findOrCreate({uuid: uuid},{name: name}).success(function(clientAccount) {
        clientAccount.updateAttributes({
            name: name
        }).success(function() {
            deferred.resolve();
        }).fail(function() {
            deferred.reject();
        });
    });
    return deferred.promise();
};