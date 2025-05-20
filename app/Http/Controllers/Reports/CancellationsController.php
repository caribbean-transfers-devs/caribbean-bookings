<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\CancellationsRepository;

//TRAIT
use App\Traits\RoleTrait;

class CancellationsController extends Controller
{
    use RoleTrait;

    private $CancellationsRepository;    

    public function __construct(CancellationsRepository $CancellationsRepository)
    {
        $this->CancellationsRepository = $CancellationsRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(71)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->CancellationsRepository->index($request);
    }
}
