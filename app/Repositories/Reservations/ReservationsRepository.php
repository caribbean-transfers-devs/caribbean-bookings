<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\ReservationsService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReservationsRepository
{
    public function index($request)
    {   

        $date = [
            "init" => date("Y-m-d"),
            "end" => date("Y-m-d")
        ];

        $bookings = DB::select("SELECT 
                                    rez.id, rez.created_at, CONCAT(rez.client_first_name,' ',rez.client_last_name) as client_full_name, rez.client_email, rez.currency, rez.is_cancelled, 
                                    rez.pay_at_arrival,
                                    SUM(s.total_sales) as total_sales,
                                    SUM(p.total_payments) as total_payments,
                                    CASE
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                        ELSE 'CONFIRMED'
                                    END AS status,
                                    site.name as site_name,
                                    GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                                    GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name,
                                    GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                                    SUM(it.passengers) as passengers,
                                    GROUP_CONCAT(DISTINCT p.payment_type_name ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name
                                FROM reservations as rez
                                    INNER JOIN sites as site ON site.id = rez.site_id
                                    LEFT JOIN (
                                        SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                        FROM sales
                                        GROUP BY reservation_id
                                    ) as s ON s.reservation_id = rez.id
                                    LEFT JOIN (
                                        SELECT reservation_id, ROUND( COALESCE(SUM(total * exchange_rate), 0), 2) as total_payments,
                                        GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                        FROM payments
                                        GROUP BY reservation_id
                                    ) as p ON p.reservation_id = rez.id
                                    LEFT JOIN (
                                        SELECT 
                                            it.reservation_id, it.passengers, it.code, zone_one.name as zone_one_name, zone_two.name as zone_two_name, it.is_round_trip, dest.name as service_type_name
                                        FROM reservations_items as it
                                        INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                        INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                        INNER JOIN destination_services as dest ON dest.id = it.destination_service_id
                                    ) as it ON it.reservation_id = rez.id
                                WHERE rez.created_at BETWEEN :init AND :end
                                GROUP BY rez.id, site.name",
                                    [
                                        'init' => $date['init'].' 00:00:00',
                                        'end' => $date['end'].' 23:59:59',
                                    ]);

        return view('reservations.index', compact('bookings') );

        /*if(!$request->lookup_date){
            $from = date('Y-m-d');
            $to = date('Y-m-d');

            $services = ReservationsService::select('reservation_item_id')->whereDate('pickup', '>=', $from)
                ->whereDate('pickup', '<=', $to)
                ->pluck('reservation_item_id');
            $items = ReservationsItem::select('reservation_id')->whereIn('id', $services)->pluck('reservation_id');
            $reservations = Reservation::whereIn('id',$items)->with('destination', 'items')->get();
            return view('reservations.index', compact('reservations','from','to'));
        }else{
            if(strlen($request->lookup_date) <= 10){
                $from = $request->lookup_date;
                $to = $request->lookup_date;
            }else{
                $dates = explode(' a ', $request->lookup_date);
                $from = $dates[0];
                $to = $dates[1];
            }          

            $services = ReservationsService::select('reservation_item_id')->whereDate('pickup', '>=', $from)
                ->whereDate('pickup', '<=', $to)
                ->pluck('reservation_item_id');
            $items = ReservationsItem::select('reservation_id')->whereIn('id', $services)->pluck('reservation_id');
            $reservations = Reservation::whereIn('id',$items)->with('destination', 'items')->get();
            return view('reservations.index', compact('reservations','from','to'));
        } */     
    }
}