<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\RatesRequest;
use App\Http\Requests\RatesNewRequest;
use App\Http\Requests\RatesDeleteRequest;
use App\Http\Requests\RatesUpdateRequest;

//REPOSITORY
use App\Repositories\Settings\RatesEnterpriseRepository;

//TRAITS
use App\Traits\RoleTrait;

class RatesEnterpriseController extends Controller
{
    use RoleTrait;

    public function index(Request $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        if(RoleTrait::hasPermission(104)){
            return $RatesEnterpriseRepository->index($request);
        }
    }

    public function items(Request $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        return $RatesEnterpriseRepository->items($request);
    }

    public function getRates(RatesRequest $request, RatesEnterpriseRepository $RatesEnterpriseRepository){
        return $RatesEnterpriseRepository->getRates($request);
    }
}
