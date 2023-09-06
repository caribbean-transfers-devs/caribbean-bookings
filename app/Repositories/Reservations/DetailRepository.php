<?php

namespace App\Repositories\Reservations;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DetailRepository
{
    public function detail($request)
    {
        return view('reservations.detail');
    }
}