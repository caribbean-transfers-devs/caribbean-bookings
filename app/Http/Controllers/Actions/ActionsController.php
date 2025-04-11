<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Actions\ActionsRepository;

//TRAITS
use App\Traits\RoleTrait;

class ActionsController extends Controller
{
    use RoleTrait;

    private $ActionsRepository;

    public function __construct(ActionsRepository $ActionsRepository)
    {
        $this->ActionsRepository = $ActionsRepository;
    }

    /**
     * 
     */
    public function deleteCommission(Request $request)
    {
        return $this->ActionsRepository->deleteCommission($request);
    }

    public function sendMessageWhatsApp(Request $request)
    {
        return $this->ActionsRepository->sendMessageWhatsApp($request);
    }

    /**
     * 
     */
    public function enablePayArrival(Request $request)
    {
        return $this->ActionsRepository->enablePayArrival($request);
    }

    /**
     * 
     */
    public function enablePlusService(Request $request)
    {
        return $this->ActionsRepository->enablePlusService($request);
    }

    /**
     * 
     */
    public function markReservationOpenCredit(Request $request)
    {
        return $this->ActionsRepository->markReservationOpenCredit($request);
    }

    /**
     * 
     */
    public function reactivateReservation(Request $request)
    {
        return $this->ActionsRepository->reactivateReservation($request);
    }    

    /**
     * 
     */
    public function refundRequest(Request $request)
    {
        return $this->ActionsRepository->refundRequest($request);
    }

    /**
     * 
     */
    public function markReservationDuplicate(Request $request)
    {
        return $this->ActionsRepository->markReservationDuplicate($request);
    }

    /**
     * 
     */
    public function updateServiceStatus(Request $request){
        return $this->ActionsRepository->updateServiceStatus($request);
    }

    /**
     * 
     */
    public function confirmService(Request $request){
        return $this->ActionsRepository->confirmService($request);
    }

    /**
     * 
     */
    public function enabledLike(Request $request){
        return $this->ActionsRepository->enabledLike($request);
    }

    /**
     * 
     */
    public function updateServiceUnlock(Request $request){
        return $this->ActionsRepository->updateServiceUnlock($request);
    }    
}
