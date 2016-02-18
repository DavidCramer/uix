module.exports = function (grunt) {
    
    // Project configuration.
    grunt.initConfig({
        pkg :   grunt.file.readJSON( '../package.json' ),
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
        replace: {
            core_file: {
                src: [ 'src/uix.php' ],
                dest: 'uix.php',
                replacements: [{
                    from: /namespace \s*(.*)/,
                    to: "namespace <%= pkg.namespace %>\\ui;"
                }]
            },
            plugin_file: {
                src: [ 'src/plugin.php' ],
                dest: '../plugin.php',
                replacements: [
                {
                    from: "{{namespace}}",
                    to: "<%= pkg.namespace %>"
                }
                ]
            }
        }

    });

    //load modules
    grunt.loadNpmTasks( 'grunt-curl' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-text-replace' );

    //installer tasks
    grunt.registerTask( 'default', [ 'curl', 'cssmin', 'uglify', 'replace:core_file' ] );

};
