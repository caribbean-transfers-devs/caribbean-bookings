<?php

namespace App\Repositories\Operation;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\Reports\PaymentsTrait;
use App\Traits\FollowUpTrait;

class OperationRepository
{
    use FiltersTrait, QueryTrait, PaymentsTrait, FollowUpTrait;

    public function reservations($request)
    {
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
        
        //Query DB (2013-2206)
        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end ';
        $havingConditions = []; $query2 = '';
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
            $query2 = " HAVING " . implode(' AND ', $havingConditions);
        }

        $bookings = $this->queryBookings($query, $query2, $queryData);
        
        return view('operation.reservations', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reservaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'bookings' => $bookings,
            'services' => $this->Services(),
            'websites' => $this->Sites(),
            'origins' => $this->Origins(),
            'reservation_status' => $this->reservationStatus(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'payment_status' => $this->paymentStatus(),
            'currencies' => $this->Currencies(),
            'methods' => $this->Methods(),
            'cancellations' => $this->CancellationTypes(),
            'data' => $data,
        ]);
    }

    public function confirmation($request)
    {

        $date = date("Y-m-d");
        if(isset( $request->date )):
            $date = $request->date;
        endif;

        $search['init_date'] = $date." 00:00:00";
        $search['end_date'] = $date." 23:59:59";

        $items = DB::select("SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_one_pickup as filtered_date, 'arrival' as operation_type, sit.name as site_name, '' as messages,
                                                COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                                                CASE
                                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                                    ELSE 'CONFIRMADO'
                                                END AS status,
                                                zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                                                zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                                                CASE 
                                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                                END AS final_service_type
                                    FROM reservations_items as it
                                    INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                    INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                    INNER JOIN sites as sit ON sit.id = rez.site_id
                                    INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                    INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                    LEFT JOIN (
                                        SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                        FROM sales
                                        WHERE deleted_at IS NULL
                                        GROUP BY reservation_id
                                    ) as s ON s.reservation_id = rez.id
                                    LEFT JOIN (
                                        SELECT reservation_id,
                                        ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                                                                WHEN operation = 'division' THEN total / exchange_rate
                                                                                ELSE total END), 2) AS total_payments,
                                        GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                        FROM payments
                                        GROUP BY reservation_id
                                    ) as p ON p.reservation_id = rez.id
                                    WHERE it.op_one_pickup BETWEEN :init_date_one AND :init_date_two
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.site_id NOT IN(21)
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                    UNION 
                                    SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_two_pickup as filtered_date, 'departure' as operation_type, sit.name as site_name, '' as messages,
                                            COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                                            CASE
                                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                                    ELSE 'CONFIRMADO'
                                            END AS status,
                                            zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                                            zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                                            CASE                                                     
                                                WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1  THEN 'DEPARTURE'
                                                WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                            END AS final_service_type
                                    FROM reservations_items as it
                                    INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                    INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                    INNER JOIN sites as sit ON sit.id = rez.site_id
                                    INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
				                    INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                    LEFT JOIN (
                                            SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales                                            
                                            FROM sales
                                            WHERE deleted_at IS NULL
                                            GROUP BY reservation_id
                                    ) as s ON s.reservation_id = rez.id
                                    LEFT JOIN (
                                            SELECT reservation_id,
                                            ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                                                                    WHEN operation = 'division' THEN total / exchange_rate
                                                                                    ELSE total END), 2) AS total_payments,
                                            GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                            FROM payments
                                            GROUP BY reservation_id
                                    ) as p ON p.reservation_id = rez.id
                                    WHERE it.op_two_pickup BETWEEN :init_date_three AND :init_date_four
                                    AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.site_id NOT IN(21)
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                        "init_date_one" => $search['init_date'],
                                        "init_date_two" => $search['end_date'],
                                        "init_date_three" => $search['init_date'],
                                        "init_date_four" => $search['end_date'],
                                    ]);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Gestión de operación",
                "active" => true
            ),
        );

        return view('operation.confirmation', compact('items','date','breadcrumbs'));
    }

    public function updateStatusConfirmation($request)
    {
        try {
            DB::beginTransaction();
            
            $item = ReservationsItem::find($request->id);
            if($request->type == "arrival"):
                $item->op_one_confirmation = (( $request->status == 1 )? 0 : 1 );
            endif;
            if($request->type == "departure"):
                $item->op_two_confirmation = (( $request->status == 1 )? 0 : 1 );
            endif;
            $item->save();            

            $this->create_followUps($request->rez_id, "Confirmación actualizada a ". (( $request->status == 1 )? 'No enviado' : 'Enviado' ), 'HISTORY', auth()->user()->name);

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    

    public function statusUpdate($request)
    {
        try {
            DB::beginTransaction();            
            $item = ReservationsItem::find($request->item_id);
            if($request->type == "arrival"):
                $item->op_one_status = $request->status;
            endif;
            if($request->type == "departure"):
                $item->op_two_status = $request->status;
            endif;
            $item->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = "ESTATUS DE RESERVACIÓN";
            $follow_up_db->text = 'El usuario: '.auth()->user()->name.", actualizo es estatus de reservación de: (".$request->type.") a ".$request->status;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $request->rez_id;
            $follow_up_db->save();
    
            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
}
