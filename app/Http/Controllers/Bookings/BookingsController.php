<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Bookings\BookingsRepository;

class BookingsController extends Controller
{
    private $BookingsRepository;

    public function __construct(BookingsRepository $BookingsRepository)
    {
        $this->BookingsRepository = $BookingsRepository;
    }

    public function ReservationDetail(Request $request){
        return $this->BookingsRepository->ReservationDetail($request);
    }
}
