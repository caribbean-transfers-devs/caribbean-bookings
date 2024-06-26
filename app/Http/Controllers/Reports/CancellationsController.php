<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Reports\CancellationsRepository;
use App\Traits\RoleTrait;

class CancellationsController extends Controller
{
    use RoleTrait;

    public function index(Request $request, CancellationsRepository $cancelRepository){
        if(!$this->hasPermission(71)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $cancelRepository->index($request);
    }

}