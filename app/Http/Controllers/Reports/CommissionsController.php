<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
