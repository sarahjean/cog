/**
 * @file
 * Task: Build.
 */

/* global module */

module.exports = function (gulp, plugins, options) {
  'use strict';

  gulp.task(
    'build',
    gulp.series(
      'clean:css',
      'compile:sass',
      'minify:css',
      gulp.parallel('lint:js-with-fail', 'lint:css-with-fail', 'compile:js'),
      'compile:styleguide',
    )
  );

  gulp.task(
    'build:dev',
    gulp.series(
      'clean:css',
      'compile:sass',
      'minify:css',
      gulp.parallel('lint:js', 'lint:css', 'compile:js'),
      'compile:styleguide'
    )
  );
};
