<?php
namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Repositories\Reservations\DetailRepository;
use App\Repositories\Reservations\ReservationsRepository;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function detail(Request $request, DetailRepository $detailRepository, $id)
    {
        return $detailRepository->detail($request,$id);
    }

    public function index(Request $request, ReservationsRepository $reservationRepository)
    {
        return $reservationRepository->index($request);
    }
}