let mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

mix
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .less('resources/less/admin-lte/AdminLTE.less', 'public/css')
    .options({
        processCssUrls:false,
        postCss: [tailwindcss('./tailwind.js')],
    });

if(!mix.inProduction()) {
    mix.browserSync({
        proxy: 'vitalgym.test',
        open: false,
        notify: false,
    }).webpackConfig({
        devtool: 'source-map'
    }).sourceMaps();
}

if (mix.inProduction()) {
    mix.version();
}

mix.purgeCss({
    whitelistPatterns: [/^toast-/],
}).disableNotifications();
