<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Finances\SalesRepository;

//TRAITS
use App\Traits\RoleTrait;

class SalesController extends Controller
{
    use RoleTrait;    

    private $SalesRepository;    

    public function __construct(SalesRepository $SalesRepository)
    {
        $this->SalesRepository = $SalesRepository;
    }

    public function index(Request $request)
    {
        return $this->SalesRepository->index($request);
    }
}
