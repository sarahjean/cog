/**
 * @file
 * Task: Test: CSS.
 */

/* global module */

module.exports = function (gulp, plugins, options) {
  'use strict';

  gulp.task('test:css', function () {
    return gulp.src(options.css.files)
      .pipe(plugins.plumber())
      .pipe(plugins.parker());
  });
};
