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
        $blockSize = 100; // Tamaño del bloque (ajusta este valor según tu necesidad)
        $offset = 0;
        $data2 = [];

        do {
            // Obtiene un bloque de pagos
            $payments = $this->getPayPalPayments($offset, $blockSize);
    
            if (empty($payments)) {
                break;
            }
                
            foreach ($payments as $payment) {                
                $dataPayment = $this->getPayment($payment->reference);
                if( !empty($dataPayment) ){
                    array_push($data2, $dataPayment);
                }                
    
                // Procesa y concilia el pago si corresponde
                // if ($dataPayment->status == "COMPLETED" && $dataPayment->amount->value == $payment->total) {
                //     $item = Payment::find($payment->id);
                //     $item->is_conciliated = 1;
                //     $item->save();
                // }
            }
    
            // Aumenta el offset para el próximo bloque
            $offset += $blockSize;
    
            // Pausa breve para evitar sobrecargar la API de PayPal y el servidor
            usleep(500000); // 0.5 segundos
        } while (count($payments) === $blockSize); // Continúa si el bloque está lleno
    
        return response()->json([
            'data' => $data2,
            'messages' => empty($data2) ? "No hay pagos para conciliar" : "Se conciliaron los pagos correctamente",
            'success' => true
        ], Response::HTTP_OK);        

        // $payments = $this->getPayPalPayments();
        // // $data = $this->getPayment('9RK27226158346400');
        // $data2 = array();
        // if( !empty($payments) ){
        //     foreach ($payments as $key => $payment) {
        //         $dataPayment = $this->getPayment($payment->reference);
        //         array_push($data2, $dataPayment);
        //         // if( $dataPayment->status == "COMPLETED" && $dataPayment->amount->value == $payment->total ){
        //         //     $item = Payment::find($payment->id);
        //         //     $item->is_conciliated = 1;
        //         //     $item->save();
        //         // }
        //     }
        // }

        // return response()->json([
        //     'data' => $data2,
        //     'messages' => ( empty($payments) ? "No hay pagos para conciliar" : "Se conciliaron los pagos correctamente" ),
        //     'success' => true
        // ], Response::HTTP_OK);
    }
}