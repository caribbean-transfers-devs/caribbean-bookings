<?php

namespace App\Http\Controllers\Finances;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Finances\ChargebacksRepository;

//TRAITS
use App\Traits\RoleTrait;

class ChargebacksController extends Controller
{
    use RoleTrait;

    private $ChargebacksRepository;

    public function __construct(ChargebacksRepository $ChargebacksRepository)
    {
        $this->ChargebacksRepository = $ChargebacksRepository;
    }

    public function index(Request $request)
    {
        return $this->ChargebacksRepository->index($request);
    }
}
