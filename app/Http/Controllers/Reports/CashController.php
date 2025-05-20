<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\CashRepository;

//TRAIT
use App\Traits\RoleTrait;

class CashController extends Controller
{
    use RoleTrait;

    private $CashRepository;    

    public function __construct(CashRepository $CashRepository)
    {
        $this->CashRepository = $CashRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(50)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->CashRepository->index($request);
    }    

    public function update(Request $request){
        if(!$this->hasPermission(50)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $cashRepository->update($request);
    }
}