<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Finances\RefundsRepository;

//TRAITS
use App\Traits\RoleTrait;

class RefundsController extends Controller
{
    use RoleTrait;    

    private $RefundsRepository;    

    public function __construct(RefundsRepository $RefundsRepository)
    {
        $this->RefundsRepository = $RefundsRepository;
    }

    public function index(Request $request)
    {
        return $this->RefundsRepository->index($request);
    }
}
