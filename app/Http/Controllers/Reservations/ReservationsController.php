<?php
namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationDetailsRequest;
use App\Http\Requests\ReservationFollowUpsRequest;
use App\Models\Reservation;
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

    public function update(ReservationDetailsRequest $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        return $reservationRepository->update($request,$reservation);
    }

    public function destroy(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        return $reservationRepository->destroy($request,$reservation);
    }

    public function followups(ReservationFollowUpsRequest $request, ReservationsRepository $reservationRepository)
    {
        return $reservationRepository->follow_ups($request);
    }

    public function get_exchange(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        return $reservationRepository->get_exchange($request,$reservation);
    }
}