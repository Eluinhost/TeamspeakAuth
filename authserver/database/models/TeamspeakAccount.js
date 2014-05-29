module.exports = function(sequelize, DataTypes) {
    return sequelize.define('TeamspeakAccount', {
        uuid: DataTypes.UUID,
        name: DataTypes.STRING(30)
    });
};