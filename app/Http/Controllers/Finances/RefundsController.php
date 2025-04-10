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
        if(!$this->hasPermission(114)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->RefundsRepository->index($request);
    }
}
