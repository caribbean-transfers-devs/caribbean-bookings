<?php
namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Management\SpamRepository;

//TRIT
use App\Traits\RoleTrait;

class SpamController extends Controller
{
    use RoleTrait;
    
    public function get(Request $request, SpamRepository $spam){
        return $spam->get($request);
    }

    public function getBasicInformation(Request $request, SpamRepository $spam){
        return $spam->getBasicInformation($request);
    }

    public function getHistory(Request $request, SpamRepository $spam){
        return $spam->getHistory($request);
    }

    public function addHistory(Request $request, SpamRepository $spam){
        return $spam->addHistory($request);
    }
}