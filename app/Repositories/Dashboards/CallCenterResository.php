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

class CallCenterResository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        return view('dashboard.callcenter', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Dashboard",
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
        $exchange_commission = 16.50;        

        $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                   AND rez.created_at BETWEEN :init AND :end 
                   AND rez.is_duplicated = 0 
                   AND us.id = :user ";
        
        $queryData = [
            'init' => "{$dates[0]} 00:00:00",
            'end' => "{$dates[1]} 23:59:59",
            'user' => $userId,
        ];
        
        // $bookings = $this->queryBookings($query, '', $queryData)->paginate(50);
        $bookings = $this->queryBookings($query, '', $queryData);

        return view('dashboard.sales_callcenter', [ 'sales' => $bookings, 'exchange' => $exchange_commission ]);
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
        $exchange_commission = 16.50;

        // Condiciones de Reservas
        // Status de reservación

        //Para la operación velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
        $paramsOperation = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'],"single");
        $queryHavingOperation = " HAVING reservation_status IN (".$paramsOperation.") ";        

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two 
                      AND rez.is_commissionable = 1 
                      AND rez.is_cancelled = 0 
                      AND rez.is_duplicated = 0 
                      AND rez.open_credit = 0 
                      AND rez.is_quotation = 0 ";

        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four 
                      AND rez.is_commissionable = 1 
                      AND rez.is_cancelled = 0 
                      AND rez.is_duplicated = 0 
                      AND rez.open_credit = 0 
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

        return view('dashboard.operations_callcenter', [ 'sales' => $operations, 'exchange' => $exchange_commission ]);
    }

    public function getStats($request)
    {
        try {
            // ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria

            $data = [
                "targets" => auth()->user()->target->object ?? [],
                "daily_goal" => 0,
                "total_day" => 0,
                "total_month" => 0,                
                "percentage_daily_goal" => 0,
                "total_services_operated" => 0,
                "subtotal_commission_operated" => 0,
                "total_commission_operated" => 0,
                "total_pending_services" => 0,
                "subtotal_commission_pending" => 0,
                "total_commission_pending" => 0,
                "percentage_commission" => 0,
            ];

            // Manejo de Fechas
            $dates = isset($request->date) && !empty($request->date) 
            ? explode(" - ", $request->date)
            : [date('Y-m-d'), date('Y-m-d')];

            $dataUser = auth()->user();
            $userId = $dataUser->id; // Obtener ID del usuario autenticado
            $percentage_commission_investment = 20;
            $exchange_commission = 16.50;

            // Condiciones de Reservas
            // Status de reservación

            //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramBookingStatus = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
            $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";

            //Para la operación velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramsOperation = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'],"single");
            $queryHavingOperation = " HAVING reservation_status IN (".$paramsOperation.") ";
                
            $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                       AND rez.created_at BETWEEN :init AND :end 
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
            $queryOne .= " AND us.id = $userId AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND us.id = $userId AND it.op_two_status IN ($params) ";

            $queryData = [
                'init' => "{$dates[0]} 00:00:00",
                'end' => "{$dates[1]} 23:59:59"
            ];

            // Asignar datos para las consultas
            $queryData['userId'] = $userId;

            // Obtener datos de reservas y operaciones
            $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);
            $operations = $this->queryOperations($queryOne, $queryTwo, $queryHavingOperation, $queryData);

            // Recorremos para poder obtener el total de venta diaria y mensual
            if( $bookings ){
                foreach ($bookings as $booking) {
                    $total_sales = $booking->currency == "USD"
                    ? ($booking->total_sales * $exchange_commission)
                    : $booking->total_sales;

                    if (Carbon::parse($booking->created_at)->isToday()) {
                        $data['total_day'] += $total_sales;
                    }
                    $data['total_month'] += $total_sales;
                }
            }

            // Recorremos para poder obtener el total de servicios pendientes y completados
            if( $operations ){
                foreach ($operations as $operation) {
                    $total_sales = $operation->currency == "USD"
                    ? ($operation->total_sales * $exchange_commission)
                    : $operation->total_sales;

                    if( ( $operation->is_round_trip == 0 && ( $operation->one_service_status == "COMPLETED" ) ) || ( $operation->is_round_trip == 1 && ( $operation->one_service_status == "COMPLETED" || $operation->two_service_status == "COMPLETED" ) ) ){
                        $data['total_services_operated'] += $total_sales;
                    }

                    if( ( $operation->is_round_trip == 0 && ( $operation->one_service_status == "PENDING" ) ) || ( $operation->is_round_trip == 1 && ( $operation->one_service_status == "PENDING" || $operation->two_service_status == "PENDING" ) ) ){
                        $data['total_pending_services'] += $total_sales;
                    }
                }
            }

            // Redondear valores finales
            $data['daily_goal'] = round($dataUser->daily_goal, 2);
            $data['total_day'] = round($data['total_day'], 2);
            $data['total_month'] = round($data['total_month'], 2);
            $data['total_services_operated'] = round($data['total_services_operated'], 2);
            // $data['total_services_operated'] = 85000;
            $data['total_pending_services'] = round($data['total_pending_services'], 2);

            $percentage_commission = ($dataUser->type_commission === 'target')
            ? array_reduce($data['targets'], function ($carry, $target) use ($data) {
                return ($data['total_services_operated'] >= $target['amount']) ? $target['percentage'] : $carry;
            }, 0)
            : $dataUser->percentage;
            $data['percentage_commission'] = $percentage_commission;

            // Calcular porcentaje que lleva para alcanzar la meta diaria
            $data['percentage_daily_goal'] = $dataUser->daily_goal > 0
                ? round(($data['total_day'] / $dataUser->daily_goal) * 100, 2)
                : 0;

            $data['subtotal_commission_operated'] = round($this->calculateTotalDiscount($data['total_services_operated'], $percentage_commission_investment), 2);            
            $data['total_commission_operated'] = round(($data['subtotal_commission_operated'] * $percentage_commission) / 100, 2);

            $data['subtotal_commission_pending'] = round($this->calculateTotalDiscount($data['total_pending_services'], $percentage_commission_investment), 2);            
            $data['total_commission_pending'] = round(($data['subtotal_commission_pending'] * $percentage_commission) / 100, 2);

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

    function calculateTotalDiscount($amount = 0, $percentage = 0):float
    {
        return $amount - ($amount * ($percentage / 100));
    }
}