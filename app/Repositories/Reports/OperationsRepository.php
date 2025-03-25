<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class OperationsRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ),
            // "filter_text" => NULL,
            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
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
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),
            // "is_balance" => ( isset($request->is_balance) ? $request->is_balance : NULL ),
            // "is_today" => ( isset($request->is_today) ? $request->is_today : NULL ),
        ];

        // AND rez.is_cancelled = 0
        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 ";
        // AND rez.is_cancelled = 0
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];

        //TIPO DE SERVICIO is_round_trip
        if(isset( $request->is_round_trip )){
            $params = $this->parseArrayQuery($request->is_round_trip);
            $queryOne .= " AND it.is_round_trip IN ($params) ";
            $queryTwo .= " AND it.is_round_trip IN ($params) ";
        }

        //SITIO
        if( isset($request->site) && !empty($request->site) ){
            $params = $this->parseArrayQuery($request->site);
            $queryOne .= " AND site.id IN ($params) ";
            $queryTwo .= " AND site.id IN ($params) ";
        }

        //ORIGEN DE VENTA
        if(isset( $request->origin ) && !empty( $request->origin )){
            $queryweb = "";
            if( in_array("0", $request->origin) ){
                $queryweb = " OR origin.id IS NULL ";
            }
            $params = $this->parseArrayQuery($request->origin);
            $queryOne .= " AND ( origin.id IN ($params) $queryweb ) ";
            $queryTwo .= " AND ( origin.id IN ($params) $queryweb ) ";
        }        

        //ESTATUS DE RESERVACIÓN
        if(isset( $request->reservation_status ) && !empty( $request->reservation_status )){
            $params = $this->parseArrayQuery($request->reservation_status,"single");            
            $havingConditions[] = " reservation_status IN (".$params.") ";
        }

        //TIPO DE SERVICIO EN OPERACIÓN
        if(isset( $request->service_operation ) && !empty( $request->service_operation )){
            $params = $this->parseArrayQuery($request->service_operation,"single");
            $havingConditions[] = " final_service_type IN (".$params.") ";
        }        

        //TIPO DE VEHÍCULO
        if(isset( $request->product_type ) && !empty( $request->product_type )){
            $params = $this->parseArrayQuery($request->product_type);            
            $queryOne .= " AND serv.id IN ($params) ";
            $queryTwo .= " AND serv.id IN ($params) ";
        }

        //ZONA DE ORIGEN
        if(isset( $request->zone_one_id ) && !empty( $request->zone_one_id )){
            $params = $this->parseArrayQuery($request->zone_one_id);
            $queryOne .= " AND zone_one.id IN ($params) ";
            $queryTwo .= " AND zone_one.id IN ($params) ";
        }

        //ZONA DE DESTINO
        if(isset( $request->zone_two_id ) && !empty( $request->zone_two_id )){
            $params = $this->parseArrayQuery($request->zone_two_id);
            $queryOne .= " AND zone_two.id IN ($params) ";
            $queryTwo .= " AND zone_two.id IN ($params) ";
        }
        
        //ESTATUS DE SERVICIO
        if(isset( $request->service_operation_status ) && !empty( $request->service_operation_status )){
            $params = $this->parseArrayQuery($request->service_operation_status,"single");            
            $queryOne .= " AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND it.op_two_status IN ($params) ";
        }

        //UNIDAD ASIGNADA AL SERVICIO
        if( isset($request->unit) && !empty($request->unit) ){
            $params = $this->parseArrayQuery($request->unit);
            $queryOne .= " AND it.vehicle_id_one IN ($params) ";
            $queryTwo .= " AND it.vehicle_id_two IN ($params) ";
        }

        //CONDUCTOR ASIGNADO AL SERVICIO
        if( isset($request->driver) && !empty($request->driver) ){
            $params = $this->parseArrayQuery($request->driver);
            $queryOne .= " AND it.driver_id_one IN ($params) ";
            $queryTwo .= " AND it.driver_id_two IN ($params) ";
        }

        //ESTATUS DE SERVICIO
        if(isset( $request->operation_status ) && !empty( $request->operation_status )){
            $params = $this->parseArrayQuery($request->operation_status,"single");            
            $queryOne .= " AND it.op_one_status_operation IN ($params) ";
            $queryTwo .= " AND it.op_two_status_operation IN ($params) ";
        }

        //ESTATUS DE PAGO
        if(isset( $request->payment_status ) && !empty( $request->payment_status )){
            $params = $this->parseArrayQuery($request->payment_status,"single");
            $havingConditions[] = " payment_status IN (".$params.") ";
        }

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $queryOne .= " AND rez.currency IN ($params) ";
            $queryTwo .= " AND rez.currency IN ($params) ";
        }

        //METODO DE PAGO
        if(isset( $request->payment_method ) && !empty( $request->payment_method )){
            $params = "";
            foreach( $request->payment_method as $key => $payment_method ){
                $params .= "FIND_IN_SET('".$payment_method."', payment_type_name) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $havingConditions[] = " (".$params.") "; 
        }

        //MOTIVOS DE CANCELACIÓN
        if(isset( $request->cancellation_status ) && !empty( $request->cancellation_status )){
            $params = $this->parseArrayQuery($request->cancellation_status);
            $queryOne .= " AND tc.id IN ($params) ";
            $queryTwo .= " AND tc.id IN ($params) ";
        }        

        if( (isset( $request->reservation_status ) && !empty( $request->reservation_status )) || isset( $request->service_operation ) && !empty( $request->service_operation ) || (isset( $request->payment_status ) && !empty( $request->payment_status )) || (isset( $request->payment_method ) && !empty( $request->payment_method )) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        // dd($queryOne, $queryTwo, $queryHaving, $queryData);
        $operations = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        return view('reports.operations.index', [
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
