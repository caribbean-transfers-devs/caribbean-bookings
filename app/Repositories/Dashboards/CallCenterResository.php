<?php

namespace App\Repositories\Dashboards;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\OperationTrait;

class CallCenterResository
{
    use FiltersTrait, QueryTrait, OperationTrait;

    private $months = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];

    public function index($request)
    {
        return view('dashboard.callcenteragent', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Dashboard de agente de Call Center: ".auth()->user()->name,
                    "active" => true
                ]
            ],
        ]);
    }

    public function getSales($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $dates = isset($request->date) && !empty($request->date) 
        ? explode(" - ", $request->date) 
        : [date('Y-m-d'), date('Y-m-d')];

        $dataUser = auth()->user();
        $userId = $dataUser->id; // Obtener ID del usuario autenticado

        // Condiciones de Reservas
        // Status de reservación

        //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
        $paramBookingStatus = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
        $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";          

        $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                   AND rez.created_at BETWEEN :init AND :end 
                   AND rez.is_commissionable = 1 
                   AND rez.is_duplicated = 0 
                   AND us.id = :user ";
        
        $queryData = [
            'init' => "{$dates[0]} 00:00:00",
            'end' => "{$dates[1]} 23:59:59",
            'user' => $userId,
        ];
        
        $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);

        return view('components.html.dashboard.callcenteragent.sales', [ 'sales' => $bookings, 'exchange' => $this->ExchangeCommission($dates[0], $dates[1]) ]);
    }

    public function getOperations($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $dates = isset($request->date) && !empty($request->date) 
        ? explode(" - ", $request->date) 
        : [date('Y-m-d'), date('Y-m-d')];

        $dataUser = auth()->user();
        $userId = $dataUser->id; // Obtener ID del usuario autenticado

        // Condiciones de Reservas
        // Status de reservación

        //Para la operación velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
        $paramsOperation = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'],"single");
        $queryHavingOperation = " HAVING reservation_status IN (".$paramsOperation.") ";        

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two 
                      AND rez.is_commissionable = 1 
                      AND rez.is_cancelled = 0 
                      AND rez.is_duplicated = 0
                    --   AND rez.open_credit = 0 
                      AND rez.is_quotation = 0 ";

        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four 
                      AND rez.is_commissionable = 1 
                      AND rez.is_cancelled = 0 
                      AND rez.is_duplicated = 0 
                    --   AND rez.open_credit = 0 
                      AND rez.is_quotation = 0 
                      AND it.is_round_trip = 1 ";

        //ESTATUS DE SERVICIO
        $params = $this->parseArrayQuery([strtoupper($request->type)],"single");
        $queryOne .= " AND us.id = $userId AND it.op_one_status IN ($params) ";
        $queryTwo .= " AND us.id = $userId AND it.op_two_status IN ($params) ";

        $queryData = [
            'init' => "{$dates[0]} 00:00:00",
            'end' => "{$dates[1]} 23:59:59",
        ];

        $operations = $this->queryOperations($queryOne, $queryTwo, $queryHavingOperation, $queryData);

        return view('components.html.dashboard.callcenteragent.operations', [ 'sales' => $operations, 'exchange' => $this->ExchangeCommission($dates[0], $dates[1]) ]);
    }

    public function getStats($request)
    {
        try {
            ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria

            $dataUser = auth()->user();
            $userId = $dataUser->id; // Obtener ID del usuario autenticado
            // Manejo de Fechas
            $dates = isset($request->date) && !empty($request->date) 
            ? explode(" - ", $request->date)
            : [date('Y-m-d'), date('Y-m-d')];
            // $now = Carbon::today(); // Asegura que obtenemos la fecha del día actual sin afectar la hora
            $startDate = Carbon::parse($dates[0]); // Tomar la primera fecha
            $start = $startDate->copy()->startOfMonth();
            $end = $startDate->copy()->endOfMonth();

            $data = [
                "exchange_commission" => $this->ExchangeCommission($dates[0], $dates[1]),
                "percentage_commission_investment" => $this->PercentageCommissionInvestment(),
                "targets" => auth()->user()->target->object ?? [],
                "daily_goal" => round($dataUser->daily_goal, 2),
                "total_day" => 0,
                "total_month" => 0,                
                "percentage_daily_goal" => 0,
                "total_services_operated" => 0,
                "total_services_operated_month" => 0,
                "total_investment_discount_operated" => 0,
                "total_services_operated_investment_discount" => 0,
                "total_commission_operated" => 0,
                "total_pending_services" => 0,
                "percentage_commission" => 0,
            ];

            // Condiciones de Reservas

            //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramBookingStatus = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
            $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";

            //Para la operación velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramsOperation = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'],"single");
            $queryHavingOperation = " HAVING reservation_status IN (".$paramsOperation.") ";            
                
            $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                       AND rez.created_at BETWEEN :init AND :end 
                       AND rez.is_commissionable = 1 
                       AND rez.is_duplicated = 0 
                       AND us.id = :userId";

            $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two 
                          AND rez.is_commissionable = 1 
                          AND rez.is_cancelled = 0 
                          AND rez.is_duplicated = 0 
                        --   AND rez.open_credit = 0 
                          AND rez.is_quotation = 0 ";

            $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four 
                          AND rez.is_commissionable = 1 
                          AND rez.is_cancelled = 0 
                          AND rez.is_duplicated = 0 
                        --   AND rez.open_credit = 0 
                          AND rez.is_quotation = 0 
                          AND it.is_round_trip = 1 ";

            $params = $this->parseArrayQuery(['PENDING', 'COMPLETED'], "single");
            $params2 = $this->parseArrayQuery(['COMPLETED'], "single");
            $queryOneM = $queryOne . " AND us.id = $userId AND it.op_one_status IN ($params2) "; //Servicios operados del mes
            $queryTwoM = $queryTwo . " AND us.id = $userId AND it.op_two_status IN ($params2) "; //Servicios operados del mes
            $queryOne .= " AND us.id = $userId AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND us.id = $userId AND it.op_two_status IN ($params) ";                     

            $queryData = [
                'init' => "{$dates[0]} 00:00:00",
                'end' => "{$dates[1]} 23:59:59"
            ];

            $queryDataM = [
                'init' => $start->toDateTimeString(), // YYYY-MM-DD HH:MM:SS
                'end' => $end->toDateTimeString(),
            ];
            // dd($queryOne, $queryTwo, $queryOneM, $queryTwoM);

            // Asignar datos para las consultas
            $queryData['userId'] = $userId;

            // Obtener datos de reservas y operaciones
            $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);
            $operations = $this->queryOperations($queryOne, $queryTwo, $queryHavingOperation, $queryData);
            $operations_month = $this->queryOperations($queryOneM, $queryTwoM, $queryHavingOperation, $queryDataM);

            // Recorremos para poder obtener el total de venta diaria y mensual
            if( $bookings ){
                foreach ($bookings as $booking) {
                    $total_sales = $booking->currency == "USD"
                    ? ($booking->total_sales * $data['exchange_commission'])
                    : $booking->total_sales;

                    if (Carbon::parse($booking->created_at)->isToday()) { $data['total_day'] += $total_sales; }
                    $data['total_month'] += $total_sales;
                }
            }

            // Recorremos para poder obtener el total de servicios pendientes y completados
            if( $operations ){
                foreach ($operations as $operation) {
                    $date_ = OperationTrait::setDateTime($operation, "date");
                    $total_sales = $operation->currency == "USD"
                    ? ($operation->cost * $data['exchange_commission'])
                    : $operation->cost;

                    if( OperationTrait::serviceStatus($operation, "no_translate") == "COMPLETED" ){
                        $data['total_services_operated'] += $total_sales;
                    }

                    if( OperationTrait::serviceStatus($operation, "no_translate") == "PENDING" ){
                        $data['total_pending_services'] += $total_sales;
                    }
                }
            }

            if( $operations_month ){
                foreach ($operations_month as $operation_m) {
                    $date_ = OperationTrait::setDateTime($operation_m, "date");
                    $total_sales = $operation_m->currency == "USD"
                    ? ($operation_m->cost * $data['exchange_commission'])
                    : $operation_m->cost;

                    if( OperationTrait::serviceStatus($operation_m, "no_translate") == "COMPLETED" ){
                        $data['total_services_operated_month'] += $total_sales;
                    }
                }
            }

            // Redondear valores finales
            $data['total_day'] = round($data['total_day'], 2);
            $data['total_month'] = round($data['total_month'], 2);
            $data['total_services_operated'] = round($data['total_services_operated'], 2);
            $data['total_services_operated_month'] = round($data['total_services_operated_month'], 2);
            $data['total_pending_services'] = round($data['total_pending_services'], 2);            

            //Recorremos la reglas para ver cual coincide en caso de no coindir coloca el 4% de comisión defaul
            // $percentage_commission = ($dataUser->type_commission === 'target')
            // ? array_reduce($data['targets'], function ($carry, $target) use ($data) {
            //     return ($data['total_services_operated'] >= $target['amount']) ? $target['percentage'] : $carry;
            // }, 0)
            // : $dataUser->percentage;
            $data['percentage_commission'] = $dataUser->type_commission === 'target' ? 0 : $dataUser->percentage;
            foreach ($data['targets'] as &$target) {
                if ($data['total_services_operated_month'] >= $target['amount']) {
                    $data['percentage_commission'] = $target['percentage'];
                    $target['status'] = true; // Modificar status a true donde se obtiene el percentage
                }
            }
            unset($target); // Romper la referencia para evitar efectos secundarios

            // Calcular porcentaje que lleva para alcanzar la meta diaria
            $data['percentage_daily_goal'] = $dataUser->daily_goal > 0
                ? round(($data['total_day'] / $dataUser->daily_goal) * 100, 2)
                : 0;

            //Desglose de las comisiones de servicios operados
            $data['total_investment_discount_operated'] = round( ($data['total_services_operated_month'] * ( $data['percentage_commission_investment'] / 100) ), 2);
            $data['total_services_operated_investment_discount'] = round($this->calculateTotalDiscount($data['total_services_operated_month'], $data['percentage_commission_investment']), 2);
            $data['total_commission_operated'] = round(( $data['total_services_operated_investment_discount'] * $data['percentage_commission'] ) / 100, 2);

            //Desglose de las comisiones de servicios pendientes de operar
            // $data['total_investment_discount_pending'] = round( ($data['total_pending_services'] * ( $data['percentage_commission_investment'] / 100 ) ), 2);
            // $data['total_services_pending_investment_discount'] = round($this->calculateTotalDiscount($data['total_pending_services'], $data['percentage_commission_investment']), 2);            
            // $data['total_commission_pending'] = round(( $data['total_services_pending_investment_discount'] * $data['percentage_commission'] ) / 100, 2);

            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }

    public function chartsSales($request)
    {
        try {
            // ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria

            $dataUser = auth()->user();
            $userId = $dataUser->id; // Obtener ID del usuario autenticado
            // Manejo de Fechas
            $dates = isset($request->date) && !empty($request->date) 
            ? explode(" - ", $request->date) 
            : [date('Y-m-d'), date('Y-m-d')];            
            // $now = Carbon::today(); // Asegura que obtenemos la fecha del día actual sin afectar la hora
            $startDate = Carbon::parse($dates[0]); // Tomar la primera fecha
            $start = $startDate->copy()->startOfMonth();
            $end = $startDate->copy()->endOfMonth();
            $exchange_commission = $this->ExchangeCommission($start->toDateString(), $end->toDateString());
            $data = $this->dataSales($start, $end);

            // Condiciones de Reservas
            // Status de reservación
            
            //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramBookingStatus = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
            $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";

            $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                       AND rez.created_at BETWEEN :init AND :end 
                       AND rez.is_commissionable = 1 
                       AND rez.is_duplicated = 0 
                       AND us.id = :userId";

            $queryData = [
                'init' => $start->toDateTimeString(), // YYYY-MM-DD HH:MM:SS
                'end' => $end->toDateTimeString(),
                'userId' => $userId
            ];

            // Obtener datos de reservas y operaciones
            $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);

            // Recorremos para poder obtener el total de venta diaria y mensual
            if( $bookings ){
                foreach ($bookings as $booking) {
                    $date_ = date("Y-m-d", strtotime( $booking->created_at ));
                    $total_sales = $booking->currency == "USD"
                    ? ($booking->total_sales * $exchange_commission)
                    : $booking->total_sales;

                    if( isset($data[$date_]) ){
                        $data[$date_]['TOTAL'] += round($total_sales,2);
                        $data[$date_][$booking->currency] += round($booking->total_sales,2);
                        $data[$date_]['QUANTITY'] ++;
                        $data[$date_]['BOOKINGS'][] = $booking;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                // 'date' => $start->toDateString().' - '.$end->toDateString(),
                // 'date' => $start->translatedFormat('j \d\e F') . ' al ' . $end->translatedFormat('j \d\e F \d\e\l Y'),
                'date' => $start->day . ' de ' . $this->months[$start->month] . ' al ' . $end->day . ' de ' . $this->months[$end->month] . ' del ' . $end->year,
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //NOS TRAE LA DATA PARA LA GRAFICA DE VENTAS DEL MES CORRIENTE
    public function chartsOperations($request)
    {
        try {
            // ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria

            $dataUser = auth()->user();
            $userId = $dataUser->id; // Obtener ID del usuario autenticado
            // Manejo de Fechas
            $dates = isset($request->date) && !empty($request->date) 
            ? explode(" - ", $request->date) 
            : [date('Y-m-d'), date('Y-m-d')];            
            // $now = Carbon::today(); // Asegura que obtenemos la fecha del día actual sin afectar la hora
            $startDate = Carbon::parse($dates[0]); // Tomar la primera fecha
            $start = $startDate->copy()->startOfMonth();
            $end = $startDate->copy()->endOfMonth();
            $exchange_commission = $this->ExchangeCommission($start->toDateString(), $end->toDateString());
            $data = $this->dataSalesOperation($start, $end);

            // Condiciones de Reservas
            // Status de reservación

            //Para la operación velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramsOperation = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'],"single");
            $queryHavingOperation = " HAVING reservation_status IN (".$paramsOperation.") ";

            $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two 
                          AND rez.is_commissionable = 1 
                          AND rez.is_cancelled = 0 
                          AND rez.is_duplicated = 0 
                        --   AND rez.open_credit = 0 
                          AND rez.is_quotation = 0 ";

            $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four 
                          AND rez.is_commissionable = 1 
                          AND rez.is_cancelled = 0 
                          AND rez.is_duplicated = 0 
                        --   AND rez.open_credit = 0 
                          AND rez.is_quotation = 0 
                          AND it.is_round_trip = 1 ";

            $params = $this->parseArrayQuery(['PENDING', 'COMPLETED'], "single");
            $queryOne .= " AND us.id = $userId AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND us.id = $userId AND it.op_two_status IN ($params) ";

            $queryData = [
                'init' => $start->toDateTimeString(), // YYYY-MM-DD HH:MM:SS
                'end' => $end->toDateTimeString()
            ];

            // Obtener datos de reservas y operaciones
            $operations = $this->queryOperations($queryOne, $queryTwo, $queryHavingOperation, $queryData);

            // Recorremos para poder obtener el total de servicios pendientes y completados
            if( $operations ){
                foreach ($operations as $operation) {
                    $date_ = OperationTrait::setDateTime($operation, "date");
                    $total_sales = $operation->currency == "USD"
                    ? ($operation->cost * $exchange_commission)
                    : $operation->cost;

                    if( isset($data[$date_]) ){
                        $data[$date_]['TOTAL'] += round($total_sales,2);
                        $data[$date_][$operation->currency] += round($operation->cost,2);
                        $data[$date_]['QUANTITY'] ++;

                        $data[$date_][OperationTrait::serviceStatus($operation, "no_translate")]['TOTAL'] += round($total_sales,2);
                        $data[$date_][OperationTrait::serviceStatus($operation, "no_translate")][$operation->currency] += round($operation->cost,2);
                        $data[$date_][OperationTrait::serviceStatus($operation, "no_translate")]['QUANTITY'] ++;
                        $data[$date_][OperationTrait::serviceStatus($operation, "no_translate")]['BOOKINGS'][] = $operation;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                // 'date' => $start->toDateString().' - '.$end->toDateString(),
                // 'date' => $start->translatedFormat('j \d\e F') . ' al ' . $end->translatedFormat('j \d\e F \d\e\l Y'),
                'date' => $start->day . ' de ' . $this->months[$start->month] . ' al ' . $end->day . ' de ' . $this->months[$end->month] . ' del ' . $end->year,
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }

    function calculateTotalDiscount($amount = 0, $percentage = 0):float
    {
        return $amount - ($amount * ($percentage / 100));
    }

    //calcular monto a descontar
    public function dataSales($start_d, $end_d)
    {
        $bookings_month = [];
        $date = clone $start_d; // Clonar la fecha antes de modificarla
    
        while ($date->lte($end_d)) {
            $bookings_month[$date->toDateString()] = [
                "DATE" => $date->format('j M'),
                "TOTAL" => 0,
                "USD" => 0,
                "MXN" => 0,
                "QUANTITY" => 0,
                "BOOKINGS" => [],
            ];
            $date->addDay(); // Modificamos solo la copia, no la original
        }
    
        return $bookings_month;
    }

    //calcular monto a descontar
    public function dataSalesOperation($start_d, $end_d)
    {
        $bookings_month = [];
        $date = clone $start_d; // 🔹 Clonar la fecha antes de modificarla

        while ($date->lte($end_d)) {
            $bookings_month[$date->toDateString()] = [
                "DATE" => $date->format('j M'),
                "TOTAL" => 0,
                "USD" => 0,
                "MXN" => 0,
                "QUANTITY" => 0,
                "COMPLETED" => [
                    "TOTAL" => 0,
                    "USD" => 0,
                    "MXN" => 0,
                    "QUANTITY" => 0,
                    "BOOKINGS" => [],
                ],
                "PENDING" => [
                    "TOTAL" => 0,
                    "USD" => 0,
                    "MXN" => 0,
                    "QUANTITY" => 0,
                    "BOOKINGS" => [],
                ],
            ];
            $date->addDay(); // Modificamos solo la copia, no la original
        }

        return $bookings_month;
    }

    public function destinationsList($request){
        try{
            //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramBookingStatus = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
            $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";
            
            $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                        AND rez.created_at BETWEEN :init AND :end 
                        AND rez.is_duplicated = 0 ";

            $dates = isset($request->date) && !empty($request->date) 
            ? explode(" - ", $request->date) 
            : ['2024-12-01', '2025-02-28'];                    

            $queryData = [
                'init' => "{$dates[0]} 00:00:00",
                'end' => "{$dates[1]} 23:59:59",
            ];

            $data = [];

            $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);

            if( $bookings ){
                foreach ($bookings as $booking) {
                    if( !isset($data[Str::slug($booking->from_name)]) ){
                        $data[Str::slug($booking->from_name)] = [
                            "NAME" => $booking->from_name,
                            "QUANTITY" => 0,
                            "TYPO_SERVICE" => [
                                "ONEWEY" => 0,
                                "ROUDTRIP" => 0
                            ]
                        ];
                    }
                    if( !isset($data[Str::slug($booking->to_name)]) ){
                        $data[Str::slug($booking->to_name)] = [
                            "NAME" => $booking->to_name,
                            "QUANTITY" => 0,
                            "TYPO_SERVICE" => [
                                "ONEWEY" => 0,
                                "ROUDTRIP" => 0
                            ]
                        ];
                    }
                    

                    $data[Str::slug($booking->from_name)]['QUANTITY'] ++;
                    $data[Str::slug($booking->to_name)]['QUANTITY'] ++;

                    if( $booking->is_round_trip == 0 ){
                        $data[Str::slug($booking->from_name)]['TYPO_SERVICE']['ONEWEY'] ++;
                        $data[Str::slug($booking->from_name)]['TYPO_SERVICE']['ONEWEY'] ++;
                    }
                    if( $booking->is_round_trip > 0 ){
                        $data[Str::slug($booking->to_name)]['TYPO_SERVICE']['ROUDTRIP'] ++;
                        $data[Str::slug($booking->to_name)]['TYPO_SERVICE']['ROUDTRIP'] ++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}