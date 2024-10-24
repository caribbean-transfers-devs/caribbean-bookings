<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\ReservationFollowUp;

class CashRepository
{    
    public function update($request){
        try {
            DB::beginTransaction();
            
            $item = Reservation::find($request->id);
            $item->payment_reconciled = $request->status;
            $item->save();            

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "El pago en efectivo se ha conciliado como (".(( $request->status == 1 )? 'Positivo' : 'Negativo' ).") por ". auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $request->id;
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}