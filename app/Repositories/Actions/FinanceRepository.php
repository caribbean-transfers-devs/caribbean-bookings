<?php

namespace App\Repositories\Actions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS
use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\ReservationsRefund;
use App\Models\Sale;
use App\Models\SalesType;
use App\Models\Payment;

//TRAITS
use App\Traits\FollowUpTrait;
use App\Traits\PayPalTrait;

class FinanceRepository
{
    use FollowUpTrait;

    /**
     * NOS AYUDA A PODER AGREGAR UN PAGO TIPO REEMBOLSO
     * @param request :la información recibida en la solicitud
    */
    public function addPaymentRefund($request)
    {
        try {
            DB::beginTransaction();

            // Asegurarse de que el monto sea negativo si es un reembolso
            $total = ($request->category === 'REFUND') ? -abs($request->total) : abs($request->total);
            // Validar si reservation_refund_id está presente
            $reservationRefundId = $request->filled('reservation_refund_id') ? $request->reservation_refund_id : null;            
            
            // Crear pago
            $payment = new Payment();
            $payment->description = 'Panel';
            $payment->total = $total;
            $payment->exchange_rate = $request->exchange_rate;
            $payment->status = 1;
            $payment->operation = $request->operation;
            $payment->payment_method = $request->payment_method;
            $payment->currency = $request->currency;
            $payment->reservation_id = $request->reservation_id;
            $payment->reference = $request->reference;
            $payment->reservation_refund_id = $reservationRefundId;
            $payment->user_id = auth()->user()->id;
            $payment->category = $request->category;

            if( $payment->save() ){
                if( $request->category === 'REFUND' ){

                    // Obtener tipo de venta (validar existencia)
                    $saleType = SalesType::find(6);
                    $saleDescription = $saleType ? $saleType->name : 'Reembolso';

                    // Crear venta
                    $sale = new Sale();
                    $sale->reservation_id = $request->reservation_id;
                    $sale->sale_type_id = 6;
                    $sale->description = $saleDescription;
                    $sale->quantity = 1;
                    $sale->total = ($request->operation === "division" ? ($total / $request->exchange_rate) : $total);
                    $sale->save();

                    // Registrar seguimientos
                    $this->create_followUps(
                        $request->reservation_id,
                        "El usuario " . auth()->user()->name . " agregó una venta tipo: " . strtoupper($saleDescription) . 
                        ", por un monto de: $total",
                        'HISTORY',
                        'CREATE_SALE'
                    );
                    
                }
                
                $this->create_followUps(
                    $request->reservation_id,
                    "El usuario " . auth()->user()->name . " agregó un pago tipo: " . $request->payment_method . 
                    ", por un monto de: $total " . $request->currency . ", Categoría: " . $request->category,
                    'HISTORY',
                    'CREATE_PAYMENT'
                );

                // Actualizar estado de reembolso si aplica
                if ($reservationRefundId) {                    
                    $refund = ReservationsRefund::find($reservationRefundId);
                    if ($refund) {
                        $refund->update([
                            'status' => "REFUND_COMPLETED",
                            'end_at' => now(),
                            'link_refund' => $request->link_refund
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Se agrego el pago correctamente',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * NOS AYUDA A OBTENER LOS LOG DE LA RESERVACIÓN
     * @param request :la información recibida en la solicitud
    */
    public function getLogReservation($request)
    {
        try {
            //code...
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}