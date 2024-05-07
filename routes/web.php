<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Payments\PaymentsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Reservations\ReservationsController;
use App\Http\Controllers\Sales\SalesController;
use App\Http\Controllers\Tpv\TpvController;
use App\Http\Controllers\Configs\ZonesController;
use App\Http\Controllers\Configs\RatesController;
use App\Http\Controllers\Operation\OperationController;
use App\Http\Controllers\Reports\PaymentsController as ReportPayment;
use App\Http\Controllers\Reports\SalesController as ReportSales;
use App\Http\Controllers\Reports\CommissionsController as ReportCommissions;
use App\Http\Controllers\Reports\CashController as ReportCash;
use App\Http\Controllers\Reports\CCFormController;

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

Route::middleware(['guest'])->group(function () {
    Route::get('login', [LoginController::class, 'index']);
    Route::post('login', [LoginController::class, 'check'])->name('login');
});

//Meter al middleware para protejer estas rutas...
Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/sales/{type}', [DashboardController::class, 'sales'])->name('dashboard.sales');
    Route::post('/dashboard/sales/{type}', [DashboardController::class, 'sales'])->name('reservations.sales.search');

    // Route::post('/dashboard/sales/general', [DashboardController::class, 'sales'])->name('reservations.sales.search');
    // Route::post('/dashboard/sales/online', [DashboardController::class, 'sales'])->name('reservations.sales.search');
    // Route::post('/dashboard/sales/airport', [DashboardController::class, 'sales'])->name('reservations.sales.search');

    Route::resource('/users', UserController::class);
    Route::put('/ChangePass/{user}', [UserController::class, 'change_pass'])->name('users.change_pass');
    Route::put('/ChangeStatus/{user}', [UserController::class, 'change_status'])->name('users.change_status');
    Route::post('/StoreIP', [UserController::class, 'store_ips'])->name('users.store_ips');
    Route::delete('/DeleteIPs/{ip}', [UserController::class, 'delete_ips'])->name('users.delete_ips');
    
    Route::resource('/roles', RoleController::class);

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/reservations', [ReservationsController::class, 'index'])->name('reservations.index');
    Route::post('/reservations', [ReservationsController::class, 'index'])->name('reservations.search');
    Route::put('/reservations/{reservation}', [ReservationsController::class, 'update'])->name('reservations.update');
    Route::put('/reservationsDuplicated/{reservation}', [ReservationsController::class, 'duplicated'])->name('reservations.duplicated');
    Route::delete('/reservations/{reservation}', [ReservationsController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations/detail/{id}', [ReservationsController::class, 'detail'])->where('id', '[0-9]+');
    Route::get('/GetExchange/{reservation}', [ReservationsController::class, 'get_exchange'])->name('reservations.get_exchange');
    Route::post('/reservationsfollowups', [ReservationsController::class, 'followups'])->name('reservations.followups');
    Route::put('/editreservitem/{item}', [ReservationsController::class, 'editreservitem'])->name('reservations.editreservitem');    
    Route::post('/reservations/confirmation/contact-points', [ReservationsController::class, 'contactPoint'])->name('reservations.confirmation');
    Route::post('/reservations/confirmation/arrival', [ReservationsController::class, 'arrivalConfirmation'])->name('reservations.confirmationArrival');
    Route::post('/reservations/confirmation/departure', [ReservationsController::class, 'departureConfirmation'])->name('reservations.confirmationDeparture');
    Route::post('/reservations/payment-request', [ReservationsController::class, 'paymentRequest'])->name('reservations.paymentRequest');

    Route::get('/operation', [OperationController::class, 'index'])->name('operation.index');
    Route::get('/operation/managment', [OperationController::class, 'managment'])->name('operation.managment');
    Route::post('/operation/managment', [OperationController::class, 'managment'])->name('operation.managment.search');
    Route::put('/operation/managment/update-status', [OperationController::class, 'statusUpdate'])->name('operation.managment.status');
    Route::get('/operation/confirmation', [OperationController::class, 'confirmation'])->name('operation.confirmation');
    Route::post('/operation/confirmation', [OperationController::class, 'confirmation'])->name('operation.confirmation.search');
    Route::put('/operation/confirmation/update-status', [OperationController::class, 'confirmationUpdate'])->name('operation.confirmation.update');
    Route::get('/operation/spam', [OperationController::class, 'spam'])->name('operation.spam');
    Route::post('/operation/spam', [OperationController::class, 'spam'])->name('operation.spam.search');
    Route::put('/operation/spam/update-status', [OperationController::class, 'spamUpdate'])->name('operation.spam.update');

    //Reportes
    Route::get('/reports/payments', [ReportPayment::class, 'managment'])->name('reports.payment');
    Route::post('/reports/payments', [ReportPayment::class, 'managment'])->name('reports.payment.action');
    Route::get('/reports/sales', [ReportSales::class, 'index'])->name('reports.sales');
    Route::post('/reports/sales', [ReportSales::class, 'index'])->name('reports.sales.action');
    Route::get('/reports/commissions', [ReportCommissions::class, 'index'])->name('reports.commissions');
    Route::post('/reports/commissions', [ReportCommissions::class, 'index'])->name('reports.commissions.action');
    Route::get('/reports/ccform', [CCFormController::class, 'index'])->name('reports.ccform');
    Route::get('/reports/ccform/pdf', [CCFormController::class, 'createPDF'])->name('reports.ccform.createPDF');
    Route::get('/reports/cash', [ReportCash::class, 'index'])->name('reports.cash');
    Route::post('/reports/cash', [ReportCash::class, 'index'])->name('reports.cash.action');
    Route::put('/reports/cash/update-status', [ReportCash::class, 'update'])->name('reports.cash.action.update');

    Route::resource('/sales',SalesController::class);
    Route::resource('/payments',PaymentsController::class);

    Route::get('/tpv/handler', [TpvController::class, 'handler'])->name('tpv.handler');
    Route::get('/tpv/edit/{code}', [TpvController::class, 'index'])->name('tpv.new');
    Route::post('/tpv/quote', [TpvController::class, 'quote'])->name('tpv.quote');
    Route::post('/tpv/create', [TpvController::class, 'create'])->name('tpv.create');
    Route::get('/tpv/autocomplete/{keyword}', [TpvController::class, 'autocomplete'])->name('tpv.autocomplete');

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
});
