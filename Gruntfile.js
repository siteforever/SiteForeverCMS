module.exports = function(grunt) {
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
            fancybox: {expand: true,flatten: true,
                src: [
                    'static/lib/fancybox/source/*.gif',
                    'static/lib/fancybox/source/*.png'
                ],
                dest: 'static/css/'
            },
            fancybox_helpers: {expand: true,flatten: true,
                src: 'static/lib/fancybox/source/helpers/fancybox_buttons.png',
                dest: 'static/css/helpers/'
            },
            jqueru_ui_theme: {expand: true,flatten: true,
                src: [
                    'static/lib/jquery-ui/themes/cupertino/images/*'
                ],
                dest: 'static/css/images/'
            },
            fonts: {expand: true,flatten: true,
                src: [
                    'static/lib/bootstrap/fonts/*'
                ],
                dest: 'static/fonts/'
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
                    'static/css/jquery.css': [
                        'static/lib/jquery-ui/themes/cupertino/jquery-ui.css',
                        'static/lib/jquery-ui/themes/cupertino/jquery-ui.structure.css',
                        'static/lib/jquery-ui/themes/cupertino/jquery-ui.theme.css'
                    ],
                    "static/css/fancybox.css": [
                        'static/lib/fancybox/source/jquery.fancybox.css',
                        'static/lib/fancybox/source/helpers/jquery.fancybox-buttons.css',
                        'static/lib/fancybox/source/helpers/jquery.fancybox-thumbs.css'
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
                    out: "static/admin.src.js",
                }
            }
        },
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
            },
            build: {
                files: {
                    "static/admin.min.js": "static/admin.src.js"
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
    grunt.registerTask('default', [/*'copy', 'less', 'cssmin',*/ 'requirejs', 'uglify']);
};
