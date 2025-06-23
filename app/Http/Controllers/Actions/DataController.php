<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Actions\DataRepository;

//TRAITS
use App\Traits\RoleTrait;

class DataController extends Controller
{
    use RoleTrait;

    private $DataRepository;

    public function __construct(DataRepository $DataRepository)
    {
        $this->DataRepository = $DataRepository;
    }

    public function typesCancellations(Request $request)
    {
        return $this->DataRepository->typesCancellations($request);
    }
}
