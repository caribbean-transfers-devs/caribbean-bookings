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

    public function PayPalPaymenReference(Request $request, $reference){
        return $this->ConciliationRepository->PayPalPaymenReference($request, $reference);
    }

    public function PayPalPaymenOrder(Request $request, $id){
        return $this->ConciliationRepository->PayPalPaymenOrder($request, $id);
    }

    public function PayPalPaymenOrders(Request $request){
        return $this->ConciliationRepository->PayPalPaymenOrders($request);
    }

    //STRIPE 

    //ESTE METODO ES EL QUE UTIIZA EL BOT PARA CONCILIAR VARIOS PAGOS AL MISMO TIEMPO
    //ESTE METODO ES UTILIZADO PARA CONCILIAR VARIOS PAGOS AL MISMO TIEMPO MEDIANTE UN RANGO DE FECHAS ESPECIFICADOS    
    public function StripePayments(Request $request)
    {
        return $this->ConciliationRepository->StripePayments($request);
    }    

    public function stripePayouts(Request $request)
    {
        return $this->ConciliationRepository->stripePayouts($request);        
    }

    public function stripeChargesReference(Request $request, $reference)
    {
        return $this->ConciliationRepository->stripeChargesReference($request, $reference);
    }

    public function stripePaymentIntentsReference(Request $request, $reference)
    {
        return $this->ConciliationRepository->stripePaymentIntentsReference($request, $reference);
    }
}
