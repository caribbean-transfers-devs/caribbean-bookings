<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationsController extends Controller
{
    
    public function index(Request $request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );

        $search = [];
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

        return view('operation.operations', compact('items','date','breadcrumbs'));                   
    }

    public function querySpam($search){
        return  DB::select("SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_one_pickup as filtered_date, 'arrival' as operation_type, sit.name as site_name, '' as messages,
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

}
