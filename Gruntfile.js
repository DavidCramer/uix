 module.exports = function (grunt) {
    
    // Project configuration.
    grunt.initConfig({
        pkg :   grunt.file.readJSON( '../package.json' ),
        namespace     : grunt.option( "slug" ),
        curl: {
            '../assets/js/handlebars.min-latest.js' : 'http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars.min-latest.js'
        },
        copy: {
            main: {
                files:[
                    {
                        expand: true,
                        cwd: 'src/assets/css',
                        src: '**',
                        dest: '../assets/css/'
                    },
                    {
                        expand: true,
                        cwd: 'src/assets/js',
                        src: '**',
                        dest: '../assets/js/'
                    },
                    {
                        expand: true,
                        cwd: 'src/classes/uixv2',
                        src: '**',
                        dest: '../classes/<%= pkg.namespace %>/'
                    }
                ]
            }
        },
        uglify: {
            min: {
              files: [{
                  expand: true,
                  cwd: '../assets/js',
                  src: '**/*.js',
                  dest: '../assets/js/*.min.js'
              }]
            }
        },
        cssmin: {
            options: {
                keepSpecialComments: 0
            },
            minify : {
                expand: true,
                cwd   : '../assets/css/',
                src   : ['*.css', '!*.min.css'],
                dest  : '../assets/css/',
                ext   : '.min.css'
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

    //installer tasks
    grunt.registerTask( 'default', [ 'curl', 'copy', 'cssmin', 'uglify', 'clean' ] );

};
