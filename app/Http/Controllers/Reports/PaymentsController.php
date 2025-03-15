<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\PaymentsRepository;

//TRAIT
use App\Traits\RoleTrait;

class PaymentsController extends Controller
{
    use RoleTrait;

    private $PaymentsRepository;    

    public function __construct(PaymentsRepository $PaymentsRepository)
    {
        $this->PaymentsRepository = $PaymentsRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(43)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->PaymentsRepository->index($request);        
    }
}
