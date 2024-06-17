<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Enterprise;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

// use App\Events\ValueUpdated;

class OperationsController extends Controller
{
    
    public function index(Request $request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );

        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";

        $items = $this->querySpam($search);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Gestión de envío de operaciones",
                "active" => true
            ),
        );

        $vehicles = Vehicle::All();
        $drivers = Driver::All();

        return view('operation.operations', compact('items','date','breadcrumbs','vehicles','drivers'));
    }

    public function querySpam($search){
        return  DB::select("SELECT 
                            rez.id as reservation_id, 
                            rez.*, 
                            it.*, 
                            serv.name as service_name, 
                            it.op_one_pickup as filtered_date, 
                            'arrival' as operation_type,
                            sit.name as site_name,
                            'TYPE_ONE' as op_type,
                            '' as messages,
                                               COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                                               CASE
                                                   WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                                   ELSE 'CONFIRMADO'
                                               END AS status,
                                               zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                                               zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                                               CASE 
                                                   WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                                   WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                                   WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                               END AS final_service_type
                                   FROM reservations_items as it
                                   INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                   INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                   INNER JOIN sites as sit ON sit.id = rez.site_id
                                   INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                   INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                   LEFT JOIN (
                                       SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                       FROM sales
                                       WHERE deleted_at IS NULL
                                       GROUP BY reservation_id
                                   ) as s ON s.reservation_id = rez.id
                                   LEFT JOIN (
                                       SELECT reservation_id,
                                       ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                                                               WHEN operation = 'division' THEN total / exchange_rate
                                                                               ELSE total END), 2) AS total_payments,
                                       GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                       FROM payments
                                       GROUP BY reservation_id
                                   ) as p ON p.reservation_id = rez.id
                                   WHERE it.op_one_pickup BETWEEN :init_date_one AND :init_date_two
                                   AND rez.is_cancelled = 0
                                   AND rez.is_duplicated = 0
                                   GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                   UNION 
                                   SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_two_pickup as filtered_date, 'departure' as operation_type, sit.name as site_name, 'TYPE_TWO' as op_type, '' as messages,
                                               COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                                               CASE
                                                       WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                                       ELSE 'CONFIRMADO'
                                               END AS status,
                                               zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                                               zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                                               CASE
                                                   WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1  THEN 'DEPARTURE'
                                                   WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                               END AS final_service_type
                                   FROM reservations_items as it
                                   INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                   INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                   INNER JOIN sites as sit ON sit.id = rez.site_id
                                   INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                   INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                   LEFT JOIN (
                                           SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales                                            
                                           FROM sales
                                           WHERE deleted_at IS NULL
                                           GROUP BY reservation_id
                                   ) as s ON s.reservation_id = rez.id
                                   LEFT JOIN (
                                           SELECT reservation_id,
                                           ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                                                                   WHEN operation = 'division' THEN total / exchange_rate
                                                                                   ELSE total END), 2) AS total_payments,
                                           GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                           FROM payments
                                           GROUP BY reservation_id
                                   ) as p ON p.reservation_id = rez.id
                                   WHERE it.op_two_pickup BETWEEN :init_date_three AND :init_date_four
                                   AND rez.is_cancelled = 0
                                   AND rez.is_duplicated = 0
                                   AND it.is_round_trip = 0
                                   GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                       "init_date_one" => $search['init'],
                                       "init_date_two" => $search['end'],
                                       "init_date_three" => $search['init'],
                                       "init_date_four" => $search['end'],
                                   ]);
    }

    public function setVehicle(Request $request){
        try {
            DB::beginTransaction();
            $item = ReservationsItem::find($request->reservation_item_id);
            $item->vehicle_id = $request->vehicle_id;
            $item->save();

            // Emitir evento
            // event(new ValueUpdated($item));

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], 500);
        }
    }

    public function setDriver(Request $request){
        try {
            DB::beginTransaction();
            $item = ReservationsItem::find($request->reservation_item_id);
            $item->driver_id = $request->driver_id;
            $item->save();

            // Emitir evento
            // event(new ValueUpdated($item));

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], 500);
        }
    }

    public function statusOperationUpdate(Request $request){
        try {
            DB::beginTransaction();            
            $item = ReservationsItem::find($request->item_id);
            if($request->type == "arrival"):
                $item->op_one_status_operation = $request->status;
            endif;
            if($request->type == "departure"):
                $item->op_two_status_operation = $request->status;
            endif;
            $item->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de estatus de operación (".$request->type.") por ".$request->status;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], 500);
        }
    }     

    public function statusUpdate(Request $request){
        try {
            DB::beginTransaction();            
            $item = ReservationsItem::find($request->item_id);
            if($request->type == "arrival"):
                $item->op_one_status = $request->status;
            endif;
            if($request->type == "departure"):
                $item->op_two_status = $request->status;
            endif;
            $item->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de estatus de reservación (".$request->type.") por ".$request->status;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();           

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], 500);
        }
    }

    public function addComment(Request $request){
        try {
            DB::beginTransaction();            
            $item = ReservationsItem::find($request->code);
            if($request->type == "arrival"):
                $item->op_one_comments = $request->comment;
            endif;
            if($request->type == "departure"):
                $item->op_two_comments = $request->comment;
            endif;
            $item->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se agrego un comentario al servicio: ".$request->code;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], 500);
        }
    }    

}
