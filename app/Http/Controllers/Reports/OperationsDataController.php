<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\OperationsDataRepository;

//TRAIT
use App\Traits\RoleTrait;

class OperationsDataController extends Controller
{
    use RoleTrait;

    private $OperationsDataRepository;    

    public function __construct(OperationsDataRepository $OperationsDataRepository)
    {
        $this->OperationsDataRepository = $OperationsDataRepository;
    } 

    public function index(Request $request){
        return $this->OperationsDataRepository->index($request);
    }
}