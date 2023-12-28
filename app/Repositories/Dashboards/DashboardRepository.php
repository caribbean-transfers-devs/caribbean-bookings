<?php

namespace App\Repositories\Dashboards;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function index(){
        return view('dashboard.default');
    }

    public function admin(){
        
        $queryData = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1';
        $items = [];
        $day_by_day = [];

        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $items[$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];            
        }

        $bookings = DB::select("SELECT 
                                    rez.id, rez.created_at, CONCAT(rez.client_first_name,' ',rez.client_last_name) as client_full_name, rez.client_email, rez.currency, rez.is_cancelled, 
                                    rez.pay_at_arrival,
                                    COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                                    CASE
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                        ELSE 'CONFIRMED'
                                    END AS status,
                                    site.name as site_name,
                                    GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                                    GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name,
                                    GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                                    SUM(it.passengers) as passengers,
                                    GROUP_CONCAT(DISTINCT p.payment_type_name ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                                    COALESCE(SUM(it.op_one_pickup_today) + SUM(it.op_one_pickup_today), 0) as is_today                                     
                                FROM reservations as rez
                                    INNER JOIN sites as site ON site.id = rez.site_id
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
                                        SELECT  it.reservation_id, it.is_round_trip,
                                                SUM(it.passengers) as passengers,
                                                GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS code,
                                                GROUP_CONCAT(DISTINCT zone_two.name ORDER BY zone_two.name ASC SEPARATOR ',') AS zone_two_name, 
                                                GROUP_CONCAT(DISTINCT zone_two.id ORDER BY zone_two.id ASC SEPARATOR ',') AS zone_two_id, 
                                                GROUP_CONCAT(DISTINCT dest.id ORDER BY dest.id ASC SEPARATOR ',') AS service_type_id, 
                                                GROUP_CONCAT(DISTINCT dest.name ORDER BY dest.name ASC SEPARATOR ',') AS service_type_name,
                                                MAX(CASE WHEN DATE(it.op_one_pickup) = CURDATE() THEN 1 ELSE 0 END) AS op_one_pickup_today,
						                        MAX(CASE WHEN DATE(it.op_two_pickup) = CURDATE() THEN 1 ELSE 0 END) AS op_two_pickup_today
                                        FROM reservations_items as it
                                        INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                        INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                        INNER JOIN destination_services as dest ON dest.id = it.destination_service_id
                                        GROUP BY it.reservation_id, it.is_round_trip
                                    ) as it ON it.reservation_id = rez.id
                                WHERE 1=1 {$query}
                                GROUP BY rez.id, site.name",
                                    $queryData);
        if(sizeof( $bookings ) >= 1){
            
            foreach($bookings as $key => $value):                
                $date_ = date("Y-m-d", strtotime( $value->created_at ));
                if( isset( $items[ $date_ ] ) ){
                    $items[ $date_ ]['items'][] = $value;
                    $items[ $date_ ]['counter']++;
                    if( $value->currency == "USD" ):
                        $items[ $date_ ]['USD'] += $value->total_sales;
                    endif;
                    if( $value->currency == "MXN" ):
                        $items[ $date_ ]['MXN'] += $value->total_sales;
                    endif;                   
                }
            endforeach;
        }

        //dd($items);
        
        
        return view('dashboard.admin', ['items' => $items]);
    }
}