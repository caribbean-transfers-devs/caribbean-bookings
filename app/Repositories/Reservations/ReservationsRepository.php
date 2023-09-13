<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\ReservationFollowUp;
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
        
        $services = [];
        $db_services = DB::select("SELECT ds.id, dest.name as destination_name, IFNULL(dest_trans.translation, ds.name) AS service_name
                                FROM destination_services as ds
                                    INNER JOIN destinations as dest ON dest.id = ds.destination_id
                                    LEFT JOIN destination_services_translate as dest_trans ON dest_trans.destination_services_id = ds.id AND dest_trans.lang = 'es'
                                    ORDER BY ds.order ASC");        
        if(sizeof($db_services) >=1 ):
            foreach( $db_services as $key => $value ):
                if( !isset(  $services[ $value->destination_name ] ) ) $services[ $value->destination_name ] = [];
                $services[ $value->destination_name ][] = $value;
            endforeach;            
        endif;

        $zones = [];
        $db_zones = DB::select("SELECT dest.name as destination_name, z.id, z.name as zone_name
                                FROM zones as z
                                    INNER JOIN destinations as dest ON dest.id = z.destination_id
                                ORDER BY z.name ASC");
        if(sizeof($db_zones) >=1 ):
            foreach( $db_zones as $key => $value ):
                if( !isset(  $zones[ $value->destination_name ] ) ) $zones[ $value->destination_name ] = [];
                $zones[ $value->destination_name ][] = $value;
            endforeach;            
        endif;

        $websites = DB::select("SELECT id, name as site_name
                                FROM sites
                                ORDER BY site_name ASC");
        // echo "<pre>";
        // print_r($sites);
        // die();

        return view('reservations.index', compact('bookings','services','zones','websites') );

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

    public function update($request,$reservation){
        try{
            DB::beginTransaction();
            $reservation->client_first_name = $request->client_first_name;
            $reservation->client_last_name = $request->client_last_name;
            $reservation->client_email = $request->client_email;
            $reservation->client_phone = $request->client_phone;
            $reservation->currency = $request->currency;
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'Se editaron datos de la reserva por '.auth()->user()->name, 'HISTORY', 'EDICIÓN');
            DB::commit();
            return response()->json(['message' => 'Reservation updated successfully', 'success' => true], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error editing reservation', 'success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($request, $reservation)
    {
        try {
            DB::beginTransaction();
            $reservation->is_cancelled = 1;
            $reservation->save();
            $reservation->items()->update(['op_one_status' => 'CANCELLED', 'op_two_status' => 'CANCELLED']);
            $check = $this->create_followUps($reservation->id, 'SE CANCELO LA RESERVA POR '.auth()->user()->name, 'HISTORY', 'CANCELACIÓN');
            DB::commit();
            return response()->json(['message' => 'Reservation cancelled successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling reservation'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function follow_ups($request)
    {        
        $check = $this->create_followUps($request->reservation_id, $request->text, $request->type, $request->name);
        if($check){
            return response()->json(['message' => 'Follow up created successfully','success' => true], Response::HTTP_OK);
        }else{
            return response()->json(['message' => 'Error creating follow up','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }   

    public function create_followUps($reservation_id, $text, $type, $name = null)
    {
        $follow_up = new ReservationFollowUp();
        $follow_up->reservation_id = $reservation_id;
        $follow_up->text = $text;
        $follow_up->type = $type;
        $follow_up->name = $name;
        $follow_up->save();

        return $follow_up->id;
    }
}