module.exports = {
    up: function(migration, DataTypes, done) {
        migration.changeColumn(
            'MinecraftAccounts',
            'name',
            {
                type: DataTypes.STRING(16),
                allowNull: false
            }
        ).success(function() {
            done();
        });
    },
    down: function(migration, DataTypes, done) {
        migration.changeColumn(
            'MinecraftAccounts',
            'name',
            {
                type: DataTypes.STRING(16),
                allowNull: true
            }
        ).success(function() {
            done();
        });
    }
};
