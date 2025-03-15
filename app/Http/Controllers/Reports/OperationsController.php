<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\OperationsRepository;

//TRAIT
use App\Traits\RoleTrait;

class OperationsController extends Controller
{
    use RoleTrait;

    private $OperationsRepository;    

    public function __construct(OperationsRepository $OperationsRepository)
    {
        $this->OperationsRepository = $OperationsRepository;
    } 

    public function index(Request $request){
        return $this->OperationsRepository->index($request);
    }
}