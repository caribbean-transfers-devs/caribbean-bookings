<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Accounting\ConciliationRepository;

//TRAITS
use App\Traits\RoleTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

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

    public function stripeChargesReference(Request $request, $reference)
    {
        return $this->ConciliationRepository->stripeChargesReference($request, $reference);
    }

    public function stripePaymentIntentsReference(Request $request, $reference)
    {
        return $this->ConciliationRepository->stripePaymentIntentsReference($request, $reference);
    }

    public function stripePayoutsReference(Request $request, $reference)
    {
        return $this->ConciliationRepository->stripePayoutsReference($request, $reference);
    }

    public function stripeInternalPayouts(Request $request)
    {
        return $this->ConciliationRepository->stripeInternalPayouts($request);
    }    

    public function stripeTemporalSemiAutomaticConciliation()
    {
        // Este código es totalmente temporal, sólo debe funcionar por unos días, ya que presenta vulnerabilidades
        $data = [];
        if(Cache::has('stripe-automatic-conciliation-key')) {
            $data = Cache::get('stripe-automatic-conciliation-key');
        }
        else {
            $data = $this->ConciliationRepository->generateStripeAutomaticConciliationData();
            Cache::put('stripe-automatic-conciliation-key', $data, Carbon::now()->addMinutes(10));
        }

        return $data;
    }    

    public function stripeTemporalConfirmAutomaticConciliation(Request $request)
    {
        // Este código es totalmente temporal, sólo debe funcionar por unos días, ya que presenta vulnerabilidades
        Validator::validate($request->all(), [
            'payments' => ['array'],
            'stripe_payments' => ['array'],
        ]);

        $this->ConciliationRepository->saveStripeAutomaticConciliation([
            'payments' => $request->payments,
            'stripe_payments' => $request->stripe_payments,
        ]);

        Cache::delete('stripe-automatic-conciliation-key');

        return response()->json([
            'status'   => 'success',
            'message'  => 'Conciliación confirmada',
            'code'     => 200
        ], Response::HTTP_OK);
    }    
}
