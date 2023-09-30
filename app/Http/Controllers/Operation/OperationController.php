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
}