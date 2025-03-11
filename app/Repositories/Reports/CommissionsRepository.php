<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\MethodsTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\OperationTrait;

class CommissionsRepository
{
    use MethodsTrait, FiltersTrait, QueryTrait, OperationTrait;

    private $months = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];    

    public function index($request)
    {
        return view('reports.commissions.index2', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reporte de comisiones: ",
                    "active" => true
                ]
            ],
        ]);
    }

    public function getStats($request)
    {
        try {
            ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria

            $userArray = MethodsTrait::parseArray($request->user ?? '');            
            $dataUser = MethodsTrait::DataUser($userArray);
            // Manejo de Fechas
            $dates = MethodsTrait::parseDateRange($request->date ?? '');
            
            $data = [
                "EXCHANGE_COMMISSION" => FiltersTrait::ExchangeCommission($dates['init'], $dates['end']),
                "PERCENTAGE_COMMISSION_INVESTMENT" => FiltersTrait::PercentageCommissionInvestment(),
                "TOTAL_SALES" => 0,
                "TOTAL_OPERATIONS" => 0,
                "TOTAL_COMMISSIONS" => 0,
                "DATA" => []
            ];

            //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramBookingStatus = MethodsTrait::parseArrayQuery2(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
            $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";

            //Para la operación velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramsOperation = MethodsTrait::parseArrayQuery2(['CONFIRMED', 'CREDIT', 'OPENCREDIT'],"single");
            $queryHavingOperation = " HAVING reservation_status IN (".$paramsOperation.") ";            
                
            $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                       AND rez.created_at BETWEEN :init AND :end 
                       AND rez.is_commissionable = 1 
                       AND rez.is_duplicated = 0 ";

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

            $params = MethodsTrait::parseArrayQuery2(['COMPLETED'], "single");
            $queryOne .= " AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND it.op_two_status IN ($params) ";

            $queryData['init'] = $dates['init']." 00:00:00";
            $queryData['end'] = $dates['end']." 23:59:59";

            if ($userArray) {
                $params = MethodsTrait::parseArrayQuery2($userArray, 'single');
                $query .= " AND us.id IN ($params) ";
                $queryOne .= " AND us.id IN ($params) ";
                $queryTwo .= " AND us.id IN ($params) ";
            }

            // Obtener datos de reservas y operaciones
            $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);
            $operations = $this->queryOperations($queryOne, $queryTwo, $queryHavingOperation, $queryData);

            // Recorremos para poder obtener el total de venta diaria y mensual
            if( $bookings ){
                foreach ($bookings as $booking) {
                    $total_sales = $booking->currency == "USD"
                    ? ($booking->total_sales * $data['EXCHANGE_COMMISSION'])
                    : $booking->total_sales;

                    $data['TOTAL_SALES'] += $total_sales;
                }
            }

            // Recorremos para poder obtener el total de servicios pendientes y completados
            if( $operations ){
                foreach ($operations as $operation) {
                    $total_sales = $operation->currency == "USD"
                    ? ($operation->cost * $data['EXCHANGE_COMMISSION'])
                    : $operation->cost;

                    $data['TOTAL_OPERATIONS'] += $total_sales;

                    $user = $dataUser->where('id', $operation->employee_code)->first();
                    if ( !isset($data["DATA"][$operation->employee_code]) && !empty($user) ) {
                        $data["DATA"][$operation->employee_code] = [
                            "NAME" => $operation->employee,
                            "TOTAL" => 0,
                            "USD" => 0,
                            "MXN" => 0,
                            "QUANTITY" => 0,
                            "SETTINGS" => [
                                'daily_goal' => $user->daily_goal ?? 0,
                                'type_commission' => $user->type_commission ?? "target",
                                'percentage' => $user->percentage ?? 0,
                                'targets' => $user->target->object ?? [],
                            ]
                        ];
                    }
                    
                    if( isset($data["DATA"][$operation->employee_code]) && !empty($user) ){
                        $data["DATA"][$operation->employee_code]['TOTAL'] += round($total_sales, 2);
                        $data["DATA"][$operation->employee_code][$booking->currency] += round($operation->cost, 2);
                        $data["DATA"][$operation->employee_code]['QUANTITY'] ++;
                    }
                }
            }

            foreach ($data['DATA'] as $item) {
                $percentage_commission = $item['SETTINGS']['type_commission'] === 'target' ? 0 : $item['SETTINGS']['percentage'];
                $targets = is_string($item['SETTINGS']['targets']) ? json_decode($item['SETTINGS']['targets'], true) : $item['SETTINGS']['targets'];
                foreach ( $targets as &$target ) {
                    if ($item['TOTAL'] >= $target['amount']) {
                        $percentage_commission = $target['percentage'];
                        $target['status'] = true; // Modificar status a true donde se obtiene el percentage
                    }
                }
                unset($targets); // Romper la referencia para evitar efectos secundarios

                //Desglose de las comisiones de servicios operados
                $total_investment_discount_operated = round( ($item['TOTAL'] * ( $data['PERCENTAGE_COMMISSION_INVESTMENT'] / 100) ), 2);
                $total_services_operated_investment_discount = round(MethodsTrait::calculateTotalDiscount($item['TOTAL'], $data['PERCENTAGE_COMMISSION_INVESTMENT']), 2);
                $total_commission_operated = round(( $total_services_operated_investment_discount * $percentage_commission ) / 100, 2);
                $data['TOTAL_COMMISSIONS'] += $total_commission_operated;
            }

            // Redondear valores finales
            $data['TOTAL_SALES'] = round($data['TOTAL_SALES'], 2);
            $data['TOTAL_OPERATIONS'] = round($data['TOTAL_OPERATIONS'], 2);
            $data['TOTAL_COMMISSIONS'] = round($data['TOTAL_COMMISSIONS'], 2);

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
            ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria
            
            $userArray = MethodsTrait::parseArray($request->user ?? '');
            $dataUser = MethodsTrait::DataUser($userArray);
            // Manejo de Fechas
            $dates = MethodsTrait::parseDateRange($request->date ?? '');
            $datesMonth = MethodsTrait::parseDateRangeMonth($dates['init']);
            $exchange_commission = FiltersTrait::ExchangeCommission($dates['init'], $dates['end']);            
            $data = MethodsTrait::SalesArrayStructure($datesMonth['initCarbon'], $datesMonth['endCarbon'], "users", $dataUser->toArray());
            
            //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
            $paramBookingStatus = MethodsTrait::parseArrayQuery2(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
            $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";

            $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                       AND rez.created_at BETWEEN :init AND :end 
                       AND rez.is_commissionable = 1 
                       AND rez.is_duplicated = 0 ";

            $queryData = [
                'init' => $datesMonth['initTime'],
                'end' => $datesMonth['endTime']
            ];

            if ($userArray) {
                $params = MethodsTrait::parseArrayQuery2($userArray, 'single');
                $query .= " AND us.id IN ($params) ";
            }

            $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);

            // Recorremos para poder obtener el total de venta diaria y mensual
            if ($bookings) {
                foreach ($bookings as $booking) {
                    $date_ = date("Y-m-d", strtotime($booking->created_at));
                    $total_sales = ($booking->currency == "USD") 
                        ? ($booking->total_sales * $exchange_commission) 
                        : $booking->total_sales;
    
                    if (isset($data[$date_])) {
                        $data[$date_]['TOTAL'] += round($total_sales, 2);
                        $data[$date_][$booking->currency] += round($booking->total_sales, 2);
                        $data[$date_]['QUANTITY']++;
                        $data[$date_]['BOOKINGS'][] = $booking;
    
                        if (isset($data[$date_]['DATA'][$booking->employee_code])) {
                            $data[$date_]['DATA'][$booking->employee_code]['TOTAL'] += round($total_sales, 2);
                            $data[$date_]['DATA'][$booking->employee_code][$booking->currency] += round($booking->total_sales, 2);
                            $data[$date_]['DATA'][$booking->employee_code]['QUANTITY']++;
                            $data[$date_]['DATA'][$booking->employee_code]['BOOKINGS'][] = $booking;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                'date' => $datesMonth['initCarbon']->day . ' de ' . $this->months[$datesMonth['initCarbon']->month] . ' al ' . $datesMonth['endCarbon']->day . ' de ' . $this->months[$datesMonth['endCarbon']->month] . ' del ' . $datesMonth['initCarbon']->year,
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function chartsOperations($request)
    {
        try {
            ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el tiempo de ejecución, pero evita desactivar los límites de memoria

            $userArray = MethodsTrait::parseArray($request->user ?? '');
            $dataUser = MethodsTrait::DataUser($userArray);
            // Manejo de Fechas
            $dates = MethodsTrait::parseDateRange($request->date ?? '');
            $datesMonth = MethodsTrait::parseDateRangeMonth($dates['init']);
            $exchange_commission = FiltersTrait::ExchangeCommission($dates['init'], $dates['end']);
            $data = MethodsTrait::SalesArrayStructure($datesMonth['initCarbon'], $datesMonth['endCarbon'], "users", $dataUser->toArray());

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

            $params = $this->parseArrayQuery(['COMPLETED'], "single");
            $queryOne .= " AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND it.op_two_status IN ($params) ";

            $queryData = [
                'init' => $datesMonth['initTime'],
                'end' => $datesMonth['endTime']
            ];

            if ($userArray) {
                $params = MethodsTrait::parseArrayQuery2($userArray, 'single');
                $queryOne .= " AND us.id IN ($params) ";
                $queryTwo .= " AND us.id IN ($params) ";
            }

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
                        $data[$date_]['BOOKINGS'][] = $operation;

                        if ( isset($data[$date_]['DATA'][$operation->employee_code]) ) {
                            $data[$date_]['DATA'][$operation->employee_code]['TOTAL'] += round($total_sales, 2);
                            $data[$date_]['DATA'][$operation->employee_code][$operation->currency] += round($operation->cost, 2);
                            $data[$date_]['DATA'][$operation->employee_code]['QUANTITY']++;
                            $data[$date_]['DATA'][$operation->employee_code]['BOOKINGS'][] = $operation;
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                'date' => $datesMonth['initCarbon']->day . ' de ' . $this->months[$datesMonth['initCarbon']->month] . ' al ' . $datesMonth['endCarbon']->day . ' de ' . $this->months[$datesMonth['endCarbon']->month] . ' del ' . $datesMonth['initCarbon']->year,                
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
