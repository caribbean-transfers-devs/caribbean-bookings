<?php

namespace App\Http\Controllers\Bots;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Bots\MasterToursRepository;

//TRAITS
use App\Traits\RoleTrait;

class MasterToursController extends Controller
{
    use RoleTrait;

    private $MasterToursRepository;    

    public function __construct(MasterToursRepository $MasterToursRepository)
    {
        $this->MasterToursRepository = $MasterToursRepository;
    }

    public function ListServicesMasterTour(Request $request)
    {
        return $this->MasterToursRepository->ListServicesMasterTour($request);
    }   
}
