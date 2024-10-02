<?php

namespace App\Traits;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait QueryTrait
{

    // pay_at_arrival: INDICA PAGO A LA LLEGADA PORQUE ELIGIO LA OPCION DE PAGO EN EFECTIVO
    public function queryBookings($query, $query2, $data){
        $bookings = DB::select("SELECT 
                                    rez.id AS reservation_id, 
                                    CONCAT(rez.client_first_name,' ',rez.client_last_name) as full_name,
                                    rez.client_email,
                                    rez.client_phone,
                                    rez.currency,
                                    rez.is_cancelled,
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
                                    -- CASE
                                    --     WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                    --     ELSE 'CONFIRMED'
                                    -- END AS status,

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

                                    GROUP_CONCAT(
                                        DISTINCT 
                                        CASE 
                                            -- WHEN p.payment_type_name IS NULL OR rez.pay_at_arrival = 1 THEN 'CASH' 
                                            -- ELSE p.payment_type_name
                                            WHEN p.payment_type_name IS NOT NULL AND ( rez.pay_at_arrival = 0 OR rez.pay_at_arrival = 1 ) THEN p.payment_type_name
                                            ELSE 'CASH'
                                        END
                                        ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name
                                    -- GROUP_CONCAT(DISTINCT p.payment_type_name ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,-- GROUP_CONCAT(DISTINCT p.payment_type_name ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name
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
                                            GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
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
                                    $data);

        if(sizeof( $bookings ) > 0):
            usort($bookings, array($this, 'orderByDateTime'));
        endif;

        return $bookings;
    }

    private function orderByDateTime($a, $b) {
        return strtotime($b->created_at) - strtotime($a->created_at);
    }
}