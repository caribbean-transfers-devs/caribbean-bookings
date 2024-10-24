<?php

namespace App\Repositories\Operation;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

class ConfirmationRepository
{

    public function updateUnlock($request){
        try {
            DB::beginTransaction();
            
            $item = ReservationsItem::find($request->id);
            if($request->type == "arrival"):
                $item->op_one_operation_close = 0;
            endif;
            if($request->type == "departure"):
                $item->op_two_operation_close = 0;
            endif;
            $item->save();            

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Bloqueo del servicio: ".$request->id.", fue actualizado de 1 a 0, por el usuario: ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $request->rez_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
}