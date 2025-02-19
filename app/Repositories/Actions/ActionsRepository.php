<?php

namespace App\Repositories\Actions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS
use App\Models\Reservation;
use App\Models\ReservationsItem;

//TRAITS
use App\Traits\FollowUpTrait;

class ActionsRepository
{
    use FollowUpTrait;

    /**
     * NOS AYUDA A PODER CAMBIAR EL ESTATUS DEL SERVICIO, EN LOS DETALLES DE LA RESERVACIÓN
     * @param request :la información recibida en la solicitud
     */
    public function updateServiceStatus($request)
    {
        try {
            DB::beginTransaction();

            // Obtener el item de la reservación
            $item = ReservationsItem::with('reservations')->where('id', $request->item_id)->first();
            // dd($item->toArray());
            
            if (!$item) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ítem no encontrado'
                ], Response::HTTP_NOT_FOUND);
            }            

            // ESTATUS DE RESERVACIÓN
            $this->create_followUps($request->rez_id, "El usuario: ".auth()->user()->name.", actualizo el estatus del servicio de: (".strtoupper($request->type).") de ".( $request->type == "arrival" ? $item->op_one_status : $item->op_two_status  ). " a ".$request->status, 'HISTORY', "UPDATE_STATUS_SERVICE");

            if($request->type == "arrival"):
                $item->op_one_status = $request->status;
                $item->op_one_cancellation_type_id = ( is_numeric($request->type_cancel) ? $request->type_cancel : NULL );
            endif;

            if($request->type == "departure"):
                $item->op_two_status = $request->status;
                $item->op_two_cancellation_type_id = ( is_numeric($request->type_cancel) ? $request->type_cancel : NULL );
            endif;

            // Guardar el cambio y verificar que se guardó correctamente
            if (!$item->save()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al guardar los cambios en el ítem'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            //Declaramos variables
            $reservationId = $item->reservations->id;
            $updateReservationStatus = true;

            // Verificar si todos los items están cancelados (solo si es round trip)
            if ($item->is_round_trip) {
                $allCancelled = ReservationsItem::where('reservation_id', $reservationId)
                    ->where(function ($query) {
                        $query->where('op_one_status', '!=', 'CANCELLED')
                              ->orWhere('op_two_status', '!=', 'CANCELLED');
                    })
                    ->doesntExist(); // Si no hay ninguno distinto a 'CANCELLED', entonces todos están cancelados.
    
                $updateReservationStatus = $allCancelled;
            }

            // Actualizar la reserva solo si debe hacerse
            if ( $request->status == "CANCELLED" && $updateReservationStatus ) {
                $resultBooking = Reservation::where('id', $reservationId)->update([ 'is_cancelled' => 1, 'cancellation_type_id' => ( is_numeric($request->type_cancel) ? $request->type_cancel : NULL ) ]);
                if ( $resultBooking ) {
                    // Enviar correo en ambos casos
                    $result = $this->sendEmail("", array(
                        "code" => $item->code,
                        "email" => $item->reservations->client_email, // Usar la reserva actualizada
                        "language" => $item->reservations->language, // Usar la reserva actualizada
                        "type" => 'cancel',
                    ));

                    if( !$result['status'] ):
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Error al enviar el correo de cancelación',
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);                        
                    endif;
                }
            }
    
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Estatus actualizado con éxito',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                // 'message' => 'Error al actualizar el estatus'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function sendEmail($baseUrl = '', $request = []){
        $data = [
            "status" => false,
            "data" => NULL
        ];

        $url = "https://api.caribbean-transfers.com/api/v1/reservation/send";

        $params = array(
            'code' => $request['code'],
            'email' => $request['email'],
            'language' => $request['language'],
            'type' => $request['type'],
        );

        $ch = curl_init();
        $urlWithParams = $url . '?' . http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $urlWithParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            $data['status'] = false;
            $data['data'] = [
                'error' => [
                    'code' => 'curl_error',
                    'message' => 'Error en la solicitud cURL: '.curl_error($ch)
                ]
            ];
            return $data;
        }
        curl_close($ch);
        
        $jsonData = json_decode($response);

        //Es un JSON por lo que algo salió mal...
        $data['status'] = true;
        $data['data'] = json_decode($response, true);
        return $data;
    }
}