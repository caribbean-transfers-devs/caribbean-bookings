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

    Route::resource('/users', UserController::class);
    Route::put('/ChangePass/{user}', [UserController::class, 'change_pass'])->name('users.change_pass');
    Route::put('/ChangeStatus/{user}', [UserController::class, 'change_status'])->name('users.change_status');
    Route::post('/StoreIP', [UserController::class, 'store_ips'])->name('users.store_ips');
    Route::delete('/DeleteIPs/{ip}', [UserController::class, 'delete_ips'])->name('users.delete_ips');
    
    Route::resource('/roles', RoleController::class);

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/reservations', [ReservationsController::class, 'index'])->name('reservations.index');
    Route::put('/reservations/{reservation}', [ReservationsController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationsController::class, 'destroy'])->name('reservations.destroy');
    Route::get('/reservations/detail/{id}', [ReservationsController::class, 'detail'])->where('id', '[0-9]+');
    Route::get('/GetExchange/{reservation}', [ReservationsController::class, 'get_exchange'])->name('reservations.get_exchange');
    Route::post('/reservationsfollowups', [ReservationsController::class, 'followups'])->name('reservations.followups');

    Route::resource('/sales',SalesController::class);
    Route::resource('/payments',PaymentsController::class);

    Route::get('/tpv/handler', [TpvController::class, 'handler'])->name('tpv.handler');
    Route::get('/tpv/new/{code}', [TpvController::class, 'index'])->name('tpv.new');
    Route::post('/tpv/new/{code}', [TpvController::class, 'quote'])->name('tpv.quote');
    Route::get('/tpv/autocomplete/{keyword}', [TpvController::class, 'autocomplete'])->name('tpv.autocomplete');
});
