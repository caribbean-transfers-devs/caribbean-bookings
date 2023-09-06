<?php

namespace App\Repositories\Tpv;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TpvRepository
{
    public function index($request)
    {
        return view('tpv.index');
    }
}