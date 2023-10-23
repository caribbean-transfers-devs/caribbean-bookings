<?php

namespace App\Repositories\Operation;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

class OperationRepository
{
    public function index($request){

        $dates = [];
        for( $i = 0; $i < 3; $i++ ):
            $new_date = date("Y-m-d", strtotime(date("Y-m-d") . " +{$i} day"));
            if(!isset( $dates[ $new_date ] )):
                $dates[ $new_date ] = [];
            endif;
        endfor;

        foreach($dates as $key => $value):
            $init = $key.' 00:00:00';
            $end = $key.' 23:59:59';
            $dates[$key] = DB::select("SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_one_pickup as filtered_date, 'arrival' as operation_type, sit.name as site_name, '' as messages,
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
                                        AND rez.is_cancelled = 0
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
                                        AND rez.is_cancelled = 0
                                        GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                        ORDER BY filtered_date ASC",[
                                        "init_date_one" => $init,
                                        "init_date_two" => $end,
                                        "init_date_three" => $init,
                                        "init_date_four" => $end,
                                    ]);
        endforeach;

        //Agregamos comentarios
        foreach($dates as $key => $value):
            foreach($value as $items):
                $items->messages = $this->getMessages($items->reservation_id);                
            endforeach;
        endforeach;
 
        return view('operation.index', compact('dates'));
    }

    public function getMessages($id){
        $xHTML  = '';

        $messages = DB::select("SELECT fup.id, fup.text, fup.type FROM reservations_follow_up as fup
                                 WHERE fup.type IN ('CLIENT','OPERATION') 
                                    AND fup.reservation_id = :id 
                                    AND fup.text IS NOT NULL 
                                    AND fup.text != '' ", ["id" => $id]);
        if( sizeof($messages) >= 1 ):
            foreach($messages as $key => $value):
                $xHTML .= '[('.$value->type.') '. $value->text.'] ';
            endforeach;
        endif;

        return $xHTML;
    }
    
    public function managment($request){
        return view('operation.managment');
    }

    public function statusUpdate($request){
        
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
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de estatus de operación (".$request->type.") por ".$request->status;
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

    public function fetchData($request){
        $date = date("Y-m-d");
        if(isset( $request->date )):
            $date = $request->date;
        endif;
        $search['init_date'] = $date." 00:00:00";
        $search['end_date'] = $date." 23:59:59";

        $op_data = [
            "status" => "OPEN",
            "date_time" => NULL
        ];
        $op_query = DB::select("SELECT * FROM operation_closing 
                                WHERE DATE_FORMAT(op_close_date,'%Y-%m-%d') = :current_date
                            ORDER BY op_close_date DESC LIMIT 1", ['current_date' => $date ]);
        if( isset( $op_query[0] ) ):
            $op_data['status'] = $op_query[0]->op_close_type;
            $op_data['date_time'] = $op_query[0]->op_close_date;
        endif;

        
        $items = $this->queryItems($search);
        $finalData = [];

        if(sizeof( $items ) > 0):
            foreach( $items as $key => $value ):
                $payment = ( $value->total_sales - $value->total_payments );
                if($payment < 0) $payment = 0;

                $time = $value->operation_type == 'arrival' ? $value->op_one_pickup : $value->op_two_pickup;
                $data = [
                    "op_pickup" => date("H:i", strtotime( $time )),
                    "site" => $value->site_name,
                    "type" => $value->final_service_type,
                    "op_status" => $value->operation_type == 'arrival' ? $value->op_one_status : $value->op_two_status,
                    "code" => $value->code,
                    "client_name" => $value->client_first_name." ".$value->client_last_name,
                    "service_name" => $value->service_name,
                    "passengers" => $value->passengers,
                    "op_from_name" => $value->operation_type == 'arrival' ? $value->from_name : $value->to_name,
                    "op_to_name" => $value->operation_type == 'arrival' ? $value->to_name : $value->from_name,
                    "payment_status" => $value->status,
                    "payment" => number_format($payment,2),
                    "currency" => $value->currency,                    
                    "reservation_id" => $value->reservation_id,
                    "id" => $value->id,
                    "options" => [
                        "can_update_op" => true
                    ]
                ];

                $finalData[] = $data;

                
            endforeach;
        endif;

        return response()->json(['op_data' => $op_data, "items" => $finalData], Response::HTTP_OK);

    }

    public function queryItems($search){

        return  DB::select("SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_one_pickup as filtered_date, 'arrival' as operation_type, sit.name as site_name, '' as messages,
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
                                    AND rez.is_cancelled = 0
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
                                    AND rez.is_cancelled = 0
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                    ORDER BY filtered_date ASC",[
                                        "init_date_one" => $search['init_date'],
                                        "init_date_two" => $search['end_date'],
                                        "init_date_three" => $search['init_date'],
                                        "init_date_four" => $search['end_date'],
                                    ]);
    }

    public function createLock($request){
        // echo "<pre>";
        // print_r($request->type);
        // die();

        try {
            DB::beginTransaction();

            $search = [];
            $search['init_date'] = $request->date." 00:00:00";
            $search['end_date'] = $request->date." 23:59:59";
            $items = $this->queryItems($search);
            $today = date("Y-m-d H:i:s");

            $update = [];
            $count = 0;
            foreach($items as $key => $value):
                $count++;

                if($value->operation_type == "arrival"):
                    $update[] = [
                        "id" => $value->id,
                        "type" => $value->operation_type,
                        "scheduled_code" => $count,
                        "scheduled_time" => $today,
                        "scheduled_type" => $request->type,
                    ];
                endif;

                if($value->operation_type == "departure"):
                    $update[] = [
                        "id" => $value->id,
                        "type" => $value->operation_type,
                        "scheduled_code" => $count,
                        "scheduled_time" => $today,
                        "scheduled_type" => $request->type,
                    ];
                endif;

            endforeach;

            echo "<pre>";
            print_r($update);
            die();
            
            //Update
            foreach($update as $key => $value):
                $item = ReservationsItem::find($value['id']);
                if($request->type == "arrival"):
                    $item->op_one_status = $request->status;
                endif;
                if($request->type == "departure"):
                    $item->op_two_status = $request->status;
                endif;
                $item->save();
            endforeach;

            echo "<pre>";
            print_r($update);
            die();

            DB::commit();
            return response()->json(['message' => 'Actualización realizada con éxito', 'success' => true], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar la operación'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }
}
