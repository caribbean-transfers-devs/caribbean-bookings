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
mix.copy('resources/js/easypick.js', data.assets + "js/easypick.min.js");

mix.copy('resources/js/views/userIndex.js', data.assets + "js/views/userIndex.js");
mix.copy('resources/js/views/rolesIndex.js', data.assets + "js/views/rolesIndex.js");
mix.copy('resources/js/views/reservations/reservationsIndex.js', data.assets + "js/views/reservations/reservationsIndex.js");
mix.copy('resources/js/views/reservations/reservationsDetail.js', data.assets + "js/views/reservations/reservationsDetail.js");
mix.sass('resources/scss/reservations/detail.scss', data.assets + "css/reservations/detail.min.css");
mix.sass('resources/scss/reservations/index.scss', data.assets + "css/reservations/index.min.css");
mix.sass('resources/scss/users/index.scss', data.assets + "css/users/index.min.css");
mix.sass('resources/scss/tpv/index.scss', data.assets + "css/tpv/index.min.css");
mix.sass('resources/scss/pos/index.scss', data.assets + "css/pos/index.min.css");
mix.sass('resources/scss/pos/detail.scss', data.assets + "css/pos/detail.min.css");
mix.sass('resources/scss/pos/capture.scss', data.assets + "css/pos/capture.min.css");
mix.sass('resources/scss/pos/vendors.scss', data.assets + "css/pos/vendors.min.css");
mix.sass('resources/scss/zones/index.scss', data.assets + "css/zones/index.min.css");
mix.copy('resources/js/views/tpv/index.js', data.assets + "js/views/tpv/index.min.js");
mix.copy('resources/js/views/pos/index.js', data.assets + "js/views/pos/index.min.js");
mix.copy('resources/js/views/pos/detail.js', data.assets + "js/views/pos/detail.min.js");
mix.copy('resources/js/views/pos/capture.js', data.assets + "js/views/pos/capture.min.js");
mix.copy('resources/js/views/pos/vendors.js', data.assets + "js/views/pos/vendors.min.js");
mix.copy('resources/js/views/zones/index.js', data.assets + "js/views/zones/index.min.js");
mix.sass('resources/scss/rates/index.scss', data.assets + "css/rates/index.min.css");
mix.sass('resources/scss/dashboards/admin.scss', data.assets + "css/dashboards/admin.min.css");

mix.copy('resources/js/views/rates/index.js', data.assets + "js/views/rates/index.min.js");
mix.copy('resources/js/views/operation/managment.js', data.assets + "js/views/operation/managment.min.js");
mix.copy('resources/js/views/operation/confirmation.js', data.assets + "js/views/operation/confirmation.min.js");
mix.copy('resources/js/views/operation/spam.js', data.assets + "js/views/operation/spam.min.js");
mix.copy('resources/js/views/reports/cash.js', data.assets + "js/views/reports/cash.min.js");