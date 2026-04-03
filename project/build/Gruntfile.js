module.exports = function (grunt) {

    var currentdate = new Date();
    var datetime = currentdate.getDate() + "/"
        + (currentdate.getMonth() + 1) + "/"
        + currentdate.getFullYear() + " @ "
        + currentdate.getHours() + ":"
        + currentdate.getMinutes() + ":"
        + currentdate.getSeconds();

    grunt.initConfig({
        concat: {
            options: {
                separator: '\n',
                sourceMap: true,
                banner: "/*Processed by Honlor Build System on " + datetime + "*/\n"
            },
            admin_js: {
                src: [
                    '../js/admin/*.js',
                ],
                dest: 'dist/admin.cc.js'
            },
            index_js: {
                src: [
                    '../js/index/*.js',
                ],
                dest: 'dist/index.cc.js'
            },
            admin_css: {
                src: [
                    '../css/admin.css',
                ],
                dest: 'dist/admin.cc.css'
            },
            index_css: {
                src: [
                    '../css/index.css',
                ],
                dest: 'dist/index.cc.css'
            }
        },
        cssmin: {
            options: {
                mergeIntoShorthands: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    '../../htdocs/css/admin.min.css': ['dist/admin.cc.css'],
                    '../../htdocs/css/index.min.css': ['dist/index.cc.css'],
                }
            }
        },
        uglify: {
            minify: {
                options: {
                    sourceMap: true,
                },
                files: {
                    '../../htdocs/js/admin.min.js': ['dist/admin.cc.js'],
                    '../../htdocs/js/index.min.js': ['dist/index.cc.js'],
                }
            }
        },
        obfuscator: {
            options: {
                banner: '// obfuscated with grunt-contrib-obfuscator.\n',
                debugProtection: true,
                debugProtectionInterval: true,
            },
            task1: {
                options: {
                },
                files: {
                    '../../htdocs/js/admin.o.js': [
                        'dist/admin.cc.js',
                    ]
                }
            }
        },
        watch: {
            css: {
                files: [
                    '../css/**/*.css',
                ],
                tasks: ['concat', 'cssmin'],
                options: {
                    spawn: false,
                },
            },
            js: {
                files: [
                    '../js/**/*.js'
                ],
                tasks: ['concat', 'uglify', 'obfuscator'],
                options: {
                    spawn: false,
                },
            },
        },
    });

    grunt.loadNpmTasks('grunt-contrib-obfuscator');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    
    grunt.registerTask('default', ['concat', 'cssmin', 'uglify', 'obfuscator']);
};
