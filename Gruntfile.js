module.exports = function(grunt) {

// Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {// Task
            dist: {// Target
                options: {// Target options
                    style: 'compressed'
                },
                files: {// Dictionary of files
                    'css/yayimages.css': 'css/yayimages.scss'
                }
            }
        },
        watch: {
            css: {
                files: ['css/*.scss'],
                tasks: ['sass']
            }
        }
    });
    // Load the plugins   
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task(s).
    grunt.registerTask('default', ['sass', 'watch']);
};
