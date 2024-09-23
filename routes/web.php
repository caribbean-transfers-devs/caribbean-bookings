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
use App\Http\Controllers\Operations\OperationsController as Operations;
use App\Http\Controllers\Reports\PaymentsController as ReportPayment;
use App\Http\Controllers\Reports\SalesController as ReportSales;
use App\Http\Controllers\Reports\CommissionsController as ReportCommissions;
use App\Http\Controllers\Reports\CashController as ReportCash;
use App\Http\Controllers\Reports\CCFormController;
use App\Http\Controllers\Reports\CancellationsController as ReportCancellations;

use App\Http\Controllers\Pos\PosController;
use App\Http\Controllers\Enterprise\EnterpriseController;
use App\Http\Controllers\Vehicle\VehicleController;
use App\Http\Controllers\Driver\DriverController;

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

    Route::get('/dashboard/salesOld/{type}', [DashboardController::class, 'sales2'])->name('dashboard.sales.old');
    Route::post('/dashboard/salesOld/{type}', [DashboardController::class, 'sales2'])->name('reservations.sales.old.search');

    Route::resource('/enterprises', EnterpriseController::class);
    Route::resource('/vehicles', VehicleController::class);
    Route::resource('/drivers', DriverController::class);

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
    Route::put('/reservationsOpenCredit/{reservation}', [ReservationsController::class, 'openCredit'])->name('reservations.openCredit');
    Route::put('/reservationsEnablePlusService/{reservation}', [ReservationsController::class, 'enablePlusService'])->name('reservations.enablePlusService');

    Route::put('/reservationsEnable/{reservation}', [ReservationsController::class, 'enable'])->name('reservations.enable');
    Route::delete('/reservations/{reservation}', [ReservationsController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations/detail/{id}', [ReservationsController::class, 'detail'])->name('reservations.details')->where('id', '[0-9]+');
    Route::get('/GetExchange/{reservation}', [ReservationsController::class, 'get_exchange'])->name('reservations.get_exchange');
    Route::post('/reservationsfollowups', [ReservationsController::class, 'followups'])->name('reservations.followups');
    Route::put('/editreservitem/{item}', [ReservationsController::class, 'editreservitem'])->name('reservations.editreservitem');    
    Route::post('/reservations/confirmation/contact-points', [ReservationsController::class, 'contactPoint'])->name('reservations.confirmation');
    Route::post('/reservations/confirmation/arrival', [ReservationsController::class, 'arrivalConfirmation'])->name('reservations.confirmationArrival');
    Route::post('/reservations/confirmation/departure', [ReservationsController::class, 'departureConfirmation'])->name('reservations.confirmationDeparture');
    Route::post('/reservations/payment-request', [ReservationsController::class, 'paymentRequest'])->name('reservations.paymentRequest');
    Route::post('/reservations/upload', [ReservationsController::class, 'uploadMedia'])->name('reservations.upload');
    Route::get('/reservations/upload/{id}', [ReservationsController::class, 'getMedia'])->name('reservations.upload.getmedia');
    Route::delete('/reservations/upload/{id}', [ReservationsController::class, 'deleteMedia'])->name('reservations.upload.deleteMedia');

    //OPERACIONES
    Route::get('/download', [OperationController::class, 'index'])->name('operation.download');
    
    Route::put('/operation/managment/update-status', [OperationController::class, 'statusUpdate'])->name('operation.managment.status');

    Route::get('/operation/confirmation', [OperationController::class, 'confirmation'])->name('operation.confirmation');
    Route::post('/operation/confirmation', [OperationController::class, 'confirmation'])->name('operation.confirmation.search');
    Route::put('/operation/confirmation/update-status', [OperationController::class, 'confirmationUpdate'])->name('operation.confirmation.update');


    Route::put('/operation/unlock/service', [OperationController::class, 'updateUnlock'])->name('operation.unlock.update');

    Route::get('/reports/ccform', [CCFormController::class, 'index'])->name('operation.ccform');
    Route::get('/reports/ccform/pdf', [CCFormController::class, 'createPDF'])->name('operation.ccform.createPDF');
    Route::get('/operation/spam', [OperationController::class, 'spam'])->name('operation.spam');
    Route::post('/operation/spam', [OperationController::class, 'spam'])->name('operation.spam.search');
    Route::get('/operation/spam/exportExcel', [OperationController::class, 'exportExcel'])->name('operation.spam.exportExcel');
    Route::put('/operation/spam/update-status', [OperationController::class, 'spamUpdate'])->name('operation.spam.update');
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

    //REPORTES
    Route::get('/reports/payments', [ReportPayment::class, 'managment'])->name('reports.payment');
    Route::post('/reports/payments', [ReportPayment::class, 'managment'])->name('reports.payment.action');
    Route::get('/reports/sales', [ReportSales::class, 'index'])->name('reports.sales');
    Route::post('/reports/sales', [ReportSales::class, 'index'])->name('reports.sales.action');
    Route::get('/reports/commissions', [ReportCommissions::class, 'index'])->name('reports.commissions');
    Route::post('/reports/commissions', [ReportCommissions::class, 'index'])->name('reports.commissions.action');
    Route::get('/reports/cash', [ReportCash::class, 'index'])->name('reports.cash');
    Route::post('/reports/cash', [ReportCash::class, 'index'])->name('reports.cash.action');
    Route::put('/reports/cash/update-status', [ReportCash::class, 'update'])->name('reports.cash.action.update');

    Route::get('/reports/cancellations', [ReportCancellations::class, 'index'])->name('reports.cancellations');
    Route::post('/reports/cancellations', [ReportCancellations::class, 'index'])->name('reports.cancellations.post');

    Route::resource('/sales',SalesController::class);
    Route::resource('/payments',PaymentsController::class);

    Route::get('/tpv/handler', [TpvController::class, 'handler'])->name('tpv.handler');
    Route::get('/tpv/edit/{code}', [TpvController::class, 'index'])->name('tpv.new');
    Route::post('/tpv/quote', [TpvController::class, 'quote'])->name('tpv.quote');
    Route::post('/tpv/create', [TpvController::class, 'create'])->name('tpv.create');
    Route::get('/tpv/autocomplete/{keyword}', [TpvController::class, 'autocomplete'])->name('tpv.autocomplete');

    Route::get('/punto-de-venta', [PosController::class, 'index'])->name('pos.index');
    Route::post('/punto-de-venta', [PosController::class, 'index'])->name('pos.index.action');

    Route::get('/punto/ventas/generales', [PosController::class, 'generals'])->name('pos.generals.index');
    Route::post('/punto/ventas/generales', [PosController::class, 'generals'])->name('pos.generals.action');

    Route::get('/punto-de-venta/detail/{id}', [PosController::class, 'detail'])->where('id', '[0-9]+');
    Route::get('/punto-de-venta/capture', [PosController::class, 'capture'])->name('pos.capture');
    Route::post('/punto-de-venta/capture/create', [PosController::class, 'create'])->name('pos.capture.create');
    Route::post('/punto-de-venta/capture/update', [PosController::class, 'update'])->name('pos.capture.update');
    Route::get('/punto-de-venta/vendors', [PosController::class, 'vendors'])->name('pos.vendors');
    Route::post('/punto-de-venta/vendors/create', [PosController::class, 'createVendor'])->name('pos.vendors.create');
    Route::put('/punto-de-venta/vendors/edit', [PosController::class, 'editVendor'])->name('pos.vendors.edit');
    Route::delete('/punto-de-venta/vendors/delete', [PosController::class, 'deleteVendor'])->name('pos.vendors.delete');
    Route::put('/punto-de-venta/edit-created-at', [PosController::class, 'editCreatedAt'])->name('pos.editCreatedAt');

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
