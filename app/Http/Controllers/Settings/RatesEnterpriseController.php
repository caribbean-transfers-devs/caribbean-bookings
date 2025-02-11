<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function index(Request $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        if(!RoleTrait::hasPermission(103)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $RatesEnterpriseRepository->index($request);
    }

    public function items(Request $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        return $RatesEnterpriseRepository->items($request);
    }

    public function getRates(RatesEnterpriseRequest $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        if(!RoleTrait::hasPermission(103)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $RatesEnterpriseRepository->getRates($request);
    }

    public function newRates(RatesEnterpriseNewRequest $request, RatesEnterpriseRepository $RatesEnterpriseRepository){        
        if(!RoleTrait::hasPermission(105)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $RatesEnterpriseRepository->newRates($request);
    }

    public function deleteRates(RatesEnterpriseDeleteRequest $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        if(!RoleTrait::hasPermission(107)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $RatesEnterpriseRepository->deleteRates($request);
    }

    public function updateRates(RatesEnterpriseUpdateRequest $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        if(!RoleTrait::hasPermission(106)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        
        return $RatesEnterpriseRepository->updateRates($request);
    }    
}
