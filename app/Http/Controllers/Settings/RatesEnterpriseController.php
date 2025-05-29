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

    private $rates;

    public function __construct(RatesEnterpriseRepository $RatesEnterpriseRepository)
    {
        $this->rates = $RatesEnterpriseRepository;
    }

    public function index(Request $request, $id = 0){
        if(!$this->hasPermission(104)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->index($request, $id);
    }

    public function create(Request $request, $id = 0)
    {
        if(!$this->hasPermission(105)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->create($request, $id);
    }

    public function store(RatesEnterpriseNewRequest $request, $id = 0)
    {
        if(!$this->hasPermission(105)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->rates->store($request, $id);
    }

    public function edit(Request $request, $id = 0)
    {
        if(!$this->hasPermission(106)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->rates->edit($request, $id);
    }

    public function update(RatesEnterpriseNewRequest $request, $id = 0)
    {
        if(!$this->hasPermission(106)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->rates->update($request, $id);
    }






    public function items(Request $request){
        return $this->rates->items($request);
    }

    public function getRatesEnterprise(RatesEnterpriseRequest $request){
        if(!$this->hasPermission(104)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->getRatesEnterprise($request);
    }

    public function newRates(RatesEnterpriseNewRequest $request){
        if(!$this->hasPermission(105)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->newRates($request);
    }

    public function deleteRates(RatesEnterpriseDeleteRequest $request){
        if(!$this->hasPermission(107)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->rates->deleteRates($request);
    }

    public function updateRates(RatesEnterpriseUpdateRequest $request){
        if(!$this->hasPermission(106)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }    
        return $this->rates->updateRates($request);
    }
}
