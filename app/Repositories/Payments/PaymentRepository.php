<?php

namespace App\Repositories\Payments;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationFollowUp;
use App\Models\SalesType;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\Reservations\ReservationsRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//TRAIS
use App\Traits\FollowUpTrait;
use App\Traits\PayPalTrait;

class PaymentRepository
{
    use PayPalTrait, FollowUpTrait;

    public function store($request)
    {
        try {
            DB::beginTransaction();
            
            $payment = new Payment();
            $payment->reservation_id = $request->reservation_id;
            $payment->description = 'Panel';
            $payment->total = $request->total;
            $payment->status = 1;
            $payment->exchange_rate = $request->exchange_rate;
            $payment->operation = $request->operation;
            $payment->payment_method = $request->payment_method;
            $payment->currency = $request->currency;
            $payment->reservation_id = $request->reservation_id;
            $payment->reference = $request->reference;

            $payment->created_at = date('Y-m-d H:m:s');
            $payment->updated_at = date('Y-m-d H:m:s');

            ( isset($request->is_conciliated) ? $payment->is_conciliated = $request->is_conciliated : "" );
            ( isset($request->conciliation_comment) ? $payment->conciliation_comment = $request->conciliation_comment : "" );

            $payment->user_id = auth()->user()->id;
            $payment->save();

            $this->create_followUps($request->reservation_id, 'El usuario: '.auth()->user()->name.', agrego un pago tipo: '.$request->payment_method.', por un monto de: '.$request->total.' '.$request->currency, 'HISTORY', 'CREATE_PAYMENT');

            DB::commit();

            // Payment created successfully
            return response()->json([
                'status' => 'success',
                'message' => 'El pago se creo correctamente',
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el pago, contacte a soporte',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($request,$payment)
    {
        try {
            DB::beginTransaction();

            //VALIDAMOS SI NO ESTA CONCILIADNO EL PAGO, PERO ALGUN DATO ES DIFERENTE DEL ORIGINAL GUARDA UN LOG
            if( !isset($request->is_conciliated) && ( ( $payment->payment_method != $request->payment_method ) || ( $payment->total != $request->total ) || ( $payment->currency != $request->currency ) ) ){
                $this->create_followUps($request->reservation_id, 'El usuario: '.auth()->user()->name.', actualizo el pago con ID: '.$payment->id.' de ( tipo: '.$payment->payment_method.', por un monto de: '.$payment->total.' '.$payment->currency.' ) a ( tipo: '.$request->payment_method.', por un monto de: '.$request->total.' '.$request->currency.' )', 'HISTORY', 'UPDATE_PAYMENT');
            }

            //
            if( isset($request->is_conciliated) && ( ( $payment->payment_method != $request->payment_method ) || ( $payment->total != $request->total ) || ( $payment->currency != $request->currency ) ) ){
                $this->create_followUps($request->reservation_id, 'El usuario: '.auth()->user()->name.', '.( $request->is_conciliated == 0 ? "desconcilio" : "concilio" ).' el pago con ID: '.$payment->id.' de ( tipo: '.$payment->payment_method.', por un monto de: '.$payment->total.' '.$payment->currency.' ) a ( tipo: '.$request->payment_method.', por un monto de: '.$request->total.' '.$request->currency.' )', 'HISTORY', 'PAYMENT_CONCILIATION');
            }

            //
            if( isset($request->is_conciliated) && $request->is_conciliated == 1 ){
                $this->create_followUps($request->reservation_id, 'El usuario: '.auth()->user()->name.', '.( $request->is_conciliated == 0 ? "desconcilio" : "concilio" ).' el pago con ID: '.$payment->id.' de ( tipo: '.$payment->payment_method.', por un monto de: '.$payment->total.' '.$payment->currency.' )', 'HISTORY', 'PAYMENT_CONCILIATION');
            }

            $payment->reservation_id = $request->reservation_id;
            $payment->description = 'Panel';
            $payment->total = $request->total;
            $payment->status = 1;
            $payment->operation = $request->operation;
            $payment->exchange_rate = $request->exchange_rate;
            $payment->payment_method = $request->payment_method;
            $payment->currency = $request->currency;
            $payment->reservation_id = $request->reservation_id;
            $payment->reference = $request->reference;

            if( isset($request->is_conciliated) && $request->is_conciliated == 1 ){
                $data_bank = ( $request->payment_method == "PAYPAL" ? $this->getPayment($request->reference) : array() );
                $payment->is_conciliated = $request->is_conciliated;

                ( $request->payment_method == "PAYPAL" && isset($data_bank->original['status']) && $data_bank->original['status'] ? $payment->date_conciliation = Carbon::parse($data_bank->original['create_time'])->format('Y-m-d H:i:s') : 0 );

                $payment->total_fee = ( $request->payment_method == "PAYPAL" ? ( isset($data_bank->original['status']) && $data_bank->original['status'] ? $data_bank->original['seller_receivable_breakdown']['paypal_fee']['value'] : 0 ) : 0 );
                $payment->total_net = ( $request->payment_method == "PAYPAL" ? ( isset($data_bank->original['status']) && $data_bank->original['status'] ? $data_bank->original['seller_receivable_breakdown']['net_amount']['value'] : 0 ) : $request->total );
                $payment->conciliation_comment = $request->conciliation_comment;
            };

            $payment->updated_at = date('Y-m-d H:m:s');

            $payment->save();            

            DB::commit();

            // Payment updated successfully
            return response()->json([
                'status' => 'success',
                'message' => 'El pago se actualizo correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el pago, contacte a soporte',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($request,$payment)
    {
        try {
            DB::beginTransaction();
            //SEND A FOLLOW UP SAYING IT WAS DELETED
            $reservation = Reservation::find($payment->reservation_id);
            // $repo = new ReservationsRepository();
            // $repo->create_followUps($reservation->id, 'Pago eliminado por '.auth()->user()->name, 'HISTORY', 'ELIMINACIÓN');
            $payment->delete();

            $this->create_followUps($payment->reservation_id, 'El usuario: '.auth()->user()->name.', elimino el pago con ID: '.$payment->id.', por un monto de: '.$payment->total.' '.$payment->currency, 'HISTORY', 'DELETE_PAYMENT');

            DB::commit();

            // Payment deleted successfully
            return response()->json([
                'status' => 'success',
                'message' => 'El pago se elimino correctamente'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el pago, contacte a soporte'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}