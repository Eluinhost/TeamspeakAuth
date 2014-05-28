var Sequelize = require('sequelize');

var AuthDatabase = function(host, port, database, username, password) {
    this.connection = new Sequelize(database, username, password, {
        dialect: 'mysql',
        host: host,
        port: port
    });
};

AuthDatabase.prototype.connection = null;

AuthDatabase.prototype.init = function() {
    var self = this;
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
};

/*var authDatabase = new AuthDatabase('localhost', 3306, 'test', 'root', '', 'mysql');
authDatabase.init();
*/