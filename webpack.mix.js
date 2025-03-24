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
        
        mix.sass('resources/scss/sections/reservation_details.scss', data.assets + "css/sections/reservation_details.min.css"); //RESERVATION DETAILS

        //DASHBOARD
            mix.sass('resources/scss/sections/dashboard/_management.scss', data.assets + "css/sections/dashboard/management.min.css"); //DASHBOARD DE GERENCIA
            mix.sass('resources/scss/sections/dashboard/_callcenteragent.scss', data.assets + "css/sections/dashboard/callcenteragent.min.css"); //DASHBOAR DE AGENTE DE CALL CENTER

        // TPV
            mix.sass('resources/scss/sections/tpv.scss', data.assets + "css/sections/tpv.min.css"); //TPV

        // FINANCES
            mix.sass('resources/scss/sections/finances/_refunds.scss', data.assets + "css/sections/finances/refunds.min.css"); //REEMBOLSOS

        //STYLES REPORTS
            mix.sass('resources/scss/sections/reports/_payments.scss', data.assets + "css/sections/reports/payments.min.css"); //PAYMENTS
            mix.sass('resources/scss/sections/reports/_cash.scss', data.assets + "css/sections/reports/cash.min.css"); //CASH
            mix.sass('resources/scss/sections/reports/_cancellations.scss', data.assets + "css/sections/reports/cancellations.min.css"); //CANCELLATIONS
            mix.sass('resources/scss/sections/reports/_commissions.scss', data.assets + "css/sections/reports/commissions.min.css"); //COMMISSIONS
            mix.sass('resources/scss/sections/reports/_commissions2.scss', data.assets + "css/sections/reports/commissions2.min.css"); //COMMISSIONS2
            mix.sass('resources/scss/sections/reports/_sales.scss', data.assets + "css/sections/reports/sales.min.css"); //SALES
            mix.sass('resources/scss/sections/reports/_operations.scss', data.assets + "css/sections/reports/operations.min.css"); //OPERATIONS
            mix.sass('resources/scss/sections/reports/_conciliations.scss', data.assets + "css/sections/reports/conciliations.min.css"); //CONCILIATIONS
            mix.sass('resources/scss/sections/reports/_receivables.scss', data.assets + "css/sections/reports/receivables.min.css"); //RECEIVABLES

        //STYLES MANAGEMENT            
            mix.sass('resources/scss/sections/management/_confirmations.scss', data.assets + "css/sections/management/confirmations.min.css"); //CONFIRMATIONS            
            mix.sass('resources/scss/sections/management/_aftersales.scss', data.assets + "css/sections/management/aftersales.min.css"); //AFTER SALES    
            mix.sass('resources/scss/sections/management/_reservations.scss', data.assets + "css/sections/management/reservations.min.css"); //RESERVATIONS
            mix.sass('resources/scss/sections/management/_operations.scss', data.assets + "css/sections/management/operations.min.css"); //OPERATIONS

        //STYLES SETTINGS
            mix.sass('resources/scss/sections/settings/_roles.scss', data.assets + "css/sections/settings/roles.min.css"); //ROLES
            mix.sass('resources/scss/sections/settings/_users.scss', data.assets + "css/sections/settings/users.min.css"); //USERS
            mix.sass('resources/scss/sections/settings/_enterprises.scss', data.assets + "css/sections/settings/enterprises.min.css"); //ENTERPRISES
            mix.sass('resources/scss/sections/settings/_sites.scss', data.assets + "css/sections/settings/sites.min.css"); //ENTERPRISES
            mix.sass('resources/scss/sections/settings/_vehicles.scss', data.assets + "css/sections/settings/vehicles.min.css"); //VEHICLES
            mix.sass('resources/scss/sections/settings/_drivers.scss', data.assets + "css/sections/settings/drivers.min.css"); //DRIVERS
            mix.sass('resources/scss/sections/settings/_exchanges_reports.scss', data.assets + "css/sections/settings/exchanges_reports.min.css"); //EXCHANGE REPORTS
            mix.sass('resources/scss/sections/settings/_zones.scss', data.assets + "css/sections/settings/zones.min.css"); //ZONES        
            mix.sass('resources/scss/sections/settings/_rates.scss', data.assets + "css/sections/settings/rates.min.css"); //RATES
            mix.sass('resources/scss/sections/settings/_rates_enterprise.scss', data.assets + "css/sections/settings/rates_enterprise.min.css"); //RATES ENTERPRISE
            mix.sass('resources/scss/sections/settings/_types_cancellations.scss', data.assets + "css/sections/settings/types_cancellations.min.css"); //TYPES CANCELLATIONS


        mix.sass('resources/scss/sections/enterprise_forms.scss', data.assets + "css/sections/enterprise_forms.min.css");
        mix.sass('resources/scss/sections/vehicle_forms.scss', data.assets + "css/sections/vehicle_forms.min.css");
        mix.sass('resources/scss/sections/driver_forms.scss', data.assets + "css/sections/driver_forms.min.css");

        /************************************************************
         * JS
        ************************************************************/        

        mix.combine(['resources/plugins/global/vendors.min.js','resources/js/bootstrap.bundle.min.js','resources/plugins/font-icons/feather/feather.min.js','resources/plugins/sweetalerts2/sweetalerts22.min.js'], data.assets + '/js/core/core.min.js');//core scripts
        mix.combine(['resources/plugins/perfect-scrollbar/perfect-scrollbar.min.js','resources/plugins/mousetrap/mousetrap.min.js','resources/plugins/waves/waves.min.js','resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/waitMe/waitme.js', 'resources/js/caribbean.js','resources/js/components.js'], data.assets + '/js/panel/panel.min.js');//panel scripts
        mix.combine(['resources/plugins/perfect-scrollbar/perfect-scrollbar.min.js','resources/plugins/mousetrap/mousetrap.min.js','resources/plugins/waves/waves.min.js','resources/plugins/notification/snackbar/snackbar.min.js', 'resources/plugins/waitMe/waitme.js','resources/js/components.js'], data.assets + '/js/panel/panel_custom.min.js');//panel scripts

        //DASHBOARD
        mix.combine(['resources/plugins/apex/apexcharts.min.js', 'resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/dashboard/management.js'], data.assets + "js/sections/dashboard/management.min.js");
        mix.combine(['resources/plugins/apex/apexcharts.min.js', 'resources/plugins/table/datatable/datatables5.js', 'resources/js/sections/dashboard/callcenteragent.js', 'resources/js/sections/management/aftersales.js'], data.assets + "js/sections/dashboard/callcenteragent.min.js");

        //TPV
        mix.combine(['resources/plugins/flatpickr/flatpickr.js', 'resources/js/sections/tpv/index.js'], data.assets + "js/sections/tpv/index.min.js");

        //FINANCES
            //REEMBOLSOS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js',
                'resources/plugins/flatpickr/flatpickr.js',
                'resources/plugins/flatpickr/es.min.js',
                'resources/plugins/flatpickr/monthSelect.js',
                'resources/plugins/bootstrap-select/select.js',
                'resources/js/sections/components/filters.js',
                'resources/plugins/lightbox/lightbox.js',
                'resources/js/sections/finances/refunds.js'
            ], data.assets + "js/sections/finances/refunds.min.js");

        //REPORTS
            //PAYMENTS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/reports/payments.js'
            ], data.assets + "js/sections/reports/payments.min.js");
            
            //CASH
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/reports/cash.js'
            ], data.assets + "js/sections/reports/cash.min.js");

            //CANCELLATIONS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/reports/cancellations.js'
            ], data.assets + "js/sections/reports/cancellations.min.js");

            //COMISIONES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/reports/commissions.js'
            ], data.assets + "js/sections/reports/commissions.min.js");
            mix.combine([
                'resources/plugins/apex/apexcharts.min.js', 
                'resources/plugins/table/datatable/datatables5.js',
                'resources/plugins/flatpickr/flatpickr.js',
                'resources/plugins/flatpickr/es.min.js',
                'resources/plugins/flatpickr/monthSelect.js',
                'resources/plugins/bootstrap-select/select.js',
                'resources/js/sections/components/filters.js',
                'resources/js/sections/reports/commissions_new.js'
            ], data.assets + "js/sections/reports/commissions_new.min.js");

            //SALES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/components/charts.js', 
                'resources/js/sections/reports/sales.js'
            ], data.assets + "js/sections/reports/sales.min.js");

            //OPERATIONS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/components/charts.js', 
                'resources/js/sections/reports/operations.js'
            ], data.assets + "js/sections/reports/operations.min.js");

            //CONCILIATIONS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/reports/conciliations.js'
            ], data.assets + "js/sections/reports/conciliations.min.js");

            //RECEIVABLES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/components/charts.js', 
                'resources/js/sections/reports/receivables.js'
            ], data.assets + "js/sections/reports/receivables.min.js");

        //MANAGEMENT
            //CONDIRMATIONS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js',
                'resources/js/sections/management/confirmations.js'
            ], data.assets + "js/sections/management/confirmations.min.js");

            //AFTERSALES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js',
                'resources/js/sections/management/aftersales.js'
            ], data.assets + "js/sections/management/aftersales.min.js");

            //RESERVATIONS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/bootstrap-select/select.js', 
                'resources/js/sections/components/filters.js', 
                'resources/js/sections/management/reservations.js'
            ], data.assets + "js/sections/management/reservations.min.js");

            //OPERATIONS
            mix.combine([
                // 'resources/plugins/table/datatable/datatables.js', 
                // 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 
                // 'resources/plugins/table/datatable/button-ext/jszip.min.js', 
                // 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js',  
                // 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/notification/snackbar/snackbar.min.js', 
                'resources/plugins/flatpickr/flatpickr.js',
                'resources/plugins/flatpickr/es.min.js',
                'resources/plugins/bootstrap-select/select.js', 
                'resources/plugins/flatpickr/flatpickr.js', 
                'resources/js/sections/management/operations.js'
            ], data.assets + "js/sections/management/operations.min.js");

        //RESERVATION DETAILS
        mix.combine(['resources/plugins/table/datatable/datatables.js', 'resources/plugins/table/datatable/button-ext/dataTables.buttons.min.js', 'resources/plugins/table/datatable/button-ext/jszip.min.js', 'resources/plugins/table/datatable/button-ext/buttons.html5.min.js', 'resources/plugins/table/datatable/button-ext/buttons.print.min.js', 'resources/js/sections/reservations/details.js'], data.assets + "js/sections/reservations/details.min.js");

        //SETTINGS
            //ROLES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/roles.js'
            ], data.assets + "js/sections/settings/roles.min.js");

            //USERS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/users.js'
            ], data.assets + "js/sections/settings/users.min.js");

            //ENTERPRISES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/enterprises.js'
            ], data.assets + "js/sections/settings/enterprises.min.js");

            //SITES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/sites.js'
            ], data.assets + "js/sections/settings/sites.min.js");

            //VEHICLES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/vehicles.js'
            ], data.assets + "js/sections/settings/vehicles.min.js");

            //DRIVERS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/drivers.js'
            ], data.assets + "js/sections/settings/drivers.min.js");

            //SCHEDULES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/plugins/flatpickr/flatpickr.js',
                'resources/plugins/flatpickr/es.min.js',
                'resources/plugins/bootstrap-select/select.js',
                'resources/js/sections/settings/schedules.js'
            ], data.assets + "js/sections/settings/schedules.min.js");            
            
            //EXCHANGES REPORTS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/exchanges_reports.js'
            ], data.assets + "js/sections/settings/exchanges_reports.min.js");

            //ZONES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/zones.js'
            ], data.assets + "js/sections/settings/zones.min.js");

            //RATES
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/rates.js'
            ], data.assets + "js/sections/settings/rates.min.js");

            //RATES ENTERPRISE
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/rates_enterprise.js'
            ], data.assets + "js/sections/settings/rates_enterprise.min.js");

            //TYPES CANCELLATIONS
            mix.combine([
                'resources/plugins/table/datatable/datatables5.js', 
                'resources/js/sections/settings/types_cancellations.js'
            ], data.assets + "js/sections/settings/types_cancellations.min.js");

        mix.combine(['resources/plugins/choices/choices.js'], data.assets + "js/sections/user_edit.min.js");

//tpv styles
mix.sass('resources/scss/tpv/one.scss', data.assets + "css/sections/tpv2.min.css");
//tpv scripts
mix.combine(['resources/js/libs/validator.js', 'resources/js/libs/intlTelInput.min.js', 'resources/js/sections/tpv/index2.js'], data.assets + "js/views/tpv/index2.min.js");

//booking details styles
mix.sass('resources/scss/sections/booking_details.scss', data.assets + "css/sections/booking/details.min.css");
// mix.sass('resources/scss/process/cancel.scss', data.assets + "/css/sections/booking/cancel.min.css");
// mix.sass('resources/scss/process/success.scss', data.assets + "/css/sections/booking/success.min.css");