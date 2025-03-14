<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Management\ReservationsRepository;

//TRAIT
use App\Traits\RoleTrait;

class ReservationsController extends Controller
{
    use RoleTrait;

    private $ReservationsRepository;

    public function __construct(ReservationsRepository $ReservationsRepository)
    {
        $this->ReservationsRepository = $ReservationsRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(10)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ReservationsRepository->index($request);
    }
}
