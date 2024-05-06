const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .copy('vendor/almasaeed2010/adminlte/dist/js/adminlte.min.js', 'public/js/adminlte.min.js')
    .copy('vendor/almasaeed2010/adminlte/dist/css/adminlte.min.css', 'public/css/adminlte.min.css');
