var jQuery = require('jquery-deferred');

var setupAuthenticationsTable = function(migration, DataTypes) {
    var deferred = jQuery.Deferred();
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
    ).then(function() {
        deferred.resolve();
    });
    return deferred.promise();
};

var setupMinecraftAccountsTable = function(migration, DataTypes) {
    var deferred = jQuery.Deferred();
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
    ).success(function() {
        deferred.resolve();
    });
    return deferred.promise();
};

var setupTeamspeakAccountsTable = function(migration, DataTypes) {
    var deferred = jQuery.Deferred();
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
    ).success(function() {
        deferred.resolve();
    });
    return deferred.promise();
};

var setupMinecraftCodesTable = function(migration, DataTypes) {
    var deferred = jQuery.Deferred();
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
    ).success(function() {
        deferred.resolve();
    });
    return deferred.promise();
};

var setupTeamspeakCodesTable = function(migration, DataTypes) {
    var deferred = jQuery.Deferred();
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
    ).success(function() {
        deferred.resolve();
    });
    return deferred.promise();
};

module.exports = {
    up: function(migration, DataTypes, done) {
        // add altering commands here, calling 'done' when finished
        jQuery.when(
            setupAuthenticationsTable(migration, DataTypes),
            setupMinecraftAccountsTable(migration, DataTypes),
            setupTeamspeakAccountsTable(migration, DataTypes),
            setupMinecraftCodesTable(migration, DataTypes),
            setupTeamspeakCodesTable(migration, DataTypes)
        ).then(function() {
            done();
        });
    },
    down: function(migration, DataTypes, done) {
        migration.dropTable('authentications').then(function() {
            migration.dropTable('minecraftaccounts');
        }).then(function() {
            migration.dropTable('teamspeakaccounts');
        }).then(function() {
            migration.dropTable('minecraftcodes');
        }).then(function() {
            migration.dropTable('teamspeakcodes');
        }).then(function() {
            done();
        });
    }
};
