module.exports = function(sequelize, DataTypes) {
    return sequelize.define('TeamspeakCode', {
        code: DataTypes.STRING(10)
    });
};