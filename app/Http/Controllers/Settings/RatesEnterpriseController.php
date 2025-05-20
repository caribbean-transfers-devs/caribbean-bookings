<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\RatesEnterpriseRequest;
use App\Http\Requests\RatesEnterpriseNewRequest;
use App\Http\Requests\RatesEnterpriseDeleteRequest;
use App\Http\Requests\RatesEnterpriseUpdateRequest;

//REPOSITORY
use App\Repositories\Settings\RatesEnterpriseRepository;

//TRAITS
use App\Traits\RoleTrait;

class RatesEnterpriseController extends Controller
{
    use RoleTrait;

    private $RatesEnterpriseRepository;

    public function __construct(RatesEnterpriseRepository $RatesEnterpriseRepository)
    {
        $this->RatesEnterpriseRepository = $RatesEnterpriseRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(103)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->RatesEnterpriseRepository->index($request);
    }

    public function items(Request $request){
        return $this->RatesEnterpriseRepository->items($request);
    }

    public function getRatesEnterprise(RatesEnterpriseRequest $request){
        if(!$this->hasPermission(103)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->RatesEnterpriseRepository->getRatesEnterprise($request);
    }

    public function newRates(RatesEnterpriseNewRequest $request){
        if(!$this->hasPermission(105)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->RatesEnterpriseRepository->newRates($request);
    }

    public function deleteRates(RatesEnterpriseDeleteRequest $request){
        if(!$this->hasPermission(107)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->RatesEnterpriseRepository->deleteRates($request);
    }

    public function updateRates(RatesEnterpriseUpdateRequest $request){
        if(!$this->hasPermission(106)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }    
        return $this->RatesEnterpriseRepository->updateRates($request);
    }
}
