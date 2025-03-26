<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Actions\FinanceRepository;

//TRIT
use App\Traits\RoleTrait;

class FinanceController extends Controller
{
    use RoleTrait;

    private $FinanceRepository;

    public function __construct(FinanceRepository $FinanceRepository)
    {
        $this->FinanceRepository = $FinanceRepository;
    }

    public function addPaymentRefund(Request $request)
    {
        return $this->FinanceRepository->addPaymentRefund($request);
    }

    public function refundNotApplicable(Request $request)
    {
        return $this->FinanceRepository->refundNotApplicable($request);
    }    

    public function getBasicInformationReservation(Request $request){
        return $this->FinanceRepository->getBasicInformationReservation($request);
    }

    public function getPhotosReservation(Request $request)
    {
        return $this->FinanceRepository->getPhotosReservation($request);
    }

    public function getHistoryReservation(Request $request)
    {
        return $this->FinanceRepository->getHistoryReservation($request);
    }

    public function getPaymentsReservation(Request $request)
    {
        return $this->FinanceRepository->getPaymentsReservation($request);
    }
}