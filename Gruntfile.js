module.exports = function(grunt) {

    var assetsPath = 'assets',
        cssPath = assetsPath + '/css',
        jsPath = assetsPath + '/js';

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            //less: {
            //    files: [
            //        'web/app/less/*.less',
            //        'web/app/less/blocks/*.less',
            //        'web/app/less/admin/*.less'
            //    ],
            //    tasks: ['less'],
            //    options: {
            //        debounceDelay: 1000
            //    }
            //},
            configFiles: {
                files: [ 'Gruntfile.js' ],
                options: {
                    reload: true
                }
            }
        },
        less: {
        //    env: {
        //        options: {
        //            compress: false
        //        },
        //        files: {
        //            "web/css/admin.src.css": "web/app/less/admin.less",
        //            "web/css/style.src.css": 'web/app/less/style.less',
        //            "web/css/glyphicon.src.css": 'web/app/less/bootstrap-fonts.less'
        //        }
        //    }
        },
        copy: {
            //fancybox: {expand: true,flatten: true,
            //    src: [
            //        'static/lib/fancybox/source/*.gif',
            //        'static/lib/fancybox/source/*.png'
            //    ],
            //    dest: cssPath
            //},
            //fancybox_helpers: {expand: true,flatten: true,
            //    src: 'static/lib/fancybox/source/helpers/fancybox_buttons.png',
            //    dest: cssPath + '/helpers'
            //},
            jqueru_ui_theme: {expand: true,flatten: true,
                src: [
                    'static/lib/jquery-ui/themes/smoothness/images/*'
                ],
                dest: assetsPath + '/admin/images'
            },
            fonts: {expand: true,flatten: true,
                src: [
                    'static/lib/bootstrap/fonts/*'
                ],
                dest: assetsPath + '/fonts'
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
                        'static/lib/bootstrap/dist/css/bootstrap.css',
                        'static/lib/bootstrap/dist/css/bootstrap-theme.css',
                        'static/lib/jquery-ui/themes/smoothness/jquery-ui.css',
                        'static/lib/jqGrid/css/ui.jqgrid.css',
                        'static/lib/jqGrid/css/ui.jqgrid-bootstrap.css',
                        'static/lib/jqGrid/css/ui.jqgrid-bootstrap-ui.css',
                        'static/system/admin.css'
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
    grunt.registerTask('default', ['copy', /*'less',*/ 'cssmin', 'requirejs', 'uglify']);
};
