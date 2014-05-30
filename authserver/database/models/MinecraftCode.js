module.exports = function(sequelize, DataTypes) {
    return sequelize.define('MinecraftCode', {
        code: DataTypes.STRING(10)
    });
};