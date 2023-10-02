<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Operation\OperationRepository;

class OperationController extends Controller
{
    public function index(Request $request, OperationRepository $operationRepository){
        return $operationRepository->index($request);        
    }

    public function managment(Request $request, OperationRepository $operationRepository){
        return $operationRepository->managment($request);        
    }

    public function statusUpdate(Request $request, OperationRepository $operationRepository){
        return $operationRepository->statusUpdate($request);        
    }
}