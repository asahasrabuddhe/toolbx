let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .js('resources/assets/js/downloads.js', 'public/js')
   .copy('resources/assets/images/*', 'public/images')
   .copy('node_modules/datatables/media/css/jquery.dataTables.min.css', 'public/css')
   .copy('node_modules/select2/dist/css/select2.min.css', 'public/css')
   .copy('node_modules/select2/dist/js/select2.full.min.js', 'public/js')
   .copy('node_modules/tinymce', 'public/tinymce');
