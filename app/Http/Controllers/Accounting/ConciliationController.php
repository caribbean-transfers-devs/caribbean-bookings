<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Accounting\ConciliationRepository;

//TRAITS
use App\Traits\RoleTrait;

class ConciliationController extends Controller
{
    use RoleTrait;    

    private $ConciliationRepository;    

    public function __construct(ConciliationRepository $ConciliationRepository)
    {
        $this->ConciliationRepository = $ConciliationRepository;
    }

    public function PayPalPayments(Request $request)
    {
        return $this->ConciliationRepository->PayPalPayments($request);
    }

    public function StripePayments(Request $request)
    {
        return $this->ConciliationRepository->StripePayments($request);
    }

    public function PayPalPaymenReference(Request $request, $reference){
        return $this->ConciliationRepository->PayPalPaymenReference($request, $reference);
    }

    public function StripePaymentReference(Request $request, $reference){
        return $this->ConciliationRepository->StripePaymentReference($request, $reference);
    }    
}
