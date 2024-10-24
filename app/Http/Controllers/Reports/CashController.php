<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\CashRepository;
use App\Traits\RoleTrait;

class CashController extends Controller
{
    use RoleTrait;

    public function update(Request $request, CashRepository $cashRepository){
        if(!$this->hasPermission(50)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $cashRepository->update($request);
    }
}