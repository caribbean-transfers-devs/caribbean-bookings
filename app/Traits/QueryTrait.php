<?php

namespace App\Traits;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait QueryTrait
{

    // pay_at_arrival: INDICA PAGO A LA LLEGADA PORQUE ELIGIO LA OPCION DE PAGO EN EFECTIVO
    public function queryBookings($query, $query2, $queryData){
        $bookings = DB::select("SELECT 
                                    rez.id AS reservation_id, 
                                    CONCAT(rez.client_first_name,' ',rez.client_last_name) as full_name,
                                    rez.client_email,
                                    rez.client_phone,
                                    rez.currency,
                                    rez.is_cancelled,
                                    rez.is_commissionable,                                    
                                    rez.site_id,
                                    rez.pay_at_arrival,
                                    rez.reference,
                                    rez.affiliate_id,
                                    rez.terminal,
                                    rez.comments,
                                    rez.is_duplicated,
                                    rez.open_credit,
                                    rez.is_complete,
                                    rez.created_at,
                                    site.name AS site_name,
                                    origin.code AS origin_code,
                                    tc.name_es AS cancellation_reason,
                                    GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                                    GROUP_CONCAT(DISTINCT it.zone_one_name ORDER BY it.zone_one_name ASC SEPARATOR ',') AS destination_name_from,
                                    GROUP_CONCAT(DISTINCT it.zone_one_id ORDER BY it.zone_one_id ASC SEPARATOR ',') AS zone_one_id,
                                    GROUP_CONCAT(DISTINCT it.from_name SEPARATOR ',') AS from_name,
                                    GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name_to,
                                    GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                                    GROUP_CONCAT(DISTINCT it.to_name SEPARATOR ',') AS to_name,
                                    GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                                    GROUP_CONCAT(DISTINCT it.pickup_from ORDER BY it.pickup_from ASC SEPARATOR ',') AS pickup_from,
                                    GROUP_CONCAT(DISTINCT it.pickup_to ORDER BY it.pickup_to ASC SEPARATOR ',') AS pickup_to,
                                    GROUP_CONCAT(DISTINCT it.one_service_status ORDER BY it.one_service_status ASC SEPARATOR ',') AS one_service_status,
                                    GROUP_CONCAT(DISTINCT it.two_service_status ORDER BY it.two_service_status ASC SEPARATOR ',') AS two_service_status,
                                    SUM(it.passengers) as passengers,
                                    COALESCE(SUM(it.op_one_pickup_today) + SUM(it.op_two_pickup_today), 0) as is_today,
                                    SUM(it.is_round_trip) as is_round_trip,
                                    COALESCE(SUM(s.total_sales), 0) as total_sales,
                                    COALESCE(SUM(p.total_payments), 0) as total_payments,                                    
                                    COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) AS total_balance,
                                    CASE
                                        WHEN (rez.is_cancelled = 1) THEN 'CANCELLED'
                                        WHEN rez.open_credit = 1 THEN 'OPENCREDIT'
                                        WHEN rez.is_duplicated = 1 THEN 'DUPLICATED'
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'CONFIRMED'
                                        ELSE 'UNKNOWN'
                                    END AS reservation_status,
                                    CASE
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'PAID'
                                        ELSE 'PENDING'
                                    END AS payment_status,
                                    GROUP_CONCAT(
                                        DISTINCT 
                                        CASE 
                                            WHEN p.payment_type_name IS NOT NULL AND ( rez.pay_at_arrival = 0 OR rez.pay_at_arrival = 1 ) THEN p.payment_type_name
                                            ELSE 'CASH'
                                        END
                                    ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                                    GROUP_CONCAT(DISTINCT p.payment_details ORDER BY p.payment_details ASC SEPARATOR ', ') AS payment_details
                                FROM reservations as rez
                                    INNER JOIN sites as site ON site.id = rez.site_id
                                    LEFT OUTER JOIN origin_sales as origin ON origin.id = rez.origin_sale_id
                                    LEFT OUTER JOIN types_cancellations as tc ON tc.id = rez.cancellation_type_id
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
                                            ROUND(SUM(CASE 
                                                WHEN operation = 'multiplication' THEN total * exchange_rate
                                                WHEN operation = 'division' THEN total / exchange_rate
                                                ELSE total END), 2) AS total_payments,
                                            GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name,
                                            GROUP_CONCAT(
                                                DISTINCT CONCAT(
                                                    payment_method, ' | ', 
                                                    ROUND(CASE 
                                                        WHEN operation = 'multiplication' THEN total * exchange_rate
                                                        WHEN operation = 'division' THEN total / exchange_rate
                                                        ELSE total END, 2), ' | ', 
                                                    reference
                                            ) ORDER BY payment_method ASC SEPARATOR ', ') AS payment_details                                            
                                        FROM payments
                                        GROUP BY reservation_id
                                    ) as p ON p.reservation_id = rez.id
                                    LEFT JOIN (
                                        SELECT  
                                            it.reservation_id, 
                                            it.is_round_trip,
                                            SUM(it.passengers) as passengers,
                                            GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS code,
                                            GROUP_CONCAT(DISTINCT zone_one.name ORDER BY zone_one.name ASC SEPARATOR ',') AS zone_one_name,
                                            GROUP_CONCAT(DISTINCT zone_one.id ORDER BY zone_one.id ASC SEPARATOR ',') AS zone_one_id,
                                            GROUP_CONCAT(DISTINCT it.from_name SEPARATOR ',') AS from_name,
                                            GROUP_CONCAT(DISTINCT zone_two.name ORDER BY zone_two.name ASC SEPARATOR ',') AS zone_two_name, 
                                            GROUP_CONCAT(DISTINCT zone_two.id ORDER BY zone_two.id ASC SEPARATOR ',') AS zone_two_id,
                                            GROUP_CONCAT(DISTINCT it.to_name SEPARATOR ',') AS to_name,
                                            GROUP_CONCAT(DISTINCT dest.id ORDER BY dest.id ASC SEPARATOR ',') AS service_type_id,
                                            GROUP_CONCAT(DISTINCT dest.name ORDER BY dest.name ASC SEPARATOR ',') AS service_type_name,
                                            GROUP_CONCAT(DISTINCT it.op_one_pickup ORDER BY it.op_one_pickup ASC SEPARATOR ',') AS pickup_from,
                                            GROUP_CONCAT(DISTINCT it.op_two_pickup ORDER BY it.op_two_pickup ASC SEPARATOR ',') AS pickup_to,
                                            GROUP_CONCAT(DISTINCT it.op_one_status ORDER BY it.op_one_status ASC SEPARATOR ',') AS one_service_status,
                                            GROUP_CONCAT(DISTINCT it.op_two_status ORDER BY it.op_two_status ASC SEPARATOR ',') AS two_service_status,
                                            MAX(CASE WHEN DATE(it.op_one_pickup) = DATE(rez.created_at) THEN 1 ELSE 0 END) AS op_one_pickup_today,
                                            MAX(CASE WHEN DATE(it.op_two_pickup) = DATE(rez.created_at) THEN 1 ELSE 0 END) AS op_two_pickup_today
                                        FROM reservations_items as it
                                            INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                            INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                            INNER JOIN destination_services as dest ON dest.id = it.destination_service_id
                                            INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                        GROUP BY it.reservation_id, it.is_round_trip
                                    ) as it ON it.reservation_id = rez.id
                                    WHERE 1=1 {$query}
                                GROUP BY rez.id, site.name {$query2}",
                                    $queryData);

        if(sizeof( $bookings ) > 0):
            usort($bookings, array($this, 'orderByDateTime'));
        endif;

        return $bookings;
    }

    public function queryOperations($queryOne, $queryTwo, $queryHaving, $queryData){
        return  DB::select("SELECT 
                                rez.id as reservation_id,
                                CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name,
                                rez.client_email,
                                rez.client_phone,
                                rez.currency,
                                rez.language,
                                rez.is_cancelled,
                                rez.is_commissionable,
                                rez.site_id,
                                rez.pay_at_arrival,
                                rez.reference,
                                rez.affiliate_id,
                                rez.terminal,
                                rez.comments,
                                rez.is_duplicated,
                                rez.open_credit,
                                rez.is_complete,
                                rez.created_at,
                                us.name AS employee,
                                site.id as site_code,
                                site.name as site_name,
                                origin.code AS origin_code,
                                tc.name_es AS cancellation_reason,
                                CASE WHEN upload.reservation_id IS NOT NULL THEN 1 ELSE 0 END as pictures,
                                CASE WHEN rfu.reservation_id IS NOT NULL THEN 1 ELSE 0 END as messages,

                                CASE
                                    WHEN rez.is_cancelled = 1 THEN 'CANCELLED'
                                    WHEN rez.open_credit = 1 THEN 'OPENCREDIT'
                                    WHEN rez.is_duplicated = 1 THEN 'DUPLICATED'
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'CONFIRMED'
                                    ELSE 'UNKNOWN'
                                END AS reservation_status,

                                'arrival' as operation_type,
                                'TYPE_ONE' as op_type,
                                it.id,
                                it.code,
                                it.flight_number,
                                it.is_round_trip,
                                it.passengers,
                                it.op_one_pickup as filtered_date,
                                zone_one.id as zone_one_id,
                                zone_one.name as destination_name_from,
                                it.from_name as from_name,
                                zone_one.is_primary as zone_one_is_primary,
                                zone_one.cut_off as zone_one_cut_off,
                                it.op_one_status as one_service_status,
                                it.op_one_status_operation as one_service_operation_status,
                                it.op_one_time_operation,
                                it.op_one_preassignment,
                                it.op_one_operating_cost,
                                it.op_one_pickup as pickup_from,
                                it.op_one_operation_close,
                                it.op_one_comments,
                                it.vehicle_id_one,
                                it.driver_id_one,
                                zone_two.id as zone_two_id,
                                zone_two.name as destination_name_to,
                                it.to_name as to_name,
                                zone_two.is_primary as zone_two_is_primary,
                                zone_two.cut_off as zone_two_cut_off,
                                it.op_two_status as two_service_status,
                                it.op_two_status_operation as two_service_operation_status,
                                it.op_two_time_operation,
                                it.op_two_preassignment,
                                it.op_two_operating_cost,
                                it.op_two_pickup as pickup_to,
                                it.op_two_operation_close,
                                it.op_two_comments,
                                it.vehicle_id_two,
                                it.driver_id_two,

                                CASE 
                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type,

                                serv.id as service_type_id,
                                serv.name as service_type_name,
                                vehicle_one.name as vehicle_one_name,
                                d_one.name as vehicle_name_one,
                                vehicle_two.name as vehicle_two_name,
                                d_two.name as vehicle_name_two,
                                CONCAT(driver_one.names,' ',driver_one.surnames) as driver_one_name,
                                CONCAT(driver_two.names,' ',driver_two.surnames) as driver_two_name,

                                it_counter.quantity,
                                GROUP_CONCAT(
                                    DISTINCT 
                                    CASE 
                                        WHEN p.payment_type_name IS NOT NULL AND ( rez.pay_at_arrival = 0 OR rez.pay_at_arrival = 1 ) THEN p.payment_type_name
                                        ELSE 'CASH'
                                    END
                                ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) AS total_balance,
                                CASE
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'PAID'
                                    ELSE 'PENDING'
                                END AS payment_status,
                                CASE 
                                    WHEN it.is_round_trip = 1 THEN COALESCE(SUM(s.total_sales), 0) / 2
                                    ELSE COALESCE(SUM(s.total_sales), 0)
                                END AS service_cost,
                                CASE 
                                    WHEN it_counter.quantity > 0 THEN COALESCE(SUM(s.total_sales), 0) / it_counter.quantity
                                    ELSE 0
                                END AS cost
                            FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN sites as site ON site.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                LEFT OUTER JOIN users as us ON us.id = rez.call_center_agent_id
                                LEFT OUTER JOIN origin_sales as origin ON origin.id = rez.origin_sale_id
                                LEFT OUTER JOIN types_cancellations as tc ON tc.id = rez.cancellation_type_id
                                LEFT OUTER JOIN vehicles as vehicle_one ON vehicle_one.id = it.vehicle_id_one
                                LEFT OUTER JOIN destination_services as d_one ON d_one.id = vehicle_one.destination_service_id
                                LEFT OUTER JOIN vehicles as vehicle_two ON vehicle_two.id = it.vehicle_id_two
                                LEFT OUTER JOIN destination_services as d_two ON d_two.id = vehicle_two.destination_service_id
                                LEFT OUTER JOIN drivers as driver_one ON driver_one.id = it.driver_id_one
                                LEFT OUTER JOIN drivers as driver_two ON driver_two.id = it.driver_id_two

                                LEFT OUTER JOIN (
                                    SELECT DISTINCT reservation_id
                                    FROM reservations_media
                                ) as upload ON upload.reservation_id = rez.id
                                LEFT OUTER JOIN (
                                    SELECT DISTINCT reservation_id
                                    FROM reservations_follow_up
                                    WHERE type IN ('CLIENT', 'OPERATION')
                                ) as rfu ON rfu.reservation_id = rez.id                                
                                LEFT JOIN (
                                        SELECT
                                            it.reservation_id,
                                            SUM(CASE WHEN it.is_round_trip = 1 THEN 2 ELSE 1 END) as quantity
                                        FROM reservations_items as it
                                        GROUP BY it.reservation_id
                                ) as it_counter ON it_counter.reservation_id = it.reservation_id
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
                                        ROUND(SUM(CASE 
                                            WHEN operation = 'multiplication' THEN total * exchange_rate
                                            WHEN operation = 'division' THEN total / exchange_rate
                                            ELSE total END), 2) AS total_payments,
                                        GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                    FROM payments
                                    GROUP BY reservation_id
                                ) as p ON p.reservation_id = rez.id
                            WHERE 1=1 {$queryOne}
                            GROUP BY it.id, rez.id, serv.id, site.id, zone_one.id, zone_two.id, us.name, upload.reservation_id, rfu.reservation_id, it_counter.quantity {$queryHaving}

                            UNION

                            SELECT 
                                rez.id as reservation_id,
                                CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name,
                                rez.client_email,
                                rez.client_phone,
                                rez.currency,
                                rez.language,
                                rez.is_cancelled,
                                rez.is_commissionable,
                                rez.site_id,
                                rez.pay_at_arrival,
                                rez.reference,
                                rez.affiliate_id,
                                rez.terminal,
                                rez.comments,
                                rez.is_duplicated,
                                rez.open_credit,
                                rez.is_complete,
                                rez.created_at,
                                us.name AS employee,
                                site.id as site_code,
                                site.name as site_name,
                                origin.code AS origin_code,
                                tc.name_es AS cancellation_reason,
                                CASE WHEN upload.reservation_id IS NOT NULL THEN 1 ELSE 0 END as pictures,
                                CASE WHEN rfu.reservation_id IS NOT NULL THEN 1 ELSE 0 END as messages,

                                CASE
                                    WHEN (rez.is_cancelled = 1) THEN 'CANCELLED'
                                    WHEN rez.open_credit = 1 THEN 'OPENCREDIT'
                                    WHEN rez.is_duplicated = 1 THEN 'DUPLICATED'
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'CONFIRMED'
                                    ELSE 'UNKNOWN'
                                END AS reservation_status,

                                'departure' as operation_type,                                  
                                'TYPE_TWO' as op_type,
                                it.id,
                                it.code,
                                it.flight_number,
                                it.is_round_trip,
                                it.passengers,
                                it.op_two_pickup as filtered_date,
                                zone_one.id as zone_one_id,
                                zone_one.name as destination_name_from,
                                it.from_name as from_name,
                                zone_one.is_primary as zone_one_is_primary, 
                                zone_one.cut_off as zone_one_cut_off,
                                it.op_one_status as one_service_status,
                                it.op_one_status_operation as one_service_operation_status,
                                it.op_one_time_operation,
                                it.op_one_preassignment,
                                it.op_one_operating_cost,
                                it.op_one_pickup as pickup_from,
                                it.op_one_operation_close,
                                it.op_one_comments,
                                it.vehicle_id_one,
                                it.driver_id_one,
                                zone_two.id as zone_two_id,
                                zone_two.name as destination_name_to,                                
                                it.to_name as to_name,
                                zone_two.is_primary as zone_two_is_primary, 
                                zone_two.cut_off as zone_two_cut_off,
                                it.op_two_status as two_service_status,
                                it.op_two_status_operation as two_service_operation_status,
                                it.op_two_time_operation,
                                it.op_two_preassignment,
                                it.op_two_operating_cost,
                                it.op_two_pickup as pickup_to,
                                it.op_two_operation_close,
                                it.op_two_comments,
                                it.vehicle_id_two,
                                it.driver_id_two,

                                CASE
                                    WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1  THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                    ELSE 'ARRIVAL'
                                END AS final_service_type,

                                serv.id as service_type_id,
                                serv.name as service_type_name,
                                vehicle_one.name as vehicle_one_name,
                                d_one.name as vehicle_name_one,
                                vehicle_two.name as vehicle_two_name,
                                d_two.name as vehicle_name_two,
                                CONCAT(driver_one.names,' ',driver_one.surnames) as driver_one_name,
                                CONCAT(driver_two.names,' ',driver_two.surnames) as driver_two_name,

                                it_counter.quantity,
                                GROUP_CONCAT(
                                    DISTINCT 
                                    CASE
                                        WHEN p.payment_type_name IS NOT NULL AND ( rez.pay_at_arrival = 0 OR rez.pay_at_arrival = 1 ) THEN p.payment_type_name
                                        ELSE 'CASH'
                                    END
                                ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) AS total_balance,
                                CASE
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) <= 0 THEN 'PAID'
                                    ELSE 'PENDING'
                                END AS payment_status,
                                CASE 
                                    WHEN it.is_round_trip = 1 THEN COALESCE(SUM(s.total_sales), 0) / 2
                                    ELSE COALESCE(SUM(s.total_sales), 0)
                                END AS service_cost,
                                CASE 
                                    WHEN it_counter.quantity > 0 THEN COALESCE(SUM(s.total_sales), 0) / it_counter.quantity
                                    ELSE 0
                                END AS cost                                
                            FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN sites as site ON site.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                LEFT OUTER JOIN users as us ON us.id = rez.call_center_agent_id
                                LEFT OUTER JOIN origin_sales as origin ON origin.id = rez.origin_sale_id
                                LEFT OUTER JOIN types_cancellations as tc ON tc.id = rez.cancellation_type_id
                                LEFT OUTER JOIN vehicles as vehicle_one ON vehicle_one.id = it.vehicle_id_one
                                LEFT OUTER JOIN destination_services as d_one ON d_one.id = vehicle_one.destination_service_id
                                LEFT OUTER JOIN vehicles as vehicle_two ON vehicle_two.id = it.vehicle_id_two
                                LEFT OUTER JOIN destination_services as d_two ON d_two.id = vehicle_two.destination_service_id
                                LEFT OUTER JOIN drivers as driver_one ON driver_one.id = it.driver_id_one
                                LEFT OUTER JOIN drivers as driver_two ON driver_two.id = it.driver_id_two

                                LEFT OUTER JOIN (
                                    SELECT DISTINCT reservation_id
                                    FROM reservations_media
                                ) as upload ON upload.reservation_id = rez.id
                                LEFT OUTER JOIN (
                                    SELECT DISTINCT reservation_id
                                    FROM reservations_follow_up
                                    WHERE type IN ('CLIENT', 'OPERATION')
                                ) as rfu ON rfu.reservation_id = rez.id
                                LEFT JOIN (
                                        SELECT
                                            it.reservation_id,
                                            SUM(CASE WHEN it.is_round_trip = 1 THEN 2 ELSE 1 END) as quantity
                                        FROM reservations_items as it
                                        GROUP BY it.reservation_id
                                ) as it_counter ON it_counter.reservation_id = it.reservation_id
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
                            WHERE 1=1 {$queryTwo}
                            GROUP BY it.id, rez.id, serv.id, site.id, zone_one.id, zone_two.id, us.name, upload.reservation_id, rfu.reservation_id, it_counter.quantity {$queryHaving}
                            ORDER BY filtered_date ASC ",[
                                "init_date_one" => $queryData['init'],
                                "init_date_two" => $queryData['end'],
                                "init_date_three" => $queryData['init'],
                                "init_date_four" => $queryData['end'],
                            ]);
    }

    public function queryConciliation($query, $query2, $queryData){
        $payments = DB::select("SELECT 
                                        rez.id as reservation_id,
                                        CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name,
                                        rez.client_email,
                                        rez.client_phone,
                                        rez.currency,
                                        rez.language,
                                        rez.is_cancelled,
                                        rez.is_commissionable,
                                        rez.site_id,
                                        rez.pay_at_arrival,
                                        rez.reference,
                                        rez.affiliate_id,
                                        rez.terminal,
                                        rez.comments,
                                        rez.is_duplicated,
                                        rez.open_credit,
                                        rez.is_complete,
                                        rez.created_at,
                                        p.id as code_payment,
                                        p.payment_method,
                                        p.description,
                                        p.total,
                                        p.currency as currency_payment,
                                        p.reference,
                                        p.is_conciliated,
                                        p.created_at as created_payment
                                    FROM payments as p
                                    INNER JOIN reservations as rez ON p.reservation_id = rez.id
                                    LEFT JOIN (
                                        SELECT 
                                            reservation_id, 
                                            ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                        FROM sales
                                            WHERE deleted_at IS NULL 
                                        GROUP BY reservation_id
                                    ) as s ON s.reservation_id = rez.id                                    
                                    WHERE 1=1 {$query} ",
                                    $queryData);

        return $payments;
    }    

    // CREATE INDEX `status_index` ON payments (`status`);
    // CREATE INDEX `payment_method_index` ON payments (`payment_method`);
    // CREATE INDEX `is_conciliated_index` ON payments (`is_conciliated`);
    // public function getPayPalPayments(){
    //    return DB::select("SELECT *
    //                         FROM payments AS p
    //                         WHERE p.payment_method = 'PAYPAL' AND p.status = 0 AND p.is_conciliated = 0 LIMIT 10");
    // }

    public function getPayPalPayments($offset, $limit) {
        return DB::select("SELECT *
                            FROM payments AS p
                            WHERE p.payment_method = 'PAYPAL' AND p.status = 0 AND p.is_conciliated = 0
                            LIMIT ?, ?", [$offset, $limit]);
    }    

    private function orderByDateTime($a, $b) {
        return strtotime($b->created_at) - strtotime($a->created_at);
    }    
}