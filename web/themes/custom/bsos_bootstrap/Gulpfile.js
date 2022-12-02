var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var minifyCSS = require('gulp-cssnano');
var prefix = require('gulp-autoprefixer');
var del = require ('del');

// gulp.task('scripts', function () {
// return gulp.src(['js/src/**/*.js'])
//   .pipe(concat('main.min.js'))
//   .pipe(uglify())
//   .pipe(gulp.dest('js/dist'));
// });

gulp.task('styles', function () {
  return gulp.src('scss/**/*.scss')
    .pipe(sass())
    .pipe(minifyCSS())
    .pipe(prefix())
    .pipe(gulp.dest('css'));
});

// gulp.task('test', function() {
//   return gulp.src(['js/src/**/*.js'])
//     .pipe(jshint())
//     .pipe(jshint.reporter('default'))
//     .pipe(jshint.reporter('fail'));
// });

gulp.task('clean', function(done) {
  del(['css'], done);
});

gulp.task('watch', function(done) {
  gulp.watch(['js/src/**/*.js', ['test', 'scripts']]);
  gulp.watch(['scss/**/*.scss'], ['styles']);
  done();
});

gulp.task('default', ['watch', 'clean', 'styles']);