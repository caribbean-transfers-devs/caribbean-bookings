<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\CommissionsRepository;

//TRAIT
use App\Traits\RoleTrait;

class CommissionsController extends Controller
{
    use RoleTrait;

    private $CommissionsRepository;    

    public function __construct(CommissionsRepository $CommissionsRepository)
    {
        $this->CommissionsRepository = $CommissionsRepository;
    }

    public function index(Request $request)
    {
        if(!$this->hasPermission(45)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->CommissionsRepository->index($request);
    }

    public function index2(Request $request)
    {
        if(!$this->hasPermission(45)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->CommissionsRepository->index2($request);
    }

    public function getSales(Request $request)
    {
        return $this->CommissionsRepository->getSales($request);
    }
    
    public function getOperations(Request $request)
    {
        return $this->CommissionsRepository->getOperations($request);
    }
    
    public function getCommissions(Request $request)
    {
        return $this->CommissionsRepository->getCommissions($request);
    }

    public function getStats(Request $request)
    {
        return $this->CommissionsRepository->getStats($request);
    }

    public function chartsSales(Request $request)
    {
        return $this->CommissionsRepository->chartsSales($request);
    }

    public function chartsOperations(Request $request)
    {
        return $this->CommissionsRepository->chartsOperations($request);
    }
}
