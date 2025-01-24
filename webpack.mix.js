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

mix.copy('resources/js/views/reservations/reservationsIndex.js', data.assets + "js/views/reservations/reservationsIndex.js");

mix.sass('resources/scss/dashboards/admin.scss', data.assets + "css/dashboards/admin.min.css");

/************************************************************
 * NUEVOS ESTILOS Y SCRIPT PARA EL PANEL DE CONTROL
************************************************************/

        /************************************************************
         * CSS
        ************************************************************/

        mix.sass('resources/scss/core.scss', data.assets + '/css/core/core.min.css');//core styles
        mix.styles(['resources/plugins/font-icons/fontawesome/css/regular.css','resources/plugins/font-icons/fontawesome/css/fontawesome.css'], data.assets + '/css/panel/panel.min.css');//panel styles
        mix.sass('resources/scss/panel.scss', data.assets + "css/panel/panel2.min.css");//panel2 styles
        mix.sass('resources/scss/error.scss', data.assets + "css/panel/error.min.css");//error styles


        mix.sass('resources/scss/sections/tpv.scss', data.assets + "css/sections/tpv.min.css"); //TPV
        mix.sass('resources/scss/sections/reservation_details.scss', data.assets + "css/sections/reservation_details.min.css"); //RESERVATION DETAILS

        mix.sass('resources/scss/sections/dashboard.scss', data.assets + "css/sections/dashboard.min.css"); //DASHBOARD
        mix.sass('resources/scss/sections/managment.scss', data.assets + "css/sections/managment.min.css"); //REPORTS 

        //STYLES REPORTS
        mix.sass('resources/scss/sections/_report_payments.scss', data.assets + "css/sections/report_payments.min.css"); //PAYMENTS
        mix.sass('resources/scss/sections/_report_sales.scss', data.assets + "css/sections/report_sales.min.css"); //SALES
        mix.sass('resources/scss/sections/_report_cash.scss', data.assets + "css/sections/report_cash.min.css"); //CASH
        mix.sass('resources/scss/sections/_report_cancellations.scss', data.assets + "css/sections/report_cancellations.min.css"); //CANCELLATIONS
        mix.sass('resources/scss/sections/_report_commissions.scss', data.assets + "css/sections/report_commissions.min.css"); //COMMISSIONS
        mix.sass('resources/scss/sections/_report_reservations.scss', data.assets + "css/sections/report_reservations.min.css"); //RESERVATIONS
        mix.sass('resources/scss/sections/_report_operations.scss', data.assets + "css/sections/report_operations.min.css"); //OPERATIONS
        mix.sass('resources/scss/sections/_report_conciliation.scss', data.assets + "css/sections/report_conciliation.min.css"); //CONCILIATION
        mix.sass('resources/scss/sections/_report_receivable.scss', data.assets + "css/sections/report_receivable.min.css"); //CUENTAS POR COBRAR

        //STYLES MANAGEMENT
        mix.sass('resources/scss/sections/_management_confirmation.scss', data.assets + "css/sections/management_confirmation.min.css"); //MANAGEMENT CONFIRMATION
        mix.sass('resources/scss/sections/_management_aftersales.scss', data.assets + "css/sections/management_aftersales.min.css"); //MANAGEMENT POST VENTA

        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/table/datatable/dataTables_fixedHeader.min.js', 'resources/js/sections/operations/spam-v2.js'], data.assets + "js/sections/operations/spam-v2.min.js");
        mix.sass('resources/scss/sections/_new_spam.scss', data.assets + "css/sections/new_spam.min.css"); //MANAGEMENT POST VENTA

        mix.combine(['resources/js/sections/operations/pending.js'], data.assets + "js/sections/operations/pending.min.js");


        mix.sass('resources/scss/sections/_management_spam.scss', data.assets + "css/sections/management_spam.min.css"); //MANAGEMENT SPAM
        mix.sass('resources/scss/sections/_management_reservations.scss', data.assets + "css/sections/management_reservations.min.css"); //MANAGEMENT RESERVATIONS
        mix.sass('resources/scss/sections/operations.scss', data.assets + "css/sections/operations.min.css"); //MANAGEMENT OPERATIONS

        mix.sass('resources/scss/sections/_settings_enterprises.scss', data.assets + "css/sections/settings_enterprises.min.css"); //SETTINGS ENTERPRISES
        mix.sass('resources/scss/sections/_settings_vehicles.scss', data.assets + "css/sections/settings_vehicles.min.css"); //SETTINGS VEHICLES
        mix.sass('resources/scss/sections/_settings_drivers.scss', data.assets + "css/sections/settings_drivers.min.css"); //SETTINGS DRIVERS
        mix.sass('resources/scss/sections/_settings_exchanges.scss', data.assets + "css/sections/settings_exchanges.min.css"); //SETTINGS EXCHANGE

        mix.sass('resources/scss/sections/zones.scss', data.assets + "css/sections/zones.min.css"); //ZONES
        mix.sass('resources/scss/sections/rates.scss', data.assets + "css/sections/rates.min.css"); //RATES
        mix.sass('resources/scss/sections/users.scss', data.assets + "css/sections/users.min.css"); //USERS
        mix.sass('resources/scss/sections/enterprise.scss', data.assets + "css/sections/enterprise.min.css");
        mix.sass('resources/scss/sections/enterprise_forms.scss', data.assets + "css/sections/enterprise_forms.min.css");
        mix.sass('resources/scss/sections/vehicle.scss', data.assets + "css/sections/vehicle.min.css");
        mix.sass('resources/scss/sections/vehicle_forms.scss', data.assets + "css/sections/vehicle_forms.min.css");
        mix.sass('resources/scss/sections/driver.scss', data.assets + "css/sections/driver.min.css");
        mix.sass('resources/scss/sections/driver_forms.scss', data.assets + "css/sections/driver_forms.min.css");

        /************************************************************
         * JS
        ************************************************************/        

        mix.combine(['resources/plugins/global/vendors.min.js','resources/js/bootstrap.bundle.min.js','resources/plugins/font-icons/feather/feather.min.js','resources/plugins/sweetalerts2/sweetalerts22.min.js'], data.assets + '/js/core/core.min.js');//core scripts
        mix.combine(['resources/plugins/perfect-scrollbar/perfect-scrollbar.min.js','resources/plugins/mousetrap/mousetrap.min.js','resources/plugins/waves/waves.min.js','resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/waitMe/waitme.js', 'resources/js/caribbean.js','resources/js/components.js'], data.assets + '/js/panel/panel.min.js');//panel scripts
        mix.combine(['resources/plugins/perfect-scrollbar/perfect-scrollbar.min.js','resources/plugins/mousetrap/mousetrap.min.js','resources/plugins/waves/waves.min.js','resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/waitMe/waitme.js','resources/js/components.js'], data.assets + '/js/panel/panel_custom.min.js');//panel scripts

        //DASHBOARD
        mix.combine(['resources/plugins/apex/apexcharts.min.js','resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js','resources/js/sections/dashboard.js'], data.assets + "js/sections/dashboard.min.js");
        mix.combine(['resources/plugins/apex/apexcharts.min.js','resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js','resources/js/sections/dashboard2.js'], data.assets + "js/sections/dashboard2.min.js");

        //TPV
        mix.combine(['resources/plugins/flatpickr/flatpickr.js', 'resources/js/sections/tpv/index.js'], data.assets + "js/sections/tpv/index.min.js");

        //RESERVATION DETAILS
        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/reservations/details.js'], data.assets + "js/sections/reservations/details.min.js");

        //REPORTS
        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/operations/managment.js'], data.assets + "js/sections/operations/managment.min.js");
        // mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/reports/payments.js'], data.assets + "js/sections/reports/payments.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/payments.js'], data.assets + "js/sections/reports/payments.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/sales.js'], data.assets + "js/sections/reports/sales.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/cash.js'], data.assets + "js/sections/reports/cash.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/cancellations.js'], data.assets + "js/sections/reports/cancellations.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/commissions.js'], data.assets + "js/sections/reports/commissions.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/reservations.js'], data.assets + "js/sections/reports/reservations.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/operations.js'], data.assets + "js/sections/reports/operations.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/conciliation.js'], data.assets + "js/sections/reports/conciliation.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/reports/receivable.js'], data.assets + "js/sections/reports/receivable.min.js");

        //MANAGEMENT
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/operations/confirmation.js'], data.assets + "js/sections/operations/confirmation.min.js");

        //OPERATION        
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/table/datatable/dataTables_fixedHeader.min.js', 'resources/js/sections/operations/spam.js'], data.assets + "js/sections/operations/spam.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/plugins/bootstrap-select/select.js', 'resources/js/sections/components/filters.js', 'resources/js/sections/operations/reservations.js'], data.assets + "js/sections/operations/reservations.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/bootstrap-select/select.js', 'resources/plugins/flatpickr/flatpickr.js', 'resources/js/sections/operations/operations.js'], data.assets + "js/sections/operations/operations.min.js");

        //SETTINGS
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/settings/enterprises.js'], data.assets + "js/sections/settings/enterprises.min.js"); //ENTERPRISES
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/settings/vehicles.js'], data.assets + "js/sections/settings/vehicles.min.js"); //VEHICLES
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/settings/drivers.js'], data.assets + "js/sections/settings/drivers.min.js"); //DRIVERS
        mix.combine(['resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/settings/exchanges.js'], data.assets + "js/sections/settings/exchanges.min.js"); //EXCHANGE

        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/zones.js'], data.assets + "js/sections/zones.min.js"); //ZONES
        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/rates.js'], data.assets + "js/sections/rates.min.js"); //RATES

        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/users.js'], data.assets + "js/sections/users.min.js");
        mix.combine(['resources/plugins/choices/choices.js'], data.assets + "js/sections/user_edit.min.js");
        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/roles.js'], data.assets + "js/sections/roles.min.js");
