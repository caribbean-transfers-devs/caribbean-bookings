<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use App\Repositories\Rates\RatesRepository;
use Illuminate\Http\Request;
use App\Http\Requests\RatesRequest;
use App\Http\Requests\RatesNewRequest;
use App\Http\Requests\RatesDeleteRequest;
use App\Http\Requests\RatesUpdateRequest;

class RatesController extends Controller
{
    public function index(Request $request, RatesRepository $ratesRepository){
        /*if(RoleTrait::hasPermission(17)){
            return $saleRepository->store($request);
        }*/

        return $ratesRepository->index($request);
    }

    public function items(Request $request, RatesRepository $ratesRepository){                
        return $ratesRepository->items($request);
    }

    public function getRates(RatesRequest $request, RatesRepository $ratesRepository){
        return $ratesRepository->getRates($request);
    }

    public function newRates(RatesNewRequest $request, RatesRepository $ratesRepository){
        return $ratesRepository->newRates($request);
    }

    public function deleteRates(RatesDeleteRequest $request, RatesRepository $ratesRepository){
        return $ratesRepository->deleteRates($request);
    }

    public function updateRates(RatesUpdateRequest $request, RatesRepository $ratesRepository){
        return $ratesRepository->updateRates($request);
    }
} 