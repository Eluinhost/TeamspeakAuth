module.exports = function(grunt) {

    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-prompt');
    grunt.loadNpmTasks('grunt-available-tasks');

    var YAML = require('yamljs');
    var jQuery = require('jquery-deferred');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        js_dir: '<%= pkg.web_vendor_dir %>/js',
        css_dir: '<%= pkg.web_vendor_dir %>/css',
        font_dir: '<%= pkg.web_vendor_dir %>/fonts',
        clean: {
            bower: ['<%= pkg.bower_dir %>'],
            build: ['<%= pkg.web_vendor_dir %>'],
            composer: ['<%= pkg.composer_vendor_dir %>'],
            cache: ['cache'],
            template_cache: ['cache/templates'],
            container_cache: ['cache/container'],
            routing_cache: ['cache/routing'],
            skins_cache: ['cache/skins']
        },
        availabletasks: {
            tasks: {
                options: {
                    filter: 'include',
                    tasks: ['default', 'install', 'clean', 'configure', 'run-migrations']
                }
            }
        },
        copy: {
            fonts: {
                src: [
                    '<%= pkg.bower_dir %>/fontawesome/fonts/*'
                ],
                dest: '<%= font_dir %>',
                flatten: true,
                expand: true,
                nonull: true
            }
        },
        concat: {
            js: {
                src: [
                    '<%= pkg.bower_dir %>/modernizr/modernizr.js',
                    '<%= pkg.bower_dir %>/jquery/dist/jquery.js',
                    '<%= pkg.bower_dir %>/fastclick/lib/fastclick.js',
                    '<%= pkg.bower_dir %>/foundation/js/foundation/foundation.js',
                    '<%= pkg.bower_dir %>/foundation/js/foundation/foundation.offcanvas.js',
                    '<%= pkg.bower_dir %>/foundation/js/foundation/foundation.alert.js'
                ],
                dest: '<%= js_dir %>/<%= pkg.name %>.js',
                options: {
                    separator: ';'
                }
            },
            css: {
                src: [
                    '<%= pkg.bower_dir %>/fontawesome/css/font-awesome.css',
                    '<%= pkg.bower_dir %>/foundation/css/foundation.css'
                ],
                dest: '<%= css_dir %>/<%= pkg.name %>.css'
            }
        },
        uglify: {
            options: {
                banner: '/*!\n' +
                    ' * <%= pkg.name %> v<%= pkg.version %>\n' +
                    ' * Copyright 2014-<%= grunt.template.today("yyyy") %> <%= pkg.author %>\n' +
                    ' * Licensed under <%= pkg.license.type %> (<%= pkg.license.url %>)\n' +
                    ' */\n',
                sourceMap: true
            },
            js: {
                files: {
                    '<%= js_dir %>/<%= pkg.name %>.min.js': ['<%= concat.js.dest %>']
                }
            }
        },
        cssmin: {
            minify: {
                expand: true,
                cwd: '<%= css_dir %>',
                src: ['*.css', '!*.min.css'],
                dest: '<%= css_dir %>',
                ext: '.min.css'
            }
        },
        prompt: {
            configYML: {
                options: {
                    questions: [
                        {
                            config: 'configYML.parameters.minutesToLast',
                            type: 'input',
                            message: 'Time codes are valid for (minutes):',
                            default: '<%= configYML.parameters.minutesToLast %>',
                            validate: function(value) {
                                return /^\+?(0|[1-9]\d*)$/.test(value) || "Please enter a positive integer";
                            }
                        },
                        {
                            config: 'configYML.parameters.teamspeak.host',
                            type: 'input',
                            message: 'Teamspeak server address:',
                            default: '<%= configYML.parameters.teamspeak.host %>'
                        },
                        {
                            config: 'configYML.parameters.teamspeak.port',
                            type: 'input',
                            message: 'Teamspeak server port:',
                            default: '<%= configYML.parameters.teamspeak.port %>',
                            validate: function(value) {
                                return /^\+?(0|[1-9]\d*)$/.test(value) || "Please enter a positive integer";
                            }
                        },
                        {
                            config: 'configYML.parameters.teamspeak.query_port',
                            type: 'input',
                            message: 'Teamspeak server query port:',
                            default: '<%= configYML.parameters.teamspeak.query_port %>',
                            validate: function(value) {
                                return /^\+?(0|[1-9]\d*)$/.test(value) || "Please enter a positive integer";
                            }
                        },
                        {
                            config: 'configYML.parameters.teamspeak.username',
                            type: 'input',
                            message: 'Teamspeak username to connect to server query:',
                            default: '<%= configYML.parameters.teamspeak.username %>'
                        },
                        {
                            config: 'configYML.parameters.teamspeak.password',
                            type: 'password',
                            message: 'Teamspeak server password:'
                        },
                        {
                            config: 'configYML.parameters.teamspeak.group_id',
                            type: 'input',
                            message: 'Teamspeak group ID to provide:',
                            default: '<%= configYML.parameters.teamspeak.group_id %>',
                            validate: function(value) {
                                return /^\+?(0|[1-9]\d*)$/.test(value) || "Please enter a positive integer";
                            }
                        },
                        {
                            config: 'configYML.parameters.database.host',
                            type: 'input',
                            message: 'MySQL host:',
                            default: '<%= configYML.parameters.database.host %>'
                        },
                        {
                            config: 'configYML.parameters.database.port',
                            type: 'input',
                            message: 'MySQL port:',
                            default: '<%= configYML.parameters.database.port %>',
                            validate: function (value) {
                                return /^\+?(0|[1-9]\d*)$/.test(value) || "Please enter a positive integer";
                            }
                        },
                        {
                            config: 'configYML.parameters.database.username',
                            type: 'input',
                            message: 'MySQL username:',
                            default: '<%= configYML.parameters.database.username %>'
                        },
                        {
                            config: 'configYML.parameters.database.password',
                            type: 'password',
                            message: 'MySQL password:'
                        },
                        {
                            config: 'configYML.parameters.database.database',
                            type: 'input',
                            message: 'MySQL database:',
                            default: '<%= configYML.parameters.database.database %>'
                        },
                        {
                            config: 'configYML.parameters.minecraft.host',
                            type: 'input',
                            message: 'Auth Server host to bind to:',
                            default: '<%= configYML.parameters.minecraft.host %>'
                        },
                        {
                            config: 'configYML.parameters.minecraft.port',
                            type: 'input',
                            message: 'Auth Server port:',
                            default: '<%= configYML.parameters.minecraft.port %>',
                            validate: function (value) {
                                return /^\+?(0|[1-9]\d*)$/.test(value) || "Please enter a positive integer";
                            }
                        },
                        {
                            config: 'configYML.parameters.minecraft.motd',
                            type: 'input',
                            message: 'Auth Server MOTD:',
                            default: '<%= configYML.parameters.minecraft.motd %>'
                        },
                        {
                            config: 'configYML.parameters.serverAddress',
                            type: 'input',
                            message: 'Server address string to tell people to connect to for website:',
                            default: '<%= configYML.parameters.serverAddress %>'
                        },
                        {
                            config: 'configYMLwrite',
                            type: 'confirm',
                            message: 'Do you want to write to the file config.yml?',
                            default: 'Y'
                        },
                        {
                            config: 'configYMLdatabasewrite',
                            type: 'confirm',
                            message: 'Do you also want to update/create the database schema to the latest version?',
                            default: 'Y'
                        }
                    ]
                }
            }
        }
    });

    grunt.registerTask(
        'dist-js',
        'Creates the distribution js in the web folder from bower dependencies',
        ['concat:js', 'uglify:js']
    );

    grunt.registerTask(
        'dist-css',
        'Creates the distribution css in the web folder from bower dependencies',
        ['concat:css', 'cssmin:minify']);

    grunt.registerTask(
        'dist-fonts',
        'Creates the distribution fonts in the web folder from bower dependencies',
        ['copy:fonts']
    );

    grunt.registerTask(
        'bower-install',
        'Cleans the bower dependencies and then installs them',
        ['clean:bower', 'bower-install-simple']
    );

    grunt.registerTask(
        'dist',
        'Creates the distribution files in the web folder from the bower dependencies',
        ['clean:build', 'dist-js', 'dist-css', 'dist-fonts']
    );

    grunt.registerTask(
        'install',
        'Installs composer and bower dependencies and creates distribution files in the web folder',
        ['composer:install', 'bower-install', 'dist', 'clean:cache']
    );

    grunt.registerTask(
        'default',
        'Show available tasks',
        ['availabletasks']
    );

    grunt.registerTask(
        'load-config',
        'Loads the config.yml file into grunt for editing with \'configure\', if file doesn\'t exist uses config.yml.dist',
        function() {
            var file = __dirname + '/config/config.yml';
            if( !grunt.file.exists(file) ) {
                file = __dirname + '/config/config.yml.dist';
            }
            var configFile = grunt.file.readYAML(file);
            grunt.config.merge({configYML: configFile});
        }
    );

    grunt.registerTask(
        'configure',
        'Create/edit the config.yml file',
        ['load-config', 'prompt:configYML', 'save-config', 'run-migrations']
    );

    grunt.registerTask(
        'save-config',
        'Save the config options in memory to config.yml after a configure',
        function() {
            if(grunt.config.get('configYMLwrite') != true) {
                grunt.log.writeln('File not written');
                return;
            }
            grunt.file.write('config/config.yml', YAML.stringify(grunt.config('configYML')));
            grunt.task.run('clean:container');
        }
    );

    grunt.registerTask(
        'run-migrations',
        'Runs the database migrations to update the database to the latest version',
        function() {
            var done = this.async();
            if(grunt.config.get('configYMLdatabasewrite') == null || grunt.config.get('configYMLdatabasewrite') == true) {
                var database = require('./authserver/database/AuthDatabase');
                var authDatabase = new database.AuthDatabase();
                jQuery.when(authDatabase.init()).then(function() {
                    console.log('Migrations complete');
                    done();
                }).fail(function() {
                    console.log('There was an error trying to apply the migrations');
                    done();
                });
            }
        }
    );
};