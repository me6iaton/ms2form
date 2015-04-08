/**
 * Created by me6iaton on 02.06.14.
 */
var gulp = require('gulp')
,concat = require('gulp-concat')
  ,sass = require('gulp-sass')
  , clean = require("gulp-clean");


var paths = {
  sass: [
    './assets/components/ms2form/css/web/custom.sass'
  ]
  ,css: [
    './assets/components/ms2form/vendor/jgrowl/jquery.jgrowl.min.css'
    ,'./assets/components/ms2form/vendor/select2/select2.css'
    ,'./assets/components/ms2form/vendor/select2/select2-bootstrap.css'
    ,'./assets/components/ms2form/vendor/bootstrap-markdown/css/bootstrap-markdown.min.css'
    ,'./assets/components/ms2form/css/web/jquery-ui-1.10.4.custom.css'
    ,'./assets/components/ms2form/css/web/custom.css'
  ]
  ,cssDest: './assets/components/ms2form/css/web/'
};


gulp.task('default', ['watch']);

// A development task to run anytime a file changes
gulp.task('watch', function () {
  gulp.watch(paths.sass, ['css']);
});

gulp.task('sass', function() {
  return gulp.src(paths.sass)
    .pipe(sass({indentedSyntax: true}))
    .pipe(gulp.dest(paths.cssDest))
});

//Rename Styles
gulp.task('css', ['sass'], function() {
  return gulp.src(paths.css)
    .pipe(concat('ms2form.css'))
    .pipe(gulp.dest(paths.cssDest))
});
