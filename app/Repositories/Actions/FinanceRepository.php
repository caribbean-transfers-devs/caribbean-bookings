<?php

namespace App\Repositories\Actions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

//MODELS
use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\ReservationsRefund;
use App\Models\Sale;
use App\Models\SalesType;
use App\Models\Payment;

//TRAITS
use App\Traits\FollowUpTrait;
use App\Traits\MethodsTrait;

class FinanceRepository
{
    use MethodsTrait, FollowUpTrait;

    /**
     * NOS AYUDA A PODER AGREGAR UN PAGO TIPO REEMBOLSO
     * @param request :la información recibida en la solicitud
    */
    public function addPaymentRefund($request)
    {
        try {
            DB::beginTransaction();

            // Asegurarse de que el monto sea negativo si es un reembolso
            $total = ($request->category === 'REFUND') ? -abs($request->total) : abs($request->total);
            // Validar si reservation_refund_id está presente
            $reservationRefundId = $request->filled('reservation_refund_id') ? $request->reservation_refund_id : null;            
            
            // Crear pago
            $payment = new Payment();
            $payment->description = 'Panel';
            $payment->total = $total;
            $payment->exchange_rate = $request->exchange_rate;
            $payment->status = 1;
            $payment->operation = $request->operation;
            $payment->payment_method = $request->payment_method;
            $payment->currency = $request->currency;
            $payment->reservation_id = $request->reservation_id;
            $payment->reference = $request->reference;
            $payment->reservation_refund_id = $reservationRefundId;
            $payment->user_id = auth()->user()->id;
            $payment->category = $request->category;

            if( $payment->save() ){
                if( $request->category === 'REFUND' ){
                    // Obtener tipo de venta (validar existencia)
                    $saleType = SalesType::find(6);
                    $saleDescription = $saleType ? $saleType->name : 'Reembolso';

                    // Crear venta
                    $sale = new Sale();
                    $sale->reservation_id = $request->reservation_id;
                    $sale->sale_type_id = 6;
                    $sale->description = $saleDescription;
                    $sale->quantity = 1;
                    $sale->total = ($request->operation === "division" ? ($total / $request->exchange_rate) : $total);
                    $sale->save();

                    // Registrar seguimientos
                    $this->create_followUps(
                        $request->reservation_id,
                        "El usuario: " . auth()->user()->name . " agregó una venta tipo: (" . strtoupper($saleDescription) . "), por un monto de: (" . $total . ")",
                        'HISTORY',
                        'CREATE_SALE'
                    );
                }
                
                $this->create_followUps(
                    $request->reservation_id,
                    "El usuario " . auth()->user()->name . " agregó un pago tipo: " . $request->payment_method . 
                    ", por un monto de: $total " . $request->currency . ", Categoría: " . $request->category,
                    'HISTORY',
                    'CREATE_PAYMENT'
                );

                // Actualizar estado de reembolso si aplica
                if ($reservationRefundId) {
                    $refund = ReservationsRefund::find($reservationRefundId);
                    if ($refund) {
                        $refund->update([
                            'status' => "REFUND_COMPLETED",
                            'end_at' => now(),
                            'link_refund' => $request->link_refund
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Se agrego el pago correctamente',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addPaymentCredit($request)
    {
        $validator = Validator::make($request->all(), [
            'codes' => 'required|string',
            'status_conciliation' => 'required|integer|in:1,2',
            'date_conciliation' => 'required|date',
            'receives_money_conciliation' => 'required|string|in:carlos,margarita',
            'response_message' => 'required|string'
        ], [
            'codes.required' => 'El campo de códigos es obligatorio.',
            'codes.array' => 'Los códigos deben ser proporcionados como un arreglo.',
            
            'status_conciliation.required' => 'El estado de conciliación es requerido.',
            'status_conciliation.integer' => 'El estado debe ser un valor numérico.',
            'status_conciliation.in' => 'El estado solo puede ser 1 o 2.',
            
            'date_conciliation.required' => 'La fecha de conciliación es obligatoria.',
            'date_conciliation.date' => 'El formato de fecha no es válido.',
            
            'receives_money_conciliation.required' => 'Debe especificar quién recibe el dinero.',
            'receives_money_conciliation.in' => 'El receptor solo puede ser Carlos o Margarita.',
            
            'response_message.required' => 'El mensaje de respuesta es requerido.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }
                
        try {
            DB::beginTransaction();
            
            // Crear pago
            $payment = new Payment();
            $payment->description = 'Panel';
            $payment->total = $total;
            $payment->exchange_rate = $request->exchange_rate;
            $payment->status = 1;
            $payment->operation = $request->operation;
            $payment->payment_method = $request->payment_method;
            $payment->currency = $request->currency;
            $payment->reservation_id = $request->reservation_id;
            $payment->reference = $request->reference;
            $payment->reservation_refund_id = $reservationRefundId;
            $payment->user_id = auth()->user()->id;
            $payment->category = $request->category;

            if( $payment->save() ){                
                $this->create_followUps(
                    $request->reservation_id,
                    "El usuario " . auth()->user()->name . " agregó un pago tipo: " . $request->payment_method . 
                    ", por un monto de: $total " . $request->currency . ", Categoría: " . $request->category,
                    'HISTORY',
                    'CREATE_PAYMENT'
                );
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Se agrego el pago correctamente',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * NOS AYUDA A DECLINAR UN SOLICITUD TIPO REEMBOLSO
     * @param request :la información recibida en la solicitud
    */
    public function refundNotApplicable($request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|integer',
            'reservation_refund_id' => 'required|integer',
            'response_message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        $refund = ReservationsRefund::find($request->reservation_refund_id);

        if (!$refund) {
            return response()->json([
                'errors' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Reembolso no encontrado'
                ],
                'status' => 'error',
                'message' => 'Reembolso no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            
            $refund->response_message = $request->response_message;
            $refund->status = 'REFUND_NOT_APPLICABLE';

            // Guardar el cambio y verificar que se guardó correctamente
            if (!$refund->save()) {
                DB::rollBack();
                return response()->json([
                    'errors' => [
                        'code' => 'UPDATE_ERROR',
                        'message' => 'Error al actualizar el reembolso'
                    ],                    
                    'status' => 'error',
                    'message' => 'Error al actualizar el reembolso'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Se actualizo reembolso correctamente',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * NOS AYUDA A CONCILIAR UN PAGO EN EFECTIVO
     * @param request :la información recibida en la solicitud
    */
    public function cashConciliation($request)
    {
        $validator = Validator::make($request->all(), [
            'codes' => 'required|string',
            'status_conciliation' => 'required|integer|in:1,2',
            'date_conciliation' => 'required|date',
            'receives_money_conciliation' => 'required|string|in:carlos,margarita',
            'response_message' => 'required|string'
        ], [
            'codes.required' => 'El campo de códigos es obligatorio.',
            'codes.array' => 'Los códigos deben ser proporcionados como un arreglo.',
            
            'status_conciliation.required' => 'El estado de conciliación es requerido.',
            'status_conciliation.integer' => 'El estado debe ser un valor numérico.',
            'status_conciliation.in' => 'El estado solo puede ser 1 o 2.',
            
            'date_conciliation.required' => 'La fecha de conciliación es obligatoria.',
            'date_conciliation.date' => 'El formato de fecha no es válido.',
            
            'receives_money_conciliation.required' => 'Debe especificar quién recibe el dinero.',
            'receives_money_conciliation.in' => 'El receptor solo puede ser Carlos o Margarita.',
            
            'response_message.required' => 'El mensaje de respuesta es requerido.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        $codesArray = MethodsTrait::parseArray2($request->codes ?? '');

        // Validar que tengamos códigos válidos
        if (empty($codesArray)) {
            return response()->json([
                'status' => 'error',
                'errors' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'No se encontraron los pagos'
                ],
                'message' => 'No se encontraron los pagos'
            ], Response::HTTP_NOT_FOUND);
        }        

        // Buscar los pagos correspondientes a los códigos
        $payments = Payment::whereIn('id', $codesArray)->get();

        // // Validar que se encontraron todos los pagos
        // if ($payments->count() !== count($request->codes)) {
        //     $foundCodes = $payments->pluck('code')->toArray();
        //     $missingCodes = array_diff($request->codes, $foundCodes);
            
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Algunos pagos no fueron encontrados',
        //         'missing_codes' => $missingCodes
        //     ], 404);
        // }

        if (!$payments) {
            return response()->json([
                'errors' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'No se encontraron los pagos'
                ],
                'status' => 'error',
                'message' => 'No se encontraron los pagos'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();
            
            foreach ($payments as $payment) {
                $payment->update([
                    'is_conciliated' => $request->status_conciliation,
                    'is_conciliated_cash' => $request->receives_money_conciliation,
                    'date_conciliation' => $request->date_conciliation,
                    'deposit_date' => $request->date_conciliation,
                    'total_fee' => 0,
                    'total_net' => $payment->total,
                    'conciliation_comment' => $request->response_message,
                    // Agrega aquí otros campos que necesites actualizar
                ]);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Pagos actualizados correctamente',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * NOS AYUDA A OBTENER INFORMACIÓN COMPLETA DE RESERVACIÓN
     * @param request :la información recibida en la solicitud
    */
    public function getInformationReservation($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        try {
            $reservation = $this->getReservation($request->id, $request->toArray());

            // Si la reserva no existe, se retorna vacío
            if (!$reservation) {
                return $request->expectsJson() ? response()->json([]) : response()->view('components.html.finances.basic-information', ["reservation" => null]);
            }
            
            // Retornar JSON
            return response()->json([
                'status' => 'success', 
                'message' => 'Se encontro correctamente la reservación',
                'data' => $reservation
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    /**
     * NOS AYUDA A OBTENER INFORMACIÓN BASICA DE RESERVACIÓN
     * @param request :la información recibida en la solicitud
    */
    public function getBasicInformationReservation($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        try {
            $reservation = $this->getReservation($request->id);

            // Si la reserva no existe, se retorna vacío
            if (!$reservation) {
                return $request->expectsJson() ? response()->json([]) : response()->view('components.html.finances.basic-information', ["reservation" => null]);
            }
            
            // Retornar la vista
            return view('components.html.finances.basic-information', ["reservation" => $reservation]);
        } catch (Exception $e) {
            // Log del error para depuración
            Log::error("Error en getBasicInformationReservation: " . $e->getMessage());

            // Retorno de error dependiendo del tipo de solicitud
            return $request->expectsJson() ? response()->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR) : response()->view('components.html.finances.basic-information', ["reservation" => null]);            
        }
    }

    /**
     * NOS AYUDA A OBTENER FOTOS DE RESERVACIÓN
     * @param request :la información recibida en la solicitud
    */
    public function getPhotosReservation($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {    
            $reservation = $this->getReservation($request->id);
            // dd($reservation->photos->toArray());

            // Si la reserva no existe, se retorna vacío
            if (!$reservation) {
                return $request->expectsJson() ? response()->json([]) : response()->view('components.html.finances.photos', ["photos" => null]);
            }
            
            // Retornar la vista
            return view('components.html.finances.photos', ["photos" => $reservation->photos]);
        } catch (Exception $e) {
            // Log del error para depuración
            Log::error("Error en getPhotosReservation: " . $e->getMessage());

            // Retorno de error dependiendo del tipo de solicitud
            return $request->expectsJson() ? response()->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR) : response()->view('components.html.finances.photos', ["photos" => null]);
        }
    }

    public function getHistoryReservation($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {    
            $reservation = $this->getReservation($request->id);

            // Si la reserva no existe, se retorna vacío
            if (!$reservation) {
                return $request->expectsJson() ? response()->json([]) : response()->view('components.html.finances.history', ["followUps" => null]);
            }
            
            // Retornar la vista
            return view('components.html.finances.history', ["followUps" => $reservation->followUps]);
        } catch (Exception $e) {
            // Log del error para depuración
            Log::error("Error en getHistoryReservation: " . $e->getMessage());

            // Retorno de error dependiendo del tipo de solicitud
            return $request->expectsJson() ? response()->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR) : response()->view('components.html.finances.history', ["followUps" => null]);
        }
    }

    public function getPaymentsReservation($request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $reservation = $this->getReservation($request->id);
            // dd($reservation->followUps->toArray());

            // Si la reserva no existe, se retorna vacío
            if (!$reservation) {
                return $request->expectsJson() ? response()->json([]) : response()->view('components.html.finances.payments', ["payments" => null]);
            }
            
            // Retornar la vista
            return view('components.html.finances.payments', ["payments" => $reservation->payments]);
        } catch (Exception $e) {
            // Log del error para depuración
            Log::error("Error en getPaymentsReservation: " . $e->getMessage());

            // Retorno de error dependiendo del tipo de solicitud
            return $request->expectsJson() ? response()->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR) : response()->view('components.html.finances.payments', ["payments" => null]);
        }
    }

    public function getReservation($id, array $request = []): object
    {
        $itemId = $request['item_id'] ?? null;
    
        $reservation = Reservation::select(['id', 'client_first_name', 'client_last_name', 'client_email', 'client_phone', 'currency', 'is_cancelled', 'is_commissionable', 'is_advanced', 'pay_at_arrival', 'site_id', 'destination_id', 'reference', 'cancellation_type_id', 'is_duplicated', 'open_credit', 'origin_sale_id', 'is_quotation', 'was_is_quotation', 'expires_at', 'campaign', 'reserve_rating'])
            ->with(['destination' => function($query) {
                $query->select(['id', 'name']);
            }])
            ->with(['site' => function($query) {
                $query->select(['id', 'name', 'type_site']);
            }])
            ->with(['sales' => function($query) {
                $query->select(['id', 'sale_type_id', 'description', 'quantity', 'total', 'created_at']);
            }])            
            ->with([
            // 'items' => function ($query) {
            'items' => function ($query) use ($itemId) {                
                if ($itemId) {
                    $query->where('reservations_items.id', $itemId);
                }                
                $query->join('zones as zone_one', 'zone_one.id', '=', 'reservations_items.from_zone')
                        ->join('zones as zone_two', 'zone_two.id', '=', 'reservations_items.to_zone')
                        ->select(
                            'reservations_items.*', 
                            'reservations_items.id as reservations_item_id',
                            'zone_one.name as from_zone_name',
                            'zone_one.is_primary as is_primary_from',
                            'zone_two.name as to_zone_name',
                            'zone_two.is_primary as is_primary_to',
                            // Final Service Type para zone_one
                            DB::raw("
                                CASE 
                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type_one
                            "),
                            
                            // Final Service Type para zone_two
                            DB::raw("
                                CASE 
                                    WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                    ELSE 'ARRIVAL'
                                END AS final_service_type_two
                            ")
                        );
            },
            'payments',
            'refunds',
            'followUps',
            'photos',
            'cancellationType',
            'originSale'
        ])->find($id);

        return $reservation;
    }
}