module.exports = function(grunt) {
    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            less: {
                files: [
                    'static/system/admin.less'
                ],
                tasks: ['less', 'cssmin'],
                options: {
                    debounceDelay: 1000
                }
            },
            configFiles: {
                files: [ 'Gruntfile.js' ],
                options: {
                    reload: true
                }
            }
        },
        less: {
            admin: {
                options: {
                    compress: false
                },
                files: {
                    "assets/admin/admin.src.css": "static/system/admin.less"
                }
            }
        },
        copy: {
            jqueru_ui_theme: {expand: true,flatten: true,
                src: [
                    'static/lib/jquery-ui/themes/smoothness/images/*'
                ],
                dest: 'assets/admin/images'
            },
            system_images: {expand: true,flatten: true,
                src: [
                    'static/images/*'
                ],
                dest: 'assets/admin/images'
            },
            fonts: {expand: true,flatten: true,
                src: [
                    'static/lib/bootstrap/fonts/*'
                ],
                dest: 'assets/fonts'
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0,
                processImport: true,
                relativeTo: true,
                rebase: true
            },
            target: {
                files: {
                    'assets/admin/admin.css': [
                        'assets/admin/admin.src.css',
                        'static/lib/jquery-ui/themes/smoothness/jquery-ui.css',
                        'static/lib/jquery-ui/themes/smoothness/jquery-ui.structure.css',
                        'static/lib/jquery-ui/themes/smoothness/jquery-ui.theme.css',
                        'static/lib/jqGrid/css/ui.jqgrid-bootstrap.css',
                        'static/lib/jqGrid/css/ui.jqgrid-bootstrap-ui.css',
                        'static/lib/jqGrid/plugins/searchFilter.css'
                    ]
                }
            }
        },
        requirejs: {
            admin: {
                options: {
                    baseUrl: 'static',
                    name: 'system/app',
                    mainConfigFile: 'static/system/app.js',
                    optimize: 'none',
                    out: "assets/admin/admin.src.js"
                }
            }
        },
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
            },
            build: {
                files: {
                    "assets/admin/admin.min.js": "assets/admin/admin.src.js",
                    "assets/require.js": "static/lib/requirejs/require.js"
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-requirejs');

    // Default task(s).
    grunt.registerTask('default', ['copy', 'less', 'cssmin', 'requirejs', 'uglify']);
};
