const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ]);

mix.styles([
    'public/frontend/css/bootstrap.min.css',
    'public/frontend/css/open-iconic-bootstrap.min.css',
    'public/frontend/css/animate.css',
    'public/frontend/css/owl.carousel.min.css',
    'public/frontend/css/owl.theme.default.min.css',
    'public/frontend/css/icomoon.css',
    'public/frontend/css/style.css',
], 'public/frontend/css/all.css');

mix.scripts([
        'public/frontend/js/jquery.min.js',
        'public/frontend/js/popper.min.js',
        'public/frontend/js/bootstrap.min.js',
        'public/frontend/js/jquery.easing.1.3.js',
        'public/frontend/js/jquery.waypoints.min.js',
        'public/frontend/js/owl.carousel.min.js',
        'public/frontend/js/jquery.animateNumber.min.js',
        'public/frontend/js/main.js',
    ], 'public/frontend/js/all.js');

if (mix.inProduction()) {
    mix.version();
}
