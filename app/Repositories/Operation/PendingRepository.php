<?php

namespace App\Repositories\Operation;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFile;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;
use Illuminate\Support\Facades\Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class PendingRepository
{
    public function get($request){

        $items = DB::select("SELECT 
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
                                site.type_site AS type_site,
                                us.name AS employee,
                                usc.name AS employee_after_sale,
                                usp.name AS employee_pull,                                
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
                                END AS payment_status                                
                            FROM reservations as rez
                                INNER JOIN sites as site ON site.id = rez.site_id
                                LEFT OUTER JOIN users as us ON us.id = rez.call_center_agent_id
                                LEFT OUTER JOIN users as usc ON usc.id = rez.agent_id_after_sales
                                LEFT OUTER JOIN users as usp ON usp.id = rez.agent_id_pull_sales
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
                                WHERE 1=1 AND DATE(rez.created_at) = :date AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0
                            GROUP BY rez.id, site.type_site, site.name
                                    HAVING payment_status = :status ORDER BY rez.created_at DESC;", ["date" => date("Y-m-d"), "status" => "PENDING"]);

        return view('management.pending.view', [ "items" => $items ]);
    }
}