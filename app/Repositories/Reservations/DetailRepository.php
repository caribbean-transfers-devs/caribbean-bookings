<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DetailRepository
{
    public function detail($request,$id)
    {
        $reservation = Reservation::with('destination','items','sales','payments','followUps','site')->find($id);
        return view('reservations.detail', compact('reservation'));
    }
}