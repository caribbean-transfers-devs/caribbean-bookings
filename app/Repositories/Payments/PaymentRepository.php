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

class PaymentRepository
{
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
            $payment->save();

            DB::commit();

            return response()->json([
                'message' => 'Payment created successfully',
                'success' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error, contacte a soporte',
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($request,$payment){
        try {
            DB::beginTransaction();

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
            $payment->save();

            DB::commit();

            return response()->json([
                'message' => 'Payment updated successfully',
                'success' => true
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error, contacte a soporte',
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($request,$payment)
    {
        try {
            DB::beginTransaction();
            //SEND A FOLLOW UP SAYING IT WAS DELETED
            $reservation = Reservation::find($payment->reservation_id);
            $repo = new ReservationsRepository();
            $repo->create_followUps($reservation->id, 'Pago eliminado por '.auth()->user()->name, 'HISTORY', 'ELIMINACIÓN');
            $payment->delete();
            DB::commit();
            return response()->json(['message' => 'Payment deleted successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Hubo un error, contacte a soporte'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}