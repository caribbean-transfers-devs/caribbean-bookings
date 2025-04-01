<?php

use App\Http\Controllers\Bots\MasterToursController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Accounting\ConciliationController;

//DASHBOARD
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CallCenterController;

//TPV
use App\Http\Controllers\Tpv\TpvController as TPV;
use App\Http\Controllers\Tpv\TpvController2;
use App\Http\Controllers\Tpv\Api\AutocompleteController as APIAutocomplete;
use App\Http\Controllers\Tpv\Api\QuoteController as APIQuote;
use App\Http\Controllers\Bookings\BookingsController;

//FINANCES
use App\Http\Controllers\Finances\RefundsController as RefundsFinances;
use App\Http\Controllers\Finances\SalesController as SaleFinance;

//REPORTS
use App\Http\Controllers\Reports\PaymentsController as PAYMENTS;
use App\Http\Controllers\Reports\CashController as CASH;
use App\Http\Controllers\Reports\CancellationsController as CANCELLATIONS;
use App\Http\Controllers\Reports\CommissionsController as COMMISSIONS;
use App\Http\Controllers\Reports\SalesController as SALES;
use App\Http\Controllers\Reports\OperationsController as OPERATIONSS;

//MANAGEMENT
use App\Http\Controllers\Management\ConfirmationsController;
use App\Http\Controllers\Management\AfterSalesController;
use App\Http\Controllers\Management\QuotationController as QUOTATION;
use App\Http\Controllers\Management\PendingController as PENDING;
use App\Http\Controllers\Management\SpamController as SPAM;
use App\Http\Controllers\Management\ReservationsController as RESERVATIONS;
use App\Http\Controllers\Operations\OperationsController as Operations;

//SETTINGS
use App\Http\Controllers\Settings\RoleController as ROLES;
use App\Http\Controllers\Settings\UserController as USERS;
use App\Http\Controllers\Settings\EnterpriseController as ENTERPRISES;
use App\Http\Controllers\Settings\SitesController as SITES;
use App\Http\Controllers\Settings\VehicleController as VEHICLES;
use App\Http\Controllers\Settings\DriverController as DRIVERS;
use App\Http\Controllers\Settings\DriverSchedulesController as SCHEDULES;
use App\Http\Controllers\Settings\ExchangeReportsController as EXCHANGE_REPORTS;
use App\Http\Controllers\Settings\ZonesController as ZONES;
use App\Http\Controllers\Settings\RatesController as RATES;
use App\Http\Controllers\Settings\RatesEnterpriseController as RATES_ENTERPRISE;
use App\Http\Controllers\Settings\TypesCancellationsController as TYPES_CANCELLATIONS;

//DETAILS RESERVATION
use App\Http\Controllers\Reservations\ReservationsController as DETAILS_RESERVATION;

//GENERALS
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Payments\PaymentsController;

//ACTIONS
use App\Http\Controllers\Actions\FinanceController as FINANCE;
use App\Http\Controllers\Actions\ActionsController as ACTIONS_RESERVATION;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/qr/create/{type}/{id}/{language}', [TpvController2::class, 'createQr'])->name('qr.createQr');
Route::post('/tpv2/autocomplete', [APIAutocomplete::class,'index']);
Route::post('/tpv2/quote', [APIQuote::class,'index']);
Route::post('/tpv2/re-quote', [APIQuote::class,'checkout']);

//TPV AND BOOKING DETAILS
Route::middleware(['locale','ApiChecker'])->group(function () {
    Route::get('/tpv2/book/{id}', [TpvController2::class, 'book'])->name('tpv.book');
    Route::post('/tpv2/book/{id}/make', [TpvController2::class, 'create'])->name('tpv.create.en');

    Route::get('/thank-you', [TpvController2::class, 'success'])->name('process.success');
    Route::get('/cancel', [TpvController2::class, 'cancel'])->name('process.cancel');
    Route::get('/my-reservation-detail', [BookingsController::class, 'ReservationDetail'])->name('reservation.detail');

    Route::prefix('{locale}')->where(['locale' => '[a-zA-Z]{2}', 'ApiChecker'])->group(function () {
        Route::get('/tpv2/book/{id}', [TpvController2::class, 'book'])->name('tpv.book.es');
        Route::post('/tpv2/book/{id}/make', [TpvController2::class, 'create'])->name('tpv.create.es');

        Route::get('/thank-you', [TpvController2::class, 'success'])->name('process.success.es');
        Route::get('/cancel', [TpvController2::class, 'cancel'])->name('process.cancel.es');
        Route::get('/my-reservation-detail', [BookingsController::class, 'ReservationDetail'])->name('reservation.detail.es');
    });
});

Route::middleware(['guest','Debug'])->group(function () {
    Route::get('login', [LoginController::class, 'index']);
    Route::post('login', [LoginController::class, 'check'])->name('login');
});

//Meter al middleware para protejer estas rutas...
Route::group(['middleware' => ['auth', 'Debug']], function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/logout/other/{sessionId}', [LoginController::class, 'logoutOtherSession'])->name('logout.other');    
    Route::post('/logout/all', [LoginController::class, 'logoutAllSessions'])->name('logout.all');

    Route::get('/db-test', function() {
        $start = microtime(true);
        
        // Test 1: Query simple
        DB::select('SELECT 1');
        $time1 = microtime(true) - $start;
        
        // Test 2: Tu query problemático
        $start = microtime(true);
        DB::select('SELECT * FROM users WHERE id = 1 LIMIT 1');
        $time2 = microtime(true) - $start;
        
        return [
            'simple_query' => $time1,
            'problem_query' => $time2,
            'server' => DB::select('SHOW VARIABLES LIKE "%version%"')
        ];
    });

    Route::get('/db-indexes', function() {
        $indexes = DB::select(
            "SHOW INDEXES FROM users WHERE Key_name = 'PRIMARY' OR Column_name = 'id'"
        );
        
        $tableStatus = DB::select("SHOW TABLE STATUS LIKE 'users'");
        
        return [
            'indexes' => $indexes,
            'table_status' => $tableStatus
        ];
    });    

    //BOTS
        //SET RATES MASTER TOUR
        Route::get('/set/rates/MasterTour', [MasterToursController::class, 'ListServicesMasterTour'])->name('list.services.master.tours')->withoutMiddleware(['auth']);

        //PAYPAL
        Route::get('/bot/conciliation/paypal', [ConciliationController::class, 'PayPalPayments'])->name('bot.paypal')->withoutMiddleware(['auth']);
        Route::get('/conciliation/paypalOrders', [ConciliationController::class, 'PayPalPaymenOrders'])->name('bot.paypal.orders')->withoutMiddleware(['auth']);
        Route::get('/conciliation/paypal/{reference}', [ConciliationController::class, 'PayPalPaymenReference'])->name('bot.paypal.reference')->withoutMiddleware(['auth']);
        Route::get('/conciliation/paypalOrder/{id}', [ConciliationController::class, 'PayPalPaymenOrder'])->name('bot.paypal.order')->withoutMiddleware(['auth']);
        //STRIPE
        Route::get('/bot/conciliation/stripe', [ConciliationController::class, 'StripePayments'])->name('bot.stripe')->withoutMiddleware(['auth']);
        Route::get('/conciliation/stripe/{reference}', [ConciliationController::class, 'StripePaymentReference'])->name('bot.stripe.reference')->withoutMiddleware(['auth']);

    //DASHBOARD        
        Route::match(['get', 'post'], '/', [DashboardController::class, 'index'])->name('dashboard'); // GERENCIA
        ////////////
        Route::match(['get', 'post'], '/callcenters', [CallCenterController::class, 'index'])->name('callcenters.index'); // AGENTES DE CALL CENTER
        Route::match(['post','get'], '/callcenters/sales/get', [CallCenterController::class, 'getSales'])->name('callcenters.sales.get');
        Route::match(['post','get'], '/callcenters/operations/get', [CallCenterController::class, 'getOperations'])->name('callcenters.operations.get');
        Route::match(['post','get'], '/callcenters/stats/get', [CallCenterController::class, 'getStats'])->name('callcenters.stats.get');
        Route::match(['post','get'], '/callcenters/stats/charts/sales', [CallCenterController::class, 'chartsSales'])->name('callcenters.charts.sales.get');
        Route::match(['post','get'], '/callcenters/stats/charts/opertions', [CallCenterController::class, 'chartsOperations'])->name('callcenters.charts.operations.get');
        Route::match(['post','get'], '/destinations/list', [CallCenterController::class, 'destinationsList'])->name('destinations.list');

    //TPV        
        Route::get('/tpv/handler', [TPV::class, 'handler'])->name('tpv.handler');
        Route::get('/tpv/edit/{code}', [TPV::class, 'index'])->name('tpv.new');
        Route::post('/tpv/quote', [TPV::class, 'quote'])->name('tpv.quote');
        Route::post('/tpv/create', [TPV::class, 'create'])->name('tpv.create');
        Route::get('/tpv/autocomplete/{keyword}', [TPV::class, 'autocomplete'])->name('tpv.autocomplete');

    //FINANZAS         
        Route::match(['get', 'post'], '/finances/refunds', [RefundsFinances::class, 'index'])->name('finances.refunds'); //REEMBOLSOS
        Route::match(['get', 'post'], '/finances/chargebacks', [RefundsFinances::class, 'index'])->name('finances.chargebacks'); //CONTRAGARGOS

        Route::match(['get', 'post'], '/finance/sales', [SaleFinance::class, 'index'])->name('finance.sales'); //PAGOS

    //REPORTES
        Route::match(['get', 'post'], '/reports/payments', [PAYMENTS::class, 'index'])->name('reports.payments'); //PAGOS
        // Route::match(['post'], '/payments/conciliation', [PAYMENTS::class, 'conciliation'])->name('payments.conciliation');
        Route::match(['get', 'post'], '/reports/cash', [CASH::class, 'index'])->name('reports.cash'); //EFECTIVO
        Route::put('/reports/cash/update-status', [CASH::class, 'update'])->name('reports.cash.action.update'); //EFECTIVO
        ////////////
        Route::match(['get', 'post'], '/reports/cancellations', [CANCELLATIONS::class, 'index'])->name('reports.cancellations'); //CANCELACIONES
        Route::match(['get', 'post'], '/reports/commissions', [COMMISSIONS::class, 'index2'])->name('reports.commissions'); //COMISIONES
        Route::match(['get', 'post'], '/reports/commissions2', [COMMISSIONS::class, 'index'])->name('reports.commissions2'); //COMISIONES
        Route::match(['post','get'], '/reports/sales/data/commissions/get', [COMMISSIONS::class, 'getSales'])->name('reports.sales.data.commissions.get');
        Route::match(['post','get'], '/reports/operations/data/commissions/get', [COMMISSIONS::class, 'getOperations'])->name('reports.operations.data.commissions.get');
        Route::match(['post','get'], '/reports/commissions/data/commissions/get', [COMMISSIONS::class, 'getCommissions'])->name('reports.commissions.data.commissions.get');
        Route::match(['post','get'], '/reports/stats/commissions/get', [COMMISSIONS::class, 'getStats'])->name('reports.stats.get');
        Route::match(['post','get'], '/reports/sales/stats/charts/commissions', [COMMISSIONS::class, 'chartsSales'])->name('reports.sales.stats.charts.commissions.get');
        Route::match(['post','get'], '/reports/operations/stats/charts/commissions', [COMMISSIONS::class, 'chartsOperations'])->name('reports.operations.stats.charts.commissions.get');
        ////////////
        Route::match(['post','get'], '/reports/sales', [SALES::class, 'index'])->name('reports.sales'); //VENTAS
        Route::match(['post','get'], '/reports/operations', [OPERATIONSS::class, 'index'])->name('reports.operations'); //OPERACIONES

    //GESTION        
        Route::match(['get', 'post'], '/management/confirmations', [ConfirmationsController::class, 'index'])->name('management.confirmations'); //CONFIRMACIONES
        ////////////
        Route::match(['get', 'post'], '/management/aftersales', [AfterSalesController::class, 'index'])->name('management.after.sales'); //POST VENTA, MAENEJO DE SPAM Y RESERVAS PENDIENTES
        Route::match(['post'], '/management/quotation/get', [QUOTATION::class, 'get'])->name('management.quotation.get'); // TRAE LAS COTIZACIONES DE AGENTE DE CALL CENTER
        Route::match(['post'], '/management/pending/get', [PENDING::class, 'get'])->name('management.pending.get'); // TRAE LAS RESERVAS PENDIENTES
        Route::match(['post'], '/management/spam/get', [SPAM::class, 'get'])->name('management.spam.get');
        Route::match(['post'], '/management/spam/get/basic-information', [SPAM::class, 'getBasicInformation'])->name('management.spam.get.basicInformation');
        Route::match(['post'], '/management/spam/history/get', [SPAM::class, 'getHistory'])->name('management.spam.get.history');
        Route::match(['post'], '/management/spam/history/add', [SPAM::class, 'addHistory'])->name('management.spam.add.history');
        ////////////
        Route::match(['get', 'post'], '/management/reservations', [RESERVATIONS::class, 'index'])->name('management.reservations'); //RESERVACIONES
        //OPERACIONES
        Route::get('/operation/board', [Operations::class, 'index'])->name('operation.index');
        Route::post('/operation/board', [Operations::class, 'index'])->name('operation.index.search');    
        Route::put('/operation/vehicle/set', [Operations::class, 'setVehicle'])->name('operation.set.vehicle');
        Route::put('/operation/driver/set', [Operations::class, 'setDriver'])->name('operation.set.driver');    
        Route::put('/operation/status/operation', [Operations::class, 'updateStatusOperation'])->name('operation.status.operation');
        Route::put('/operation/status/booking', [Operations::class, 'updateStatusBooking'])->name('operation.status.booking');
        Route::post('/operation/comment/add', [Operations::class, 'addComment'])->name('operation.comment.add');
        Route::get('/operation/comment/get', [Operations::class, 'getComment'])->name('operation.comment.get');
        Route::get('/operation/history/get', [Operations::class, 'getHistory'])->name('operation.history.get');
        Route::get('/operation/data/customer/get', [Operations::class, 'getDataCustomer'])->name('operation.data.customer.get');
        Route::post('/operation/preassignments', [Operations::class, 'preassignments'])->name('operation.preassignments');
        Route::post('/operation/closeOperation', [Operations::class, 'closeOperation'])->name('operation.close.operation');
        Route::put('/operation/preassignment', [Operations::class, 'preassignment'])->name('operation.preassignment');
        Route::post('/operation/capture/service', [Operations::class, 'createService'])->name('operation.capture.service');
        Route::get('/operation/board/exportExcel', [Operations::class, 'exportExcelBoard'])->name('operation.board.exportExcel');
        Route::get('/operation/board/exportExcelCommission', [Operations::class, 'exportExcelBoardCommision'])->name('operation.board.exportExcelComission');
        Route::match(['get', 'post'], '/management/operation/schedules/get', [Operations::class, 'getSchedules'])->name('management.operation.schedules.get');
        Route::match(['post'], '/management/operation/schedules/update', [Operations::class, 'updateSchedules'])->name('management.operation.schedules.update');

    //CONFIGURACIONES
        Route::resource('/roles', ROLES::class);
        //USERS
        Route::resource('/users', USERS::class);
        Route::put('/ChangePass/{user}', [USERS::class, 'change_pass'])->name('users.change_pass');
        Route::put('/ChangeStatus/{user}', [USERS::class, 'change_status'])->name('users.change_status');
        Route::post('/StoreIP', [USERS::class, 'store_ips'])->name('users.store_ips');
        Route::delete('/DeleteIPs/{ip}', [USERS::class, 'delete_ips'])->name('users.delete_ips');
        //EMPRESAS
        Route::resource('/enterprises', ENTERPRISES::class);
        //SITIOS
        Route::resource('/sites', SITES::class);
        //VEHICULOS
        Route::resource('/vehicles', VEHICLES::class);
        //CONDUCTORES
        Route::resource('/drivers', DRIVERS::class);

        //HORARIO DE CONDUCTORES
        Route::match(['get', 'post'], '/schedules', [SCHEDULES::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [SCHEDULES::class, 'create'])->name('schedules.create');
        Route::post('/schedules/store', [SCHEDULES::class, 'store'])->name('schedules.store');
        Route::get('/schedules/{schedule}/edit', [SCHEDULES::class, 'edit'])->name('schedules.edit');
        Route::put('/schedules/{schedule}', [SCHEDULES::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{schedule}', [SCHEDULES::class, 'destroy'])->name('schedules.destroy');

        //ZONES
        Route::get('/config/destinations', [ZONES::class, 'index'])->name('config.zones');
        Route::get('/config/destinations/{id}', [ZONES::class, 'getZones'])->name('config.zones.getZones');
        Route::get('/config/destinations/{id}/points', [ZONES::class, 'getPoints'])->name('config.getPoints');
        Route::put('/config/destinations/{id}/points', [ZONES::class, 'setPoints'])->name('config.setPoints');
        //RATES        
        Route::get('/config/rates/destination', [RATES::class, 'index'])->name('config.ratesDestination');
        Route::get('/config/rates/destination/{id}/get', [RATES::class, 'items'])->name('config.ratesZones');
        Route::post('/config/rates/get', [RATES::class, 'getRates'])->name('config.getRates');
        Route::post('/config/rates/new', [RATES::class, 'newRates'])->name('config.newRates');
        Route::delete('/config/rates/delete', [RATES::class, 'deleteRates'])->name('config.deleteRates');
        Route::put('/config/rates/update', [RATES::class, 'updateRates'])->name('config.updateRates');        
        //RATES ENTERPRISES
        Route::get('/config/rates/enterprise', [RATES_ENTERPRISE::class, 'index'])->name('config.ratesEnterprise');
        Route::get('/config/rates/enterprise/destination/{id}/get', [RATES_ENTERPRISE::class, 'items'])->name('config.ratesEnterpriseZones');
        Route::post('/config/rates/enterprise/get', [RATES_ENTERPRISE::class, 'getRates'])->name('config.getRatesEnterprise');
        Route::post('/config/rates/enterprise/new', [RATES_ENTERPRISE::class, 'newRates'])->name('config.newRatesEnterprise');
        Route::delete('/config/rates/enterprise/delete', [RATES_ENTERPRISE::class, 'deleteRates'])->name('config.deleteRatesEnterprise');
        Route::put('/config/rates/enterprise/update', [RATES_ENTERPRISE::class, 'updateRates'])->name('config.updateRatesEnterprise');

        //TIPO DE CAMBIO PARA REPORTES
        Route::get('/config/exchange-reports', [EXCHANGE_REPORTS::class, 'index'])->name('exchanges.index');
        Route::get('/config/exchange-reports/create', [EXCHANGE_REPORTS::class, 'create'])->name('exchanges.create');
        Route::post('/config/exchange-reports/store', [EXCHANGE_REPORTS::class, 'store'])->name('exchanges.store');
        Route::get('/config/exchange-reports/{exchage}/edit', [EXCHANGE_REPORTS::class, 'edit'])->name('exchanges.edit');
        Route::put('/config/exchange-reports/{exchage}', [EXCHANGE_REPORTS::class, 'update'])->name('exchanges.update');
        Route::delete('/config/exchange-reports/{exchage}', [EXCHANGE_REPORTS::class, 'destroy'])->name('exchanges.destroy');

        //TYPES CANCELLATIONS
        Route::get('/config/types-cancellations', [TYPES_CANCELLATIONS::class, 'index'])->name('config.types-cancellations.index');
        Route::get('/config/types-cancellations/create', [TYPES_CANCELLATIONS::class, 'create'])->name('config.types-cancellations.create');
        Route::post('/config/types-cancellations', [TYPES_CANCELLATIONS::class, 'store'])->name('config.types-cancellations.store');
        Route::get('/config/types-cancellations/{cancellation}/edit', [TYPES_CANCELLATIONS::class, 'edit'])->name('config.types-cancellations.edit');
        Route::put('/config/types-cancellations/{cancellation}', [TYPES_CANCELLATIONS::class, 'update'])->name('config.types-cancellations.update');
        Route::delete('/config/types-cancellations/{cancellation}', [TYPES_CANCELLATIONS::class, 'destroy'])->name('config.types-cancellations.destroy');

        Route::put('/reservations/{reservation}', [DETAILS_RESERVATION::class, 'update'])->name('reservations.update');
        Route::put('/reservationsEnable/{reservation}', [DETAILS_RESERVATION::class, 'enable'])->name('reservations.enable');
        Route::delete('/reservations/{reservation}', [DETAILS_RESERVATION::class, 'destroy'])->name('reservations.destroy');//LA CANCELACIÓNDE LA RESERVA
        Route::get('/reservations/detail/{id}', [DETAILS_RESERVATION::class, 'detail'])->name('reservations.details')->where('id', '[0-9]+');
        Route::get('/GetExchange/{reservation}', [DETAILS_RESERVATION::class, 'get_exchange'])->name('reservations.get_exchange');
        Route::post('/reservationsfollowups', [DETAILS_RESERVATION::class, 'followups'])->name('reservations.followups');
        Route::put('/editreservitem/{item}', [DETAILS_RESERVATION::class, 'editreservitem'])->name('reservations.editreservitem');

        Route::post('/reservations/confirmation/arrival', [DETAILS_RESERVATION::class, 'arrivalConfirmation'])->name('reservations.confirmationArrival');
        Route::post('/reservations/confirmation/departure', [DETAILS_RESERVATION::class, 'departureConfirmation'])->name('reservations.confirmationDeparture');
        Route::post('/reservations/payment-request', [DETAILS_RESERVATION::class, 'paymentRequest'])->name('reservations.paymentRequest');
        Route::post('/reservations/upload', [DETAILS_RESERVATION::class, 'uploadMedia'])->name('reservations.upload');
        Route::get('/reservations/upload/{id}', [DETAILS_RESERVATION::class, 'getMedia'])->name('reservations.upload.getmedia');
        Route::delete('/reservations/upload/{id}', [DETAILS_RESERVATION::class, 'deleteMedia'])->name('reservations.upload.deleteMedia');


    //ACCIONES GENRALES UTILIZADAS EN DETALLE DE RESERVACION
    Route::resource('/sales',SalesController::class);
    Route::resource('/payments',PaymentsController::class);

    //ACCIONES UTILIZADAS EN FINANZAS
    Route::post('/action/addPaymentRefund', [FINANCE::class, 'addPaymentRefund'])->name('add.payment.refund');
    Route::post('/action/refundNotApplicable', [FINANCE::class, 'refundNotApplicable'])->name('add.not.applicable.refund');
    Route::match(['get', 'post'], '/action/getBasicInformationReservation', [FINANCE::class, 'getBasicInformationReservation'])->name('get.basic-information.reservation');
    Route::match(['get', 'post'], '/action/getPhotosReservation', [FINANCE::class, 'getPhotosReservation'])->name('get.photos.reservation');
    Route::match(['get', 'post'], '/action/getHistoryReservation', [FINANCE::class, 'getHistoryReservation'])->name('get.history.reservation');
    Route::match(['get', 'post'], '/action/getPaymentsReservation', [FINANCE::class, 'getPaymentsReservation'])->name('get.payments.reservation');    

    //ACCIONES GENERALES DE DETALLES DE RESERVA
    Route::post('/action/deleteCommission', [ACTIONS_RESERVATION::class, 'deleteCommission'])->name('update.booking.delete.commission');

    Route::post('/action/enablePayArrival', [ACTIONS_RESERVATION::class, 'enablePayArrival'])->name('update.booking.pay.arrival');
    Route::post('/action/enablePlusService', [ACTIONS_RESERVATION::class, 'enablePlusService'])->name('update.booking.plus.service');
    Route::post('/action/markReservationOpenCredit', [ACTIONS_RESERVATION::class, 'markReservationOpenCredit'])->name('update.booking.mark.open.credit');
    Route::post('/action/refundRequest', [ACTIONS_RESERVATION::class, 'refundRequest'])->name('update.booking.refund.request');
    Route::post('/action/markReservationDuplicate', [ACTIONS_RESERVATION::class, 'markReservationDuplicate'])->name('update.booking.mark.duplicate');    

    Route::put('/action/updateServiceStatus', [ACTIONS_RESERVATION::class, 'updateServiceStatus'])->name('update.service.status');
    Route::post('/action/enabledLike', [ACTIONS_RESERVATION::class, 'enabledLike'])->name('update.booking.like');
    Route::post('/action/confirmService', [ACTIONS_RESERVATION::class, 'confirmService'])->name('update.service.confirm');
    Route::post('/action/updateServiceUnlock', [ACTIONS_RESERVATION::class, 'updateServiceUnlock'])->name('update.service.unlock');
});
