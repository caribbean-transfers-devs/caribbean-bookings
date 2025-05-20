<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Management\ConfirmationsRepository;

//TRAIT
use App\Traits\RoleTrait;

class ConfirmationsController extends Controller
{
    use RoleTrait;

    private $ConfirmationsRepository;

    public function __construct(ConfirmationsRepository $ConfirmationsRepository)
    {
        $this->ConfirmationsRepository = $ConfirmationsRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(39)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ConfirmationsRepository->index($request);
    }
}
