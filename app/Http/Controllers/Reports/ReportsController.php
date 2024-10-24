<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\PaymentsRepository;
use App\Repositories\Reports\ReportsRepository;
use App\Repositories\Reports\SalesRepository;

//TRAIT
use App\Traits\RoleTrait;

class ReportsController extends Controller
{
    use RoleTrait;

    private $ReportsRepository;    

    public function __construct(ReportsRepository $ReportsRepository)
    {
        $this->ReportsRepository = $ReportsRepository;
    }

    public function payments(Request $request){
        if(!$this->hasPermission(43)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReportsRepository->payments($request);        
    }

    public function sales(Request $request){
        if(!$this->hasPermission(44)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReportsRepository->sales($request);        
    }

    public function cash(Request $request){
        if(!$this->hasPermission(50)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReportsRepository->cash($request);
    }

    public function cancellations(Request $request){
        if(!$this->hasPermission(71)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReportsRepository->cancellations($request);
    }

    public function commissions(Request $request){
        if(!$this->hasPermission(45)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReportsRepository->commissions($request);
    }

    public function commissions2(Request $request){
        if(!$this->hasPermission(45)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReportsRepository->commissions2($request);
    }

    public function reservations(Request $request){
        return $this->ReportsRepository->reservations($request);
    }    

    public function operations(Request $request){
        return $this->ReportsRepository->operations($request);
    }

}