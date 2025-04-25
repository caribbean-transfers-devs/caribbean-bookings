<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Finances\ReceivablesRepository;

//TRAITS
use App\Traits\RoleTrait;

class ReceivablesController extends Controller
{
    use RoleTrait;

    private $ReceivablesRepository;    

    public function __construct(ReceivablesRepository $ReceivablesRepository)
    {
        $this->ReceivablesRepository = $ReceivablesRepository;
    }

    public function index(Request $request)
    {
        if(!$this->hasPermission(119)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
        return $this->ReceivablesRepository->index($request);
    }    
}
