<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Operation\OperationRepository;
use App\Repositories\Operation\ConfirmationRepository;
use App\Repositories\Operation\SpamRepository;
use App\Traits\RoleTrait;

class OperationController extends Controller
{
    use RoleTrait;

    public function index(Request $request, OperationRepository $operationRepository){
        return $operationRepository->index($request);
    }

    public function managment(Request $request, OperationRepository $operationRepository){
        return $operationRepository->managment($request);        
    }

    public function statusUpdate(Request $request, OperationRepository $operationRepository){
        return $operationRepository->statusUpdate($request);        
    }
    
    public function confirmation(Request $request, ConfirmationRepository $operationRepository){
        if(!$this->hasPermission(39)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $operationRepository->index($request);
    }
    
    public function confirmationUpdate(Request $request, ConfirmationRepository $operationRepository){
        if(!$this->hasPermission(40)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $operationRepository->statusUpdate($request);        
    }

    public function updateUnlock(Request $request, ConfirmationRepository $operationRepository){
        if(!$this->hasPermission(92)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $operationRepository->updateUnlock($request);        
    }

    public function spam(Request $request, SpamRepository $spamRepository){
        if(!$this->hasPermission(47)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $spamRepository->index($request);
    }

    public function exportExcel(Request $request, SpamRepository $spamRepository){
        if(!$this->hasPermission(47)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $spamRepository->exportExcel($request);
    }    
    
    public function spamUpdate(Request $request, SpamRepository $spamRepository){
        if(!$this->hasPermission(47)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $spamRepository->spamUpdate($request);
    }
}