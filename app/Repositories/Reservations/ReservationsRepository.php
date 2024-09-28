<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\DestinationService;
use App\Models\ReservationFollowUp;
use App\Models\ReservationsItem;
use App\Models\ReservationsService;
use App\Models\Payment;
use App\Models\OriginSale;
use App\Models\ContactPoints;
use App\Models\Zones;
use App\Models\Site;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\MailjetTrait;
use App\Traits\GeneralTrait;
use App\Traits\QueryTrait;
use App\Traits\Reports\PaymentsTrait;

class ReservationsRepository
{
    use MailjetTrait, QueryTrait, GeneralTrait, PaymentsTrait;
    
    public function index($request)
    {
        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
            "filter_text" => NULL,
            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "status_booking" => ( isset($request->status_booking) ? $request->status_booking : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),
            "is_today" => ( isset($request->is_today) ? $request->is_today : 0 ),
            "is_balance" => ( isset($request->is_balance) ? $request->is_balance : 0 ),
        ];
        
        //Query DB        
        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end AND rez.is_duplicated = 0 ';
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
        if(isset( $request->status_booking ) && !empty( $request->status_booking )){
            $params = $this->parseArrayQuery($request->status_booking,"single");
            $havingConditions[] = " status IN (".$params.") ";
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

        //MOTIVOS DE CANCELACIÓN
        if(isset( $request->cancellation_status ) && !empty( $request->cancellation_status )){
            $params = $this->parseArrayQuery($request->cancellation_status);
            $query .= " AND tc.id IN ($params) ";
        }

        //RESERVAS CON UN BALANCE
        if(isset( $request->is_balance ) && !empty( $request->is_balance)){
            $havingConditions[] = ' total_balance > 0 ';
        }        

        //RESERVAS OPERADAS EL MISMO DIA DE SU CREACION
        if(isset( $request->is_today ) && !empty( $request->is_today)){
            $havingConditions[] = ' is_today != 0 ';
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

        if(  (isset( $request->status_booking ) && !empty( $request->status_booking )) || (isset( $request->payment_method ) && !empty( $request->payment_method )) || (isset( $request->is_balance ) && !empty( $request->is_balance)) || (isset( $request->is_today ) && !empty( $request->is_today)) ){
            $query2 = " HAVING " . implode(' AND ', $havingConditions);
        }
                
        // dd($query, $query2, $data, $queryData, $havingConditions);
        $bookings = $this->queryBookings($query, $query2, $queryData);
        
        return view('reservations.index', [
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
            'status' => $this->Status(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'currencies' => $this->Currencies(),
            'methods' => $this->Methods(),
            'cancellations' => $this->CancellationTypes(),
            'data' => $data,
            'request' => $request,
        ]);
    }

    public function reservationPayments($reservation){
        return $this->getPayments($reservation->id);
    }

    public function update($request,$reservation){
        try{
            DB::beginTransaction();
            $payments = Payment::where('reservation_id', $reservation->id)->get();
            $this->logBooking($request, $reservation);
            $reservation->client_first_name = $request->client_first_name;
            $reservation->client_last_name = $request->client_last_name;
            $reservation->client_email = $request->client_email;
            $reservation->client_phone = $request->client_phone;
            $reservation->site_id = $request->site_id;
            $reservation->reference = $request->reference;
            if( isset($request->origin_sale_id) && $request->origin_sale_id != 0 ){
                $reservation->origin_sale_id = $request->origin_sale_id;
            }            
            $reservation->currency = $request->currency;
            if ( ($request->site_id == 11 || $request->site_id == 21) && $request->vendor_id == NULL && $request->terminal == NULL && count($payments) == 0 ) {
                $reservation->is_complete = 0;
                $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", completo la reservación de Taquilla, que se creo desde operaciones", 'HISTORY', 'EDICIÓN RESERVACIÓN');
            }
            $reservation->save();
            DB::commit();
            return response()->json(['message' => 'Reservation updated successfully', 'success' => true], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error editing reservation', 'success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($request, $reservation){
        try {
            DB::beginTransaction();
            $reservation->is_cancelled = 1;
            ( isset($request->type) ? $reservation->cancellation_type_id = $request->type : '' );
            $reservation->save();
            $reservation->items()->update(['op_one_status' => 'CANCELLED', 'op_two_status' => 'CANCELLED']);
            $check = $this->create_followUps($reservation->id, 'El usuario: '.auth()->user()->name.", cancelo la reservación: ".$reservation->id, 'HISTORY', 'CANCELACIÓN');
            DB::commit();
            return response()->json(['message' => 'Reservation cancelled successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling reservation'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function duplicated($request, $reservation){
        try {            
            DB::beginTransaction();
            $reservation->is_duplicated = 1;
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'El usuario: '.auth()->user()->name.", marco marco como duplicada la reservación: ".$reservation->id, 'HISTORY', 'DUPLICADA');
            DB::commit();
            return response()->json(['message' => 'Reservation duplicated successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error cancelling reservation'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function openCredit($request, $reservation){
        try {            
            DB::beginTransaction();
            $reservation->open_credit = 1;
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'El usuario: '.auth()->user()->name.", activo el crédito abierto a la reservación: ".$reservation->id, 'HISTORY', 'CRÉDITO ABIERTO');
            DB::commit();
            return response()->json(['message' => 'Update successfully completed'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error marking as open credit'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function enablePlusService($request, $reservation){
        try {
            DB::beginTransaction();
            $reservation->is_advanced = 1;
            $reservation->save();
            $check = $this->create_followUps($reservation->id, 'El usuario: '.auth()->user()->name.", activo el servicio plus a la reservación: ".$reservation->id, 'HISTORY', 'SERVICIO PLUS');
            DB::commit();
            return response()->json(['message' => 'Update successfully completed'], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error activating plus service'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    public function enableReservation($request, $reservation){
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
            $this->logBookingService($request, $item);
            $item->destination_service_id = $request->destination_service_id;
            $item->passengers = $request->passengers;
            $item->flight_number = $request->flight_number;
            
            $item->from_name = $request->from_name;
            $item->to_name = $request->to_name;
        
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
            return response()->json([
                'message' => 'Item updated successfully', 
                'success' => true
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating item', 
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            $check = $this->create_followUps($item[0]->reservation_id, 'El usuario: '.auth()->user()->name.", a enviado E-mail (confirmación de llegada) para la reservación: ".$item[0]->reservation_id, 'INTERN', 'SISTEMA');
            // E-mail enviado (confirmación de llegada) por '.auth()->user()->name

            return response()->json(['status' => "success", "message" => $message], 200);
        else:
            $check = $this->create_followUps($item[0]->reservation_id, 'No fue posible enviar el e-mail de confirmación de llegada, por favor contactar a Desarrollo', 'INTERN', 'SISTEMA');
            // No fue posible enviar el e-mail de confirmación de llegada, por favor contactar a Desarrollo

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
            $check = $this->create_followUps($item[0]->reservation_id, 'El usuario: '.auth()->user()->name.", a enviado E-mail (confirmación de regreso) para la reservación: ".$item[0]->reservation_id, 'INTERN', 'SISTEMA');
            // 'E-mail enviado (confirmación de regreso) por '.auth()->user()->name

            return response()->json(['status' => "success", "message" => $message], 200);
        else:
            $check = $this->create_followUps($item[0]->reservation_id, 'No fue posible enviar el e-mail de confirmación de regreso, por favor contactar a Desarrollo', 'INTERN', 'SISTEMA');
            // No fue posible enviar el e-mail de confirmación de regreso, por favor contactar a Desarrollo
            
            return response()->json([
                'error' => [
                    'code' => 'mailing_system',
                    'message' => 'The mailing platform has a problem, please report to development'
                ]
            ], 404);
        endif;
    }

    public function departureMessage($lang = "en", $item = [], $destination_id = 0, $type = "departure"){
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
            $check = $this->create_followUps($item->reservation_id, 'El usuario: '.auth()->user()->name.", a enviado E-mail (solicitúd de pago) para la reservación: ".$item->reservation_id, 'INTERN', 'SISTEMA');
            // E-mail enviado (solicitúd de pago) por '.auth()->user()->name

            return response()->json(['status' => "success"], 200);
        else:
            $check = $this->create_followUps($item->reservation_id, 'No fue posible enviar el e-mail de solicitúd de pago, por favor contactar a Desarrollo', 'INTERN', 'SISTEMA');
            // No fue posible enviar el e-mail de solicitúd de pago, por favor contactar a Desarrollo
            
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

    // NOS PERMITE GENERAR EL LOG DE CADA UNO DE LOS CAMBIOS DE LA RESERVACIÓN
    public function logBooking($request, $reservation){
        if( $request->client_first_name != $reservation->client_first_name ){
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo el nombre del cliente de: ".$reservation->client_first_name." a ".$request->client_first_name, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }

        if( $request->client_last_name != $reservation->client_last_name ){
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo los apellidos del cliente de: ".$reservation->client_last_name." a ".$request->client_last_name, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }

        if( $request->client_email != $reservation->client_email ){
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo el correo del cliente de: ".$reservation->client_email." a ".$request->client_email, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }

        if( $request->client_phone != $reservation->client_phone ){
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo el teléfono del cliente de: ".$reservation->client_phone." a ".$request->client_phone, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }

        if( $request->site_id != $reservation->site_id ){
            $site_old = Site::find($reservation->site_id);
            $site_new = Site::find($request->site_id);
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo el sitio de: ".$site_old->name." a ".$site_new->name, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }

        if( $request->reference != $reservation->reference ){
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo la referencia de: ".$reservation->reference." a ".$request->reference, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }

        if( isset($request->origin_sale_id) && $request->origin_sale_id != 0 && $request->origin_sale_id != $reservation->origin_sale_id ){
            $origin_old = OriginSale::find($reservation->origin_sale_id);
            $origin_new = OriginSale::find($request->origin_sale_id);
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo el origen de venta de: ".( isset($origin_old->code) ? $origin_old->code : "NULL" )." a ".$origin_new->code, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }        
        
        if( $request->currency != $reservation->currency ){
            $this->create_followUps($reservation->id, "El usuario: ".auth()->user()->name.", actualizo la moneda de: ".$reservation->currency." a ".$request->currency, 'HISTORY', 'EDICIÓN RESERVACIÓN');
        }
    }

    // NOS PERMITE GENERAR EL LOG DE CADA UNO DE LOS CAMBIOS DE LOS SERVICIOS DE LA RESERVACIÓN
    public function logBookingService($request, $item){
        //LOG DE TIPO DE VEHÍVULO
        if( $request->destination_service_id != $item->destination_service_id ){
            $destination_old = DestinationService::find($item->destination_service_id);
            $destination_new = DestinationService::find($request->destination_service_id);
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo el tipo de Vehículo de: ".$destination_old->name." a ".$destination_new->name, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        //LOG DE NUMERO DE PASAJEROS
        if( $request->passengers != $item->passengers  ){
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo el número de pasajeros de: ".$item->passengers." a ".$request->passengers, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        //LOG DE NÚMERO DE VUELO
        if( $request->flight_number != $item->flight_number  ){
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo el numéro de vuelo de: ".$item->flight_number." a ".$request->flight_number, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        //LOG DE ZONA DESDE
        if( $request->from_zone_id != $item->from_zone ){
            $zone_old = Zones::find($item->from_zone);            
            $zone_new = Zones::find($request->from_zone_id);
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo la zona Desde de: ".$zone_old->name." a ".$zone_new->name, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        if( $request->from_name != $item->from_name ){
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo Desde de: ".$item->from_name." a ".$request->from_name, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        if( $request->from_lat != $item->from_lat ){
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo las coordenadas Desde lat: ".$item->from_lat." a ".$request->from_lat.", lng: ".$item->from_lng." a ".$request->from_lng, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        //LOG DE ZONA HACIA
        if( $request->to_zone_id != $item->to_zone ){
            $zone_old = Zones::find($item->to_zone);
            $zone_new = Zones::find($request->to_zone_id);
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo la zona Hacia de: ".$zone_old->name." a ".$zone_new->name, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        if( $request->to_name != $item->to_name ){
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo Hacia de: ".$item->to_name." a ".$request->to_name, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        if( $request->to_lat != $item->to_lat ){
            //CREAMOS UN LOG
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo las coordenadas Hacias lat: ".$item->to_lat." a ".$request->to_lat.", lng: ".$item->to_lng." a ".$request->to_lng, 'HISTORY', 'EDICIÓN SERVICIO');
        }

        //LOG DE FECHA Y HORA DE RECOGIDA
        if( $request->op_one_pickup != $item->op_one_pickup ){
            $one_pickup_request_date = date("Y-m-d", strtotime($request->op_one_pickup));
            $one_pickup_request_time = date("H:i", strtotime($request->op_one_pickup));
            $one_pickup_item_date = date("Y-m-d", strtotime($item->op_one_pickup));
            $one_pickup_item_time = date("H:i", strtotime($item->op_one_pickup));

            if( $one_pickup_request_date != $one_pickup_item_date ){
                //CREAMOS UN LOG
                $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo la fecha de recogida de: ".$one_pickup_item_date." a ".$one_pickup_request_date, 'HISTORY', 'EDICIÓN SERVICIO');
            }

            if( $one_pickup_request_time != $one_pickup_item_time ){
                //CREAMOS UN LOG
                $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo la hora de recogida de: ".$one_pickup_item_time." a ".$one_pickup_request_time, 'HISTORY', 'EDICIÓN SERVICIO');
            }
        }

        //LOG DE FECHA Y HORA DE REGRESO
        if( $item->is_round_trip == 1 && $request->op_two_pickup != $item->op_two_pickup ){
            $two_pickup_request_date = date("Y-m-d", strtotime($request->op_two_pickup));
            $two_pickup_request_time = date("H:i", strtotime($request->op_two_pickup));
            $two_pickup_item_date = date("Y-m-d", strtotime($item->op_two_pickup));
            $two_pickup_item_time = date("H:i", strtotime($item->op_two_pickup));

            if( $two_pickup_request_date != $two_pickup_item_date ){
                //CREAMOS UN LOG
                $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo la fecha de regreso de: ".$two_pickup_item_date." a ".$two_pickup_request_date, 'HISTORY', 'EDICIÓN SERVICIO');
            }

            if( $two_pickup_request_time != $two_pickup_item_time ){
                //CREAMOS UN LOG
                $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", actualizo la hora de regreso de: ".$two_pickup_item_time." a ".$two_pickup_request_time, 'HISTORY', 'EDICIÓN SERVICIO');
            }            
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