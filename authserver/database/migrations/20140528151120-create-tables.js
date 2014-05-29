var async = require('async'),
    config = require('./../../config/config');

var setupAuthenticationsTable = function(migration, DataTypes) {
    migration.createTable(
        config.database.tablePrefix + 'authentications',
        {
            id: {
                type: DataTypes.INTEGER,
                primaryKey: true,
                autoIncrement: true,
                allowNull: false
            },
            createdAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            updatedAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            MinecraftAccountId: {
                type: DataTypes.INTEGER,
                allowNull: false
            },
            TeamspeakAccountId: {
                type: DataTypes.INTEGER,
                allowNull: false
            }
        }
    );
};

var setupMinecraftAccountsTable = function(migration, DataTypes) {
    migration.createTable(
        config.database.tablePrefix + 'minecraftaccounts',
        {
            id: {
                type: DataTypes.INTEGER,
                primaryKey: true,
                autoIncrement: true,
                allowNull: false
            },
            createdAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            updatedAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            uuid: {
                type: DataTypes.UUID,
                allowNull: false
            },
            name: {
                type: DataTypes.STRING(16),
                allowNull: false
            }
        }
    );
};

var setupTeamspeakAccountsTable = function(migration, DataTypes) {
    migration.createTable(
        config.database.tablePrefix + 'teamspeakaccounts',
        {
            id: {
                type: DataTypes.INTEGER,
                primaryKey: true,
                autoIncrement: true,
                allowNull: false
            },
            createdAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            updatedAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            uuid: {
                type: DataTypes.UUID,
                allowNull: false
            },
            name: {
                type: DataTypes.STRING(30),
                allowNull: false
            }
        }
    );
};

var setupMinecraftCodesTable = function(migration, DataTypes) {
    migration.createTable(
        config.database.tablePrefix + 'minecraftcodes',
        {
            id: {
                type: DataTypes.INTEGER,
                primaryKey: true,
                autoIncrement: true,
                allowNull: false
            },
            createdAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            updatedAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            code: {
                type: DataTypes.STRING(10),
                allowNull: false
            },
            MinecraftAccountId: {
                type: DataTypes.INTEGER,
                allowNull: false
            }
        }
    );
};

var setupTeamspeakCodesTable = function(migration, DataTypes) {
    migration.createTable(
        config.database.tablePrefix + 'teamspeakcodes',
        {
            id: {
                type: DataTypes.INTEGER,
                primaryKey: true,
                autoIncrement: true,
                allowNull: false
            },
            createdAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            updatedAt: {
                type: DataTypes.DATE,
                allowNull: false
            },
            code: {
                type: DataTypes.STRING(10),
                allowNull: false
            },
            TeamspeakAccountId: {
                type: DataTypes.INTEGER,
                allowNull: false
            }
        }
    );
};

module.exports = {
    up: function(migration, DataTypes, done) {
        // add altering commands here, calling 'done' when finished
        async.series(
            [
                setupAuthenticationsTable(migration, DataTypes),
                setupMinecraftAccountsTable(migration, DataTypes),
                setupTeamspeakAccountsTable(migration, DataTypes),
                setupMinecraftCodesTable(migration, DataTypes),
                setupTeamspeakCodesTable(migration, DataTypes),
                done()
            ]
        );
    },
    down: function(migration, DataTypes, done) {
        async.series(
            migration.dropTable(config.database.tablePrefix + 'authentications'),
            migration.dropTable(config.database.tablePrefix + 'minecraftaccounts'),
            migration.dropTable(config.database.tablePrefix + 'teamspeakaccounts'),
            migration.dropTable(config.database.tablePrefix + 'minecraftcodes'),
            migration.dropTable(config.database.tablePrefix + 'teamspeakcodes'),
            done()
        );
    }
};
