'use strict';

const gulp = require('gulp');
const config = require('./tasks.config');
const tasks = require('boar-tasks-server').getTasks(gulp, config);
const eslint = require('gulp-eslint');

gulp.task('test', ['code-style', 'server-test']);
gulp.task('code-style', () => {
  return gulp
    .src(['**/*.js', '!./node_modules/**'])
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failAfterError());
});
gulp.task('server-test', tasks.server.test);
