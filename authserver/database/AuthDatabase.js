var Sequelize = require('sequelize'),
    config = require('./../config/config'),
    sequelize = new Sequelize(config.database.database, config.database.username, config.database.password, {
        dialect: 'mysql',
        host: config.database.host,
        port: config.database.port
    }),
    MinecraftCode = sequelize.import(__dirname + '/models/MinecraftCode'),
    MinecraftAccount = sequelize.import(__dirname + '/models/MinecraftAccount'),
    TeamspeakCode = sequelize.import(__dirname + '/models/TeamspeakCode'),
    TeamspeakAccount = sequelize.import(__dirname + '/models/TeamspeakAccount'),
    Authentication = sequelize.import(__dirname + '/models/Authentication'),
    jQuery = require('jquery-deferred');

MinecraftCode.belongsTo(MinecraftAccount, {as: 'Account'});
MinecraftAccount.hasOne(MinecraftCode, {as: 'CurrentCode'});
TeamspeakCode.belongsTo(TeamspeakAccount, {as: 'Account'});
TeamspeakAccount.hasOne(TeamspeakCode, {as: 'CurrentCode'});
MinecraftAccount.hasMany(Authentication, {as: 'Authentications'});
TeamspeakAccount.hasMany(Authentication, {as: 'Authentications'});
Authentication.belongsTo(MinecraftAccount, {as: 'MinecraftAccount'});
Authentication.belongsTo(TeamspeakAccount, {as: 'TeamspeakAccount'});

var AuthDatabase = function() {};

/**
 * Initialize the database
 * @returns {Deferred} A promise that resolve on completion or rejects on failure
 */
AuthDatabase.prototype.init = function() {
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
