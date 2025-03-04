<?php

use App\Http\Controllers\Bots\MasterToursController;

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CallCenterController;

use App\Http\Controllers\Accounting\ConciliationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Configs\RatesController;
use App\Http\Controllers\Configs\ZonesController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Operation\OperationController;
use App\Http\Controllers\Operations\OperationsController as Operations;
use App\Http\Controllers\Payments\PaymentsController;

use App\Http\Controllers\Finance\SalesController as SaleFinance;

use App\Http\Controllers\Reports\ReportsController;
use App\Http\Controllers\Management\ManagementController;
use App\Http\Controllers\Operation\SpamController as SPAM;
use App\Http\Controllers\Operation\PendingController as PENDING;
use App\Http\Controllers\Operation\QuotationController as QUOTATION;
use App\Http\Controllers\Reports\CashController as ReportCash;

use App\Http\Controllers\Reservations\ReservationsController;
use App\Http\Controllers\RoleController;

use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Vehicle\VehicleController;

//Tpv
use App\Http\Controllers\Tpv\TpvController;
use App\Http\Controllers\Tpv\TpvController2;
use App\Http\Controllers\Tpv\Api\AutocompleteController as APIAutocomplete;
use App\Http\Controllers\Tpv\Api\QuoteController as APIQuote;
use App\Http\Controllers\Bookings\BookingsController;

//Settings
use App\Http\Controllers\Settings\EnterpriseController;
use App\Http\Controllers\Settings\SitesController;
use App\Http\Controllers\Settings\ExchangeReportsController;
use App\Http\Controllers\Settings\RatesEnterpriseController;
use App\Http\Controllers\Settings\TypesCancellationsController;

use App\Http\Controllers\Actions\ActionsController;

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

            Route::get('/thank-you', [TpvController::class, 'success'])->name('process.success.es');
            Route::get('/cancel', [TpvController::class, 'cancel'])->name('process.cancel.es');
            Route::get('/my-reservation-detail', [BookingsController::class, 'ReservationDetail'])->name('reservation.detail.es');
        });
    });

Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'index']);
    Route::post('login', [LoginController::class, 'check'])->name('login');
});

//Meter al middleware para protejer estas rutas...
Route::group(['middleware' => ['auth']], function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    //BOTS
        //SET RATE MASTER TOUR
        Route::get('/set/rate/master', [MasterToursController::class, 'ListServicesMasterTour'])->name('list.services.master.tours')->withoutMiddleware(['auth']);
        //PAYPAL
        Route::get('/bot/conciliation/paypal', [ConciliationController::class, 'PayPalPayments'])->name('bot.paypal')->withoutMiddleware(['auth']);
        Route::get('/conciliation/paypalOrders', [ConciliationController::class, 'PayPalPaymenOrders'])->name('bot.paypal.orders')->withoutMiddleware(['auth']);
        Route::get('/conciliation/paypal/{reference}', [ConciliationController::class, 'PayPalPaymenReference'])->name('bot.paypal.reference')->withoutMiddleware(['auth']);
        Route::get('/conciliation/paypalOrder/{id}', [ConciliationController::class, 'PayPalPaymenOrder'])->name('bot.paypal.order')->withoutMiddleware(['auth']);
        //STRIPE
        Route::get('/bot/conciliation/stripe', [ConciliationController::class, 'StripePayments'])->name('bot.stripe')->withoutMiddleware(['auth']);
        Route::get('/conciliation/stripe/{reference}', [ConciliationController::class, 'StripePaymentReference'])->name('bot.stripe.reference')->withoutMiddleware(['auth']);

    //DASHBOARD
        Route::match(['get', 'post'], '/', [DashboardController::class, 'index'])->name('dashboard');
        // AGENTES DE CALL CENTER
        Route::match(['get', 'post'], '/callcenters', [CallCenterController::class, 'index'])->name('callcenters.index');
        Route::match(['post'], '/callcenters/sales/get', [CallCenterController::class, 'getSales'])->name('callcenters.sales.get');
        Route::match(['post'], '/callcenters/operations/get', [CallCenterController::class, 'getOperations'])->name('callcenters.operations.get');
        Route::match(['post','get'], '/callcenters/stats/get', [CallCenterController::class, 'getStats'])->name('callcenters.stats.get');
        Route::match(['post','get'], '/callcenters/stats/charts/sales', [CallCenterController::class, 'chartsSales'])->name('callcenters.charts.sales.get');
        Route::match(['post','get'], '/callcenters/stats/charts/opertions', [CallCenterController::class, 'chartsOperations'])->name('callcenters.charts.operations.get');
        Route::match(['post','get'], '/destinations/list', [CallCenterController::class, 'destinationsList'])->name('destinations.list');

    //TPV        
        Route::get('/tpv/handler', [TpvController::class, 'handler'])->name('tpv.handler');
        Route::get('/tpv/edit/{code}', [TpvController::class, 'index'])->name('tpv.new');
        Route::post('/tpv/quote', [TpvController::class, 'quote'])->name('tpv.quote');
        Route::post('/tpv/create', [TpvController::class, 'create'])->name('tpv.create');
        Route::get('/tpv/autocomplete/{keyword}', [TpvController::class, 'autocomplete'])->name('tpv.autocomplete');

    //FINANZAS    
        //PAGOS
        Route::get('/finance/sales', [SaleFinance::class, 'index'])->name('finance.sales');
        Route::post('/finance/sales', [SaleFinance::class, 'index'])->name('finance.sales.action');

    //REPORTES
        //PAGOS
        Route::get('/reports/payments', [ReportsController::class, 'payments'])->name('reports.payment');
        Route::post('/reports/payments', [ReportsController::class, 'payments'])->name('reports.payment.action');
        //EFECTIVO
        Route::get('/reports/cash', [ReportsController::class, 'cash'])->name('reports.cash');
        Route::post('/reports/cash', [ReportsController::class, 'cash'])->name('reports.cash.action');
        Route::put('/reports/cash/update-status', [ReportCash::class, 'update'])->name('reports.cash.action.update');
        //CANCELACIONES
        Route::get('/reports/cancellations', [ReportsController::class, 'cancellations'])->name('reports.cancellations');
        Route::post('/reports/cancellations', [ReportsController::class, 'cancellations'])->name('reports.cancellations.action');
        //COMISIONES
        Route::get('/reports/commissions', [ReportsController::class, 'commissions2'])->name('reports.commissions');
        Route::post('/reports/commissions', [ReportsController::class, 'commissions2'])->name('reports.commissions.action');
        //COMISIONES VERSION 2
        Route::get('/reports/commissions2', [ReportsController::class, 'commissions'])->name('reports.commissions2');
        Route::post('/reports/commissions2', [ReportsController::class, 'commissions'])->name('reports.commissions2.action');
        //VENTAS
        Route::get('/reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
        Route::post('/reports/sales', [ReportsController::class, 'sales'])->name('reports.sales.action');
        //OPERACIONES
        Route::get('/reports/operations', [ReportsController::class, 'operations'])->name('reports.operations');
        Route::post('/reports/operations', [ReportsController::class, 'operations'])->name('reports.operations.action');
        //PAGOS
        Route::get('/reports/conciliation', [ReportsController::class, 'conciliation'])->name('reports.conciliation');
        Route::post('/reports/conciliation', [ReportsController::class, 'conciliation'])->name('reports.conciliation.action');
        //CUENTAS POR COBRAR
        Route::get('/reports/accounts-receivable', [ReportsController::class, 'receivable'])->name('reports.receivable');
        Route::post('/reports/accounts-receivable', [ReportsController::class, 'receivable'])->name('reports.receivable.action');

    //GESTION
        //CONFIRMACIONES
        Route::get('/operation/confirmation', [ManagementController::class, 'confirmation'])->name('operation.confirmation');
        Route::post('/operation/confirmation', [ManagementController::class, 'confirmation'])->name('operation.confirmation.search');
        //POST VENTA, MAENEJO DE SPAM Y RESERVAS PENDIENTES
        Route::match(['get', 'post'], '/aftersales', [ManagementController::class, 'afterSales'])->name('operation.after.sales');
        Route::match(['post'], '/operation/quotation/get', [QUOTATION::class, 'get'])->name('operation.quotation.get'); // TRAE LAS COTIZACIONES DE AGENTE DE CALL CENTER
        Route::match(['post'], '/operation/pending/get', [PENDING::class, 'get'])->name('operation.pending.get'); // TRAE LAS RESERVAS PENDIENTES
        Route::match(['post'], '/operation/spam/get', [SPAM::class, 'get'])->name('operation.spam.get');
        Route::match(['post'], '/operation/spam/get/basic-information', [SPAM::class, 'getBasicInformation'])->name('operation.spam.get.basicInformation');
        Route::match(['post'], '/operation/spam/history/get', [SPAM::class, 'getHistory'])->name('operation.spam.get.history');
        Route::match(['post'], '/operation/spam/history/add', [SPAM::class, 'addHistory'])->name('operation.spam.add.history');

        Route::put('/operation/confirmation/update-status', [OperationController::class, 'updateStatusConfirmation'])->name('operation.confirmation.update');   
        Route::put('/operation/unlock/service', [OperationController::class, 'updateUnlock'])->name('operation.unlock.update');

        //RESERVACIONES
        Route::get('/operation/reservations', [OperationController::class, 'reservations'])->name('operation.reservations');
        Route::post('/operation/reservations', [OperationController::class, 'reservations'])->name('operation.reservations.search');

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

    //CONFIGURACIONES
        Route::put('/reservations/{reservation}', [ReservationsController::class, 'update'])->name('reservations.update');
        Route::get('/reservation/payments/{reservation}', [ReservationsController::class, 'reservationPayments'])->name('reservation.payments');
        Route::put('/reservationsDuplicated/{reservation}', [ReservationsController::class, 'duplicated'])->name('reservations.duplicated');
        Route::put('/reservation/removeCommission/{reservation}', [ReservationsController::class, 'removeCommission'])->name('reservation.removeCommission');
        Route::put('/reservationsOpenCredit/{reservation}', [ReservationsController::class, 'openCredit'])->name('reservations.openCredit');
        Route::put('/reservationsEnablePlusService/{reservation}', [ReservationsController::class, 'enablePlusService'])->name('reservations.enablePlusService');
        Route::put('/reservationsEnable/{reservation}', [ReservationsController::class, 'enable'])->name('reservations.enable');
        Route::delete('/reservations/{reservation}', [ReservationsController::class, 'destroy'])->name('reservations.destroy');//LA CANCELACIÓNDE LA RESERVA
        Route::get('/reservations/detail/{id}', [ReservationsController::class, 'detail'])->name('reservations.details')->where('id', '[0-9]+');
        Route::get('/GetExchange/{reservation}', [ReservationsController::class, 'get_exchange'])->name('reservations.get_exchange');
        Route::post('/reservationsfollowups', [ReservationsController::class, 'followups'])->name('reservations.followups');
        Route::put('/editreservitem/{item}', [ReservationsController::class, 'editreservitem'])->name('reservations.editreservitem');    
        // Route::post('/reservations/confirmation/contact-points', [ReservationsController::class, 'contactPoint'])->name('reservations.confirmation');
        Route::post('/reservations/confirmation/arrival', [ReservationsController::class, 'arrivalConfirmation'])->name('reservations.confirmationArrival');
        Route::post('/reservations/confirmation/departure', [ReservationsController::class, 'departureConfirmation'])->name('reservations.confirmationDeparture');
        Route::post('/reservations/payment-request', [ReservationsController::class, 'paymentRequest'])->name('reservations.paymentRequest');
        Route::post('/reservations/upload', [ReservationsController::class, 'uploadMedia'])->name('reservations.upload');
        Route::get('/reservations/upload/{id}', [ReservationsController::class, 'getMedia'])->name('reservations.upload.getmedia');
        Route::delete('/reservations/upload/{id}', [ReservationsController::class, 'deleteMedia'])->name('reservations.upload.deleteMedia');

    Route::resource('/sales',SalesController::class);
    Route::resource('/payments',PaymentsController::class);
    Route::match(['post'], '/payments/conciliation', [PaymentsController::class, 'conciliation'])->name('payments.conciliation');

        //EMPRESAS
        Route::resource('/enterprises', EnterpriseController::class);
        //SITIOS
        Route::resource('/sites', SitesController::class);
        //TIPO DE CAMBIO PARA REPORTES
        Route::get('/config/exchange-reports', [ExchangeReportsController::class, 'index'])->name('exchanges.index');
        Route::get('/config/exchange-reports/create', [ExchangeReportsController::class, 'create'])->name('exchanges.create');
        Route::post('/config/exchange-reports/store', [ExchangeReportsController::class, 'store'])->name('exchanges.store');
        Route::get('/config/exchange-reports/{exchage}/edit', [ExchangeReportsController::class, 'edit'])->name('exchanges.edit');
        Route::put('/config/exchange-reports/{exchage}', [ExchangeReportsController::class, 'update'])->name('exchanges.update');
        Route::delete('/config/exchange-reports/{exchage}', [ExchangeReportsController::class, 'destroy'])->name('exchanges.destroy');
        //TARIFAS DE EMPRESAS
        Route::get('/config/rates/enterprise', [RatesEnterpriseController::class, 'index'])->name('config.ratesEnterprise');
        Route::get('/config/rates/enterprise/destination/{id}/get', [RatesEnterpriseController::class, 'items'])->name('config.ratesEnterpriseZones');
        Route::post('/config/rates/enterprise/get', [RatesEnterpriseController::class, 'getRates'])->name('config.getRatesEnterprise');
        Route::post('/config/rates/enterprise/new', [RatesEnterpriseController::class, 'newRates'])->name('config.newRatesEnterprise');
        Route::delete('/config/rates/enterprise/delete', [RatesEnterpriseController::class, 'deleteRates'])->name('config.deleteRatesEnterprise');
        Route::put('/config/rates/enterprise/update', [RatesEnterpriseController::class, 'updateRates'])->name('config.updateRatesEnterprise');
        //TIPO DE CANCELACIONES
        Route::get('/config/types-cancellations', [TypesCancellationsController::class, 'index'])->name('config.types-cancellations.index');
        Route::get('/config/types-cancellations/create', [TypesCancellationsController::class, 'create'])->name('config.types-cancellations.create');
        Route::post('/config/types-cancellations', [TypesCancellationsController::class, 'store'])->name('config.types-cancellations.store');
        Route::get('/config/types-cancellations/{cancellation}/edit', [TypesCancellationsController::class, 'edit'])->name('config.types-cancellations.edit');
        Route::put('/config/types-cancellations/{cancellation}', [TypesCancellationsController::class, 'update'])->name('config.types-cancellations.update');
        Route::delete('/config/types-cancellations/{cancellation}', [TypesCancellationsController::class, 'destroy'])->name('config.types-cancellations.destroy');
        




    Route::resource('/vehicles', VehicleController::class);
    Route::resource('/drivers', DriverController::class);
    Route::resource('/users', UserController::class);

    Route::put('/ChangePass/{user}', [UserController::class, 'change_pass'])->name('users.change_pass');
    Route::put('/ChangeStatus/{user}', [UserController::class, 'change_status'])->name('users.change_status');
    Route::post('/StoreIP', [UserController::class, 'store_ips'])->name('users.store_ips');
    Route::delete('/DeleteIPs/{ip}', [UserController::class, 'delete_ips'])->name('users.delete_ips');
    Route::resource('/roles', RoleController::class);

    Route::get('/config/destinations', [ZonesController::class, 'index'])->name('config.zones');
    Route::get('/config/destinations/{id}', [ZonesController::class, 'getZones'])->name('config.zones.getZones');
    Route::get('/config/destinations/{id}/points', [ZonesController::class, 'getPoints'])->name('config.getPoints');
    Route::put('/config/destinations/{id}/points', [ZonesController::class, 'setPoints'])->name('config.setPoints');
    Route::get('/config/rates/destination', [RatesController::class, 'index'])->name('config.ratesDestination');
    Route::get('/config/rates/destination/{id}/get', [RatesController::class, 'items'])->name('config.ratesZones');
    Route::post('/config/rates/get', [RatesController::class, 'getRates'])->name('config.getRates');
    Route::post('/config/rates/new', [RatesController::class, 'newRates'])->name('config.newRates');
    Route::delete('/config/rates/delete', [RatesController::class, 'deleteRates'])->name('config.deleteRates');
    Route::put('/config/rates/update', [RatesController::class, 'updateRates'])->name('config.updateRates');


    //ACCIONES GENERALES
    Route::post('/action/enablePayArrival', [ActionsController::class, 'enablePayArrival'])->name('update.booking.pay.arrival');    
    Route::put('/action/updateServiceStatus', [ActionsController::class, 'updateServiceStatus'])->name('update.service.status');    
});
