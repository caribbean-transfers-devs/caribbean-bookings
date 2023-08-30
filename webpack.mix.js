// webpack.mix.js

let mix = require('laravel-mix');
const data = {
    src: "resources/", // source files
    dist: "public/", // build files
    assets: "public/assets/" //build assets files
};

if (mix.inProduction()) {
    mix.version();     
}

mix.sass('resources/scss/core/fonts.scss', data.assets + "css/base/fonts.min.css");
mix.copy('resources/css/base.min.css', data.assets + "css/base/base.min.css");
mix.copy('resources/js/base.js', data.assets + "js/base.min.js");
mix.copy('resources/js/datatables.js', data.assets + "js/datatables.js");