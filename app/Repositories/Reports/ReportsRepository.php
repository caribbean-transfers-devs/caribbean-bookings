<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\Reports\PaymentsTrait;

class ReportsRepository
{
    use FiltersTrait, QueryTrait, PaymentsTrait;

    public function payments($request)
    {
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
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0
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
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                        "init_date_one" => $search['init_date'],
                                        "init_date_two" => $search['end_date'],
                                        "init_date_three" => $search['init_date'],
                                        "init_date_four" => $search['end_date'],
                                    ]);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Reporte de pagos",
                "active" => true
            ),
        );

        return view('reports.payments', compact('items','date','breadcrumbs'));
    }

    public function sales($request)
    {
        $data = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
            "filter_text" => NULL,
            "site" => 0,
            "product_type" => 0,
            "zone_one_id" => 0,
            "payment_method" => NULL
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_duplicated = 0 ';

        $queryData = [
            'init' => date("Y-m-d") . " 00:00:00",
            'end' => date("Y-m-d") . " 23:59:59",
        ];

        if(isset( $request->date ) && !empty( $request->date )){            
            $tmp_date = explode(" - ", $request->date);
            $data['init'] = $tmp_date[0];
            $data['end'] = $tmp_date[1];            
            $queryData['init'] = $data['init'].' 00:00:00';
            $queryData['end'] = $data['end'].' 23:59:59';
        }

        if(isset( $request->product_type ) && !empty( $request->product_type )){
            $data['product_type'] = $request->product_type;

            $queryData['product_type'] = $data['product_type'];
            $query .= " AND FIND_IN_SET(:product_type, service_type_id) > 0";
        }

        if(isset( $request->zone ) && !empty( $request->zone )){
            $data['zone'] = $request->zone;
            $queryData['zone'] = $data['zone'];
            $query .= " AND FIND_IN_SET(:zone, zone_two_id) > 0";
        }

        if(isset( $request->site ) && sizeof($request->site) > 0 ){                
            $query .= ' AND site.id IN( '.implode(",", $request->site).' )';
        }
        if(isset( $request->payment_method ) && !empty( $request->payment_method )){
            $data['payment_method'] = $request->payment_method;
        }
        if(isset( $request->filter_text ) && !empty( $request->filter_text )){            
            $data['filter_text'] = $request->filter_text;
            $queryData = [];
            $query  = " AND (
                        ( CONCAT(rez.client_first_name,' ',rez.client_last_name) like '%".$data['filter_text']."%') OR
                        ( rez.client_phone like '%".$data['filter_text']."%') OR
                        ( rez.client_email like '%".$data['filter_text']."%') OR
                        ( rez.reference like '%".$data['filter_text']."%') OR
                        ( it.code like '".$data['filter_text']."' )
                    )";            
        }

        $query .= " AND rez.is_cancelled <> 1";
        
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


        return view('reports.sales', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reporte de reservacione del ". date("Y-m-d", strtotime($data['init'])) ." al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true                    
                ]
            ],
            'bookings' => $bookings,
            'websites' => $this->Sites(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'data' => $data,
        ]);
    }

    public function cash($request)
    {
        
        $search = [];
        $date_search = date("Y-m-d"). " - ".date("Y-m-d");
        if(isset($request->date)){
            $dateRange = explode(" - ", $request->date);
            $search['init_date'] = $dateRange[0]." 00:00:00";
            $search['end_date'] = $dateRange[1]." 23:59:59";
            $date_search = $dateRange[0]. " - ".$dateRange[1];
        } else {
            $date = date("Y-m-d");
            $search['init_date'] = $date." 00:00:00";
            $search['end_date'] = $date." 23:59:59";
        }

        $items = DB::select("SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_one_pickup as filtered_date, 'arrival' as operation_type, sit.name as site_name, sit.is_cxc, sit.is_cxp, '' as messages,
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
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND sit.is_cxc <> 1
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                    UNION 
                                    SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_two_pickup as filtered_date, 'departure' as operation_type, sit.name as site_name, sit.is_cxc, sit.is_cxp, '' as messages,
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
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND sit.is_cxc <> 1
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                        "init_date_one" => $search['init_date'],
                                        "init_date_two" => $search['end_date'],
                                        "init_date_three" => $search['init_date'],
                                        "init_date_four" => $search['end_date'],
                                    ]);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Reporte de pagos en efectivo del ". date("Y-m-d", strtotime($search['init_date'])) ." al ". date("Y-m-d", strtotime($search['end_date'])),
                "active" => true
            ),
        );

        return view('reports.cash', compact('items','date_search','breadcrumbs'));
    }

    public function cancellations($request)
    {
        $date = date("Y-m-d");
        if(isset( $request->date )):
            $date = $request->date;
        endif;

        $search['init'] = ( isset( $request->date ) && !empty( $request->date ) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00";
        $search['end'] = ( isset( $request->date ) && !empty( $request->date ) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59";

        $items = DB::select("SELECT 
                                rez.id as reservation_id, 
                                rez.*, 
                                it.*, 
                                serv.name as service_name, 
                                it.op_one_pickup as filtered_date, 
                                'arrival' as operation_type, 
                                sit.name as site_name, 
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
                                END AS final_service_type,
                                tc.id as cancellation_type_id, 
                                tc.name_es as cancellation_type_name
                            FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                LEFT JOIN types_cancellations as tc ON tc.id = rez.cancellation_type_id
                                LEFT JOIN (
                                    SELECT 
                                        reservation_id,  
                                        ROUND( COALESCE(SUM(total), 0), 2) as total_sales
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
                                AND rez.is_duplicated = 0
                                GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id, tc.id
                            UNION 
                            SELECT 
                                rez.id as reservation_id, 
                                rez.*, 
                                it.*, 
                                serv.name as service_name, 
                                it.op_two_pickup as filtered_date, 
                                'departure' as operation_type, 
                                sit.name as site_name, 
                                '' as messages,
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
                                                END AS final_service_type,
                                                tc.id as cancellation_type_id, tc.name_es as cancellation_type_name
                            FROM reservations_items as it
                                    INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                    INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                    INNER JOIN sites as sit ON sit.id = rez.site_id
                                    INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
				                    INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                    LEFT JOIN types_cancellations as tc ON tc.id = rez.cancellation_type_id
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
                                    AND rez.is_duplicated = 0
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id, tc.id",[
                                        "init_date_one" => $search['init'],
                                        "init_date_two" => $search['end'],
                                        "init_date_three" => $search['init'],
                                        "init_date_four" => $search['end'],
                                    ]);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Gestión de operación",
                "active" => true
            ),
        );

        return view('reports.cancellations', compact('items','search','breadcrumbs'));
    }

    public function commissions($request)
    {
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
        
        $paid = DB::select("SELECT 
                                rez.id as reservation_id, 
                                CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name, 
                                rez.currency, 
                                rez.language, 
                                rez.is_cancelled, 
                                rez.is_commissionable, 
                                rez.is_duplicated, 
                                rez.affiliate_id, 
                                rez.pay_at_arrival,
                                rez.open_credit, 
                                rez.reference, 
                                rez.created_at,
                                it.code,
                                it.is_round_trip, 
                                it.op_one_pickup, 
                                it.op_one_status, 
                                it.op_two_pickup, 
                                it.op_two_status,
                                it.passengers,
                                serv.name as service_name, 
                                it.op_one_pickup as filtered_date,
                                'arrival' as operation_type,
                                sit.name as site_name, '' as messages,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                CASE
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                ELSE 'CONFIRMADO'
                                END AS status,
                                zone_one.id as zone_one_id, 
                                zone_one.name as zone_one_name, 
                                zone_one.is_primary as zone_one_is_primary,
                                zone_two.id as zone_two_id, 
                                zone_two.name as zone_two_name, 
                                zone_two.is_primary as zone_two_is_primary,
                                CASE 
                                        WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                        WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                        WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type,
                                it_counter.quantity,
                                IFNULL(p.payment_type_name, 'CASH') as payment_type_name,
                                -- emp.employee
                                us.name AS employee
                        FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN users as us ON us.id = rez.call_center_agent_id
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone

                                LEFT JOIN (
                                        SELECT 
                                                reservation_id, 
                                                ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                        FROM sales
                                                WHERE deleted_at IS NULL
                                        GROUP BY reservation_id
                                ) as s ON s.reservation_id = rez.id 
                                LEFT JOIN (
                                        SELECT 
                                                reservation_id,
                                                ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                        WHEN operation = 'division' THEN total / exchange_rate
                                                        ELSE total END), 2) AS total_payments,
                                                GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                        FROM payments
                                                GROUP BY reservation_id
                                ) as p ON p.reservation_id = rez.id 
                                LEFT JOIN (
                                        SELECT  
                                                it.reservation_id,
                                                count(it.code) as quantity
                                        FROM reservations_items as it
                                                WHERE it.op_one_status NOT IN ('CANCELLED') AND it.op_two_status NOT IN ('CANCELLED')
                                                GROUP BY it.reservation_id
                                ) as it_counter ON it_counter.reservation_id = it.reservation_id
                                -- LEFT JOIN (
				-- 	SELECT 
                                --                 s.reservation_id,
                                --                 GROUP_CONCAT(DISTINCT us.name ORDER BY us.name ASC SEPARATOR ',') AS employee
				-- 	FROM sales AS s
				-- 	        INNER JOIN users as us ON us.id = s.call_center_agent_id
				-- 	WHERE s.deleted_at IS NULL
				-- 		GROUP BY s.reservation_id
				-- ) as emp ON emp.reservation_id = rez.id                                

                        WHERE rez.created_at BETWEEN :init_date_one AND :init_date_two
                        -- WHERE (it.op_one_pickup BETWEEN :init_date_one AND :init_date_two OR it.op_two_pickup BETWEEN :init_date_three AND :init_date_four)
                                AND rez.is_commissionable = 1 
                                AND rez.is_cancelled = 0 
                                AND rez.is_duplicated = 0 
                                AND rez.pay_at_arrival = 0 
                        GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id, it_counter.quantity, p.payment_type_name, us.name", [
                                "init_date_one" => $search['init'],
                                "init_date_two" => $search['end'],
                                // "init_date_one" => $search['init'],
                                // "init_date_two" => $search['end'],
                                // "init_date_three" => $search['init'],
                                // "init_date_four" => $search['end'],
                        ]);

        $cash = DB::select("SELECT 
                                rez.id as reservation_id, 
                                CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name, 
                                rez.currency, 
                                rez.language, 
                                rez.is_cancelled, 
                                rez.is_commissionable, 
                                rez.is_duplicated, 
                                rez.affiliate_id, 
                                rez.pay_at_arrival,
                                rez.open_credit, 
                                rez.reference, 
                                rez.created_at,
                                it.code,
                                it.is_round_trip, 
                                it.op_one_pickup, 
                                it.op_one_status, 
                                it.op_two_pickup, 
                                it.op_two_status,
                                it.passengers,
                                serv.name as service_name, 
                                it.op_one_pickup as filtered_date,                                 
                                'arrival' as operation_type, 
                                sit.name as site_name, 
                                '' as messages,
                                COALESCE(SUM(s.total_sales), 0) as total_sales,
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                CASE
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                ELSE 'CONFIRMADO'
                                END AS status,
                                zone_one.id as zone_one_id, 
                                zone_one.name as zone_one_name, 
                                zone_one.is_primary as zone_one_is_primary,
                                zone_two.id as zone_two_id, 
                                zone_two.name as zone_two_name, 
                                zone_two.is_primary as zone_two_is_primary,
                                CASE 
                                        WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                        WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                        WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type,
                                it_counter.quantity,
                                IFNULL(p.payment_type_name, 'CASH') as payment_type_name, 
                                -- emp.employee
                                us.name AS employee
                        FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN users as us ON us.id = rez.call_center_agent_id
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                        
                                LEFT JOIN (
                                        SELECT 
                                                reservation_id,  
                                                ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                        FROM sales
                                                WHERE deleted_at IS NULL
                                        GROUP BY reservation_id
                                ) as s ON s.reservation_id = rez.id
                                LEFT JOIN (
                                        SELECT 
                                                reservation_id,
                                                ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                        WHEN operation = 'division' THEN total / exchange_rate
                                                        ELSE total END), 2) AS total_payments,
                                                GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                        FROM payments
                                                GROUP BY reservation_id
                                ) as p ON p.reservation_id = rez.id
                                LEFT JOIN (
                                        SELECT  
                                                it.reservation_id, 
                                                count(it.code) as quantity
                                        FROM reservations_items as it
                                                WHERE it.op_one_status NOT IN ('CANCELLED') AND it.op_two_status NOT IN ('CANCELLED')
                                                GROUP BY it.reservation_id
                                ) as it_counter ON it_counter.reservation_id = it.reservation_id
                                -- LEFT JOIN (
                                --         SELECT 
                                --                 s.reservation_id,
                                --                 GROUP_CONCAT(DISTINCT us.name ORDER BY us.name ASC SEPARATOR ',') AS employee
                                --         FROM sales AS s
                                --                 INNER JOIN users as us ON us.id = s.call_center_agent_id
                                --         WHERE s.deleted_at IS NULL
                                --         GROUP BY s.reservation_id
                                -- ) as emp ON emp.reservation_id = rez.id
                                        
                        WHERE (it.op_one_pickup BETWEEN :init_date_one AND :init_date_two OR it.op_two_pickup BETWEEN :init_date_three AND :init_date_four)
                                AND rez.is_commissionable = 1
                                AND rez.is_cancelled = 0
                                AND rez.is_duplicated = 0
                                AND rez.pay_at_arrival = 1
                        GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id, it_counter.quantity, p.payment_type_name, us.name", [
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

        // Recorremos las reservaciones para eliminar las que estén pendiente y cambialos los labels de las reservas completadas y confirmadas
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
        
        return view('reports.commissions', [
                'breadcrumbs' => [
                        [
                                "route" => "",
                                "name" => "Reporte de comisiones del ". date("Y-m-d", strtotime($search['init'])) ." al ". date("Y-m-d", strtotime($search['end'])),
                                "active" => true
                        ]
                ],
                'search' => $search, 
                'items' => $items,
        ]);
    }

    public function commissions2($request)
    {

        $havingConditions = []; $queryHaving = "";
        // $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_commissionable = 1 AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.call_center_agent_id = 27 ";
        // $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_commissionable = 1 AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND it.is_round_trip = 1 AND rez.call_center_agent_id = 27 ";
        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_commissionable = 1 AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_commissionable = 1 AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND it.is_round_trip = 1 ";
        $queryData = [
                'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
                'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];
    
        // dd($queryOne, $queryTwo, $queryHaving, $queryData);
        $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);        

        // dd($items);

        return view('reports.commissions2', [
                'breadcrumbs' => [
                        [
                                "route" => "",
                                "name" => "Reporte de comisiones del ". date("Y-m-d", strtotime($queryData['init'])) ." al ". date("Y-m-d", strtotime($queryData['end'])),
                                "active" => true
                        ]
                ],
                'search' => $queryData, 
                'operations' => $items,
        ]);        
    }

    public function reservations($request)
    {
        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
            "filter_text" => NULL,
            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "is_commissionable" => ( isset($request->is_commissionable) ? $request->is_commissionable : NULL ),
            "is_pay_at_arrival" => ( isset($request->is_pay_at_arrival) ? $request->is_pay_at_arrival : NULL ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),            
            "is_balance" => ( isset($request->is_balance) ? $request->is_balance : NULL ),
            "is_today" => ( isset($request->is_today) ? $request->is_today : NULL ),
            "is_duplicated" => ( isset($request->is_duplicated) ? $request->is_duplicated : NULL ),
        ];
        
        //Query DB (2013-2206)
        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end ';
        $havingConditions = []; $query2 = '';
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];

        //TIPO DE SERVICIO
        if(isset( $request->is_round_trip )){
            $params = "";
            foreach( $request->is_round_trip as $key => $is_round_trip ){
                $queryData['is_round_trip' . $key] = $is_round_trip;
                $params .= "FIND_IN_SET(:is_round_trip".$key.", is_round_trip) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $query .= " AND (".$params.") ";            
        }

        //SITIO
        if(isset( $request->site ) && !empty( $request->site )){
            $params = $this->parseArrayQuery($request->site);
            $query .= " AND site.id IN ($params) ";
        }
        
        //ORIGEN DE VENTA
        if(isset( $request->origin ) && !empty( $request->origin )){
            $queryweb = "";
            if( in_array("0", $request->origin) ){
                $queryweb = " OR origin.id IS NULL ";
            }
            $params = $this->parseArrayQuery($request->origin);
            $query .= " AND ( origin.id IN ($params) $queryweb ) ";
        }

        //ESTATUS DE RESERVACIÓN
        if(isset( $request->reservation_status ) && !empty( $request->reservation_status )){
            $params = $this->parseArrayQuery($request->reservation_status,"single");
            $havingConditions[] = " reservation_status IN (".$params.") ";
        }

        //TIPO DE VEHÍCULO
        if(isset( $request->product_type ) && !empty( $request->product_type )){
            $params = "";
            foreach( $request->product_type as $key => $product_type ){
                $queryData['product_type' . $key] = $product_type;
                $params .= "FIND_IN_SET(:product_type".$key.", service_type_id) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $query .= " AND (".$params.") ";
        }

        //ZONA DE ORIGEN
        if(isset( $request->zone_one_id ) && !empty( $request->zone_one_id )){
            $params = "";
            foreach( $request->zone_one_id as $key => $zone_one_id ){
                $queryData['zone_one_id' . $key] = $zone_one_id;
                $params .= "FIND_IN_SET(:zone_one_id".$key.", zone_one_id) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $query .= " AND (".$params.") ";
        }

        //ZONA DE DESTINO
        if(isset( $request->zone_two_id ) && !empty( $request->zone_two_id )){
            $params = "";
            foreach( $request->zone_two_id as $key => $zone_two_id ){
                $queryData['zone_two_id' . $key] = $zone_two_id;
                $params .= "FIND_IN_SET(:zone_two_id".$key.", zone_two_id) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $query .= " AND (".$params.") ";
        }

        //ESTATUS DE PAGO
        if(isset( $request->payment_status ) && !empty( $request->payment_status )){
            $params = $this->parseArrayQuery($request->payment_status,"single");
            $havingConditions[] = " payment_status IN (".$params.") ";
        }        

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $query .= " AND rez.currency IN ($params) ";
        }

        //METODO DE PAGO
        if(isset( $request->payment_method ) && !empty( $request->payment_method )){
            $params = "";
            foreach( $request->payment_method as $key => $payment_method ){
                $queryData['payment_method' . $key] = $payment_method;
                $params .= "FIND_IN_SET(:payment_method".$key.", payment_type_name) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $havingConditions[] = " (".$params.") "; 
        }

        //COMISIONABLES
        if(isset( $request->is_commissionable )){
            $params = $request->is_commissionable;
            $query .= " AND rez.is_commissionable = $params ";
        }        

        //MOTIVOS DE CANCELACIÓN
        if(isset( $request->cancellation_status ) && !empty( $request->cancellation_status )){
            $params = $this->parseArrayQuery($request->cancellation_status);
            $query .= " AND tc.id IN ($params) ";
        }

        //RESERVAS CON UN BALANCE
        if(isset( $request->is_balance )){
            $havingConditions[] = ( $request->is_balance == 1 ? ' total_balance > 0 ' : ' total_balance <= 0 ' );
        }        

        //RESERVAS OPERADAS EL MISMO DIA DE SU CREACION
        if(isset( $request->is_today )){
            $havingConditions[] = ( $request->is_today == 1 ? ' is_today != 0 ' : ' is_today = 0 ' );
        }

        //TIPO DE SERVICIO
        if(!isset( $request->is_duplicated )){
            $query .= " AND rez.is_duplicated = 0 ";
        }        
        if(isset( $request->is_duplicated )){
            $query .= " AND rez.is_duplicated IN (1,0) ";
        }

        if(isset( $request->filter_text ) && !empty( $request->filter_text )){
            $data['filter_text'] = $request->filter_text;
            $queryData = [];
            $query  = " AND (
                        ( CONCAT(rez.client_first_name,' ',rez.client_last_name) like '%".$data['filter_text']."%') OR
                        ( rez.client_phone like '%".$data['filter_text']."%') OR
                        ( rez.client_email like '%".$data['filter_text']."%') OR
                        ( rez.reference like '%".$data['filter_text']."%') OR
                        ( it.code like '%".$data['filter_text']."%' )
                    )";
        }

        if(  (isset( $request->reservation_status ) && !empty( $request->reservation_status )) || (isset( $request->payment_status ) && !empty( $request->payment_status )) || (isset( $request->payment_method ) && !empty( $request->payment_method )) || (isset( $request->is_balance )) || (isset( $request->is_today )) ){
            $query2 = " HAVING " . implode(' AND ', $havingConditions);
        }

        $bookings = $this->queryBookings($query, $query2, $queryData);
        
        return view('reports.reservations', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reservaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'bookings' => $bookings,
            'services' => $this->Services(),
            'websites' => $this->Sites(),
            'origins' => $this->Origins(),
            'reservation_status' => $this->reservationStatus(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'payment_status' => $this->paymentStatus(),
            'currencies' => $this->Currencies(),
            'methods' => $this->Methods(),
            'cancellations' => $this->CancellationTypes(),
            'data' => $data,
        ]);
    }

    public function operations($request)
    {
        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
            "filter_text" => NULL,
            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "service_operation" => ( isset($request->service_operation) ? $request->service_operation : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "service_operation_status" => ( isset($request->service_operation_status) ? $request->service_operation_status : 0 ),
            "unit" => ( isset($request->unit) ? $request->unit : 0 ),
            "driver" => ( isset($request->driver) ? $request->driver : 0 ),
            "operation_status" => ( isset( $request->operation_status ) && !empty( $request->operation_status ) ? $request->operation_status : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),
            // "is_balance" => ( isset($request->is_balance) ? $request->is_balance : NULL ),
            // "is_today" => ( isset($request->is_today) ? $request->is_today : NULL ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];

        //TIPO DE SERVICIO is_round_trip
        if(isset( $request->is_round_trip )){
            $params = $this->parseArrayQuery($request->is_round_trip);
            $queryOne .= " AND it.is_round_trip IN ($params) ";
            $queryTwo .= " AND it.is_round_trip IN ($params) ";
        }

        //SITIO
        if( isset($request->site) && !empty($request->site) ){
            $params = $this->parseArrayQuery($request->site);
            $queryOne .= " AND site.id IN ($params) ";
            $queryTwo .= " AND site.id IN ($params) ";
        }

        //ORIGEN DE VENTA
        if(isset( $request->origin ) && !empty( $request->origin )){
            $queryweb = "";
            if( in_array("0", $request->origin) ){
                $queryweb = " OR origin.id IS NULL ";
            }
            $params = $this->parseArrayQuery($request->origin);
            $queryOne .= " AND ( origin.id IN ($params) $queryweb ) ";
            $queryTwo .= " AND ( origin.id IN ($params) $queryweb ) ";
        }        

        //ESTATUS DE RESERVACIÓN
        if(isset( $request->reservation_status ) && !empty( $request->reservation_status )){
            $params = $this->parseArrayQuery($request->reservation_status,"single");
            $havingConditions[] = " reservation_status IN (".$params.") ";
        }

        //TIPO DE SERVICIO EN OPERACIÓN
        if(isset( $request->service_operation ) && !empty( $request->service_operation )){
            $params = $this->parseArrayQuery($request->service_operation,"single");
            $havingConditions[] = " final_service_type IN (".$params.") ";
        }        

        //TIPO DE VEHÍCULO
        if(isset( $request->product_type ) && !empty( $request->product_type )){
            $params = $this->parseArrayQuery($request->product_type);            
            $queryOne .= " AND serv.id IN ($params) ";
            $queryTwo .= " AND serv.id IN ($params) ";
        }

        //ZONA DE ORIGEN
        if(isset( $request->zone_one_id ) && !empty( $request->zone_one_id )){
            $params = $this->parseArrayQuery($request->zone_one_id);
            $queryOne .= " AND zone_one.id IN ($params) ";
            $queryTwo .= " AND zone_one.id IN ($params) ";
        }

        //ZONA DE DESTINO
        if(isset( $request->zone_two_id ) && !empty( $request->zone_two_id )){
            $params = $this->parseArrayQuery($request->zone_two_id);
            $queryOne .= " AND zone_two.id IN ($params) ";
            $queryTwo .= " AND zone_two.id IN ($params) ";
        }
        
        //ESTATUS DE SERVICIO
        if(isset( $request->service_operation_status ) && !empty( $request->service_operation_status )){
            $params = $this->parseArrayQuery($request->service_operation_status,"single");            
            $queryOne .= " AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND it.op_two_status IN ($params) ";
        }

        //UNIDAD ASIGNADA AL SERVICIO
        if( isset($request->unit) && !empty($request->unit) ){
            $params = $this->parseArrayQuery($request->unit);
            $queryOne .= " AND it.vehicle_id_one IN ($params) ";
            $queryTwo .= " AND it.vehicle_id_two IN ($params) ";
        }

        //CONDUCTOR ASIGNADO AL SERVICIO
        if( isset($request->driver) && !empty($request->driver) ){
            $params = $this->parseArrayQuery($request->driver);
            $queryOne .= " AND it.driver_id_one IN ($params) ";
            $queryTwo .= " AND it.driver_id_two IN ($params) ";
        }

        //ESTATUS DE SERVICIO
        if(isset( $request->operation_status ) && !empty( $request->operation_status )){
            $params = $this->parseArrayQuery($request->operation_status,"single");            
            $queryOne .= " AND it.op_one_status_operation IN ($params) ";
            $queryTwo .= " AND it.op_two_status_operation IN ($params) ";
        }

        //ESTATUS DE PAGO
        if(isset( $request->payment_status ) && !empty( $request->payment_status )){
            $params = $this->parseArrayQuery($request->payment_status,"single");
            $havingConditions[] = " payment_status IN (".$params.") ";
        }

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $queryOne .= " AND rez.currency IN ($params) ";
            $queryTwo .= " AND rez.currency IN ($params) ";
        }

        //METODO DE PAGO
        if(isset( $request->payment_method ) && !empty( $request->payment_method )){
            $params = "";
            foreach( $request->payment_method as $key => $payment_method ){
                $params .= "FIND_IN_SET('".$payment_method."', payment_type_name) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $havingConditions[] = " (".$params.") "; 
        }

        //MOTIVOS DE CANCELACIÓN
        if(isset( $request->cancellation_status ) && !empty( $request->cancellation_status )){
            $params = $this->parseArrayQuery($request->cancellation_status);
            $queryOne .= " AND tc.id IN ($params) ";
            $queryTwo .= " AND tc.id IN ($params) ";
        }        

        if(  (isset( $request->reservation_status ) && !empty( $request->reservation_status )) || isset( $request->service_operation ) && !empty( $request->service_operation ) || (isset( $request->payment_status ) && !empty( $request->payment_status )) || (isset( $request->payment_method ) && !empty( $request->payment_method )) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        // dd($queryOne, $queryTwo, $queryHaving, $queryData);
        $operations = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        return view('reports.operations', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Operacion del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'operations' => $operations,
            'services' => $this->Services(),
            'websites' => $this->Sites(),
            'origins' => $this->Origins(),
            'reservation_status' => $this->reservationStatus(),
            'services_operation' => $this->servicesOperation(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'service_operation_status' => $this->statusOperationService(),
            'units' => $this->Units(), //LAS UNIDADES DADAS DE ALTA
            'drivers' => $this->Drivers(),
            'operation_status' => $this->statusOperation(),
            'payment_status' => $this->paymentStatus(),
            'currencies' => $this->Currencies(),
            'methods' => $this->Methods(),
            'cancellations' => $this->CancellationTypes(),
            'data' => $data,
        ]);
    }

    public function conciliation($request)
    {
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
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0
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
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                        "init_date_one" => $search['init_date'],
                                        "init_date_two" => $search['end_date'],
                                        "init_date_three" => $search['init_date'],
                                        "init_date_four" => $search['end_date'],
                                    ]);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Reporte de pagos y conciliaciones",
                "active" => true
            ),
        );

        return view('reports.conciliation', compact('items','date','breadcrumbs'));
    }
}
