<?php

namespace App\Repositories\Dashboards;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class DashboardRepository
{
    public function index(){
        return view('dashboard.default');
    }

    public function admin(){
        
        $bookings_month = [];
        $queryData = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 ';

        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $bookings_month[$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];
        }

        $bookings_data_month = $this->dataBooking($query, $queryData);

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):                
                $date_ = date("Y-m-d", strtotime( $value->created_at ));
                if( isset( $bookings_month[ $date_ ] ) ){
                    $bookings_month[ $date_ ]['items'][] = $value;
                    $bookings_month[ $date_ ]['counter']++;
                    if( $value->currency == "USD" ):
                        $bookings_month[ $date_ ]['USD'] += $value->total_sales;
                    endif;
                    if( $value->currency == "MXN" ):
                        $bookings_month[ $date_ ]['MXN'] += $value->total_sales;
                    endif;                   
                }
            endforeach;
        }
        
        return view('dashboard.admin', ['items' => $bookings_month]);
    }

    public function sales($request){
        $bookings_day = [];
        $bookings_month = [];
        $queryDataDay = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
        ];        
        $queryDataMonth = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 ';

        $bookings_day[date("Y-m-d")] = [
            "items" => [],
            "counter" => 0,
            "USD" => 0,
            "MXN" => 0,
        ];

        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $bookings_month[$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];
        }

        $bookings_data_day = $this->dataBooking($query, $queryDataDay);
        $bookings_data_month = $this->dataBooking($query, $queryDataMonth);

        if(sizeof( $bookings_data_day ) >= 1){
            foreach($bookings_data_day as $bookingsDay):
                $date_ = date("Y-m-d", strtotime( $bookingsDay->created_at ));
                if( isset( $bookings_day[ $date_ ] ) ){
                    $bookings_day[ $date_ ]['items'][] = $bookingsDay;
                    $bookings_day[ $date_ ]['counter']++;
                    if( $bookingsDay->currency == "USD" ):
                        $bookings_day[ $date_ ]['USD'] += $bookingsDay->total_sales;
                    endif;
                    if( $bookingsDay->currency == "MXN" ):
                        $bookings_day[ $date_ ]['MXN'] += $bookingsDay->total_sales;
                    endif;                   
                }
            endforeach;
        }

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):
                $date_ = date("Y-m-d", strtotime( $value->created_at ));
                if( isset( $bookings_month[ $date_ ] ) ){
                    $bookings_month[ $date_ ]['items'][] = $value;
                    $bookings_month[ $date_ ]['counter']++;
                    if( $value->currency == "USD" ):
                        $bookings_month[ $date_ ]['USD'] += $value->total_sales;
                    endif;
                    if( $value->currency == "MXN" ):
                        $bookings_month[ $date_ ]['MXN'] += $value->total_sales;
                    endif;                   
                }
            endforeach;
        }
        
        return view('dashboard.sales', ['bookings_day' => $bookings_day, 'bookings_month' => $bookings_month]);
    }    

    public function online($request){
        $bookings_day = [];
        $bookings_month = [];
        $queryDataDay = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
        ];        
        $queryDataMonth = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 AND rez.site_id NOT IN (11,21) ';

        $bookings_day[date("Y-m-d")] = [
            "items" => [],
            "counter" => 0,
            "USD" => 0,
            "MXN" => 0,
        ];

        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $bookings_month[$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];
        }

        $bookings_data_day = $this->dataBooking($query, $queryDataDay);
        $bookings_data_month = $this->dataBooking($query, $queryDataMonth);

        if(sizeof( $bookings_data_day ) >= 1){
            foreach($bookings_data_day as $bookingsDay):
                $date_ = date("Y-m-d", strtotime( $bookingsDay->created_at ));
                if( isset( $bookings_day[ $date_ ] ) ){
                    $bookings_day[ $date_ ]['items'][] = $bookingsDay;
                    $bookings_day[ $date_ ]['counter']++;
                    if( $bookingsDay->currency == "USD" ):
                        $bookings_day[ $date_ ]['USD'] += $bookingsDay->total_sales;
                    endif;
                    if( $bookingsDay->currency == "MXN" ):
                        $bookings_day[ $date_ ]['MXN'] += $bookingsDay->total_sales;
                    endif;                   
                }
            endforeach;
        }

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):
                $date_ = date("Y-m-d", strtotime( $value->created_at ));
                if( isset( $bookings_month[ $date_ ] ) ){
                    $bookings_month[ $date_ ]['items'][] = $value;
                    $bookings_month[ $date_ ]['counter']++;
                    if( $value->currency == "USD" ):
                        $bookings_month[ $date_ ]['USD'] += $value->total_sales;
                    endif;
                    if( $value->currency == "MXN" ):
                        $bookings_month[ $date_ ]['MXN'] += $value->total_sales;
                    endif;                   
                }
            endforeach;
        }
        
        return view('dashboard.online', ['bookings_day' => $bookings_day, 'bookings_month' => $bookings_month]);
    }

    public function airport($request){
        $bookings_day = [];
        $bookings_month = [];
        $queryDataDay = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
        ];        
        $queryDataMonth = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 AND rez.site_id IN (11,21) ';

        $bookings_day[date("Y-m-d")] = [
            "items" => [],
            "counter" => 0,
            "USD" => 0,
            "MXN" => 0,
        ];

        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $bookings_month[$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];
        }

        $bookings_data_day = $this->dataBooking($query, $queryDataDay);
        $bookings_data_month = $this->dataBooking($query, $queryDataMonth);

        if(sizeof( $bookings_data_day ) >= 1){
            foreach($bookings_data_day as $bookingsDay):
                $date_ = date("Y-m-d", strtotime( $bookingsDay->created_at ));
                if( isset( $bookings_day[ $date_ ] ) ){
                    $bookings_day[ $date_ ]['items'][] = $bookingsDay;
                    $bookings_day[ $date_ ]['counter']++;
                    if( $bookingsDay->currency == "USD" ):
                        $bookings_day[ $date_ ]['USD'] += $bookingsDay->total_sales;
                    endif;
                    if( $bookingsDay->currency == "MXN" ):
                        $bookings_day[ $date_ ]['MXN'] += $bookingsDay->total_sales;
                    endif;                   
                }
            endforeach;
        }

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):
                $date_ = date("Y-m-d", strtotime( $value->created_at ));
                if( isset( $bookings_month[ $date_ ] ) ){
                    $bookings_month[ $date_ ]['items'][] = $value;
                    $bookings_month[ $date_ ]['counter']++;
                    if( $value->currency == "USD" ):
                        $bookings_month[ $date_ ]['USD'] += $value->total_sales;
                    endif;
                    if( $value->currency == "MXN" ):
                        $bookings_month[ $date_ ]['MXN'] += $value->total_sales;
                    endif;                   
                }
            endforeach;
        }
        
        return view('dashboard.airport', ['bookings_day' => $bookings_day, 'bookings_month' => $bookings_month]);
    }

    public function dataBooking($query, $queryData){
        return DB::select("SELECT 
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
    }
}