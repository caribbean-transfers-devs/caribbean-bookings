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

    public function getLogReservation(Request $request)
    {
        return $this->FinanceRepository->getLogReservation($request);
    }
}
