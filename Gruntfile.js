module.exports = function (grunt) {
    // Project configuration.
    
    grunt.initConfig({
        pkg     : grunt.file.readJSON( 'package.json' ),
        gitclone: {
            clone: {
                options: {
                    repository: 'https://github.com/Desertsnowman/UIX',
                    branch: 'master',
                    directory: 'uix'
                }
            }
        },
        shell: {
            install: {
                command: 'npm install --prefix ./uix'
            },
            build: {
                command: "grunt --slug=<%= pkg.namespace %> --base ./uix --gruntfile ./uix/GruntFile.js default"
            }
        }        
    });

    //load modules
    grunt.loadNpmTasks( 'grunt-shell');
    grunt.loadNpmTasks( 'grunt-git' );

    //register default task
    grunt.registerTask( 'uix', [ 'gitclone', 'shell:install', 'shell:build' ] );

};
