<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportsRepository
{
    public function operations($request){
        return view('reports.operations');
    }
}
