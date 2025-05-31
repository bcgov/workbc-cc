module.exports = function(grunt) {

    grunt.initConfig({
        'dart-sass': {
          target: {
            options: {
              outputStyle: 'compressed',
              sourceMap: true
            },
            files: {
              'web/themes/custom/workbc_cdq/css/style.css': 'web/themes/custom/workbc_cdq/scss/style.scss',
              'web/themes/custom/workbc_cdq/css/ck5style.css': 'web/themes/custom/workbc_cdq/scss/ck5style.scss'
            }
          }
        },
        watch: {
          src: {
            files: ['web/themes/custom/workbc_cdq/scss/**/*.scss'],
            tasks: ['dart-sass'],
          },
        },
      });

    grunt.loadNpmTasks('grunt-dart-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
};
