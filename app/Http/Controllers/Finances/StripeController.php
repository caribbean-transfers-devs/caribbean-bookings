<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Finances\StripeRepository;

//TRAITS
use App\Traits\RoleTrait;

class StripeController extends Controller
{
    use RoleTrait;    

    private $StripeRepository;    

    public function __construct(StripeRepository $StripeRepository)
    {
        $this->StripeRepository = $StripeRepository;
    }

    public function index(Request $request){
        return $this->StripeRepository->index($request);
    }
}
