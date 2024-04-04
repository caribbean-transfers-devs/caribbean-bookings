<?php

namespace App\Repositories\Pos;

use App\Models\Autocomplete;
use App\Models\Destination;
use App\Models\Zones;
use Illuminate\Http\Response;
use App\Models\DestinationService;
use App\Models\Payment;
use App\Models\Vendor;
use App\Models\Reservation;
use App\Models\ReservationFollowUp;
use App\Models\ReservationsItem;
use App\Models\Sale;
use App\Models\SalesType;
use App\Models\Site;
use App\Models\TerminalPaymentExchangeRate;
use App\Models\User;
use App\Models\Clip;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\CodeTrait;

class PosRepository
{
    use CodeTrait;

    public function index($request){
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
                ( rez.reference like '%".$data['filter_text']."%') OR
                ( it.code like '".$data['filter_text']."' )
            )";            
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
            WHERE 1=1 AND vendor_id IS NOT NULL {$query}
            GROUP BY rez.id, site.name",
        $queryData);

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

        return view('pos.index', compact('bookings','services','zones','websites','data') );
    }

    public function detail($request,$id){
        $reservation = Reservation::with('destination','items','sales', 'callCenterAgent','payments','followUps','site', 'user', 'vendor', 'clip')->whereNotNull('vendor_id')->where('id', $id)->first();
        if( !$reservation ) abort(404);

        $users_ids = UserRole::where('role_id', 3)->orWhere('role_id',4)->pluck('user_id');
        $sellers = User::whereIn('id', $users_ids)->get();
        
        $sales_types = SalesType::all();
        $services_types = DestinationService::where('status',1)->where('destination_id',$reservation->destination_id)->get();
        $sites = Site::get();

        //Sumamos las ventas y restamos pagos para saber si la reserva está confirmada o no..
        $data = [
            "status" => "PENDING",
            "total_sales" => 0,
            "total_payments" => 0,
        ];

        foreach( $reservation->sales as $sale ):
            $data['total_sales'] += $sale->total;            
        endforeach;

        foreach( $reservation->payments as $payment ):
            if($payment->operation == "multiplication"):
                $data['total_payments'] += ($payment->total * $payment->exchange_rate);
            endif;
            if($payment->operation == "division"):
                $data['total_payments'] += ($payment->total / $payment->exchange_rate);
            endif;                      
        endforeach;

        if( round( $data['total_payments'], 2) >= round( $data['total_sales'], 2) ):
            $data['status'] = "CONFIRMED";
        endif;
        if($reservation->is_cancelled == 1):
            $data['status'] = "CANCELLED";
        endif;

        // return $reservation;

        return view('pos.detail', compact('reservation','sellers','sales_types','services_types','data','sites'));
    }

    public function capture($request){
        $destination_services =  DestinationService::all();
        $zones = Zones::all();
        $vendors =  Vendor::where('status', 1)->get();
        $clips =  Clip::where('status', 1)->get();
        $currency_exchange_data = TerminalPaymentExchangeRate::all();
        $currency_exchange_data = json_encode($currency_exchange_data->toArray());

        return view('pos.capture', compact('destination_services', 'zones', 'clips', 'vendors', 'currency_exchange_data'));
    }

    public function create($request){
        $duplicated_reservation = Reservation::where('reference', $request->folio)->count();
        if( $duplicated_reservation ) return response()->json(['message' => 'Ese folio ya ha sido registrado','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR); 

        $default_site_id = 11; // Considerando que el id corresponde a: "[APTO] caribbean-taxi.com"
        $site = Site::find( $default_site_id );

        $default_destination_id = 1; // Considerando que el id corresponde a: "Cancún"
        $destination = Destination::find( $default_destination_id );

        $from_zone = Zones::find( $request->from_zone_id );
        $from_autocomplete = Autocomplete::where('name', 'like', $from_zone->name)->first();
        // if( !$from_autocomplete ) return response()->json(['message' => 'No se pudo obtener la latitud y longitud del autocomplete','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);

        $to_zone = Zones::find( $request->to_zone_id );
        $to_autocomplete = Autocomplete::where('name', 'like', $to_zone->name)->first();
        // if( !$to_autocomplete ) return response()->json(['message' => 'No se pudo obtener la latitud y longitud del autocomplete','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);

        $destination_service = DestinationService::find( $request->destination_service_id );
        if( !$destination_service ) return response()->json(['message' => 'No se encontró el vehículo','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);

        // Obteniendo pagos
        $payments = [];
        for($i = 0; $i < $request->number_of_payments; $i++) {
            $payment_method_variable = "payment_method_$i";
            $clip_id_variable = "clip_id_$i";
            $payment_variable = "payment_$i";
            $currency_variable = "currency_$i";

            $terminal_exchange_rate = TerminalPaymentExchangeRate::where('terminal', $request->terminal)
            ->where('origin', $request->$currency_variable)
            ->where('destination', $request->sold_in_currency)
            ->first();
            if( !$terminal_exchange_rate ) return response()->json(['message' => 'No se pudo convertir el cambio de monera, posiblemente necesites agregar el caso de conversión que estás solicitando','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);

            $payments[] = [
                'payment_method' => $request->$payment_method_variable,
                'clip_id' => $request->$clip_id_variable,
                'total' => $request->$payment_variable,
                'operation' =>  $terminal_exchange_rate->operation,
                'exchange_rate' =>  $terminal_exchange_rate->exchange_rate,
            ];
        }
        if( sizeof($payments) === 0 ) return response()->json(['message' => 'Se necesitan agregar pagos para la captura','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR); 


        try {
            DB::transaction(function () use ($request, $site, $default_destination_id, $default_site_id, $destination, $from_autocomplete, $from_zone, $to_autocomplete, $to_zone, $destination_service, $payments) {
                // Creando reservación
                $reservation = new Reservation;
                $reservation->client_first_name = $request->client_first_name;
                $reservation->client_last_name = $request->client_last_name;
                $reservation->client_email = $request->client_email ? $request->client_email : null;
                $reservation->client_phone = $request->client_phone ? $request->client_phone : null;
                $reservation->currency = $request->sold_in_currency;
                $reservation->rate_group = 'xLjDl18';
                $reservation->pay_at_arrival = 1; // pendiente por checar
                $reservation->site_id = $site ? $default_site_id : null;
                $reservation->destination_id = $destination ? $default_destination_id : null;
                $reservation->vendor_id = $request->vendor_id;
                $reservation->user_id = auth()->user()->id;
                $reservation->reference = $request->folio; // Pendiente
                $reservation->created_at = Carbon::now();
                $reservation->updated_at = Carbon::now();
                $reservation->save();
        
                // Creando follow_up
                $follow_up = new ReservationFollowUp();
                $follow_up->reservation_id = $reservation->id;
                $follow_up->name = 'Captura';
                $follow_up->text = 'Se capturó la venta (POS)';
                $follow_up->type = 'HISTORY';
                $follow_up->save();
        
                // Creando item de reservación
                $item = new ReservationsItem();
                $item->reservation_id = $reservation->id;
                $item->code = $this->generateCode(); // Pendiente
                $item->destination_service_id = $request->destination_service_id;
                $item->from_name = $request->from_name ? $request->from_name : $from_zone->name;
                $item->from_lat = ($from_autocomplete && $from_autocomplete->latitude) ? $from_autocomplete->latitude : '';
                $item->from_lng = ($from_autocomplete && $from_autocomplete->longitude) ? $from_autocomplete->longitude : '';
                $item->from_zone = $from_zone->id;
                $item->to_name = $request->to_name ? $request->to_name : $to_zone->name;
                $item->to_lat = ($to_autocomplete && $to_autocomplete->latitude) ? $to_autocomplete->latitude : '';
                $item->to_lng = ($to_autocomplete && $to_autocomplete->longitude) ? $to_autocomplete->longitude : '';
                $item->to_zone = $to_zone->id;
                $item->distance_time = $to_zone->time ? $this->timeToSeconds( $to_zone->time ) : 0;
                $item->distance_km = $to_zone->distance ? $to_zone->distance : '';
                $item->is_round_trip = $request->is_round_trip;
                $item->passengers = $request->passengers;
                $item->op_one_status = 'NOSHOW';
                $item->op_two_status = 'NOSHOW';
                $item->created_at = Carbon::now();
                $item->updated_at = Carbon::now();
                $item->save();
        
                // Creando Sale
                $sale = new Sale();
                $sale->reservation_id = $reservation->id;
                $sale->description = $destination_service->name . ' | ' . ($request->is_round_trip ? 'Round Trip' : 'One Way');
                $sale->quantity = 1;
                $sale->total = $request->total;
                $sale->created_at = Carbon::now();
                $sale->updated_at = Carbon::now();
                $sale->save();

                // Creando Payments
                foreach($payments as $_payment) {
                    $payment = new Payment();
                    $payment->description = 'Panel'; // Pendiente
                    $payment->total = $_payment['total'];
                    $payment->exchange_rate = $_payment['exchange_rate'];
                    $payment->status = 0; // Pendiente
                    $payment->operation = $_payment['operation'];
                    $payment->payment_method = $_payment['payment_method'];
                    $payment->currency = $request->sold_in_currency;
                    $payment->clip_id = $_payment['payment_method'] === 'CARD' ? $_payment['clip_id'] : null;
                    $payment->reservation_id = $reservation->id;
                    $payment->reference = $request->reference ? $request->reference : null;
                    $payment->created_at = Carbon::now();
                    $payment->updated_at = Carbon::now();
                    $payment->save();
                }
            });
        } catch(\Exception $e) {
            return response()->json(['message' => 'Ocurrió un error al insertar la información a la base de datos','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'sale created successfully','success' => true], Response::HTTP_OK);
    }

    public function vendors($request){
        $vendors = Vendor::all();

        return view('pos.vendors', compact('vendors'));
    }

    public function createVendor($request){
        $vendor = new Vendor();

        $vendor->fill( $request->all() );
        $result = $vendor->save();

        if( !$result ) return response()->json(['message' => 'Ocurrió un error al insertar la información a la base de datos','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        return response()->json(['vendor' => $vendor, 'message' => 'Vendor created successfully','success' => true], Response::HTTP_OK);
    }

    public function editVendor($request){
        $vendor = Vendor::findOrFail($request->id);

        $vendor->fill( $request->all() );
        $result = $vendor->save();

        if( !$result ) return response()->json(['message' => 'Ocurrió un error al guardar la información en la base de datos','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        return response()->json(['message' => 'Vendor edited successfully','success' => true], Response::HTTP_OK);
    }

    public function deleteVendor($request){
        $vendor = Vendor::findOrFail($request->id);
        $result = $vendor->delete();

        if( !$result ) return response()->json(['message' => 'Ocurrió un error al borrar la información de la base de datos','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        return response()->json(['message' => 'Se eliminó el vendedor correctamente','success' => true], Response::HTTP_OK);
    }

    private function timeToSeconds($time) {
        $parts = explode(' ', $time);
        
        $hours = 0;
        $minutes = 0;
        
        foreach ($parts as $key => $part) {
            if ($part == 'H') {
                $hours = (int)$parts[$key - 1];
            } elseif ($part == 'Min') {
                $minutes = (int)$parts[$key - 1];
            }
        }
        
        $seconds = $hours * 3600 + $minutes * 60;
        
        return $seconds;
    }

}
