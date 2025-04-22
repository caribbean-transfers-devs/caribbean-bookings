<?php

namespace App\Repositories\Reservations;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use Carbon\Carbon;

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
use App\Models\Sale;

//TRAITS
use App\Traits\MailjetTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\FollowUpTrait;

class ReservationsRepository
{
    use MailjetTrait, FiltersTrait, QueryTrait, FollowUpTrait;

    public function update($request,$reservation)
    {
        $validatedData = $this->validateBookingRequest($request); //Validar y preparar los datos de la solicitud

        try{
            DB::beginTransaction();

            $this->logBookingChanges($reservation, $request);
            $this->updateBookingAttributes($reservation, $validatedData); //Update item attributes with validated data

            DB::commit();
            return response()->json([
                'message' => 'Reserva actualizada exitosamente', 
                'success' => true,
                'status' => 'success',
            ], Response::HTTP_OK);                 
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al editar la reserva: ' . $e->getMessage(), 
                'success' => false,
                'status' => 'error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //METODO QUE NOS AYUDA A CANCELAR LA RESERVACIÓN
    public function destroy($request, $reservation){
        try {
            // Verificar si existe algún ítem relacionado con status COMPLETED
            $hasCompletedItems = $reservation->items()->where(function ($query) {
                $query->where('op_one_status', 'COMPLETED')
                ->orWhere('op_two_status', 'COMPLETED');
            })->exists();

            // No se puede cancelar la reserva porque tiene artículos con estado COMPLETADO
            if ($hasCompletedItems) {                
                return response()->json(['status' => 'warning', 'message' => 'No se puede cancelar la reserva porque tiene servicios con estado COMPLETADO'], Response::HTTP_BAD_REQUEST);
            }

            DB::beginTransaction();

            $reservation->is_cancelled = 1;
            ( isset($request->type) ? $reservation->cancellation_type_id = $request->type : '' );
            $reservation->save();

            // Actualizar los estados de los ítems y el ID por el tipo de cancelación
            $reservation->items()->update([
                'vehicle_id_one' => NULL,
                'driver_id_one' => NULL,
                'op_one_status' => 'CANCELLED',
                'op_one_status_operation' => 'PENDING',
                'op_one_time_operation' => NULL,
                'op_one_preassignment' => NULL,
                'op_one_operating_cost' => 0,
                'op_one_cancellation_type_id' => $request->type,
                'vehicle_id_two' => NULL,
                'driver_id_two' => NULL,
                'op_two_status' => 'CANCELLED',
                'op_two_status_operation' => 'PENDING',
                'op_two_time_operation' => NULL,
                'op_two_preassignment' => NULL,
                'op_two_operating_cost' => 0,
                'op_two_cancellation_type_id' => $request->type,
            ]);

            $check = $this->create_followUps($reservation->id, 'El usuario: '.auth()->user()->name.", cancelo la reservación: ".$reservation->id, 'HISTORY', 'CANCELLATION_BOOKING');
            
            DB::commit();
            // Reservation cancelled successfully
            return response()->json([
                'status' => 'success',
                'message' => 'Reserva cancelada correctamente'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            // Error cancelling reservation
            return response()->json([
                'status' => 'danger',
                'message' => 'Error al cancelar la reserva',
                // 'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_exchange($request, $reservation){
        $currency = $request->currency;
        $to_currency = $reservation->currency;
        $exchange = DB::table('payments_exchange_rate')->where('origin',$currency)->where('destination',$to_currency)->first();
        return $exchange;
    }

    public function editreservitem($request, $item)
    {
        $validatedData = $this->validateServiceRequest($request); //Validar y preparar los datos de la solicitud
        
        try {
            DB::beginTransaction();
            
            $this->logBookingServiceChanges($item, $request);
            $this->updateItemAttributes($item, $validatedData); //Update item attributes with validated data
            $this->updateReservationExpiration($item->reservation_id, $validatedData);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Item updated successfully', 
                'success' => true,
                'status' => 'success',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();            
            return response()->json([
                'message' => 'Error updating item: ' . $e->getMessage(), 
                'success' => false,
                'status' => 'error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Validar y preparar los datos de la solicitud
     */
    protected function validateBookingRequest($request): array
    {
        return [
            'client_first_name' => $request->client_first_name,
            'client_last_name' => $request->client_last_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'site_id' => $request->site_id,
            'reference' => $request->reference,
            'origin_sale_id' => isset($request->origin_sale_id) && ( $request->origin_sale_id !== '' || $request->origin_sale_id !== 0 ) ? $request->origin_sale_id : NULL,
            'currency' => $request->currency,
            'special_request' => $request->special_request ?: NULL,
        ];
    }

    /**
     * Validar y preparar los datos de la solicitud
     */
    protected function validateServiceRequest($request): array
    {
        return [
            'serviceTypeForm' => $request->serviceTypeForm ?: '',
            'destination_service_id' => $request->destination_service_id,
            'passengers' => $request->passengers,
            'flight_number' => $request->flight_number,
            'from_zone_id' => $request->from_zone_id,
            'from_name' => $request->from_name,
            'from_lat' => $request->from_lat,
            'from_lng' => $request->from_lng,
            'to_zone_id' => $request->to_zone_id,
            'to_name' => $request->to_name,
            'to_lat' => $request->to_lat,
            'to_lng' => $request->to_lng,
            'op_one_pickup' => $request->op_one_pickup,
            'op_two_pickup' => $request->op_two_pickup ?? null,
        ];
    }

    protected function logBookingChanges($reservation, $request)
    {
        $changes = [
            'client_first_name' => [
                'condition' => $request->client_first_name != $reservation->client_first_name,
                'message' => "actualizó el nombre del cliente de: ({$reservation->client_first_name}) a ({$request->client_first_name})"
            ],
            'client_last_name' => [
                'condition' => $request->client_last_name != $reservation->client_last_name,
                'message' => "actualizó los apellidos del cliente de: ({$reservation->client_last_name}) a ({$request->client_last_name})"
            ],
            'client_email' => [
                'condition' => $request->client_email != $reservation->client_email,
                'message' => "actualizó el correo del cliente de: ({$reservation->client_email}) a ({$request->client_email})"
            ],
            'client_phone' => [
                'condition' => $request->client_phone != $reservation->client_phone,
                'message' => "actualizó el teléfono del cliente de: ({$reservation->client_phone}) a ({$request->client_phone})"
            ],
            'site_id' => [
                'condition' => $request->site_id != $reservation->site_id,
                'action' => function() use ($request, $reservation) {
                    $old = Site::find($reservation->site_id);
                    $new = Site::find($request->site_id);
                    return "actualizo el sitio de: ({$old->name}) a ({$new->name})";
                }
            ],
            'reference' => [
                'condition' => $request->reference != $reservation->reference,
                'message' => "actualizo la referencia de: ({$reservation->reference}) a ({$request->reference})"
            ],
            'origin_sale_id' => [
                'condition' => isset($request->origin_sale_id) && ( $request->origin_sale_id !== '' || $request->origin_sale_id !== 0 ) && ( $request->origin_sale_id != $reservation->origin_sale_id ),
                'action' => function() use ($request, $reservation) {
                    $old = OriginSale::find($reservation->origin_sale_id);
                    $new = OriginSale::find($request->origin_sale_id);
                    $name_old = ( isset($old->code) ? $old->code : "PAGINA WEB" );
                    $name_new = ( isset($new->code) ? $new->code : "PAGINA WEB" );
                    return "actualizo el origen de venta de: ({$name_old}) a ({$name_new})";
                }
            ],
            'currency' => [
                'condition' => $request->currency != $reservation->currency,
                'message' => "actualizo la moneda de: ({$reservation->currency}) a ({$request->currency})"
            ],
            'special_request' => [
                'condition' => '',
                'message' => ""
            ],            
        ];
    
        foreach ($changes as $change) {
            if ($change['condition']) {
                $message = $change['message'] ?? null;
                if (isset($change['action'])) {
                    $result = $change['action']();
                    if (is_array($result)) {
                        foreach ($result as $msg) {
                            $this->createLogEntry($reservation->id, $msg, 'UPDATE_RESERVATION');
                        }
                        continue;
                    }
                    $message = $result;
                }                
                $this->createLogEntry($reservation->id, $message, 'UPDATE_RESERVATION');
            }
        }
    }    
    
    protected function logBookingServiceChanges($item, $request)
    {
        $changes = [
            'service_type' => [
                'condition' => isset($request->serviceTypeForm) && $request->serviceTypeForm == 1 && $request->serviceTypeForm != $item->is_round_trip,
                'message' => 'actualizó el servicio: {$item->id}({$item->code}) de: (One Way) a (Round Trip)'
            ],
            'vehicle_type' => [
                'condition' => $request->destination_service_id != $item->destination_service_id,
                'action' => function() use ($request, $item) {
                    $old = DestinationService::find($item->destination_service_id);
                    $new = DestinationService::find($request->destination_service_id);
                    return "actualizó el tipo de Vehículo de: ({$old->name}) a ({$new->name})";
                }
            ],
            'passengers' => [
                'condition' => $request->passengers != $item->passengers,
                'message' => "actualizó el número de pasajeros de: ({$item->passengers}) a ({$request->passengers})"
            ],
            'flight_number' => [
                'condition' => $request->flight_number != $item->flight_number,
                'message' => "actualizó el número de vuelo de: ({$item->flight_number}) a ({$request->flight_number})"
            ],
            'from_zone' => [
                'condition' => $request->from_zone_id != $item->from_zone,
                'action' => function() use ($request, $item) {
                    $old = Zones::find($item->from_zone);
                    $new = Zones::find($request->from_zone_id);
                    return "actualizó la zona Desde de: ({$old->name}) a ({$new->name})";
                }
            ],
            'from_name' => [
                'condition' => $request->from_name != $item->from_name,
                'message' => "actualizó Desde de: ({$item->from_name}) a ({$request->from_name})"
            ],
            'from_coords' => [
                'condition' => $request->from_lat != $item->from_lat,
                'message' => "actualizó las coordenadas Desde lat: ({$item->from_lat}) a ({$request->from_lat}), lng: ({$item->from_lng}) a ({$request->from_lng})"
            ],
            'to_zone' => [
                'condition' => $request->to_zone_id != $item->to_zone,
                'action' => function() use ($request, $item) {
                    $old = Zones::find($item->to_zone);
                    $new = Zones::find($request->to_zone_id);
                    return "actualizó la zona Hacia de: ({$old->name}) a ({$new->name})";
                }
            ],
            'to_name' => [
                'condition' => $request->to_name != $item->to_name,
                'message' => "actualizó Hacia de: ({$item->to_name}) a ({$request->to_name})"
            ],
            'to_coords' => [
                'condition' => $request->to_lat != $item->to_lat,
                'message' => "actualizó las coordenadas Hacia lat: ({$item->to_lat}) a ({$request->to_lat}), lng: ({$item->to_lng}) a ({$request->to_lng})"
            ],
            'pickup_time' => [
                'condition' => $request->op_one_pickup != $item->op_one_pickup,
                'action' => function() use ($request, $item) {
                    $requestDate = date("Y-m-d", strtotime($request->op_one_pickup));
                    $requestTime = date("H:i", strtotime($request->op_one_pickup));
                    $itemDate = date("Y-m-d", strtotime($item->op_one_pickup));
                    $itemTime = date("H:i", strtotime($item->op_one_pickup));
                    
                    $messages = [];
                    if ($requestDate != $itemDate) {
                        $messages[] = "actualizó la fecha de recogida de: ({$itemDate}) a ({$requestDate})";
                    }
                    if ($requestTime != $itemTime) {
                        $messages[] = "actualizó la hora de recogida de: ({$itemTime}) a ({$requestTime})";
                    }
                    return $messages;
                }
            ],
            'return_time' => [
                'condition' => $item->is_round_trip == 1 && $request->op_two_pickup != $item->op_two_pickup,
                'action' => function() use ($request, $item) {
                    $requestDate = date("Y-m-d", strtotime($request->op_two_pickup));
                    $requestTime = date("H:i", strtotime($request->op_two_pickup));
                    $itemDate = date("Y-m-d", strtotime($item->op_two_pickup));
                    $itemTime = date("H:i", strtotime($item->op_two_pickup));
                    
                    $messages = [];
                    if ($requestDate != $itemDate) {
                        $messages[] = "actualizó la fecha de regreso de: ({$itemDate}) a ({$requestDate})";
                    }
                    if ($requestTime != $itemTime) {
                        $messages[] = "actualizó la hora de regreso de: ({$itemTime}) a ({$requestTime})";
                    }
                    return $messages;
                }
            ]
        ];
    
        foreach ($changes as $change) {
            if ($change['condition']) {
                $message = $change['message'] ?? null;                
                if (isset($change['action'])) {
                    $result = $change['action']();
                    if (is_array($result)) {
                        foreach ($result as $msg) {
                            $this->createLogEntry($item->reservation_id, $msg, 'UPDATE_SERVICE');
                        }
                        continue;
                    }
                    $message = $result;
                }                
                $this->createLogEntry($item->reservation_id, $message, 'UPDATE_SERVICE');
            }
        }
    }

    protected function createLogEntry($reservationId, $message, $action = "")
    {
        $this->create_followUps(
            $reservationId, 
            "El usuario: " . auth()->user()->name . ", " . $message, 
            'HISTORY', 
            // 'EDICIÓN RESERVACIÓN'
            // 'EDICIÓN SERVICIO',
            $action
        );
    }  
    
    /**
     * Update item attributes with validated data
     */    
    protected function updateBookingAttributes($reservation, array $data): void
    {
        $oldSiteId = $reservation->site_id;

        // Actualización de campos básicos
        $reservation->fill($data);

        // Manejo de solicitud especial
        if (!empty($data['special_request'])) {
            $this->createFollowUp(
                $reservation->id, 
                $data['special_request'], 
                'CLIENT', 
                auth()->user()->name
            );
        }

        // Manejo de cambio de sitio
        if ($oldSiteId != $data['site_id']) {
            $this->handleSiteChange($reservation, $data['site_id']);
        }

        $reservation->save();
    }    

    /**
     * Update item attributes with validated data
     */
    protected function updateItemAttributes($item, array $data): void
    {
        ( isset($data['serviceTypeForm']) && $data['serviceTypeForm'] == 1 ? $item->is_round_trip = $data['serviceTypeForm'] : '' );
        $item->destination_service_id = $data['destination_service_id'];
        $item->passengers = $data['passengers'];
        $item->flight_number = $data['flight_number'];
        $item->from_name = $data['from_name'];
        $item->to_name = $data['to_name'];
        $item->from_zone = $data['from_zone_id'];
        $item->to_zone = $data['to_zone_id'];
        $item->op_one_pickup = $data['op_one_pickup'];
        $item->op_two_pickup = $data['op_two_pickup'];

        // Conditional location updates
        if (!empty($data['from_lat'])) {
            $item->from_lat = $data['from_lat'];
            $item->from_lng = $data['from_lng'];
        }

        if (!empty($data['to_lat'])) {
            $item->to_lat = $data['to_lat'];
            $item->to_lng = $data['to_lng'];
        }

        $item->save();
    }

    protected function updateReservationExpiration(int $reservation_id, array $data): void
    {
        $booking = Reservation::find($reservation_id);
        if ($booking && $booking->is_quotation == 1) {
            $newDate = $this->calculateNewExpirationDate(date('Y-m-d H:i:s'), $data['op_one_pickup']);
            if ($newDate) {
                $booking->expires_at = $newDate;
                $booking->save();
            }
        }
    }

    protected function calculateNewExpirationDate($currentDate, $pickupDate)
    {
        $current = Carbon::parse($currentDate);
        $pickup = Carbon::parse($pickupDate);
        $daysDifference = $current->diffInDays($pickup);
    
        return match(true) {
            $daysDifference === 0 || $daysDifference === 1 => $pickup->copy()->subHours(3),
            $daysDifference >= 2 && $daysDifference <= 4 => $pickup->copy()->subDay(),
            $daysDifference >= 5 => $pickup->copy()->subDays(2),
            default => null
        };
    }
    
    protected function handleSiteChange($reservation, $newSiteId)
    {
        $site = Site::where('id', $newSiteId)->where('is_cxc', 1)->first();
        
        if ($site) {
            $reservation->is_quotation = 0;
            $reservation->expires_at = null;
            return;
        }
        
        // if ($reservation->was_is_quotation == 1) {
        //     $item = $reservation->items->first();
        //     if ($item) {
        //         $newDate = $this->calculateNewExpirationDate(now(), $item['op_one_pickup']);
        //         if ($newDate) {
        //             $reservation->is_quotation = 1;
        //             $reservation->expires_at = $newDate;
        //         }
        //     }
        // }      
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
                    <p>When you are ready, meet our uniformed Caribbean Transfers staff at the Airport. </p>
                    <img src="https://ik.imagekit.io/zqiqdytbq/transportation-api/mailing/terminals/rep.jpg?updatedAt=1725124715647" width="250">
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
                <p>Cuando esté listo, localice a nuestro personal uniformado de Caribbean Transfers en el Aeropuerto. </p>
                <img src="https://ik.imagekit.io/zqiqdytbq/transportation-api/mailing/terminals/rep.jpg?updatedAt=1725124715647" width="250">
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
        // if($destination_id == 1 && $lang == "en"):
        //     $message = '<p>The Cancun airport recommends users to arrive three hours in advance for international flights and two hours in advance for domestic flights.</p>';
        // endif;
        // if($destination_id == 1 && $lang == "es"):
        //     $message = '<p>El aeropuerto de Cancún recomienda a sus usuarios llegar con tres horas de anticipación en vuelos internacionales y dos horas en vuelos nacionales.</p>';
        // endif;

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

    public function follow_ups($request){
        $check = $this->create_followUps($request->reservation_id, "El usuario: ".auth()->user()->name.", agrego el siguiente comentario: ".$request->text, $request->type, Str::slug(strtoupper($request->name)));
        if($check){
            return response()->json(['message' => 'Follow up created successfully','success' => true], Response::HTTP_OK);
        }else{
            return response()->json(['message' => 'Error creating follow up','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}