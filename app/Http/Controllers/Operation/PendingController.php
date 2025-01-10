<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Operation\PendingRepository;

//TRIT
use App\Traits\RoleTrait;

class PendingController extends Controller
{
    use RoleTrait;
    
    public function get(Request $request, PendingRepository $pending){
        return $pending->get($request);
    }
}