module.exports = function(grunt) {

    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        clean: [
            'web/vendor',
            'bower_components'
        ],
        copy: {
            fonts: {
                src: [
                    'bower_components/bootstrap/dist/fonts/*',
                    'bower_components/fontawesome/fonts/*'
                ],
                dest: 'web/vendor/fonts/',
                flatten: true,
                expand: true
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            js: {
                src: [
                    'bower_components/jquery/dist/jquery.js',
                    'bower_components/bootstrap/dist/js/bootstrap.js'
                ],
                dest: 'web/vendor/js/<%= pkg.name %>.js'
            },
            css: {
                src: [
                    'bower_components/bootstrap/dist/css/bootstrap.css',
                    'bower_components/bootstrap/dist/css/bootstrap-theme.css',
                    'bower_components/font-awesome/css/font-awesome.css'
                ],
                dest: 'web/vendor/css/<%= pkg.name %>.css'
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
                    'web/vendor/js/<%= pkg.name %>.min.js': ['<%= concat.js.dest %>']
                }
            }
        },
        cssmin: {
            minify: {
                expand: true,
                cwd: 'web/vendor/css/',
                src: ['*.css', '!*.min.css'],
                dest: 'web/vendor/css/',
                ext: '.min.css'
            }
        }
    });

    grunt.registerTask('install', ['clean', 'bower-install-simple', 'concat', 'uglify', 'cssmin', 'copy']);
};