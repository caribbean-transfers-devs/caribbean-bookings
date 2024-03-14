<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Reservation;
use App\Models\ReservationFollowUp;

class CashRepository
{    
    public function index($request){

        $date = date("Y-m-d");
        if(isset( $request->date )):
            $date = $request->date;
        endif;

        $search['init_date'] = $date." 00:00:00";
        $search['end_date'] = $date." 23:59:59";

        $items = DB::select("SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_one_pickup as filtered_date, 'arrival' as operation_type, sit.name as site_name, '' as messages,
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
                                    AND rez.is_cancelled = 0 AND rez.pay_at_arrival = 1
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                    UNION 
                                    SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_two_pickup as filtered_date, 'departure' as operation_type, sit.name as site_name, '' as messages,
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
                                    AND rez.is_cancelled = 0 AND rez.pay_at_arrival = 1
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                        "init_date_one" => $search['init_date'],
                                        "init_date_two" => $search['end_date'],
                                        "init_date_three" => $search['init_date'],
                                        "init_date_four" => $search['end_date'],
                                    ]);

        return view('reports.cash', compact('items','date'));
    }

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