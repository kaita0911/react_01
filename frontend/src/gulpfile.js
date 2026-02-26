const gulp = require("gulp")
const sass = require("gulp-sass")(require("sass"))

function compileSass() {
  return gulp
    .src("scss/**/*.scss")   // folder SCSS
    .pipe(sass().on("error", sass.logError))
    .pipe(gulp.dest("css"))  // output CSS
}

function watchFiles() {
  gulp.watch("scss/**/*.scss", compileSass)
}

exports.default = gulp.series(compileSass, watchFiles)
