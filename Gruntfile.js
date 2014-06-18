module.exports = function(grunt) {

    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-composer');
    grunt.loadNpmTasks('grunt-available-tasks');

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
                    tasks: ['default', 'install', 'clean']
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
};