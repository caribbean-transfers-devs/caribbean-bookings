<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Operation\OperationRepository;
use App\Repositories\Operation\ConfirmationRepository;
use App\Repositories\Operation\SpamRepository;

//TRIT
use App\Traits\RoleTrait;

class OperationController extends Controller
{
    use RoleTrait;

    private $OperationRepository;

    public function __construct(OperationRepository $OperationRepository)
    {
        $this->OperationRepository = $OperationRepository;
    }

    public function reservations(Request $request){
        if(RoleTrait::hasPermission(10)){
            return $this->OperationRepository->reservations($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }
    
    public function updateStatusConfirmation(Request $request){
        if(!$this->hasPermission(40)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->OperationRepository->updateStatusConfirmation($request);        
    }

    public function updateUnlock(Request $request, ConfirmationRepository $operationRepository){
        if(!$this->hasPermission(92)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $operationRepository->updateUnlock($request);        
    }
}