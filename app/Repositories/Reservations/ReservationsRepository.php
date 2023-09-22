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
        $data = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
            "filter_text" => NULL,
            "product_type" => 0,
            "zone" => 0,
            "site" => 0,
            "payment_method" => NULL
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end';
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
        if(isset( $request->site ) && $request->site != 0){
            $data['site'] = $request->site;
            $query .= ' AND site.id = :site';
            $queryData['site'] = $data['site'];
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
                        ( it.code like '".$data['filter_text']."' )
                    )";            
        }         
        
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
                                    GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
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
                                        SELECT reservation_id,
                                        ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                                    WHEN operation = 'division' THEN total / exchange_rate
                                                            ELSE total END), 2) AS total_payments,
                                        GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                        FROM payments
                                        GROUP BY reservation_id
                                    ) as p ON p.reservation_id = rez.id
                                    LEFT JOIN (
                                        SELECT 
                                            it.reservation_id, it.passengers, it.code, zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_two.id as zone_two_id, zone_two.name as zone_two_name, it.is_round_trip, 
                                            dest.id as service_type_id,
                                            dest.name as service_type_name
                                        FROM reservations_items as it
                                        INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                        INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                        INNER JOIN destination_services as dest ON dest.id = it.destination_service_id
                                    ) as it ON it.reservation_id = rez.id
                                WHERE 1=1 {$query}
                                GROUP BY rez.id, site.name",
                                    $queryData);
        // echo "<pre>";
        // print_r($bookings);
        // die();

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

        return view('reservations.index', compact('bookings','services','zones','websites','data') );

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

    public function get_exchange($request, $reservation)
    {
        $currency = $request->currency;
        $to_currency = $reservation->currency;
        $exchange = DB::table('payments_exchange_rate')->where('origin',$currency)->where('destination',$to_currency)->first();
        return $exchange;
    }

    public function editreservitem($request, $item)
    {
        try {
            DB::beginTransaction();
            $item->destination_service_id = $request->destination_service_id;
            $item->passengers = $request->passengers;
            $item->from_name = $request->from_name;
            $item->to_name = $request->to_name;
            $item->flight_number = $request->flight_number;
            if($request->from_lat){
                $item->from_lat = $request->from_lat;
                $item->from_lng = $request->from_lng;
            }
            if($request->to_lat){
                $item->to_lat = $request->to_lat;
                $item->to_lng = $request->to_lng;
            }
            $item->op_one_pickup = $request->op_one_pickup;
            $item->op_two_pickup = $request->op_two_pickup ?? null;
            $item->save();
            DB::commit();
            return response()->json(['message' => 'Item updated successfully', 'success' => true], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating item', 'success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}