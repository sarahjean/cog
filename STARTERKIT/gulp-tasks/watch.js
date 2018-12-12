/**
 * @file
 * Task: Watch.
 */

/* global module */

module.exports = function (gulp, plugins, options) {

  gulp.task('watch:sass', function (done) {
    gulp.watch([options.sass.files], gulp.series(
      'compile:sass',
      'minify:css',
      'browsersync:reload'
    ));
    done();
  });

  gulp.task('watch:js', function (done) {
    gulp.watch([options.js.files], gulp.series(
      'lint:js',
      'browsersync:reload'
    ));
    done();
  });

  gulp.task('watch', gulp.parallel('watch:sass', 'watch:js'));
};
