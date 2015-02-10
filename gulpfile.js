/**
 * Created by me6iaton on 02.06.14.
 */
var gulp = require('gulp')
,concat = require('gulp-concat')
//,clean = require("gulp-clean")

var paths = {
  styles: [
    './assets/components/ms2form/css/www/_custom.css'
    ,'./assets/components/ms2form/vendor/jgrowl/jquery.jgrowl.min.css'
    ,'./assets/components/ms2form/vendor/select2/select2.css'
    ,'./assets/components/ms2form/vendor/select2/select2-bootstrap.css'
    ,'./assets/components/ms2form/vendor/bootstrap-markdown/css/bootstrap-markdown.min.css'
  ]
  ,buildCss: './assets/components/ms2form/css/www/'
};


// A development task to run anytime a file changes
gulp.task('default', ['styles']);

// Delete the build directory

gulp.task('cleanStyles', function() {
  return gulp.src(paths.styles.build)
  .pipe(clean());
});

//Rename Styles
gulp.task('styles', function() {
  gulp.src(paths.styles)
    .pipe(concat('ms2form.css'))
    .pipe(gulp.dest(paths.buildCss))
});
