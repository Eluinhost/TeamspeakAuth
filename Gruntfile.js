module.exports = function(grunt) {

    grunt.loadNpmTasks("grunt-bower-install-simple");
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-composer');

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        js_dir: '<%= pkg.vendor_dir %>/js',
        css_dir: '<%= pkg.vendor_dir %>/css',
        font_dir: '<%= pkg.vendor_dir %>/fonts',
        clean: {
            bower: ['<%= pkg.bower_dir %>'],
            build: ['<%= pkg.vendor_dir %>']
        },
        copy: {
            fonts: {
                src: [
                    '<%= pkg.bower_dir %>/bootstrap/dist/fonts/*',
                    '<%= pkg.bower_dir %>/fontawesome/fonts/*'
                ],
                dest: '<%= font_dir %>',
                flatten: true,
                expand: true,
                nonull: true
            }
        },
        concat: {
            options: {
                separator: ';'
            },
            js: {
                src: [
                    '<%= pkg.bower_dir %>/jquery/dist/jquery.js',
                    '<%= pkg.bower_dir %>/bootstrap/dist/js/bootstrap.js'
                ],
                dest: '<%= js_dir %>/<%= pkg.name %>.js'
            },
            css: {
                src: [
                    '<%= pkg.bower_dir %>/bootstrap/dist/css/bootstrap.css',
                    '<%= pkg.bower_dir %>/bootstrap/dist/css/bootstrap-theme.css',
                    '<%= pkg.bower_dir %>/font-awesome/css/font-awesome.css'
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

    grunt.registerTask('dist-js', ['concat:js', 'uglify:js']);
    grunt.registerTask('dist-css', ['concat:css', 'cssmin:minify']);
    grunt.registerTask('dist-fonts', ['copy:fonts']);

    grunt.registerTask('bower-install', ['clean:bower', 'bower-install-simple']);
    grunt.registerTask('composer-install', ['composer:install']);
    grunt.registerTask('dist', ['clean:build', 'dist-js', 'dist-css', 'dist-fonts']);

    grunt.registerTask('default', ['composer-install', 'bower-install', 'dist']);
};