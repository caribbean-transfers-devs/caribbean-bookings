<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Management\AfterSalesRepository;

//TRAIT
use App\Traits\RoleTrait;

class AfterSalesController extends Controller
{
    use RoleTrait;

    private $AfterSalesRepository;

    public function __construct(AfterSalesRepository $AfterSalesRepository)
    {
        $this->AfterSalesRepository = $AfterSalesRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(47)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->AfterSalesRepository->index($request);
    }
}
