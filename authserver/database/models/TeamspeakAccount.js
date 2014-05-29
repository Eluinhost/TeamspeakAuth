var config = require('./../../config/config');

module.exports = function(sequelize, DataTypes) {
    return sequelize.define(
        'TeamspeakAccount',
        {
            uuid: DataTypes.UUID,
            name: DataTypes.STRING(30)
        },
        {
            tableName: config.database.tablePrefix + 'teamspeakaccounts'
        }
    );
};