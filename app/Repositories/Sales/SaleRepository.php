<?php

namespace App\Repositories\Sales;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\Reservation;
use App\Models\Sale;

//TRAIS
use App\Traits\FollowUpTrait;

class SaleRepository
{
    use FollowUpTrait;
    
    public function store($request)
    {
        try {
            DB::beginTransaction();

            $sale = new Sale();
            $sale->reservation_id = $request->reservation_id;
            $sale->sale_type_id = $request->sale_type_id;
            $sale->description = $request->description;
            $sale->quantity = $request->quantity;
            $sale->total = $request->total;

            $sale->created_at = date('Y-m-d H:m:s');
            $sale->updated_at = date('Y-m-d H:m:s');

            $sale->save();

            $this->create_followUps($request->reservation_id, 'El usuario: '.auth()->user()->name.', agrego una venta tipo: '.$request->sale_type_id.', por un monto de: '.$request->total, 'HISTORY', 'CREATE_SALE');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'La venta se creo correctamente',                
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($request,$sale){
        try {
            DB::beginTransaction();

            $this->create_followUps($request->reservation_id, 'El usuario: '.auth()->user()->name.', actualizo la venta con ID: '.$sale->id.' de ( tipo: '.$sale->sale_type_id.', por un monto de: '.$sale->total.' ) a ( tipo: '.$request->sale_type_id.', por un monto de: '.$request->total.' )', 'HISTORY', 'UPDATE_SALE');

            $sale->reservation_id = $request->reservation_id;
            $sale->sale_type_id = $request->sale_type_id;
            $sale->description = $request->description;
            $sale->quantity = $request->quantity;
            $sale->total = $request->total;
            $sale->updated_at = date('Y-m-d H:m:s');
            // $sale->call_center_agent_id = $request->call_center_agent_id;
            $sale->save();

            DB::commit();
            
            // Sale updated successfully
            return response()->json([
                'status' => 'success',
                'message' => 'La venta se actualizo correctamente',                
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la venta, contacte a soporte',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($request,$sale)
    {
        try {
            DB::beginTransaction();

            $reservation = Reservation::find($sale->reservation_id);
            $sale->delete();

            $this->create_followUps($sale->reservation_id, 'El usuario: '.auth()->user()->name.', elimino la venta con ID: '.$sale->id.', por un monto de: '.$sale->total, 'HISTORY', 'DELETE_SALE');

            DB::commit();

            // Sale deleted successfully
            return response()->json([
                'status' => 'success',
                'message' => 'La venta se elimino correctamente'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar la venta, contacte a soporte'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}