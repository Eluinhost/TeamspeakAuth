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
    Authentication = sequelize.import(__dirname + '/models/Authentication');

MinecraftCode.belongsTo(MinecraftAccount, {as: 'Account'});
MinecraftAccount.hasOne(MinecraftCode, {as: 'CurrentCode'});
TeamspeakCode.belongsTo(TeamspeakAccount, {as: 'Account'});
TeamspeakAccount.hasOne(TeamspeakCode, {as: 'CurrentCode'});
MinecraftAccount.hasMany(Authentication, {as: 'Authentications'});
TeamspeakAccount.hasMany(Authentication, {as: 'Authentications'});
Authentication.belongsTo(MinecraftAccount, {as: 'MinecraftAccount'});
Authentication.belongsTo(TeamspeakAccount, {as: 'TeamspeakAccount'});

var AuthDatabase = function() {};

AuthDatabase.prototype.init = function() {
    sequelize.sync().success(function() {
        console.log('done');
    }).error(function(err){
        console.log(err);
    });
/*
    this.connection.authenticate().complete(function(err) {
        if(!!err) {
            console.log('Unable to connect to the database: ', err);
            return;
        }
        var migrator = self.connection.getMigrator({
            path:        __dirname + '/migrations'
        });
        migrator.migrate().success(function() {
            console.log('done');
        });
    });
*/
};

var authDatabase = new AuthDatabase();
authDatabase.init();
