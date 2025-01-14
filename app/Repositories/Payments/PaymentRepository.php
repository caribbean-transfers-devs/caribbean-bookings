<?php

namespace App\Repositories\Payments;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationFollowUp;
use App\Models\SalesType;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//REPOSITORY
use App\Repositories\Reservations\ReservationsRepository;
use App\Repositories\Accounting\ConciliationRepository;

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

            //AQUI REGISTRAMOS EL PAGO, PARA SABER SI UN AGENTE O SUPERVISOR DE CALLCENTER LE ESTA DANDO SEGUIMIENTO, LO HACEMOS MEDIANTE EL ROL
            // 3 Gerente - Call Center
            // 4 Agente - Call Center
            $roles = session()->get('roles');
            if( isset($request->type_site) && !empty($request->type_site) && ( in_array(3, $roles['roles']) || in_array(4, $roles['roles']) ) ){
                $reservation = Reservation::find($request->reservation_id);
                if( $request->type_site == "CALLCENTER" ){
                    $reservation->agent_id_after_sales = auth()->user()->id;
                }else{
                    $reservation->agent_id_pull_sales = auth()->user()->id;
                }
                $reservation->type_after_sales = ( $request->platform == "Bookign" ? "PENDING" : "SPAM" );
                $reservation->save();
            }

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
                // 'message' => $e->getMessage()
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
                $conciliation = new ConciliationRepository();
                if( $request->payment_method == "PAYPAL" ){
                    $conciliation->conciliationPayPalPayment($request, $payment);
                }

                if( $request->payment_method == "STRIPE" || $request->payment_method == "CARD" ){
                    $conciliation->conciliationStripePayment($request, $payment);
                }

                if( $request->payment_method != "PAYPAL" && $request->payment_method != "STRIPE" && $request->payment_method != "CARD" ){
                    $payment->is_conciliated = $request->is_conciliated;
                    $payment->conciliation_comment = $request->conciliation_comment;
                }
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