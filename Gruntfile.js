module.exports = function (grunt) {


    // Project configuration.
    grunt.initConfig({
        pkg     : grunt.file.readJSON( 'package.json' ),
        curl: {
            'assets/js/handlebars.min-latest.js' : 'http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars.min-latest.js',
            'assets/js/jquery.textcomplete.min.js' : 'https://raw.githubusercontent.com/yuku-t/jquery-textcomplete/master/dist/jquery.textcomplete.min.js'
        },
        uglify: {
            min: {
                files: grunt.file.expandMapping( [
                    'assets/js/*.js',
                    '!assets/js/*.min.js',
                    '!assets/js/*.min-latest.js'
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
                cwd   : 'assets/css/',
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
                    to: "namespace <%= pkg.namespace %>\\uix;"
                }]
            }
        }

    });

    //load modules
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-curl' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-text-replace' );

    //installer tasks
    grunt.registerTask( 'default', [ 'curl', 'cssmin', 'uglify', 'replace:core_file' ] );

};
