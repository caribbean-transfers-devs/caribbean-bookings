<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\SalesRepository;

//TRAIT
use App\Traits\RoleTrait;

class SalesController extends Controller
{
    use RoleTrait;

    private $SalesRepository;    

    public function __construct(SalesRepository $SalesRepository)
    {
        $this->SalesRepository = $SalesRepository;
    }

    public function index(Request $request){
        return $this->SalesRepository->index($request);
    }
}
