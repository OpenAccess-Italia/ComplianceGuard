const mix = require('laravel-mix');
const rimraf = require('rimraf');
const path = require('path');

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

// 3rd party plugins css/js
mix.sass('resources/plugins/plugins.scss', 'public/plugins/global/plugins.bundle.css').then(() => {
    // remove unused preprocessed fonts folder
    rimraf(path.resolve('public/fonts'), () => {});
    rimraf(path.resolve('public/images'), () => {});
}).sourceMaps(!mix.inProduction())
    // .setResourceRoot('./')
    .options({processCssUrls: false}).js(['resources/plugins/plugins.js'], 'public/plugins/global/plugins.bundle.js');


mix.sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/app.js', 'public/js');

// copy images folder into laravel public folder
mix.copyDirectory('resources/demo1/src/media', 'public/assets/media');

// Global jquery
//mix.autoload({
//'jquery': ['$', 'jQuery'],
//Popper: ['popper.js', 'default'],
//});

/**
 * plugins specific issue workaround for webpack
 * @see https://github.com/morrisjs/morris.js/issues/697
 * @see https://stackoverflow.com/questions/33998262/jquery-ui-and-webpack-how-to-manage-it-into-module
 */
mix.webpackConfig({
    resolve: {
        alias: {
            'morris.js': 'morris.js/morris.js',
            'jquery-ui': 'jquery-ui',
        },
    },
});