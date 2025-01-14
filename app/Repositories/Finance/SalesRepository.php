<?php

namespace App\Repositories\Finance;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS
use App\Models\Payment;

//TRAITS
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\PayPalTrait;
use App\Traits\StripeTrait;

class SalesRepository
{
    use FiltersTrait;

    public function index($request)
    {

        $payment_paypal = DB::select("SELECT 
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

                                p.id as code_payment,
                                p.payment_method,
                                p.description,
                                p.exchange_rate,
                                p.total,
                                p.total_fee,
                                p.total_net,
                                p.currency as currency_payment,
                                p.reference,
                                p.is_conciliated,
                                p.is_refund,
                                p.conciliation_comment,
                                p.created_at as created_payment,
                                p.updated_at as updated_payment,
                                p.date_conciliation as conciliation_payment,                                

                                site.type_site AS type_site,
                                site.name AS site_name,

                                GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                                -- GROUP_CONCAT(DISTINCT it.zone_one_name ORDER BY it.zone_one_name ASC SEPARATOR ',') AS destination_name_from,
                                -- GROUP_CONCAT(DISTINCT it.zone_one_id ORDER BY it.zone_one_id ASC SEPARATOR ',') AS zone_one_id,
                                -- GROUP_CONCAT(DISTINCT it.from_name SEPARATOR ',') AS from_name,
                                -- GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name_to,
                                -- GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                                -- GROUP_CONCAT(DISTINCT it.to_name SEPARATOR ',') AS to_name,
                                -- GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
                                -- GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                                -- GROUP_CONCAT(DISTINCT it.pickup_from ORDER BY it.pickup_from ASC SEPARATOR ',') AS pickup_from,
                                -- GROUP_CONCAT(DISTINCT it.pickup_to ORDER BY it.pickup_to ASC SEPARATOR ',') AS pickup_to,
                                -- GROUP_CONCAT(DISTINCT it.one_service_status ORDER BY it.one_service_status ASC SEPARATOR ',') AS one_service_status,
                                -- GROUP_CONCAT(DISTINCT it.two_service_status ORDER BY it.two_service_status ASC SEPARATOR ',') AS two_service_status,
                                -- SUM(it.passengers) as passengers,
                                it.op_one_pickup_today + it.op_two_pickup_today as is_today,
                                it.is_round_trip as is_round_trip,                                
                                s.total_sales as total_sales,
                                CASE
                                    WHEN (rez.is_cancelled = 1) THEN 'CANCELLED'
                                    WHEN rez.open_credit = 1 THEN 'OPENCREDIT'
                                    WHEN rez.is_duplicated = 1 THEN 'DUPLICATED'
                                    WHEN s.total_sales - p.total > 0 THEN 'PENDING'
                                    WHEN s.total_sales - p.total <= 0 THEN 'CONFIRMED'                                    
                                    ELSE 'UNKNOWN'
                                END AS reservation_status,
                                CASE
                                    WHEN s.total_sales - p.total <= 0 THEN 'PAID'
                                    ELSE 'PENDING'
                                END AS payment_status
                            FROM payments as p
                                INNER JOIN reservations as rez ON p.reservation_id = rez.id
                                INNER JOIN sites as site ON site.id = rez.site_id
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
                                WHERE 1=1 AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND p.payment_method = :method
                                GROUP BY rez.id, site.type_site, site.name, rez.client_first_name, rez.client_last_name,
                                rez.client_email, rez.client_phone, rez.currency, rez.is_cancelled,
                                rez.is_commissionable, rez.site_id, rez.pay_at_arrival, rez.reference,
                                rez.affiliate_id, rez.terminal, rez.comments, rez.is_duplicated,
                                rez.open_credit, rez.is_complete, rez.created_at,
                                p.id, p.payment_method, p.description, p.exchange_rate, p.total,
                                p.total_fee, p.total_net, p.currency, p.reference, p.is_conciliated,
                                p.is_refund, p.conciliation_comment, p.created_at, p.updated_at,
                                p.date_conciliation, it.op_one_pickup_today, it.op_two_pickup_today,
                                it.is_round_trip, s.total_sales
                                HAVING is_today != 0 ORDER BY rez.created_at DESC", [
                                    "init" => "2024-12-01 00:00:00", 
                                    "end" => "2024-12-31 23:59:59", 
                                    "method" => "PAYPAL"
                                ]);

                                // dd($payment_paypal);

        return view('finance.sales', [
            "paypal" => $payment_paypal,
            // 'exchange' => $this->Exchange(( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ), ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") )),
            'exchange' => $this->Exchange("2024-12-01 00:00:00", "2024-12-31 23:59:59"),
        ]);
    }    
}