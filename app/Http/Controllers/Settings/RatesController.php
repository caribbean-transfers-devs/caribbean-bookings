<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\RatesRequest;
use App\Http\Requests\RatesNewRequest;
use App\Http\Requests\RatesDeleteRequest;
use App\Http\Requests\RatesUpdateRequest;

use App\Repositories\Settings\RatesRepository;


use App\Traits\RoleTrait;

class RatesController extends Controller
{
    use RoleTrait;

    private $rates;

    public function __construct(RatesRepository $RatesRepository)
    {
        $this->rates = $RatesRepository;
    }

    public function index(Request $request, $id = 0){
        if(!$this->hasPermission(32)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->index($request, $id);
    }

    public function create(Request $request, $id = 0)
    {
        if(!$this->hasPermission(33)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->create($request, $id);
    }

    public function store(RatesNewRequest $request, $id = 0)
    {
        if(!$this->hasPermission(33)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->rates->store($request, $id);
    }

    public function edit(Request $request, $id = 0)
    {
        if(!$this->hasPermission(34)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->rates->edit($request, $id);
    }

    public function update(RatesNewRequest $request, $id = 0)
    {
        if(!$this->hasPermission(34)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->rates->update($request, $id);
    }






    public function items(Request $request){
        return $this->rates->items($request);
    }

    public function getRates(RatesRequest $request){
        if(!$this->hasPermission(32)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->getRates($request);
    }

    public function newRates(RatesNewRequest $request){
        if(!$this->hasPermission(33)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->newRates($request);
    }

    public function deleteRates(RatesDeleteRequest $request){
        if(!$this->hasPermission(35)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->deleteRates($request);
    }

    public function updateRates(RatesUpdateRequest $request){
        if(!$this->hasPermission(34)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->updateRates($request);
    }
} 