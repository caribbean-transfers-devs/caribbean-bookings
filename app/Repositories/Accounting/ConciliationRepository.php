<?php

namespace App\Repositories\Accounting;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS
use App\Models\Payment;

//TRAITS
use App\Traits\QueryTrait;
// use App\Traits\PayPalTrait;
use App\Traits\StripeTrait;

class ConciliationRepository
{
    use QueryTrait, StripeTrait;

    // public function __construct()
    // {
    //     // $this->initStripe();
    //     // $this->initPayPal();
    // }    

    public function PayPalPayments($request){
        // ini_set('memory_limit', '-1'); // Sin límite
        // set_time_limit(120); // Aumenta el límite a 60 segundos

        // $init = ( isset($request->startDate) ? $request->startDate." 00:00:00" : "" );
        // $end = ( isset($request->endDate) ? $request->endDate." 23:59:59" : "" );
        // $payments = $this->getPayPalPayments($init, $end);

        // // $info = array(
        // //     "status" => $data->original['status'],
        // //     "amount" => $data->original['amount']['value'],
        // //     "gross_amount" => $data->original['seller_receivable_breakdown']['gross_amount']['value'],
        // //     "paypal_fee" => $data->original['seller_receivable_breakdown']['paypal_fee']['value'],
        // //     "net_amount" => $data->original['seller_receivable_breakdown']['net_amount']['value'],
        // // );

        // $data2 = array();
        // if( !empty($payments) ){
        //     foreach ($payments as $key => $payment) {
        //         $dataPayment = $this->getPayment($payment->reference);
        //         if( !empty($dataPayment) && isset($dataPayment->original['status']) && $dataPayment->original['status'] == "COMPLETED" && $dataPayment->original['amount']['value'] == $payment->total ){
        //             $item = Payment::find($payment->id);
        //             $item->is_conciliated = 1;
        //             $item->date_conciliation = Carbon::parse($dataPayment->original['create_time'])->format('Y-m-d H:i:s');
        //             $item->total_fee = $dataPayment->original['seller_receivable_breakdown']['paypal_fee']['value'];
        //             $item->total_net = $dataPayment->original['seller_receivable_breakdown']['net_amount']['value'];
        //             $item->save();
        //             array_push($data2, $item);
        //         }
        //     }
        // }

        // return response()->json([
        //     // 'payments' => $payments,
        //     // 'count' => count($payments),
        //     // 'data' => $data,
        //     'data2' => $data2,
        //     // 'info' => $info,
        //     'message' => ( empty($payments) ? "No hay pagos para conciliar" : "Se conciliaron los pagos correctamente" ),
        //     'status' => ( empty($payments) ? "info" : "success" )
        // ], Response::HTTP_OK);
    }

    public function StripePayments(){
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = $this->getPaymentInfo('ch_3QX4izAUOjRsxU4D3oCgAG6L');
        // $data = array();

        return $data;
    }
}