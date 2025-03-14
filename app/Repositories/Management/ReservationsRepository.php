<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;

//TRAITS
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\FollowUpTrait;
use App\Traits\Reports\PaymentsTrait;

class ReservationsRepository
{
    use FiltersTrait, QueryTrait, PaymentsTrait, FollowUpTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite

        $data = [
            "init" => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 00:00:00",
            "end" => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 23:59:59",
            "filter_text" => NULL,
            "is_round_trip" => ( isset($request->is_round_trip) ? $request->is_round_trip : NULL ),
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "origin" => ( isset($request->origin) ? $request->origin : NULL ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "product_type" => ( isset($request->product_type) ? $request->product_type : 0 ),
            "zone_one_id" => ( isset($request->zone_one_id) ? $request->zone_one_id : 0 ),
            "zone_two_id" => ( isset($request->zone_two_id) ? $request->zone_two_id : 0 ),
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
            "is_commissionable" => ( isset($request->is_commissionable) ? $request->is_commissionable : NULL ),
            "is_pay_at_arrival" => ( isset($request->is_pay_at_arrival) ? $request->is_pay_at_arrival : NULL ),
            "cancellation_status" => ( isset( $request->cancellation_status ) && !empty( $request->cancellation_status ) ? $request->cancellation_status : 0 ),            
            "is_balance" => ( isset($request->is_balance) ? $request->is_balance : NULL ),
            "is_today" => ( isset($request->is_today) ? $request->is_today : NULL ),
            "is_duplicated" => ( isset($request->is_duplicated) ? $request->is_duplicated : NULL ),
        ];
        
        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end ';
        $havingConditions = []; $queryHaving = '';
        $queryData = [
            'init' => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 00:00:00",
            'end' => ( isset($request->date) ? $request->date : date('Y-m-d') ) . " 23:59:59",
        ];

        //TIPO DE SERVICIO
        if(isset( $request->is_round_trip )){
            $params = "";
            foreach( $request->is_round_trip as $key => $is_round_trip ){
                $queryData['is_round_trip' . $key] = $is_round_trip;
                $params .= "FIND_IN_SET(:is_round_trip".$key.", is_round_trip) > 0 OR ";
            }
            $params = rtrim($params, ' OR ');
            $query .= " AND (".$params.") ";            
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

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $query .= " AND rez.currency IN ($params) ";
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

        //COMISIONABLES
        if(isset( $request->is_commissionable )){
            $params = $request->is_commissionable;
            $query .= " AND rez.is_commissionable = $params ";
        }

        //PAGO A LA LLEGADA
        if( $request->is_pay_at_arrival !=  NULL ){
            $params = $request->is_pay_at_arrival;
            $query .= " AND rez.pay_at_arrival = $params ";
        }        

        //MOTIVOS DE CANCELACIÓN
        if(isset( $request->cancellation_status ) && !empty( $request->cancellation_status )){
            $params = $this->parseArrayQuery($request->cancellation_status);
            $query .= " AND tc.id IN ($params) ";
        }

        //RESERVAS CON UN BALANCE
        if(isset( $request->is_balance )){
            $havingConditions[] = ( $request->is_balance == 1 ? ' total_balance > 0 ' : ' total_balance <= 0 ' );
        }        

        //RESERVAS OPERADAS EL MISMO DIA DE SU CREACION
        if(isset( $request->is_today )){
            $havingConditions[] = ( $request->is_today == 1 ? ' is_today != 0 ' : ' is_today = 0 ' );
        }

        //TIPO DE SERVICIO
        if(!isset( $request->is_duplicated )){
            $query .= " AND rez.is_duplicated = 0 ";
        }        
        if(isset( $request->is_duplicated )){
            $query .= " AND rez.is_duplicated IN (1,0) ";
        }

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

        if(  (isset( $request->reservation_status ) && !empty( $request->reservation_status )) || (isset( $request->payment_status ) && !empty( $request->payment_status )) || (isset( $request->payment_method ) && !empty( $request->payment_method )) || (isset( $request->is_balance )) || (isset( $request->is_today )) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        // dd($query, $queryHaving, $queryData);
        $bookings = $this->queryBookings($query, $queryHaving, $queryData);
        
        return view('management.reservations.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de reservaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'bookings' => $bookings,
            'data' => $data,
        ]);
    }
}