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
use App\Traits\PayPalTrait;
use App\Traits\StripeTrait;

class ConciliationRepository
{
    use QueryTrait, PayPalTrait, StripeTrait;

    public function __construct()
    {
        $this->initPayPal();
        $this->initStripe();        
    }

    //PAYPAL

    public function PayPalPayments($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $init = ( isset($request->startDate) ? $request->startDate." 00:00:00" : "" );
        $end = ( isset($request->endDate) ? $request->endDate." 23:59:59" : "" );
        $payments = $this->getPaymentsConciliation("PAYPAL", $init, $end);

        $data2 = array();
        if( !empty($payments) ){
            foreach ($payments as $key => $payment) {
                $charge = $this->getPaymentInfo($payment->reference);
                if( !empty($charge) && isset($charge->original['status']) && $charge->original['status'] == "COMPLETED" && $charge->original['amount']['value'] == $payment->total ){
                    $item = Payment::find($payment->id);
                    $item->is_conciliated = 1;
                    $item->date_conciliation = Carbon::parse($charge->original['create_time'])->format('Y-m-d H:i:s');
                    $item->total_fee = $charge->original['seller_receivable_breakdown']['paypal_fee']['value'];
                    $item->total_net = $charge->original['seller_receivable_breakdown']['net_amount']['value'];
                    $item->save();
                    array_push($data2, $item);
                }
            }
        }

        return response()->json([
            'data2' => $data2,
            'message' => ( empty($payments) ? "No hay pagos para conciliar" : "Se conciliaron los pagos correctamente" ),
            'status' => ( empty($payments) ? "info" : "success" )
        ], Response::HTTP_OK);
    }

    public function PayPalPaymenReference($request, $reference)
    {
        return $this->getPaymentInfo($reference);
    }

    public function PayPalPaymenOrder($request, $id)
    {
        return $this->getOrderInfo($id);
    }

    public function PayPalPaymenOrders($request)
    {
        return $this->getPayPalOrders("", "", 1, 10);
    }    

    public function conciliationPayPalPayment($request, $payment)
    {
        $charge = $this->getPaymentInfo($payment->reference);
        if( !empty($charge) && isset($charge->original['status']) && $charge->original['status'] == "COMPLETED" && $charge->original['amount']['value'] == $payment->total ){
            $payment->is_conciliated = $request->is_conciliated;
            $payment->date_conciliation = Carbon::parse($charge->original['create_time'])->format('Y-m-d H:i:s');
            $payment->total_fee = $charge->original['seller_receivable_breakdown']['paypal_fee']['value'];
            $payment->total_net = $charge->original['seller_receivable_breakdown']['net_amount']['value'];
            $payment->conciliation_comment = $request->conciliation_comment;
            $payment->save();
        }
    }

    //STRIPE 

    //ESTE METODO ES EL QUE UTIIZA EL BOT PARA CONCILIAR VARIOS PAGOS AL MISMO TIEMPO
    //ESTE METODO ES UTILIZADO PARA CONCILIAR VARIOS PAGOS AL MISMO TIEMPO MEDIANTE UN RANGO DE FECHAS ESPECIFICADOS
    public function StripePayments($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $succesPayment = array();
        $errorPayment = array();
        $init = ( isset($request->startDate) ? $request->startDate." 00:00:00" : "" );
        $end = ( isset($request->endDate) ? $request->endDate." 23:59:59" : "" );
        //CONSULTAMOS LOS PAGOS
        $payments = $this->getPaymentsConciliation("STRIPE", $init, $end);

        if (empty($payments)) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'code' => 'NOT_FOUND',
                    'message' => "No se encontro ningun pago para conciliar"
                ],
                'message' => "No se encontro ningun pago para conciliar"
            ], Response::HTTP_NOT_FOUND);
        }
        
        foreach ($payments as $key => $payment) {
            $reference = $payment->reference;
            $date_payment = Carbon::parse($payment->created_at)->format('Y-m-d');

            if( explode('_',$reference)[0] == "pi" ){
                $intent = ( $date_payment > "2024-12-17" ? $this->getPaymentIntentsV2($reference) : $this->getPaymentIntentsV1($reference) );
                $intentData = $intent->getData(true);
                if( $intentData['status'] == "succeeded" ){
                    $reference  = ( isset($intentData['latest_charge']) ? $intentData['latest_charge'] : "" );
                }
            }

            // $data[]['expand[]'] = "refunds";
            $data = ['expand' => ['refunds']];
            $charge = ( $date_payment > "2024-12-17" ? $this->getChargesV2($reference, $data) : $this->getChargesV1($reference, $data) );
            $chargeData = $charge->getData(true);
            
            if( $chargeData['status'] == "succeeded" ){
                $newPayment = Payment::find($payment->id);
                $balance = ( $date_payment > "2024-12-17" ? $this->getBalanceInfoV2($chargeData['balance_transaction']) : $this->getBalanceInfoV1($chargeData['balance_transaction']) );
                $balanceData = $balance->getData(true);
                if ( isset($balanceData['object']) && $balanceData['object'] == "balance_transaction" ) {
                    $chargeData['balance_transaction'] = $balanceData;
                }

                // if( isset($chargeData['balance_transaction']['status']) && $chargeData['balance_transaction']['status'] == "available" ){
                    // Corrección para el error count() - convertir a array si es necesario
                    $refundsData = $chargeData['refunds']['data'] ?? [];
                    if (is_object($refundsData)) {
                        $refundsData = (array)$refundsData;
                    }
                    $newPayment->is_refund = (!empty($refundsData) ? 1 : 0);
                    // $newPayment->is_refund = ( isset($chargeData['refunds']['data']) && count($chargeData['refunds']['data']) > 0 ? 1 : 0 );
                    // $newPayment->date_conciliation = Carbon::parse($chargeData['balance_transaction']['available_on'])->format('Y-m-d H:i:s');
                    // $newPayment->amount = ($chargeData['balance_transaction']['amount']/100);
                    // $newPayment->total_fee = ($chargeData['balance_transaction']['fee']/100);
                    // $newPayment->total_net = ($chargeData['balance_transaction']['net']/100);

                    $newPayment->date_conciliation = Carbon::parse($chargeData['created'] ?? null)->format('Y-m-d H:i:s') ?? null;
                    $newPayment->amount = ($chargeData['balance_transaction']['amount'] ?? 0) / 100;
                    $newPayment->total_fee = ($chargeData['balance_transaction']['fee'] ?? 0) / 100;
                    $newPayment->total_net = ($chargeData['balance_transaction']['net'] ?? 0) / 100;                    
                    
                    if( $newPayment->save() ){
                        array_push($succesPayment, $newPayment);
                    }

                    array_push($errorPayment, $newPayment);                    
                // }
            }

            array_push($errorPayment, $payment);
        }

        return response()->json([
            'status' => 'success',
            'message' => ( count($succesPayment) == count($payments) ? 'Se conciliaron todos los pagos' : 'Solo se pudieron conciliar '.count($succesPayment).' pagos' ),
            'data' => $succesPayment
        ], Response::HTTP_OK);
    }

    public function stripeChargesReference($request, $reference)
    {
        // Validar que la referencia no esté vacía
        if (empty($reference)) {
            return response()->json([
                'status'   => 'error',
                'message'  => 'Payment reference is required',
                'code'     => 400 // Bad Request
            ], 400);
        }

        if( !isset($request->notPayment) ){ 
            $payment = Payment::where('reference', $reference)->first();

            if (empty($payment)) {
                return response()->json([
                    'status'   => 'error',
                    'message'  => 'Payment not found',
                    'code'     => 404 // Not found
                ], 404);
            }
        }

        try {
            //Validaremos la fecha del pago para saber a que api consultar            
            $date_payment = Carbon::parse(( isset($payment->created_at) ? $payment->created_at : $request->created_at ))->format('Y-m-d');
            $v2 = ( $date_payment > "2024-12-17" ? true : false );
            // $data[]['expand[]'] = "refunds";
            $data = ['expand' => ['refunds']];
            $charge = ( $v2 ? $this->getChargesV2($reference, $data) : $this->getChargesV1($reference, $data) );
            $chargeData = $charge->getData(true);

            if (empty($chargeData) || ( isset($chargeData['status']) && $chargeData['status'] == "error" )) {
                return response()->json([
                    'status'   => 'error',
                    'message'  => ( isset($chargeData['status']) && $chargeData['status'] == "error" ? $chargeData['message'] : 'Payment not found' ),
                    'code'     => 404 // Not found
                ], 404);
            }

            $balance = ( $v2 ? $this->getBalanceInfoV2($chargeData['balance_transaction']) : $this->getBalanceInfoV1($chargeData['balance_transaction']) );
            $balanceData = $balance->getData(true);
            if ( isset($balanceData['object']) && $balanceData['object'] == "balance_transaction" ) {
                $chargeData['balance_transaction'] = $balanceData;
            }

            return response()->json([
                'status'   => 'success',
                'message'  => 'Payment ok',
                'code'     => 200, // ok
                'data'     => $chargeData
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'   => 'error',
                'message'  => $e->getMessage(),
                'code'     => 500 // Bad Request
            ], 500);
        }
    }
    
    public function stripePaymentIntentsReference($request, $reference)
    {
        // Validar que la referencia no esté vacía
        if (empty($reference)) {
            return response()->json([
                'status'   => 'error',
                'message'  => 'Payment reference is required',
                'code'     => 400 // Bad Request
            ], 400);
        }

        $payment = Payment::where('reference', $reference)->first();

        if (empty($payment)) {
            return response()->json([
                'status'   => 'error',
                'message'  => 'Payment not found',
                'code'     => 404 // Not found
            ], 404);
        }

        try {
            //Validaremos la fecha del pago para saber a que api consultar
            $date_payment = Carbon::parse($payment->created_at)->format('Y-m-d');
            $v2 = ( $date_payment > "2024-12-17" ? true : false );
            $intents = ( $v2 ? $this->getPaymentIntentsV2($reference) : $this->getPaymentIntentsV1($reference) );
            $intentsData = $intents->getData(true);

            if (empty($intentsData) || ( isset($intentsData['status']) && $intentsData['status'] == "error" )) {
                return response()->json([
                    'status'   => 'error',
                    'message'  => ( isset($intentsData['status']) && $intentsData['status'] == "error" ? $intentsData['message'] : 'Payment not found' ),
                    'code'     => 404 // Not found
                ], 404);
            }

            return response()->json([
                'status'   => 'success',
                'message'  => 'Payment ok',
                'code'     => 200, // ok
                'data'     => $intentsData
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'   => 'error',
                'message'  => $e->getMessage(),
                'code'     => 500 // Bad Request
            ], 500);
        }
    }

    //UTILIZADO EN EL PAYMENT REPOSITORI
    public function conciliationStripePayment($request, $payment)
    {
        // $date_payment = Carbon::parse($payment->created_at)->format('Y-m-d');
        // $charge = ( $date_payment > "2024-12-17" ? $this->getPaymentInfoV2($payment->reference) : $this->getPaymentInfoV1($payment->reference) );
        // if( !empty($charge) && isset($charge->original['status']) && $charge->original['status'] == "succeeded" ){
        //     $this->processStripe($charge->original, $payment, ( $date_payment > "2024-12-17" ? 1 : 0 ), $request);
        // }
    }

    /**
     * @param charge: son los datos obtenidos de paypal
     * @param payment: son los datos del pago, modelo de payment
     * @param status_data: nos ayudara a saber a que version de stripe consultar
     */
    public function processStripe($charge, $payment, $status_date, $request)
    {
        //DECALARACION DE VARIABLES
        $total = 0; $fee = 0; $net = 0;
        $refunds = ( isset($charge['refunds']['data']) ? $charge['refunds']['data'] : array() );
        $balanceTransaction = ( $status_date == 1 ? $this->getBalanceInfoV2($charge['balance_transaction']) : $this->getBalanceInfoV1($charge['balance_transaction']) );
        // Calcular el porcentaje cobrado        
        // Tarifa cobrada por Stripe // Total cobrado
        $percentage = ($balanceTransaction->original['fee'] / $balanceTransaction->original['amount']) * 100;  // Calcular el porcentaje

        //PROCESAMIENTO DEL PAGO
        if (count($refunds) > 0) {
            $total_refunds = 0;
            // echo "=========================================================================\n";
            //     echo "Monto Cobrado: " . ($charge['amount']/ 100) . "\n";
            //     echo ( $charge['amount_refunded'] != 0 ? "tiene reembolso" : "no tiene reembolso" ) . ( $status_date == 1 ? ' - V2 - ' : " - V1 - " ) . $charge['id'] . "\n";
            //     echo "Monto Reembolsado: " . ($charge['amount_refunded']/ 100) . "\n";
            //     echo "Tarifa de Stripe: " . ($balanceTransaction->original['fee']/ 100) . "\n";
            //     echo "Total a Recibir: " . ($balanceTransaction->original['net']/ 100) . "\n";
            //     echo "Porcentaje: " . $percentage . "\n"; 
            //     echo "Fecha de creación: " . Carbon::parse($charge['created'])->format('Y-m-d H:i:s') . "\n";
            //     echo "######################################################################################\n";
                foreach ($refunds as $refund) {
                    $total_refunds += $refund['amount'];
                    // echo "- Reembolso ID: " . $refund['id'] . ", Monto: " . ($refund['amount']/ 100) . "\n";

                    // $refundTransaction = ( $status_date == 1 ? $this->getBalanceInfoV2($refund['balance_transaction']) : $this->getBalanceInfoV1($refund['balance_transaction']) );
                    // echo "  Impacto del reembolso en balance: " . ($refundTransaction->original['amount']/ 100) . "\n";
                    // echo "  Tarifas asociadas al reembolso: " . ($refundTransaction->original['fee']/ 100) . "\n";
                    // echo "  Total neto del reembolso: " . ($refundTransaction->original['net']/ 100) . "\n";
                }
            //     echo "######################################################################################\n";
            //     // $total_discount = ( $charge['amount'] * ( $percentage / 100 ) );
            //     echo "Nuevo Monto Cobrado: " . ( ($charge['amount'] - $total_refunds) / 100 ) . "\n";
            //     echo "Nueva Tarifa de Stripe: " . round(( ( ($charge['amount'] - $total_refunds) / 100 ) * ( $percentage / 100 ) ),2) . "\n";
            //     echo "Nuevo Total a Recibir: " . round(( ( ($charge['amount'] - $total_refunds) / 100 ) - ( ( ($charge['amount'] - $total_refunds) / 100 ) * ( $percentage / 100 ) ) ),2) . "\n";
            // echo "=========================================================================\n";

            $total = ( ($charge['amount'] - $total_refunds) / 100 );
            $fee = round(( ( ($charge['amount'] - $total_refunds) / 100 ) * ( $percentage / 100 ) ),2);
            $net = round(( ( ($charge['amount'] - $total_refunds) / 100 ) - ( ( ($charge['amount'] - $total_refunds) / 100 ) * ( $percentage / 100 ) ) ),2);
        } else{
            // echo "=========================================================================\n";
            //     echo "Monto Cobrado: " . ($charge['amount']/ 100) . "\n";
            //     echo ( $charge['amount_refunded'] != 0 ? "tiene reembolso" : "no tiene reembolso" ) . ( $status_date == 1 ? ' - V2 - ' : " - V1 - " ) . $charge['id'] . "\n";
            //     echo "Monto Reembolsado: " . ($charge['amount_refunded']/ 100) . "\n";
            //     echo "Tarifa de Stripe: " . ($balanceTransaction->original['fee']/ 100) . "\n";
            //     echo "Total a Recibir: " . ($balanceTransaction->original['net']/ 100) . "\n";
            //     echo "Porcentaje: " . $percentage . "\n";
            //     echo "Fecha de creación: " . Carbon::parse($charge['created'])->format('Y-m-d H:i:s') . "\n";
            // echo "=========================================================================\n";

            $total = ($charge['amount']/ 100);
            $fee = ($balanceTransaction->original['fee']/ 100);
            $net = ($balanceTransaction->original['net']/ 100);
        }

        $payment->object = json_encode($charge);
        $payment->is_conciliated = 1;
        $payment->is_refund = ( count($refunds) > 0 ? 1 : 0 );
        $payment->date_conciliation = Carbon::parse($charge['created'])->format('Y-m-d H:i:s');
        $payment->total_fee = $fee;
        $payment->total_net = $net;
        $payment->conciliation_comment = ( isset($request->conciliation_comment) ? $request->conciliation_comment : "" );
        $payment->save();
    }
}