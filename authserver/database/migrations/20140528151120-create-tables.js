var async = require('async');

var setupAuthenticationsTable = function(migration, DataTypes) {
    migration.createTable(
        'authentications',
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
        'minecraftaccounts',
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
        'teamspeakaccounts',
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
        'minecraftcodes',
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
        'teamspeakcodes',
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

var deleteTables = function(migration) {

    return jobArray;
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
            migration.dropTable('authentications'),
            migration.dropTable('minecraftaccounts'),
            migration.dropTable('teamspeakaccounts'),
            migration.dropTable('minecraftcodes'),
            migration.dropTable('teamspeakcodes'),
            done()
        );
    }
};
