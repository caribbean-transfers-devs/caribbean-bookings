<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\GeneralTrait;
use App\Traits\QueryTrait;
use App\Traits\Reports\PaymentsTrait;

class ReportsRepository
{
    use QueryTrait, GeneralTrait, PaymentsTrait;

    public function operations($request)
    {
        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
            "filter_text" => NULL,
            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),
            "is_today" => ( isset($request->is_today) ? $request->is_today : 0 ),
            "is_balance" => ( isset($request->is_balance) ? $request->is_balance : 0 ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND it.is_round_trip = 1 ";
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];        

        if( isset($request->site) && !empty($request->site) ){
            $params = $this->parseArrayQuery($request->site);
            $queryOne .= " AND site.id IN ($params) ";
            $queryTwo .= " AND site.id IN ($params) ";
        }

        if( isset($request->unit) && !empty($request->unit) ){
            $params = $this->parseArrayQuery($request->unit);
            $queryOne .= " AND it.vehicle_id_one IN ($params) ";
            $queryTwo .= " AND it.vehicle_id_two IN ($params) ";            
        }

        if( isset($request->driver) && !empty($request->driver) ){
            $params = $this->parseArrayQuery($request->driver);
            $queryOne .= " AND it.driver_id_one IN ($params) ";
            $queryTwo .= " AND it.driver_id_two IN ($params) ";            
        }

        // dd($query, $query2, $data, $queryData, $havingConditions);
        $operations = $this->queryOperations($queryOne, $queryTwo, $queryData);

        return view('reports.operations', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Operacion del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'operations' => $operations,
            'services' => $this->Services(),
            'websites' => $this->Sites(),
            'origins' => $this->Origins(),
            'reservation_status' => $this->reservationStatus(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'currencies' => $this->Currencies(),
            'methods' => $this->Methods(),
            'cancellations' => $this->CancellationTypes(),
            'data' => $data,
        ]);
    }
}
