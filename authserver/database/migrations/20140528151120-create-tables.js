var async = require('async'),
    config = require('./../../config/config');

var setupAuthenticationsTable = function(migration, DataTypes) {
    var AuthenticationsTable = {
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
        }
    };
    AuthenticationsTable[config.database.tablePrefix + 'MinecraftAccountId'] = {
        type: DataTypes.INTEGER,
        allowNull: false
    };
    AuthenticationsTable[config.database.tablePrefix + 'TeamspeakAccountId'] = {
        type: DataTypes.INTEGER,
        allowNull: false
    };

    migration.createTable(
        config.database.tablePrefix + 'authentications',
        AuthenticationsTable
    );
};

var setupMinecraftAccountsTable = function(migration, DataTypes) {
    var MinecraftAccountsTable = {
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
    };
    migration.createTable(
        config.database.tablePrefix + 'minecraftaccounts',
        MinecraftAccountsTable
    );
};

var setupTeamspeakAccountsTable = function(migration, DataTypes) {
    var TeamspeakAccountsTable = {
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
    };
    migration.createTable(
        config.database.tablePrefix + 'teamspeakaccounts',
        TeamspeakAccountsTable
    );
};

var setupMinecraftCodesTable = function(migration, DataTypes) {
    var MinecraftCodesTable = {
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
        }
    };
    MinecraftCodesTable[config.database.tablePrefix + 'MinecraftAccountId'] = {
        type: DataTypes.INTEGER,
        allowNull: false
    };
    migration.createTable(
        config.database.tablePrefix + 'minecraftcodes',
        MinecraftCodesTable
    );
};

var setupTeamspeakCodesTable = function(migration, DataTypes) {
    var TeamspeakCodesTable = {
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
    };
    TeamspeakCodesTable[config.database.tablePrefix + 'TeamspeakAccountId'] = {
        type: DataTypes.INTEGER,
        allowNull: false
    };
    migration.createTable(
        config.database.tablePrefix + 'teamspeakcodes',
        TeamspeakCodesTable
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
