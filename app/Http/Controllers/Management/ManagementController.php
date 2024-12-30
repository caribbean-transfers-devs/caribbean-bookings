<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Management\ManagementRepository;

//TRAIT
use App\Traits\RoleTrait;

class ManagementController extends Controller
{
    use RoleTrait;

    private $ManagementRepository;    

    public function __construct(ManagementRepository $ManagementRepository)
    {
        $this->ManagementRepository = $ManagementRepository;
    }

    public function confirmation(Request $request){
        if(!$this->hasPermission(39)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ManagementRepository->confirmation($request);
    }

    public function afterSales(Request $request){
        if(!$this->hasPermission(47)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ManagementRepository->afterSales($request);
    }    
}
