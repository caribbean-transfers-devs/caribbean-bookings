<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

// MODELS
use App\Models\OperatorFee;
use App\Models\ReservationsItem;

// OBSERVER
use App\Observers\OperatorFeeObserver;
use App\Observers\ReservationItemObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Paginator::useBootstrap();
        OperatorFee::observe(OperatorFeeObserver::class);
        ReservationsItem::observe(ReservationItemObserver::class);
    }
}
