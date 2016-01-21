
/*
 * This file is part of the Lug package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

var gulp = require('gulp');
var concat = require('gulp-concat');
var gulpif = require('gulp-if');
var uglify = require('gulp-uglify');
var uglifycss = require('gulp-uglifycss');

var prod = process.env.GULP_ENV === 'prod';
var dest = 'web/assets/';

var paths = {
    js: [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/admin-lte/dist/js/app.min.js',
        'src/Bundle/*/Resources/js/**/*.js'
    ],
    css: [
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/admin-lte/dist/css/AdminLTE.min.css',
        'node_modules/admin-lte/dist/css/skins/skin-blue.min.css',
        'node_modules/font-awesome/css/font-awesome.min.css'
    ],
    fonts: [
        'node_modules/font-awesome/fonts/**'
    ]
};

gulp.task('js', function () {
    return gulp.src(paths.js)
        .pipe(concat('app.js'))
        .pipe(gulpif(prod, uglify()))
        .pipe(gulp.dest(dest + 'js'));
});

gulp.task('css', function() {
    return gulp.src(paths.css)
        .pipe(concat('app.css'))
        .pipe(gulpif(prod, uglifycss()))
        .pipe(gulp.dest(dest + 'css'));
});

gulp.task('fonts', function () {
    return gulp.src(paths.fonts).pipe(gulp.dest(dest + 'fonts'));
});

gulp.task('watch', function() {
    gulp.watch(paths.js, ['js']);
    gulp.watch(paths.css, ['css']);
    gulp.watch(paths.fonts, ['fonts']);
});

gulp.task('default', ['js', 'css', 'fonts']);
