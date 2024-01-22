<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\SalesRepository;
use App\Traits\RoleTrait;

class SalesController extends Controller
{
    use RoleTrait;

    public function index(Request $request, SalesRepository $salesRepository){
        if(!$this->hasPermission(44)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $salesRepository->index($request);        
    }
    
}