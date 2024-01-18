<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\PaymentsRepository;
use App\Traits\RoleTrait;

class PaymentsController extends Controller
{
    use RoleTrait;

    public function managment(Request $request, PaymentsRepository $paymentsRepository){
        if(!$this->hasPermission(43)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $paymentsRepository->managment($request);        
    }
    
}