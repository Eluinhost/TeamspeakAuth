var config = require('./../../config/config');

module.exports = function(sequelize, DataTypes) {
    return sequelize.define(
        'MinecraftCode',
        {
            code: DataTypes.STRING(10)
        },
        {
            tableName: config.database.tablePrefix + 'minecraftcodes'
        }
    );
};