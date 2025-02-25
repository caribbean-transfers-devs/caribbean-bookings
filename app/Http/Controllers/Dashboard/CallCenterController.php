<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Dashboards\CallCenterResository;

//TRAITS
use App\Traits\RoleTrait;

class CallCenterController extends Controller
{
    private $CallCenterResository;

    public function __construct(CallCenterResository $CallCenterResository)
    {
        return $this->CallCenterResository = $CallCenterResository;
    }    

    public function index(Request $request){
        if(RoleTrait::hasPermission(112)){
            return $this->CallCenterResository->index($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
    }

    public function getSales(Request $request)
    {
        return $this->CallCenterResository->getSales($request);
    }

    public function getOperations(Request $request)
    {
        return $this->CallCenterResository->getOperations($request);
    }

    public function getStats(Request $request)
    {
        return $this->CallCenterResository->getStats($request);
    }

    public function chartsSales(Request $request)
    {
        return $this->CallCenterResository->chartsSales($request);
    }

    public function chartsOperations(Request $request)
    {
        return $this->CallCenterResository->chartsOperations($request);
    }
}
