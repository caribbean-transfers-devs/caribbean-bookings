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
mix.copy('resources/js/sweetalert2.all.min.js', data.assets + "js/sweetalert2.js");
mix.copy('resources/js/views/userIndex.js', data.assets + "js/views/userIndex.js");
mix.copy('resources/js/views/rolesIndex.js', data.assets + "js/views/rolesIndex.js");
mix.copy('resources/js/views/reservationsIndex.js', data.assets + "js/views/reservationsIndex.js");
mix.sass('resources/scss/reservations/detail.scss', data.assets + "css/reservations/detail.min.css");
mix.sass('resources/scss/tpv/index.scss', data.assets + "css/tpv/index.min.css");
mix.copy('resources/js/views/tpv/index.js', data.assets + "js/views/tpv/index.min.js");
mix.copy('resources/js/autoComplete.min.js', data.assets + "js/autoComplete.min.js");