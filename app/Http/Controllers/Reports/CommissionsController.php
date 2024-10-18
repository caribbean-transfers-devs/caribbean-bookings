<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\CommissionsRepository;
use App\Traits\RoleTrait;

class CommissionsController extends Controller
{
    use RoleTrait;

    public function index(Request $request, CommissionsRepository $commissionsRepository){
        if(!$this->hasPermission(45)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $commissionsRepository->index($request);        
    }

    public function index2(Request $request, CommissionsRepository $commissionsRepository){
        if(!$this->hasPermission(45)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $commissionsRepository->index2($request);        
    }    
    
}