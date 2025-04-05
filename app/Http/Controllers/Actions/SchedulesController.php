<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Actions\SchedulesRepository;

//TRAITS
use App\Traits\RoleTrait;

class SchedulesController extends Controller
{
    use RoleTrait;

    private $SchedulesRepository;

    public function __construct(SchedulesRepository $SchedulesRepository)
    {
        $this->SchedulesRepository = $SchedulesRepository;
    }

    /**
     * 
     */
    public function deleteCommission(Request $request)
    {
        return $this->SchedulesRepository->deleteCommission($request);
    }
}
