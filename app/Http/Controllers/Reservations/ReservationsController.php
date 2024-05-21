<?php
namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationDetailsRequest;
use App\Http\Requests\ReservationFollowUpsRequest;
use App\Http\Requests\ReservationItemRequest;
use App\Http\Requests\ReservationConfirmationRequest;
use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\Role;
use App\Repositories\Reservations\DetailRepository;
use App\Repositories\Reservations\ReservationsRepository;
use App\Repositories\Reservations\UploadRepository;
use Illuminate\Http\Request;
use App\Traits\RoleTrait;

class ReservationsController extends Controller
{
    public function detail(Request $request, DetailRepository $detailRepository, $id)
    {
        if(RoleTrait::hasPermission(10) || RoleTrait::hasPermission(61)){
            return $detailRepository->detail($request,$id);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function index(Request $request, ReservationsRepository $reservationRepository)
    {
        if(RoleTrait::hasPermission(10)){
            return $reservationRepository->index($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function update(ReservationDetailsRequest $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        if(RoleTrait::hasPermission(11)){
            return $reservationRepository->update($request,$reservation);
        }
    }

    public function destroy(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        if(RoleTrait::hasPermission(24)){
            return $reservationRepository->destroy($request,$reservation);
        }
    }

    public function duplicated(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        if(RoleTrait::hasPermission(24)){
            return $reservationRepository->duplicated($request,$reservation);
        }
    }

    public function followups(ReservationFollowUpsRequest $request, ReservationsRepository $reservationRepository)
    {
        if(RoleTrait::hasPermission(23)){
            return $reservationRepository->follow_ups($request);
        }
    }

    public function get_exchange(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        return $reservationRepository->get_exchange($request,$reservation);
    }

    public function editreservitem(ReservationItemRequest $request, ReservationsRepository $reservationRepository, ReservationsItem $item)
    {
        if(RoleTrait::hasPermission(13)){
            return $reservationRepository->editreservitem($request,$item);
        }
    }

    public function contactPoint(Request $request, ReservationsRepository $reservationRepository){
        return $reservationRepository->getContactPoints($request);
    }

    public function arrivalConfirmation(ReservationConfirmationRequest $request, ReservationsRepository $reservationRepository){
        return $reservationRepository->sendArrivalConfirmation($request);
    }

    public function departureConfirmation(Request $request, ReservationsRepository $reservationRepository){
        return $reservationRepository->sendDepartureConfirmation($request);
    }

    public function paymentRequest(Request $request, ReservationsRepository $reservationRepository){
        return $reservationRepository->sendPaymentRequest($request);
    }

    public function uploadMedia(Request $request, UploadRepository $uploadRepository){
        if(RoleTrait::hasPermission(64)){
            return $uploadRepository->add($request);
        }
    }

    public function deleteMedia(Request $request, UploadRepository $uploadRepository){
        if(RoleTrait::hasPermission(66)){
            return $uploadRepository->delete($request);
        }
    }

    public function getMedia(Request $request, DetailRepository $detailRepository){
        if(RoleTrait::hasPermission(65)){
            return $detailRepository->getMedia($request);
        }
    }
}