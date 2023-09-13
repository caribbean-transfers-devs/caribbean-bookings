<?php

namespace App\Repositories\Sales;

use App\Models\Reservation;
use App\Models\ReservationFollowUp;
use App\Models\Sale;
use App\Models\SalesType;
use App\Models\User;
use App\Models\UserRole;
use App\Repositories\Reservations\ReservationsRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SaleRepository
{
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
            $sale->call_center_agent_id = $request->call_center_agent_id;
            $sale->save();

            DB::commit();

            return response()->json([
                'message' => 'Sale created successfully',
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

    public function update($request,$sale){
        try {
            DB::beginTransaction();

            $sale->reservation_id = $request->reservation_id;
            $sale->sale_type_id = $request->sale_type_id;
            $sale->description = $request->description;
            $sale->quantity = $request->quantity;
            $sale->total = $request->total;
            $sale->call_center_agent_id = $request->call_center_agent_id;
            $sale->save();

            DB::commit();

            return response()->json([
                'message' => 'Sale updated successfully',
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

    public function destroy($request,$sale)
    {
        try {
            DB::beginTransaction();
            //SEND A FOLLOW UP SAYING IT WAS DELETED
            $reservation = Reservation::find($sale->reservation_id);
            $repo = new ReservationsRepository();
            $repo->create_followUps($reservation->id, 'Venta eliminada por '.auth()->user()->name, 'HISTORY', 'ELIMINACIÓN');
            $sale->delete();
            DB::commit();
            return response()->json(['message' => 'Sale deleted successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Hubo un error, contacte a soporte'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}