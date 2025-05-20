<?php

namespace App\Repositories\Sales;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\Reservation;
use App\Models\Sale;
use App\Models\SalesType;

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
            // $sale->quantity = $request->quantity;
            $sale->total = $request->total;
            $sale->call_center_agent_id = auth()->user()->id;
            $sale->save();

            // Obtener tipo de venta (validar existencia)
            $saleType = SalesType::find($request->sale_type_id);
            $saleDescription = $saleType ? $saleType->name : 'Reembolso';

            // Registrar seguimientos
            $this->create_followUps(
                $request->reservation_id,
                "El usuario: " . auth()->user()->name . " agregó una venta tipo: (" . strtoupper($saleDescription) . "), por un monto de: (" . $request->total . ")",
                'HISTORY', 
                'CREATE_SALE'
            );

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

            $saleTypeIdOld = $sale->sale_type_id;
            $saleTotalOld = $sale->total;

            $sale->reservation_id = $request->reservation_id;
            $sale->sale_type_id = $request->sale_type_id;
            $sale->description = $request->description;
            // $sale->quantity = $request->quantity;
            $sale->total = $request->total;
            $sale->updated_at = date('Y-m-d H:m:s');            
            $sale->save();

            // Obtener tipo de venta (validar existencia)
            $saleTypeOld = SalesType::find($saleTypeIdOld);
            $saleDescriptionOld = $saleTypeOld ? $saleTypeOld->name : 'Reembolso';

            $saleType = SalesType::find($request->sale_type_id);
            $saleDescription = $saleType ? $saleType->name : 'Reembolso';

            // Registrar seguimientos
            $this->create_followUps(
                $request->reservation_id, 
                'El usuario: '.auth()->user()->name.', actualizo la venta con ID: '.$sale->id.' de ( tipo: '.strtoupper($saleDescriptionOld).', por un monto de: '.$saleTotalOld.' ) a ( tipo: '.strtoupper($saleDescription).', por un monto de: '.$request->total.' )', 
                'HISTORY', 
                'UPDATE_SALE'
            );

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