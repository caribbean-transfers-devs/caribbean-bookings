<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Actions\ActionsRepository;

//TRIT
use App\Traits\RoleTrait;

class ActionsController extends Controller
{
    use RoleTrait;

    private $ActionsRepository;

    public function __construct(ActionsRepository $ActionsRepository)
    {
        $this->ActionsRepository = $ActionsRepository;
    }

    public function updateServiceStatus(Request $request){
        return $this->ActionsRepository->updateServiceStatus($request);        
    }    
}
