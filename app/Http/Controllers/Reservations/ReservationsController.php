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
    use RoleTrait;
    
    public function detail(Request $request, DetailRepository $detailRepository, $id){
        if($this->hasPermission(10) || $this->hasPermission(61)){
            return $detailRepository->detail($request,$id);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    //NOS TRAE LOS PAGOS DE LA RESERVACIÓN
    public function reservationPayments(ReservationsRepository $reservationRepository, Reservation $reservation){
        return $reservationRepository->reservationPayments($reservation);
    }

    public function update(ReservationDetailsRequest $request, ReservationsRepository $reservationRepository, Reservation $reservation){
        if($this->hasPermission(11)){
            return $reservationRepository->update($request,$reservation);
        }
    }

    public function destroy(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation){
        if($this->hasPermission(24)){
            return $reservationRepository->destroy($request,$reservation);
        }
    }

    public function duplicated(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation){
        if($this->hasPermission(24)){
            return $reservationRepository->duplicated($request,$reservation);
        }
    }

    public function removeCommission(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        if($this->hasPermission(24)){
            return $reservationRepository->removeCommission($request,$reservation);
        }
    }

    public function openCredit(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation)
    {
        if($this->hasPermission(72)){
            return $reservationRepository->openCredit($request,$reservation);
        }
    }

    public function enablePlusService(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation){
        if($this->hasPermission(94)){
            return $reservationRepository->enablePlusService($request,$reservation);
        }
    }

    public function enable(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation){
        if($this->hasPermission(67)){
            return $reservationRepository->enableReservation($request,$reservation);
        }
    }

    public function followups(ReservationFollowUpsRequest $request, ReservationsRepository $reservationRepository){
        if($this->hasPermission(23)){
            return $reservationRepository->follow_ups($request);
        }
    }

    public function get_exchange(Request $request, ReservationsRepository $reservationRepository, Reservation $reservation){
        return $reservationRepository->get_exchange($request,$reservation);
    }

    public function editreservitem(ReservationItemRequest $request, ReservationsRepository $reservationRepository, ReservationsItem $item){
        if($this->hasPermission(13)){
            return $reservationRepository->editreservitem($request,$item);
        }
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
        if($this->hasPermission(64)){
            return $uploadRepository->add($request);
        }
    } 

    public function deleteMedia(Request $request, UploadRepository $uploadRepository){
        if($this->hasPermission(66)){
            return $uploadRepository->delete($request);
        }
    }

    public function getMedia(Request $request, DetailRepository $detailRepository){
        if($this->hasPermission(65)){
            return $detailRepository->getMedia($request);
        }
    }
}