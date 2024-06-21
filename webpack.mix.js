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
// mix.js('resources/js/app.js', 'public/js');

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

/************************************************************
 * NUEVOS ESTILOS Y SCRIPT PARA EL PANEL DE CONTROL
************************************************************/

mix.sass('resources/scss/core.scss', data.assets + '/css/core/core.min.css');//core styles
mix.styles(['resources/plugins/font-icons/fontawesome/css/regular.css','resources/plugins/font-icons/fontawesome/css/fontawesome.css'], data.assets + '/css/panel/panel.min.css');//panel styles
mix.sass('resources/scss/panel.scss', data.assets + "css/panel/panel2.min.css");//panel2 styles
mix.sass('resources/scss/sections/dashboard.scss', data.assets + "css/sections/dashboard.min.css"); //DASHBOARD
mix.sass('resources/scss/sections/managment.scss', data.assets + "css/sections/managment.min.css"); //REPORTS

mix.sass('resources/scss/sections/download.scss', data.assets + "css/sections/download.min.css"); //DOWNLOAD
mix.sass('resources/scss/sections/management.scss', data.assets + "css/sections/management.min.css"); //MANAGEMENT
mix.sass('resources/scss/sections/confirmation.scss', data.assets + "css/sections/confirmation.min.css"); //CONFIRMATION
mix.sass('resources/scss/sections/ccform.scss', data.assets + "css/sections/ccform.min.css"); //CCFORM
mix.sass('resources/scss/sections/spam.scss', data.assets + "css/sections/spam.min.css"); //SPAM
mix.sass('resources/scss/sections/operations.scss', data.assets + "css/sections/operations.min.css"); //OPERATIONS

mix.sass('resources/scss/sections/zones.scss', data.assets + "css/sections/zones.min.css"); //ZONES
mix.sass('resources/scss/sections/rates.scss', data.assets + "css/sections/rates.min.css"); //RATES
mix.sass('resources/scss/sections/users.scss', data.assets + "css/sections/users.min.css"); //USERS
mix.sass('resources/scss/sections/enterprise.scss', data.assets + "css/sections/enterprise.min.css");
mix.sass('resources/scss/sections/enterprise_forms.scss', data.assets + "css/sections/enterprise_forms.min.css");
mix.sass('resources/scss/sections/vehicle.scss', data.assets + "css/sections/vehicle.min.css");
mix.sass('resources/scss/sections/vehicle_forms.scss', data.assets + "css/sections/vehicle_forms.min.css");
mix.sass('resources/scss/sections/driver.scss', data.assets + "css/sections/driver.min.css");
mix.sass('resources/scss/sections/driver_forms.scss', data.assets + "css/sections/driver_forms.min.css");

mix.combine(['resources/plugins/global/vendors.min.js','resources/js/bootstrap.bundle.min.js','resources/plugins/sweetalerts2/sweetalerts22.min.js'], data.assets + '/js/core/core.min.js');//core scripts
mix.combine(['resources/js/loader.js','resources/plugins/perfect-scrollbar/perfect-scrollbar.min.js','resources/plugins/mousetrap/mousetrap.min.js','resources/plugins/waves/waves.min.js','resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/waitMe/waitme.js', 'resources/js/caribbean.js','resources/js/components.js'], data.assets + '/js/panel/panel.min.js');//panel scripts
mix.combine(['resources/js/loader.js','resources/plugins/perfect-scrollbar/perfect-scrollbar.min.js','resources/plugins/mousetrap/mousetrap.min.js','resources/plugins/waves/waves.min.js','resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/waitMe/waitme.js','resources/js/components.js'], data.assets + '/js/panel/panel_custom.min.js');//panel scripts
//DASHBOARD
mix.combine(['resources/plugins/apex/apexcharts.min.js','resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js','resources/js/sections/dashboard.js'], data.assets + "js/sections/dashboard.min.js");

//REPORTS
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/operations/managment.js'], data.assets + "js/sections/operations/managment.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/tomSelect/tom-select.base.js', 'resources/js/sections/reservations/sales.js'], data.assets + "js/sections/reservations/sales.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/reports/commissions.js'], data.assets + "js/sections/reports/commissions.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/reports/cash.js'], data.assets + "js/sections/reports/cash.min.js");

//POST
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/tomSelect/tom-select.base.js', 'resources/js/sections/pos/sales.js'], data.assets + "js/sections/pos/sales.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/pos/sellers.js'], data.assets + "js/sections/pos/sellers.min.js");

//OPERATION
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/operations/download.js'], data.assets + "js/sections/operations/download.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/operations/management.js'], data.assets + "js/sections/operations/management.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/operations/confirmation.js'], data.assets + "js/sections/operations/confirmation.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/operations/spam.js'], data.assets + "js/sections/operations/spam.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/bootstrap-select/bootstrap-select.js', 'resources/js/sections/operations/operations.js'], data.assets + "js/sections/operations/operations.min.js");

//BOOKINGS
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/tomSelect/tom-select.base.js', 'resources/js/sections/reservations/bookings.js'], data.assets + "js/sections/reservations/bookings.min.js");

//SETTINGS
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/zones.js'], data.assets + "js/sections/zones.min.js"); //ZONES
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/rates.js'], data.assets + "js/sections/rates.min.js"); //RATES

mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/users.js'], data.assets + "js/sections/users.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/roles.js'], data.assets + "js/sections/roles.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/enterprise.js'], data.assets + "js/sections/enterprise.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/vehicle.js'], data.assets + "js/sections/vehicle.min.js");
mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/driver.js'], data.assets + "js/sections/driver.min.js");
