<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\ReservationFollowUp;
use App\Models\ReservationsItem;
use App\Models\ReservationsService;
use App\Models\Payment;
use App\Models\OriginSale;
use App\Models\ContactPoints;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\MailjetTrait;

class ReservationsRepository
{
    use MailjetTrait;
    
    public function index($request)
    {
        // dump($request->input());
        $data = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
            "filter_text" => NULL,
            "product_type" => 0,
            "zone" => 0,
            "site" => 0,
            "origin" => 0,
            "payment_method" => NULL,
            "is_today" => 0,
        ];
        
        //Query DB
        $query = ' AND rez.site_id NOT IN(21) AND rez.created_at BETWEEN :init AND :end AND rez.is_duplicated = 0 ';
        $queryData = [
            'init' => date("Y-m-d") . " 00:00:00",
            'end' => date("Y-m-d") . " 23:59:59",
        ];

        $query2 = '';
        if(isset( $request->is_today ) && !empty( $request->is_today)){
            $data['is_today'] = $request->is_today;            
            $query2 = ' HAVING is_today != 0 ';
        }
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
        if(isset( $request->origin ) && $request->origin != 0){
            $data['origin'] = $request->origin;
            $query .= ' AND original.id = :origin';
            $queryData['origin'] = $data['origin'];
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
                                    rez.id, 
                                    CONCAT(rez.client_first_name,' ',rez.client_last_name) as full_name, 
                                    rez.client_email, 
                                    rez.currency, 
                                    rez.is_cancelled, 
                                    rez.is_duplicated, 
                                    rez.affiliate_id, 
                                    rez.pay_at_arrival, 
                                    rez.open_credit,
                                    rez.created_at, 
                                    COALESCE(SUM(s.total_sales), 0) as total_sales,
                                    COALESCE(SUM(p.total_payments), 0) as total_payments,
                                    CASE
                                        WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                        ELSE 'CONFIRMED'
                                    END AS status,                                    
                                    site.name as site_name,
                                    origin.code as origin_code,
                                    GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                                    GROUP_CONCAT(DISTINCT it.zone_one_name ORDER BY it.zone_one_name ASC SEPARATOR ',') AS destination_name_from,
                                    GROUP_CONCAT(DISTINCT it.zone_one_id ORDER BY it.zone_one_id ASC SEPARATOR ',') AS zone_one_id,
                                    GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name_to,
                                    GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
                                    GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                                    SUM(it.passengers) as passengers,
                                    GROUP_CONCAT(DISTINCT p.payment_type_name ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                                    COALESCE(SUM(it.op_one_pickup_today) + SUM(it.op_two_pickup_today), 0) as is_today
                                FROM reservations as rez
                                    INNER JOIN sites as site ON site.id = rez.site_id
                                    LEFT OUTER JOIN origin_sales as origin ON origin.id = rez.origin_sale_id
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
                                        SELECT  
                                            it.reservation_id, 
                                            it.is_round_trip,
                                            SUM(it.passengers) as passengers,
                                            GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS code,
                                            GROUP_CONCAT(DISTINCT zone_one.name ORDER BY zone_one.name ASC SEPARATOR ',') AS zone_one_name,
                                            GROUP_CONCAT(DISTINCT zone_one.id ORDER BY zone_one.id ASC SEPARATOR ',') AS zone_one_id, 
                                            GROUP_CONCAT(DISTINCT zone_two.name ORDER BY zone_two.name ASC SEPARATOR ',') AS zone_two_name, 
                                            GROUP_CONCAT(DISTINCT zone_two.id ORDER BY zone_two.id ASC SEPARATOR ',') AS zone_two_id, 
                                            GROUP_CONCAT(DISTINCT dest.id ORDER BY dest.id ASC SEPARATOR ',') AS service_type_id, 
                                            GROUP_CONCAT(DISTINCT dest.name ORDER BY dest.name ASC SEPARATOR ',') AS service_type_name,
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
                                GROUP BY rez.id, site.name, site.type_site
                                {$query2}",
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

        $origin_sales = OriginSale::All();

        
        if(sizeof( $bookings ) > 0):
            usort($bookings, array($this, 'orderByDateTime'));
        endif;

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Reservaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                "active" => true
            ),
        );
        
        return view('reservations.index', compact('bookings','services','zones','websites','origin_sales','data','breadcrumbs') );
    }

    public function update($request,$reservation){
        try{
            DB::beginTransaction();
            $payments = Payment::where('reservation_id', $reservation->id)->get();

            $reservation->client_first_name = $request->client_first_name;
            $reservation->client_last_name = $request->client_last_name;
            $reservation->client_email = $request->client_email;
            $reservation->client_phone = $request->client_phone;
            $reservation->site_id = $request->site_id;
            $reservation->reference = $request->reference;
            $reservation->currency = $request->currency;
            if ( ($request->site_id == 11 || $request->site_id == 21) && $request->vendor_id == NULL && $request->terminal == NULL && count($payments) == 0 ) {
                $reservation->is_complete = 0;
            }
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
            ( isset($request->type) ? $reservation->cancellation_type_id = $request->type : '' );
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

    public function duplicated($request, $reservation)
    {
        try {            
            DB::beginTransaction();
            $reservation->is_duplicated = 1;
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'SE MARCO COMO DUPLICADA RESERVA POR '.auth()->user()->name, 'HISTORY', 'DUPLICADA');
            DB::commit();
            return response()->json(['message' => 'Reservation duplicated successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling reservation'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function openCredit($request, $reservation)
    {
        try {            
            DB::beginTransaction();
            $reservation->open_credit = 1;
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'Se marco como Crédito Abierto por '.auth()->user()->name, 'HISTORY', 'CRÉDITO ABIERTO');
            DB::commit();
            return response()->json(['message' => 'Update successfully completed'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error marking as open credit'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function enableReservation($request, $reservation)
    {
        try {            
            DB::beginTransaction();
            $reservation->is_cancelled = 0;
            $reservation->is_duplicated = 0;
            $reservation->items()->update(['op_one_status' => 'PENDING', 'op_two_status' => 'PENDING']);
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'Se ha activado la reservación por: '.auth()->user()->name, 'HISTORY', 'DUPLICADA');
            DB::commit();
            return response()->json(['message' => 'Reservation duplicated successfully'], Response::HTTP_OK);
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
            $item->from_zone = $request->from_zone_id;
            if($request->from_lat){
                $item->from_lat = $request->from_lat;
                $item->from_lng = $request->from_lng;
            }
            $item->to_zone = $request->to_zone_id;
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

    public function getContactPoints($request){
        $contact_points = ContactPoints::where('destination_id', $request['destination_id'] )->get();
        return response()->json($contact_points, Response::HTTP_OK);
    }

    public function sendArrivalConfirmation($request){

        $lang = $request['lang'];
        $point_id = $request['terminal_id'];

        if($request['terminal_id'] == 0):
            return response()->json(['message' => 'Es necesario seleccionar un punto', 'success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        endif;            

        $item = DB::select("SELECT it.code, it.from_name, it.to_name, it.flight_number, it.passengers, it.op_one_pickup, it.op_two_pickup, rez.client_first_name, rez.client_email, sit.transactional_phone, rez.id as reservation_id
                                FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                    WHERE it.id = :id", ['id' => $request['item_id'] ]);

        $point = DB::table('contact_points as cp')
                        ->select(DB::raw('cp.name as point_name, IFNULL(cp_translate.translation, cp.point_description) AS point_description'))
                        ->leftJoin('contact_points_translate as cp_translate', function ($join) use($lang) {
                            $join->on('cp_translate.contact_point_id', '=', 'cp.id')
                                ->where('cp_translate.lang', '=', $lang );
                        })->where('cp.id', '=', $point_id)->get();
                        
        $message = $this->arrivalMessage($lang, $item[0], $point[0]);
        
        //Data to send in confirmation..
        $email_data = array(
            "Messages" => array(
                array(
                    "From" => array(
                        "Email" => 'bookings@caribbean-transfers.com',
                        "Name" => "Bookings"
                    ),
                    "To" => array(
                        array(
                            "Email" => $item[0]->client_email,
                            "Name" => $item[0]->client_first_name,
                        )
                    ),
                    "Bcc" => array(
                        array(
                            "Email" => 'bookings@caribbean-transfers.com',
                            "Name" => "Bookings"
                        )
                    ),
                    "Subject" => (($lang == "en")?'Service confirmation message':'Mensaje de confirmación de servicio'),
                    "TextPart" => (($lang == "en")?'Dear client':'Estimado cliente'),
                    "HTMLPart" => $message
                )
            )
        );

        $email_response = $this->sendMailjet($email_data);

        if(isset($email_response['Messages'][0]['Status']) && $email_response['Messages'][0]['Status'] == "success"):
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = 'Sistema';
            $follow_up_db->text = 'E-mail enviado (confirmación de llegada) por '.auth()->user()->name;
            $follow_up_db->type = 'INTERN';
            $follow_up_db->reservation_id = $item[0]->reservation_id;
            $follow_up_db->save();

            return response()->json(['status' => "success"], 200);
        else:
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = 'Sistema';
            $follow_up_db->text = 'No fue posible enviar el e-mail de confirmación de llegada, por favor contactar a Desarrollo';
            $follow_up_db->type = 'INTERN';
            $follow_up_db->reservation_id = $item[0]->reservation_id;
            $follow_up_db->save();
            
            return response()->json([
                'error' => [
                    'code' => 'mailing_system',
                    'message' => 'The mailing platform has a problem, please report to development'
                ]
            ], 404);
        endif;
    }    

    public function arrivalMessage($lang = "en", $item = [], $point = []){         
        $arrival_date = date("Y-m-d H:i", strtotime($item->op_one_pickup));
        if($lang == "en"):
            return <<<EOF
                    <p>Arrival confirmation</p>
                    <p>Before boarding, you will be asked to show photo identification of the cardholder of the card with which the payment was made.</p>
                    <p>This is your reservation voucher, please verify that the following information is correct.</p>
                    <p>Dear $item->client_first_name | Reservation No: $item->code.</p>
                    <p>Thank you for choosing Caribbean Transfers, we appreciate your confidence, the information below will facilitate your contact with our staff at the airport, flight $item->flight_number lands at $point->point_name on $arrival_date hrs therefore our representative will be waiting for you with a Caribbean Transfers identifier.</p>
                    <p>To facilitate contact, please turn on your cell phone as soon as you land, you can use the free WIFI network at the airport to contact us. Let us know when you are ready to board your unit (after clearing customs and collecting your bags), a representative will be ready to meet you and take you to your assigned unit.</p>
                    <p>Please confirm receipt</p>
                    <p>Thank you for your confidence, have a great trip.</p>
                    <p>*In case you require additional assistance, please send a message to the number $item->transactional_phone</p>
                    <p>Tips not included</p>
                    <p>All company personnel are identified with badges and uniforms, please do not pay attention to scam attempts as these payments will not be reimbursed</p>
            EOF;
        else:
            return <<<EOF
                <p>Confirmación de llegada</p>
                <p>Antes de abordar se le solicitará la identificación con fotografía del titular de la tarjeta con la que se realizó el pago</p>
                <p>Este es su comprobante de reserva, verifique que la información detallada a continuación sea correcta.</p>
                <p>Estimado/a $item->client_first_name | Reservación No: $item->code</p>                
                <p>Gracias por elegir a Caribbean Transfers, agradecemos su confianza, la información escrita a continuación facilitará su contacto con nuestro staff en el Aeropuerto, el vuelo $item->flight_number aterriza en $point->point_name el día $arrival_date hrs por lo tanto nuestro representante lo estará esperando en $point->point_description con un identificador de Caribbean Transfers</p>
                <p>Para facilitar el contacto encienda su celular tan pronto como aterrice, puede usar la red gratuita del WIFI en el aeropuerto para poder contactarnos. Avísenos cuando esté listo para abordar su unidad (después de pasar aduana y recolectar sus maletas), un representante estará listo para recibirle y acercarlo a la unidad asignada.</p>
                <p>Por favor confirme de recibido</p>
                <p>Gracias por su confianza, que tenga un excelente viaje</p>
                <p>*En caso de requerir ayuda adicional, envíe un mensaje al número $item->transactional_phone</p>
                <p>Propinas no incluidas</p>
                <p>Todo el personal de la empresa está identificado con gafete y uniforme por favor no haga caso de intentos de estafa ya que estos pagos no serán reembolsados.</p>
            EOF;            
        endif;
    }

    function sendDepartureConfirmation($request){
        $lang = $request['lang'];
        $item = DB::select("SELECT it.code, it.from_name, it.to_name, it.flight_number, it.passengers, it.op_one_pickup, it.op_two_pickup, rez.client_first_name, rez.client_email, sit.transactional_phone, rez.id as reservation_id
                                FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                    WHERE it.id = :id", ['id' => $request['item_id'] ]);
        
        $message = $this->departureMessage($lang, $item[0], $request['destination_id'], $request['type']);        

        //Data to send in confirmation..
        $email_data = array(
            "Messages" => array(
                array(
                    "From" => array(
                        "Email" => 'bookings@caribbean-transfers.com',
                        "Name" => "Bookings"
                    ),
                    "To" => array(
                        array(
                            "Email" => $item[0]->client_email,
                            "Name" => $item[0]->client_first_name,
                        )
                    ),
                    "Bcc" => array(
                        array(
                            "Email" => 'bookings@caribbean-transfers.com',
                            "Name" => "Bookings"
                        )
                    ),
                    "Subject" => (($lang == "en")?'Service departure confirmation message':'Mensaje de confirmación de servicio de regreso'),
                    "TextPart" => (($lang == "en")?'Dear client':'Estimado cliente'),
                    "HTMLPart" => $message
                )
            )
        );

        $email_response = $this->sendMailjet($email_data);

        if(isset($email_response['Messages'][0]['Status']) && $email_response['Messages'][0]['Status'] == "success"):
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = 'Sistema';
            $follow_up_db->text = 'E-mail enviado (confirmación de regreso) por '.auth()->user()->name;
            $follow_up_db->type = 'INTERN';
            $follow_up_db->reservation_id = $item[0]->reservation_id;
            $follow_up_db->save();

            return response()->json(['status' => "success"], 200);
        else:
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = 'Sistema';
            $follow_up_db->text = 'No fue posible enviar el e-mail de confirmación de regreso, por favor contactar a Desarrollo';
            $follow_up_db->type = 'INTERN';
            $follow_up_db->reservation_id = $item[0]->reservation_id;
            $follow_up_db->save();
            
            return response()->json([
                'error' => [
                    'code' => 'mailing_system',
                    'message' => 'The mailing platform has a problem, please report to development'
                ]
            ], 404);
        endif;

    }

    public function departureMessage($lang = "en", $item = [], $destination_id, $type = "departure"){      
        $departure_date = NULL;
        $destination = NULL;     
        
        if($type == "transfer-pickup"):
            $departure_date = date("Y-m-d H:i", strtotime($item->op_one_pickup));
            $destination = $item->from_name;
        endif;

        if($type == "transfer-return"):
            $departure_date = date("Y-m-d H:i", strtotime($item->op_two_pickup));
            $destination = $item->to_name;
        endif;

        if($type == "departure"):            
            if(empty( $item->op_two_pickup )):
                $destination = $item->from_name;
                $departure_date = date("Y-m-d H:i", strtotime($item->op_one_pickup));
            else:
                $destination = $item->to_name;
                $departure_date = date("Y-m-d H:i", strtotime($item->op_two_pickup));
            endif;
        endif;

        $message = '';
        if($destination_id == 1 && $lang == "en"):
            $message = '<p>The Cancun airport recommends users to arrive three hours in advance for international flights and two hours in advance for domestic flights.</p>';
        endif;
        if($destination_id == 1 && $lang == "es"):
            $message = '<p>El aeropuerto de Cancún recomienda a sus usuarios llegar con tres horas de anticipación en vuelos internacionales y dos horas en vuelos nacionales.</p>';
        endif;

        if($lang == "en"):
            return <<<EOF
                    <p>Departure confirmation</p>
                    <p>Dear $item->client_first_name | Reservation Number: $item->code</p>
                    <p>Thank you for choosing Caribbean Transfers the reason for this email is to confirm your pick up time. The date indicated on your reservation is $departure_date hrs. We will be waiting for you in $destination at that time.</p>
                    $message     
                    <p>You can also confirm by phone: $item->transactional_phone</p>
                    <p>Tips not included</p>
                EOF; 
        else:
            return <<<EOF
                    <p>Confirmación de salida</p>
                    <p>Estimado/a $item->client_first_name | Reservación No: $item->code</p>
                    <p>Gracias por elegir a Caribbean Transfers el motivo de este correo es confirmar su hora de recolección. La fecha indicada en su reserva es $departure_date hrs. Le estaremos esperando en $destination a esa hora.</p>
                    $message     
                    <p>También puedes confirmar por teléfono: $item->transactional_phone</p>
                    <p>Propinas no incluidas</p>
                EOF;           
        endif;
    }

    public function sendPaymentRequest($request){

        $item = DB::select("SELECT sit.payment_domain, sit.transactional_phone, rez.client_email, rez.client_first_name, rez.id as reservation_id, sit.success_payment_url, sit.cancel_payment_url
                            FROM reservations as rez 
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                            WHERE rez.id = :id", ['id' => $request['item_id'] ]);
        $item = $item[0];
        $lang = $request['lang'];
        $message = '';

        $paypal_URL = $this->makePaymentURL( $request, $item, 'PAYPAL' );
        $stripe_URL = $this->makePaymentURL( $request, $item, 'STRIPE' );

        if($lang == "en"):
            $message = <<<EOF
                    <p>Hello again!</p>
                    <p>We have noticed that your reservation has not been confirmed yet, don't miss the opportunity to secure your transfer with us and enjoy our special online prices!</p>
                    <p>Why pay now?</p>
                    <ul>
                        <li><strong>Guaranteed security:</strong> By pre-paying, you are assured to have your transportation ready and waiting for your arrival.</li>
                        <li><strong>Time saving:</strong> Avoid long waits and complications at the airport.</li>
                        <li><strong>Total peace of mind:</strong> Enjoy your trip knowing that everything is organized and worry-free.</li>
                        <li><strong>Secure payment:</strong> We use HTTPS to guarantee the security of your data, and we work with the best payment platforms such as PayPal and Stripe.</li>
                    </ul>
                    
                    <p>To pay with STRIPE, click <a href="$stripe_URL" title="Pay with Stripe">here</a></p>                    
                    <p>To pay with PayPal, click <a href="$paypal_URL" title="Pay with PayPal">here</a></p>

                    <p>If you have any questions, our team is ready to assist you. Contact us at: $item->transactional_phone </p>
                    <p><strong>Business hours:</strong> From 7:00 am to 11:00 pm.</p>
                    <p>We look forward to seeing you soon and providing you with exceptional service</p>
                EOF;
        else:
            $message = <<<EOF
                    <p>¡Hola de nuevo!</p>
                    <p>Hemos notado que su reservación aún no ha sido confirmada. ¡No pierda la oportunidad de asegurar su traslado con nosotros y disfrutar de nuestros precios especiales en línea!</p>
                    <p>¿Por qué pagar ahora?</p>
                    <ul>
                        <li><strong>Seguridad garantizada:</strong> Al pre-pagar, se asegura de tener su transporte listo y esperando a su llegada.</li>
                        <li><strong>Ahorro de tiempo:</strong> Evite largas esperas y complicaciones en el aeropuerto.</li>
                        <li><strong>Tranquilidad total:</strong> Disfrute de su viaje sabiendo que todo está organizado y sin preocupaciones.</li>
                        <li><strong>Pago seguro:</strong> Utilizamos HTTPS para garantizar la seguridad de sus datos, y trabajamos con las mejores plataformas de pago como PayPal y Stripe.</li>
                    </ul>
                    
                    <p>Para pagar con STRIPE, de click <a href="$stripe_URL" title="Paga con Stripe">aquí</a></p>                    
                    <p>Para pagar con PayPal, de click <a href="$paypal_URL" title="Paga con PayPal">aquí</a></p>

                    <p>Si tiene alguna pregunta, nuestro equipo está listo para asistirle. Contáctenos al: $item->transactional_phone </p>
                    <p><strong>Horario de atención:</strong> De 7:00 a 23:00 h.</p>
                    <p>Esperamos verle pronto y brindarle un servicio excepcional.</p>
                EOF;
        endif;        

        //Data to send in confirmation..
        $email_data = array(
            "Messages" => array(
                array(
                    "From" => array(
                        "Email" => 'bookings@caribbean-transfers.com',
                        "Name" => "Bookings"
                    ),
                    "To" => array(
                        array(
                            "Email" => $item->client_email,
                            "Name" => $item->client_first_name,
                        )
                    ),
                    "Bcc" => array(
                        array(
                            "Email" => 'bookings@caribbean-transfers.com',
                            "Name" => "Bookings"
                        )
                    ),
                    "Subject" => (($lang == "en")?'Payment request':'Solicitúd de pago'),
                    "TextPart" => (($lang == "en")?'Dear client':'Estimado cliente'),
                    "HTMLPart" => $message
                )
            )
        );

        $email_response = $this->sendMailjet($email_data);

        if(isset($email_response['Messages'][0]['Status']) && $email_response['Messages'][0]['Status'] == "success"):
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = 'Sistema';
            $follow_up_db->text = 'E-mail enviado (solicitúd de pago) por '.auth()->user()->name;
            $follow_up_db->type = 'INTERN';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            return response()->json(['status' => "success"], 200);
        else:
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = 'Sistema';
            $follow_up_db->text = 'No fue posible enviar el e-mail de solicitúd de pago, por favor contactar a Desarrollo';
            $follow_up_db->type = 'INTERN';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();
            
            return response()->json([
                'error' => [
                    'code' => 'mailing_system',
                    'message' => 'The mailing platform has a problem, please report to development'
                ]
            ], 404);
        endif;

        echo $message; die();
    }

    private function orderByDateTime($a, $b) {
        return strtotime($b->created_at) - strtotime($a->created_at);
    }

    private function makePaymentURL($request, $item, $type = "STRIPE"){        

        $data = [
            "type" => $type,
            "id" => $request->item_id,
            "language" => $request->lang,
            "success_url" => $item->success_payment_url,
            "cancel_url" => $item->cancel_payment_url,
            "redirect" => 1
        ];

        return 'https://api.caribbean-transfers.com/api/v1/reservation/payment/handler?'.http_build_query($data);        
    }

}