<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class OperationsDataRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ),
            "filter_text" => ( isset( $request->filter_text ) && !empty( $request->filter_text ) ? $request->filter_text : NULL ),

            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),            
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "service_operation" => ( isset($request->service_operation) ? $request->service_operation : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "service_operation_status" => ( isset($request->service_operation_status) ? $request->service_operation_status : 0 ),
            "unit" => ( isset($request->unit) ? $request->unit : 0 ),
            "driver" => ( isset($request->driver) ? $request->driver : 0 ),
            "operation_status" => ( isset( $request->operation_status ) && !empty( $request->operation_status ) ? $request->operation_status : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "is_balance" => ( isset($request->is_balance) ? $request->is_balance : NULL ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "was_is_quotation" => ( isset($request->was_is_quotation) ? $request->was_is_quotation : NULL ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),
            "is_pay_at_arrival" => ( isset($request->is_pay_at_arrival) ? $request->is_pay_at_arrival : NULL ),
            "refund_request_count" => ( isset($request->refund_request_count) ? $request->refund_request_count : NULL ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ]; 

        //ESTATUS DE RESERVACIÓN
        $params = $this->parseArrayQuery(['CREDIT','PENDING','PAY_AT_ARRIVAL','CONFIRMED'],"single");            
        $havingConditions[] = " reservation_status IN (".$params.") ";

        //if( (isset( $request->reservation_status ) && !empty( $request->reservation_status )) || isset( $request->service_operation ) && !empty( $request->service_operation ) || (isset( $request->payment_status ) && !empty( $request->payment_status )) || (isset( $request->payment_method ) && !empty( $request->payment_method )) ){
            if( !empty($havingConditions) ){
                $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
            }
        //}

        // dd($queryOne, $queryTwo, $queryHaving, $queryData);
        $operations = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        return view('reports.operations_data.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reporte de operaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'operations' => $operations,
            'exchange' => $this->Exchange(date("Y-m-d", strtotime($data['init'])), date("Y-m-d", strtotime($data['end']))),
            'data' => $data,
            'request' => $request->input(),
        ]);
    }
}
