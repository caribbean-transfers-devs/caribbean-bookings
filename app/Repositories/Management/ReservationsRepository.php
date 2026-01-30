<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;

//TRAITS
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class ReservationsRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite

        $data = [
            "init" => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 00:00:00",
            "end" => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 23:59:59",
            "filter_text" => ( isset( $request->filter_text ) && !empty( $request->filter_text ) ? $request->filter_text : NULL ),

            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "is_today" => ( isset($request->is_today) ? $request->is_today : NULL ),
            "is_duplicated" => ( isset($request->is_duplicated) ? $request->is_duplicated : NULL ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),

            "users" => ( isset($request->user) ? $request->user : NULL ),
            
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "is_balance" => ( isset($request->is_balance) ? $request->is_balance : NULL ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "reserve_rating" => ( isset($request->reserve_rating) ? $request->reserve_rating : NULL ),
            "is_commissionable" => ( isset($request->is_commissionable) ? $request->is_commissionable : NULL ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),
            "is_pay_at_arrival" => ( isset($request->is_pay_at_arrival) ? $request->is_pay_at_arrival : NULL ),            
        ];
        
        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end ';
        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.is_round_trip = 1 ";

        $havingConditions = []; $queryHaving = '';
        $havingConditionsA = []; $queryHavingA = '';
        $havingConditionsD = []; $queryHavingD = '';
        $queryData = [
            'init' => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 00:00:00",
            'end' => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 23:59:59",
        ];

        $queryDataOperation = [
            'init' => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 00:00:00",
            'end' => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 23:59:59",
        ];

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

        //TIPO DE SERVICIO is_round_trip
        if(isset( $request->is_round_trip )){
            $params = "";
            foreach( $request->is_round_trip as $key => $is_round_trip ){
                $queryData['is_round_trip' . $key] = $is_round_trip;
                $params .= "FIND_IN_SET(:is_round_trip".$key.", is_round_trip) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $query .= " AND (".$params.") ";            
        }

        //RESERVAS OPERADAS EL MISMO DIA DE SU CREACION
        if(isset( $request->is_today )){
            $havingConditions[] = ( $request->is_today == 1 ? ' is_today != 0 ' : ' is_today = 0 ' );
        }

        //DUPLICADAS
        if(isset( $request->is_duplicated )){
            $query .= " AND rez.is_duplicated = 1 ";
        }        

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $query .= " AND rez.currency IN ($params) ";
        }

        //USUARIO
        if(isset( $request->user ) && !empty( $request->user )){
            $queryweb = "";
            if( in_array("0", $request->user) ){
                $queryweb = " OR us.id IS NULL ";
            }
            $params = $this->parseArrayQuery($request->user);
            $query .= " AND ( us.id IN ($params) $queryweb ) ";
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
        if(isset( $request->reservation_status ) && !empty( $request->reservation_status )){
            $params = $this->parseArrayQuery($request->reservation_status,"single");
            $havingConditions[] = " reservation_status IN (".$params.") ";
        }

        //TIPO DE SERVICIO EN OPERACIÓN        
        $paramsArrival = $this->parseArrayQuery(['ARRIVAL','DEPARTURE'],"single");
        $havingConditionsA[] = " final_service_type IN (".$paramsArrival.") ";

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

        //ESTATUS DE PAGO
        if(isset( $request->payment_status ) && !empty( $request->payment_status )){
            $params = $this->parseArrayQuery($request->payment_status,"single");
            $havingConditions[] = " payment_status IN (".$params.") ";
        }        

        //RESERVAS CON UN BALANCE
        if(isset( $request->is_balance )){
            $havingConditions[] = ( $request->is_balance == 1 ? ' total_balance > 0 ' : ' total_balance <= 0 ' );
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

        //TIPO DE LIKE
        if(isset( $request->reserve_rating )){
            $params = $request->reserve_rating;
            $query .= " AND rez.reserve_rating = $params ";      
        }        

        //RESERVAS COMISIONABLES
        if(isset( $request->is_commissionable )){
            $params = $request->is_commissionable;
            $query .= " AND rez.is_commissionable = $params ";
        }

        //MOTIVOS DE CANCELACIÓN
        if(isset( $request->cancellation_status ) && !empty( $request->cancellation_status )){
            $params = $this->parseArrayQuery($request->cancellation_status);
            $query .= " AND tc.id IN ($params) ";
        }        

        //PAGO A LA LLEGADA
        if( $request->is_pay_at_arrival !=  NULL ){
            $params = $request->is_pay_at_arrival;
            $query .= " AND rez.pay_at_arrival = $params ";
        }

        if( !empty($havingConditions) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        if( !empty($havingConditionsA) ){
            $queryHavingA = " HAVING " . implode(' AND ', $havingConditionsA);
        }

        // dd($query, $queryHaving, $queryData);
        $bookings = $this->queryBookings($query, $queryHaving, $queryData);
        $items = $this->queryOperations($queryOne, $queryTwo, $queryHavingA, $queryDataOperation);

        $arrivalItems = array_filter($items, function ($item) {
            return isset($item->final_service_type) && $item->final_service_type === 'ARRIVAL';
        });

        $departureItems = array_filter($items, function ($item) {
            return isset($item->final_service_type) && $item->final_service_type === 'DEPARTURE';
        });        
        
        return view('management.reservations.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de reservaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'bookings' => $bookings,
            'arrivals' => $arrivalItems,
            'departures' => $departureItems,
            'data' => $data,
        ]);
    }
}