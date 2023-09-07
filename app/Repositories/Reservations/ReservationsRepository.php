<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReservationsRepository
{
    public function index($request)
    {
        $reservations = Reservation::with('destination', 'items')->get();
        return view('reservations.index', compact('reservations'));
    }
}