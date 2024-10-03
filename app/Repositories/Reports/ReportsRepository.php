<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\GeneralTrait;
use App\Traits\QueryTrait;
use App\Traits\Reports\PaymentsTrait;

class ReportsRepository
{
    use QueryTrait, GeneralTrait, PaymentsTrait;

    public function operations($request){
        return view('reports.operations');
    }
}
