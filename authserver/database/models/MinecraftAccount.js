var config = require('./../../config/config');

module.exports = function(sequelize, DataTypes) {
    return sequelize.define(
        'MinecraftAccount',
        {
            uuid: DataTypes.UUID,
            name: DataTypes.STRING(16)
        },
        {
            tableName: config.database.tablePrefix + 'minecraftaccounts'
        }
    );
};