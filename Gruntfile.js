module.exports = function(grunt) {

    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-available-tasks');

    var web_dir = 'web/vendor';
    var build_dir = 'build';

    var bower_dir = 'bower_components';

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        web_dir: web_dir,
        clean: {
            bower: [bower_dir],
            build: [build_dir],
            web: [web_dir]
        },
        availabletasks: {
            tasks: {
                options: {
                    filter: 'include',
                    tasks: ['default', 'install', 'clean']
                }
            }
        },
        copy: {
            fonts: {
                src: [ bower_dir + '/fontawesome/fonts/*'],
                dest: web_dir + '/fonts',
                flatten: true,
                expand: true,
                nonull: true
            }
        },
        concat: {
            js: {
                src: [
                    bower_dir + '/requirejs/require.js',
                    bower_dir + '/angular/angular.js',
                    bower_dir + '/modernizr/modernizr.js',
                    bower_dir + '/jquery/dist/jquery.js',
                    bower_dir + '/fastclick/lib/fastclick.js',
                    bower_dir + '/foundation/js/foundation/foundation.js',
                    bower_dir + '/foundation/js/foundation/foundation.offcanvas.js',
                    bower_dir + '/foundation/js/foundation/foundation.alert.js'
                ],
                dest: build_dir + '/js/<%= pkg.name %>.js',
                options: {
                    separator: ';'
                }
            },
            css: {
                src: [
                    bower_dir + '/fontawesome/css/font-awesome.css',
                    bower_dir + '/foundation/css/foundation.css'
                ],
                dest: build_dir + '/css/<%= pkg.name %>.css'
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
                    '<%= web_dir %>/js/<%= pkg.name %>.min.js': ['<%= concat.js.dest %>']
                }
            }
        },
        cssmin: {
            minify: {
                expand: true,
                cwd: build_dir + '/css',
                src: ['*.css', '!*.min.css'],
                dest: web_dir + '/css',
                ext: '.min.css'
            }
        }
    });

    grunt.registerTask(
        'dist-js',
        'Compiles all the Javascript into one file and minifies it to the web folder',
        ['clean:build', 'concat:js', 'uglify:js', 'clean:build']
    );

    grunt.registerTask(
        'dist-css',
        'Compiles all the CSS into one file and minifies it to the web folder',
        ['clean:build', 'concat:css', 'cssmin:minify', 'clean:build']);

    grunt.registerTask(
        'dist-fonts',
        'Copies all the fonts to the web folder',
        ['copy:fonts']
    );

    grunt.registerTask(
        'bower-install',
        'Cleans the bower dependencies and then re-installs them',
        ['clean:bower', 'bower-install-simple']
    );

    grunt.registerTask(
        'dist',
        'Recreates the distribution files in the web folder from the bower dependencies',
        ['clean:web', 'dist-js', 'dist-css', 'dist-fonts']
    );

    grunt.registerTask(
        'install',
        'Reinstalls the bower dependencies and recreates distribution files in the web folder',
        ['bower-install', 'dist']
    );

    grunt.registerTask(
        'default',
        'Show available tasks',
        ['availabletasks']
    );
};