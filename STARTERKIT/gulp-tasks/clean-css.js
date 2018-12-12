/**
 * @file
 * Task: Clean:CSS.
 */

/* global module */

module.exports = function (gulp, plugins, options) {
  'use strict';

  // Clean CSS files.
  function cleanCss(cb) {
    plugins.del([options.css.files]);
    cb();
  }

  gulp.task(
    'clean:css',
    gulp.series(
      cleanCss
    )
  );
};
