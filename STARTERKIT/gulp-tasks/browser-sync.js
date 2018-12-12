/**
 * @file
 * Task: Browsersync.
 */

/* global module */

module.exports = function (gulp, plugins, options) {
  'use strict';

  const browserSync = plugins.browserSync.create();

  gulp.task('browsersync', function () {
    browserSync.init(options.browserSync);
  });

  gulp.task('browsersync:reload', function (done) {
    browserSync.reload();
    done();
  });
};
