<?php

namespace App\Repositories\Accounting;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\Payment;

//TRAITS
use App\Traits\QueryTrait;
use App\Traits\PayPalTrait;

class ConciliationRepository
{
    use QueryTrait, PayPalTrait;

    public function PayPalPayments($request){
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $init = ( isset($request->startDate) ? $request->startDate." 00:00:00" : "" );
        $end = ( isset($request->endDate) ? $request->endDate." 23:59:59" : "" );

        $payments = $this->getPayPalPayments($init, $end);

        //26J96896MD306042F : NOS INDICA UN ERROR Failed to retrieve payment
        $data = $this->getPayment('72L93112TH167364Y');
        // $data = $this->getOrder('81074207LV178403W');

        // $info = array(
        //     "status" => $data->original['status'],
        //     "amount" => $data->original['amount']['value'],
        //     "gross_amount" => $data->original['seller_receivable_breakdown']['gross_amount']['value'],
        //     "paypal_fee" => $data->original['seller_receivable_breakdown']['paypal_fee']['value'],
        //     "net_amount" => $data->original['seller_receivable_breakdown']['net_amount']['value'],
        // );

        // $data2 = array();
        // if( !empty($payments) ){
        //     foreach ($payments as $key => $payment) {
        //         $dataPayment = $this->getPayment($payment->reference);
        //         // if( !isset($dataPayment->original['status']) ){
        //         //     dd($payment, $dataPayment);
        //         // }
        //         // && $dataPayment->original['amount']['value'] == $payment->total
        //         // !empty($dataPayment) && 
        //         if( isset($dataPayment->original['status']) && $dataPayment->original['status'] == "COMPLETED" ){
        //             $item = Payment::find($payment->id);
        //             $item->is_conciliated = 1;
        //             $item->total_fee = $dataPayment->original['seller_receivable_breakdown']['paypal_fee']['value'];
        //             $item->total_net = $dataPayment->original['seller_receivable_breakdown']['net_amount']['value'];
        //             $item->save();
        //             array_push($data2, $item);
        //         }
        //     }
        // }

        return response()->json([
            'data' => $data,
            // 'data2' => $data2,
            // 'info' => $info,
            'message' => ( empty($payments) ? "No hay pagos para conciliar" : "Se conciliaron los pagos correctamente" ),
            'status' => ( empty($payments) ? "info" : "success" )
        ], Response::HTTP_OK);
    }
}