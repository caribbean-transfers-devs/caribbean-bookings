<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Management\HotelesRepository;

//TRAIT
use App\Traits\RoleTrait;

class HotelsController extends Controller
{
    use RoleTrait;

    private $HotelesRepository;

    public function __construct(HotelesRepository $HotelesRepository)
    {
        $this->HotelesRepository = $HotelesRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(123)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->HotelesRepository->index($request);
    }

    public function hotelAdd(Request $request){
        if(!$this->hasPermission(124)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->HotelesRepository->hotelAdd($request);        
    }

    public function delete(Request $request){
        if(!$this->hasPermission(134)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->HotelesRepository->delete($request);        
    }
}
