var config = require('./../../config/config');

module.exports = function(sequelize, DataTypes) {
    return sequelize.define('Authentication', {}, {tableName: config.database.tablePrefix + 'authentications'});
};