<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\CCFormRepository;
use App\Traits\RoleTrait;

class CCFormController extends Controller
{
    use RoleTrait;

    public function index(Request $request, CCFormRepository $CCFormRepository){
        if(!$this->hasPermission(46)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $CCFormRepository->index($request);        
    }

    public function createPDF(Request $request, CCFormRepository $CCFormRepository){
        if(!$this->hasPermission(46)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $CCFormRepository->createPDF($request);        
    }
    
}