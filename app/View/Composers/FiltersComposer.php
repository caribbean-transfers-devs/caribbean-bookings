<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Traits\FiltersTrait;

class FiltersComposer
{
    use FiltersTrait;
    
    public function compose(View $view)
    {
        $users = $this->CallCenterAgent();
        $services = $this->Services();
        $websites = $this->Sites();
        $origins = $this->Origins();
        $reservation_status = $this->reservationStatus();
        $vehicles = $this->Vehicles();
        $zones = $this->Zones();
        $payment_status = $this->paymentStatus();
        $currencies = $this->Currencies();
        $methods = $this->Methods();
        $cancellations = $this->CancellationTypes();

        // Aquí puedes cargar los datos y pasarlos a la vista
        $view->with('users', $users);
        $view->with('services', $services);
        $view->with('websites', $websites);
        $view->with('origins', $origins);
        $view->with('reservation_status', $reservation_status);
        $view->with('vehicles', $vehicles);
        $view->with('zones', $zones);
        $view->with('payment_status', $payment_status);
        $view->with('currencies', $currencies);
        $view->with('methods', $methods);
        $view->with('cancellations', $cancellations);
        // $view->with('data', []);
    }
}