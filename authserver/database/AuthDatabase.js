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
 * @returns {Deferred} A promise that resolves to the MinecraftAccount complete or fails on error (shouldn't happen)
 */
module.exports.AuthDatabase.prototype.updateMinecraftAccountUUIDWithName = function(uuid, name) {
    var deferred = jQuery.Deferred();
    var self = this;
    //try to find the account with the given ID or create it if it doesn't exist
    module.exports.MinecraftAccount.findOrCreate({uuid: uuid},{name: name}).success(function(clientAccount) {
        clientAccount.updateAttributes({
            name: name
        }).success(function(updatedAccount) {
            jQuery.when(self.removeNamesForConflictingAccounts(updatedAccount)).then(function() {
                deferred.resolve(updatedAccount);
            });
        }).fail(function() {
            deferred.reject();
        });
    });
    return deferred.promise();
};

/**
 * Unsets the name for the any other account with the same name
 * @param {MinecraftAccount} account the account NOT to unset
 * @returns {Deferred} A promise that resolves when complete
 */
module.exports.AuthDatabase.prototype.removeNamesForConflictingAccounts = function(account) {
    var deferred = jQuery.Deferred();
    module.exports.MinecraftAccount.findAll({
       where: {
           id: {
               ne: account.id
           },
           name: {
               eq: account.name
           }
       }
    }).success(function(accounts) {
        accounts.forEach(function(loopAccount) {
            loopAccount.updateAttributes({
                name: null
            });
        });
        deferred.resolve();
    }).fail(function() {
        deferred.reject();
    });
    return deferred.promise();
};

/**
 * Adds a minecraft code for the given minecraft account
 * @param {MinecraftAccount} account the account to add to
 * @param {String} code the code to give them
 * @returns {Deferred} A promise that resolves on complete or rejects on error (shouldnt happen)
 */
module.exports.AuthDatabase.prototype.addCodeForAccount = function(account, code) {
    var deferred = jQuery.Deferred();
    module.exports.MinecraftCode.create({
        code: code
    }).success(function(minecraftCode) {
        minecraftCode.setAccount(account).success(function() {
            deferred.resolve();
        }).fail(function() {
            deferred.reject();
        });
    }).fail(function() {
        deferred.reject();
    });
    return deferred.promise();
};