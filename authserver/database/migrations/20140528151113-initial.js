module.exports = {
    up: function (migration, DataTypes, done) {
        migration.createTable(
            '',
            {
                id: {
                    type: DataTypes.INTEGER,
                    primaryKey: true,
                    autoIncrement: true
                },
                createdAt: {
                    type: DataTypes.DATE
                },
                updatedAt: {
                    type: DataTypes.DATE
                },
                attr1: DataTypes.STRING,
                attr2: DataTypes.INTEGER,
                attr3: {
                    type: DataTypes.BOOLEAN,
                    defaultValue: false,
                    allowNull: false
                }
            },
            {
                engine: 'MYISAM', // default: 'InnoDB'
                charset: 'latin1' // default: null
            }
        );
        done();
    },
    down: function (migration, DataTypes, done) {
        migration.showAllTables().success(function (tableNames) {

            // Dont drop the SequelizeMeta table
            var tables = tableNames.filter(function (name) {
                return name.toLowerCase() !== 'sequelizemeta';
            });

            function dropTable(tableName, cb) {
                migration.dropTable(tableName).complete(cb);
            }

            async.each(tables, dropTable, done);
        });
    }
};
