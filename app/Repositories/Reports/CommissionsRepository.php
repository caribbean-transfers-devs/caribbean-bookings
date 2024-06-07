<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

class CommissionsRepository
{
    
    public function index($request){       
        $search = [
            "init" => date("Y-m-d")." 00:00:00",
            "end" => date("Y-m-d")." 23:59:59",
        ];

        if(isset( $request->date )):
                $new_date = explode(" - ", $request->date);
                $search['init'] = $new_date[0]." 00:00:00";
                $search['end'] = $new_date[1]." 23:59:59";
        endif;

        $items = [];
        
        $paid = DB::select("SELECT rez.id as reservation_id, CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name, rez.currency, rez.language, rez.is_cancelled, rez.is_commissionable, rez.pay_at_arrival, rez.created_at,
                                        it.code, it.is_round_trip, it.op_one_pickup, it.op_one_status, it.op_two_pickup, it.op_two_status, it.op_one_pickup as filtered_date, it.passengers,
                                        serv.name as service_name, 'arrival' as operation_type, 
                                        sit.name as site_name, '' as messages,
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
                                        END AS final_service_type,
                                        it_counter.quantity, IFNULL(p.payment_type_name, 'CASH') as payment_type_name, emp.employee
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
                                        LEFT JOIN (
                                                        SELECT  it.reservation_id, count(it.code) as quantity
                                                        FROM reservations_items as it
                                                        WHERE it.op_one_status NOT IN ('CANCELLED') AND it.op_two_status NOT IN ('CANCELLED')
                                                        GROUP BY it.reservation_id
                                        ) as it_counter ON it_counter.reservation_id = it.reservation_id
                                        LEFT JOIN (
						SELECT s.reservation_id, GROUP_CONCAT(DISTINCT us.name ORDER BY us.name ASC SEPARATOR ',') AS employee
						FROM sales AS s
						INNER JOIN users as us ON us.id = s.call_center_agent_id
						WHERE s.deleted_at IS NULL
						GROUP BY s.reservation_id
				        ) as emp ON emp.reservation_id = rez.id
        WHERE rez.created_at BETWEEN :init_date_one AND :init_date_two
        AND rez.is_commissionable = 1 AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.pay_at_arrival = 0
        GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id, it_counter.quantity, p.payment_type_name, emp.employee", [
                                        "init_date_one" => $search['init'],
                                        "init_date_two" => $search['end'],
                                    ]);

        $cash = DB::select("SELECT rez.id as reservation_id, CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name, rez.currency, rez.language, rez.is_cancelled, rez.is_commissionable, rez.pay_at_arrival, rez.created_at,
                                        it.code, it.is_round_trip, it.op_one_pickup, it.op_one_status, it.op_two_pickup, it.op_two_status, it.op_one_pickup as filtered_date, it.passengers,
                                        serv.name as service_name, 'arrival' as operation_type, 
                                        sit.name as site_name, '' as messages,
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
                                        END AS final_service_type,
                                        it_counter.quantity, IFNULL(p.payment_type_name, 'CASH') as payment_type_name, emp.employee
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
                                        LEFT JOIN (
                                                        SELECT  it.reservation_id, count(it.code) as quantity
                                                        FROM reservations_items as it
                                                        WHERE it.op_one_status NOT IN ('CANCELLED') AND it.op_two_status NOT IN ('CANCELLED')
                                                        GROUP BY it.reservation_id
                                        ) as it_counter ON it_counter.reservation_id = it.reservation_id
                                        LEFT JOIN (
						SELECT s.reservation_id, GROUP_CONCAT(DISTINCT us.name ORDER BY us.name ASC SEPARATOR ',') AS employee
						FROM sales AS s
						INNER JOIN users as us ON us.id = s.call_center_agent_id
						WHERE s.deleted_at IS NULL
						GROUP BY s.reservation_id
				        ) as emp ON emp.reservation_id = rez.id
        WHERE (it.op_one_pickup BETWEEN :init_date_one AND :init_date_two OR it.op_two_pickup BETWEEN :init_date_three AND :init_date_four)
        AND rez.is_commissionable = 1 AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.pay_at_arrival = 1
        GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id, it_counter.quantity, p.payment_type_name, emp.employee", [
                                    "init_date_one" => $search['init'],
                                    "init_date_two" => $search['end'],
                                    "init_date_three" => $search['init'],
                                    "init_date_four" => $search['end'],
                                ]);

        if(sizeof($paid) >= 1):
                foreach($paid as $key => $value):
                        if($value->status == "CONFIRMADO"):
                                if($value->quantity > 1):
                                        $paid[$key]->total_sales = ($paid[$key]->total_sales / $value->quantity);
                                        $paid[$key]->total_payments = ($paid[$key]->total_payments / $value->quantity);
                                endif;
                        endif;
                        $items[] = $paid[$key];
                endforeach;
        endif;

        if(sizeof($cash) >= 1):
                foreach($cash as $key => $value):
                        if($value->status == "CONFIRMADO"):
                                if($value->quantity > 1):
                                        $cash[$key]->total_sales = ($cash[$key]->total_sales / $value->quantity);
                                        $cash[$key]->total_payments = ($cash[$key]->total_payments / $value->quantity);
                                endif;
                        endif;

                        if($value->is_round_trip == 0 && $value->op_one_status == "COMPLETED"):
                                $items[] = $cash[$key];
                        endif;
                        if($value->is_round_trip == 1 && $value->op_one_status == "COMPLETED" && $value->op_two_status == "COMPLETED" ):
                                $items[] = $cash[$key];
                        endif;
                endforeach;
        endif;
        
        usort($items, function ($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
        });

        //Recorremos las reservaciones para eliminar las que estén pendiente y cambialos los labels de las reservas completadas y confirmadas
        if(sizeof($items) >= 1):
                foreach($items as $key => $value):                        
                        $status = $value->status;
                        if($value->is_round_trip == 0 && $value->op_one_status == "COMPLETED"):
                                $status = "COMPLETADO";
                        endif;
                        if($value->is_round_trip == 1 && $value->op_one_status == "COMPLETED" && $value->op_two_status == "COMPLETED"):
                                $status = "COMPLETADO";
                        endif;

                        $items[$key]->status = $status;

                        if( $status == "PENDIENTE" || empty( $value->employee ) ):
                                unset( $items[$key] );
                        endif;
                endforeach;
        endif;
        
        return view('reports.commissions', compact('search', 'items'));
    }
}