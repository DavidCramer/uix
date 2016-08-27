 module.exports = function (grunt) {
    
    // Project configuration.
    grunt.initConfig({
        pkg :   grunt.file.readJSON( 'package.json' ),
        namespace     : grunt.option( "slug" ),
        curl: {
            'assets/js/handlebars.min-latest.js' : 'http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars.min-latest.js'
        },
        uglify: {
            min: {
                files: grunt.file.expandMapping( [
                    'src/assets/js/*.js',
                    '!src/assets/js/*.min.js',
                    '!src/assets/js/*.min-latest.js'
                ], 'assets/js/', {
                    rename : function ( destBase, destPath ) {
                        return destBase + destPath.replace( '.js', '.min.js' );
                    },
                    flatten: true
                } )
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0
            },
            minify : {
                expand: true,
                cwd   : 'src/assets/css/',
                src   : ['*.css', '!*.min.css'],
                dest  : 'assets/css/',
                ext   : '.min.css'
            }
        },
        copy: {
            main: {
                files:[
                    {
                        expand: true,
                        cwd: 'src/assets/css/fonts',
                        src: '**',
                        dest: 'assets/css/fonts/'
                    },
                    {
                        expand: true,
                        cwd: 'src/classes/uixv2',
                        src: '**',
                        dest: 'classes/<%= pkg.namespace %>/'
                    }
                ]
            }
        },        
        replace: {
            core_file: {
                src: [ 'classes/**/*.php', 'plugin.php' ],
                overwrite: true,
                replacements: [
                    {
                        from: 'uixv2',
                        to: "<%= pkg.namespace %>"
                    },
                    {
                        from: 'UIXV2',
                        to: "<%= pkg.slug %>"
                    }
                ]
            },
            plugin_file: {
                src: [ 'src/plugin.php' ],
                dest: 'plugin.php',
                replacements: [
                {
                    from: "{{namespace}}",
                    to: "<%= pkg.namespace %>"
                },
                {
                    from: "{{slug}}",
                    to: "<%= pkg.slug %>"
                },
                {
                    from: "{{prefix}}",
                    to: "<%= pkg.prefix %>"
                },
                {
                    from: "{{name}}",
                    to: "<%= pkg.plugin_name %>"
                },
                {
                    from: "{{description}}",
                    to: "<%= pkg.description %>"
                },
                {
                    from: "{{author}}",
                    to: "<%= pkg.author %>"
                },
                {
                    from: "{{url}}",
                    to: "<%= pkg.url %>"
                },
                {
                    from: "{{version}}",
                    to: "<%= pkg.version %>"
                }
                ]
            }
        },
        clean: {
          build: ["src/**", "etc/**", "node_modules/**",".git/**",".gitignore","composer.json","Gruntfile.js","package.json"],
        },

    });

    //load modules
    grunt.loadNpmTasks( 'grunt-curl' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-text-replace' );

    //installer tasks
    //grunt.registerTask( 'default', [ 'curl', 'cssmin', 'uglify', 'copy', 'replace', 'clean' ] );
    grunt.registerTask( 'default', [ 'curl', 'cssmin', 'uglify', 'copy', 'replace' ] );

};
