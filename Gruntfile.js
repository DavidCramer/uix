 module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg :   grunt.file.readJSON( './package.json' ),
        curl: {
            './assets/js/handlebars.min-latest.js' : 'http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars.min-latest.js'
        },
        uglify: {
            min: {
                files: grunt.file.expandMapping( [
                    'assets/js/*.js',
                    '!assets/js/*.min.js',
                    '!assets/js/*.min-latest.js'
                ], './assets/js/', {
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
                cwd   : './assets/css/',
                src   : ['*.css', '!*.min.css'],
                dest  : './assets/css/',
                ext   : '.min.css'
            }
        }

    });

     //load modules
     grunt.loadNpmTasks( 'grunt-curl' );
     grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
     grunt.loadNpmTasks( 'grunt-contrib-uglify' );

     //installer tasks
     grunt.registerTask( 'default', [ 'cssmin', 'uglify' ] );
     grunt.registerTask( 'update', [ 'curl', 'cssmin', 'uglify' ] );

 };
