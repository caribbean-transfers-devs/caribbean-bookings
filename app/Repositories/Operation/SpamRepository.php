<?php

namespace App\Repositories\Operation;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFile;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;
use Illuminate\Support\Facades\Validator;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class SpamRepository
{
    public function index($request){
        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ),            
        ];

        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];        

        $items = $this->querySpam($queryData);

        return view('operation.spam', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de spam del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'items' => $items,
            'data' => $data,
        ]);

    }

    public function exportExcel($request){
        // $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        // $search = [];
        // $search['init'] = $date." 00:00:00";
        // $search['end'] = $date." 23:59:59";
        // $search['language'] = $request->language;

        if(isset( $request->date ) && !empty( $request->date )){
            $tmp_date = explode(" - ", $request->date);
            $data['init'] = $tmp_date[0]." 00:00:00";
            $data['end'] = $tmp_date[1]." 23:59:59";
        }
        $data['language'] = $request->language;

        $items = $this->querySpam2($data);
        // $items = $this->querySpam($search);
        // dd($items);

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Rellenar con datos
        $sheet->setCellValue('A1', 'code');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'phone');
        // $sheet->setCellValue('D1', 'language');

        $count = 2;

        foreach( $items as $key => $item ){
            if( ( $item->op_one_status == "COMPLETED" && $item->site_name != "Taquilla | Llegadas" ) || ( $item->site_name == "Taquilla | Llegadas" ) ){
                $sheet->setCellValue('A'.strval($count), $item->id);
                $sheet->setCellValue('B'.strval($count), $item->client_first_name);
                $sheet->setCellValue('C'.strval($count), $item->client_phone);
                // $sheet->setCellValue('D'.strval($count), $item->language);
                $count = $count + 1;
            }
        }

        // Crear un escritor de archivos Excel
        $writer = new Xlsx($spreadsheet);

        // Crear una respuesta HTTP para la descarga del archivo
        $fileName = 'spam_'.$request->date.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return ResponseFile::download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function querySpam($search){
        return  DB::select("SELECT 
                                        rez.id as reservation_id, 
                                        rez.*, 
                                        it.*, 
                                        serv.name as service_name, 
                                        it.op_one_pickup as filtered_date, 
                                        'arrival' as operation_type, 
                                        sit.name as site_name, 
                                        '' as messages,
                                        COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                        COALESCE(SUM(p.total_payments), 0) as total_payments,
                                        CASE
                                            WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                            ELSE 'CONFIRMADO'
                                        END AS status,
                                        zone_one.id as zone_one_id, 
                                        zone_one.name as zone_one_name, 
                                        zone_one.is_primary as zone_one_is_primary,
                                        zone_two.id as zone_two_id, 
                                        zone_two.name as zone_two_name, 
                                        zone_two.is_primary as zone_two_is_primary,
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
                                    AND rez.is_cancelled = 0
                                    AND rez.is_duplicated = 0
                                    AND rez.open_credit = 0
                                    AND it.is_round_trip = 0
                                    AND sit.id != 29
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
                                    AND rez.is_cancelled = 0
                                    AND rez.is_duplicated = 0
                                    AND rez.open_credit = 0
                                    AND it.is_round_trip = 0
                                    AND sit.id != 29
                                    GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                        "init_date_one" => $search['init'],
                                        "init_date_two" => $search['end'],
                                        "init_date_three" => $search['init'],
                                        "init_date_four" => $search['end'],
                                    ]);
    }

    public function querySpam2($search){
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
                                   WHERE rez.language = :lang_one AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two
                                   AND rez.is_cancelled = 0
                                   AND rez.is_duplicated = 0
                                   AND rez.open_credit = 0
                                   AND it.is_round_trip = 0
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
                                   WHERE rez.language = :lang_two AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four
                                   AND rez.is_cancelled = 0
                                   AND rez.is_duplicated = 0
                                   AND rez.open_credit = 0
                                   AND it.is_round_trip = 0
                                   GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id",[
                                       "lang_one" => $search['language'],
                                       "lang_two" => $search['language'],
                                       "init_date_one" => $search['init'],
                                       "init_date_two" => $search['end'],
                                       "init_date_three" => $search['init'],
                                       "init_date_four" => $search['end'],
                                   ]);
    }

    public function spamUpdate($request){
        try {
            DB::beginTransaction();

            $item = ReservationsItem::find($request->id);
            $before = $item->spam; //Respaldo del estatus actual...
            $rez_id = $item->reservation_id; //Guardamos el ID de la reserva
            $item->spam = $request->type;
            $item->save();

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Envío de SPAM actualizado de (".$before.") a ".$request->type;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $rez_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get($request){
        $partial = false;

        $validator = Validator::make($request->all(), [            
            'date' => 'required|date_format:Y-m-d',
            'status' => 'required|in:PENDING,SENT,LATER,CONFIRMED,REJECTED,ACCEPT',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'error' => [
                        'code' => 'REQUIRED_PARAMS', 
                        'message' =>  $validator->errors()->all() 
                    ]
                ], 422);
        }

        if($request->partial == 1):
            $partial = true;
        endif;

        $search = [
            "filter_date" => $request->date
        ];

        $items = [
            "PENDING" => ['total' => 0, 'items' => []],
            "SENT" => ['total' => 0, 'items' => []],
            "LATER" => ['total' => 0, 'items' => []],
            "CONFIRMED" => ['total' => 0, 'items' => []],
            "REJECTED" => ['total' => 0, 'items' => []],
            "ACCEPT" => ['total' => 0, 'items' => []],
        ];

        $search = DB::select("SELECT rez.id as rez_id, rit.id as rit_id, rit.code, CONCAT(rez.client_first_name, ' ', rez.client_last_name) as client_full_name, rez.client_phone, rit.spam, COALESCE(comments_.counter, 0) AS counter, comments_.last_date, comments_.last_user,
        rit.from_name, rit.to_name
                            FROM reservations_items as rit
                        INNER JOIN reservations as rez ON rez.id = rit.reservation_id
                        LEFT JOIN (
                            SELECT fup.reservation_id as reservation_id, count(*) as counter,  MAX(fup.created_at) AS last_date,
                                    (SELECT usr.name
                                FROM reservations_follow_up AS fup_inner
                                        LEFT JOIN users as usr ON usr.id = fup_inner.categories_reminder_user_create
                                WHERE fup_inner.reservation_id = fup.reservation_id 
                                AND fup_inner.type = 'COMMENTS' 
                                AND fup_inner.categories = 'SPAM' 
                                ORDER BY fup_inner.created_at DESC 
                                LIMIT 1
                            ) AS last_user
                                FROM reservations_follow_up AS fup
                            WHERE fup.type = 'COMMENTS' AND fup.categories = 'SPAM'
                            GROUP BY fup.reservation_id
                        ) as comments_ ON comments_.reservation_id = rez.id
                        WHERE rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND DATE(rit.op_one_pickup) = :filter_date AND rit.is_round_trip = 0 AND rit.op_one_status IN('PENDING','CONFIRMED')",  $search);

        if($search):
            foreach($search as $key => $value):
                if( isset( $items[ $value->spam ] )):
                    $items[ $value->spam ]['total']++;
                    $items[ $value->spam ]['items'][] = $value;
                endif;
            endforeach;
        endif;

        if($partial == false):
            return view('management.spam.view', ['date' => $request->date, 'status' => $request->status, 'items' => $items]);
        else:
            return view('management.spam.partial', ['date' => $request->date, 'status' => $request->status, 'items' => $items]);
        endif;
    }
    
    public function getBasicInformation($request){

        $validator = Validator::make($request->all(), [            
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'error' => [
                        'code' => 'REQUIRED_PARAMS', 
                        'message' =>  $validator->errors()->all() 
                    ]
                ], 422);
        }
        
        $item = DB::select("SELECT rez.id as rez_id, rit.id as rit_id, rit.code, CONCAT(rez.client_first_name, ' ', rez.client_last_name) as client_full_name, rez.client_phone, rez.client_email, rit.spam, COALESCE(comments_.counter, 0) AS counter, comments_.last_date, comments_.last_user,
                                    rit.from_name, rit.to_name, sit.name as site_name, COALESCE(SUM(s.total_sales), 0) as total_sales, rez.currency, rit.op_one_pickup, rit.op_one_status
                                    FROM reservations_items as rit
                            INNER JOIN reservations as rez ON rez.id = rit.reservation_id
                            INNER JOIN sites as sit ON sit.id = rez.site_id
                            LEFT JOIN (
                                        SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                            FROM sales
                                                WHERE deleted_at IS NULL
                                            GROUP BY reservation_id
                            ) as s ON s.reservation_id = rez.id
                            LEFT JOIN (
                                    SELECT fup.reservation_id as reservation_id, count(*) as counter,  MAX(fup.created_at) AS last_date,
                                                    (SELECT usr.name
                                            FROM reservations_follow_up AS fup_inner
                                                            LEFT JOIN users as usr ON usr.id = fup_inner.categories_reminder_user_create
                                            WHERE fup_inner.reservation_id = fup.reservation_id 
                                            AND fup_inner.type = 'COMMENTS' 
                                            AND fup_inner.categories = 'SPAM' 
                                            ORDER BY fup_inner.created_at DESC 
                                            LIMIT 1
                                    ) AS last_user
                                            FROM reservations_follow_up AS fup
                                    WHERE fup.type = 'COMMENTS' AND fup.categories = 'SPAM'
                                    GROUP BY fup.reservation_id
                            ) as comments_ ON comments_.reservation_id = rez.id
                            WHERE rit.id = :id
                            GROUP BY 
                                rez.id, 
                                rit.id, 
                                rit.code, 
                                rez.client_first_name, 
                                rez.client_last_name, 
                                rez.client_phone, 
                                rit.spam, 
                                comments_.counter, 
                                comments_.last_date, 
                                comments_.last_user,
                                rit.from_name, 
                                rit.to_name, 
                                sit.name, 
                                rez.currency, 
                                rit.op_one_pickup, 
                                rit.op_one_status;", [ 'id' => $request->id ]);

        return view('management.spam.basic-information', ["item" => $item[0]]);
    }

    public function getHistory($request){

        $validator = Validator::make($request->all(), [            
            'id' => 'required',            
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'error' => [
                        'code' => 'REQUIRED_PARAMS', 
                        'message' =>  $validator->errors()->all() 
                    ]
                ], 422);
        }

        $items = DB::select("SELECT fup.name as title_name, fup.text as description, fup.created_at, fup.categories_reminder, fup.categories_reminder_disable, fup.categories_reminder_user_create, usr_create.name as user_create_name, usr_resolve.name as user_resolve_name, fup.categories_reminder_user_date
                                FROM reservations_follow_up as fup
                            LEFT JOIN users as usr_create ON usr_create.id = fup.categories_reminder_user_create
                            LEFT JOIN users as usr_resolve ON usr_resolve.id = fup.categories_reminder_user
                                WHERE fup.categories = 'SPAM' AND fup.reservation_id = :id
                            ORDER BY created_at DESC", [ 'id' => $request->id ]);

        return view('management.spam.history', [ "items" => $items ]);
    }

    public function addHistory($request){        

        $validator = Validator::make($request->all(), [            
            'id' => 'required',            
            'spam_comment' => 'required',
            'spam_status' => 'required|in:PENDING,SENT,LATER,CONFIRMED,REJECTED,ACCEPT',
            'spam_remember_date' => [
                'required_if:spam_remember,1',
                'date_format:Y-m-d',
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'error' => [
                        'code' => 'REQUIRED_PARAMS', 
                        'message' =>  $validator->errors()->all() 
                    ]
                ], 422);
        }

        try {
            DB::beginTransaction();

            $item = ReservationsItem::find($request->id_item);
            $item->spam = $request->spam_status;
            $item->save();

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = "Seguimiento de SPAM";
            $follow_up_db->text = "[". $request->spam_status ."] ".$request->spam_comment . " por ".auth()->user()->name;
            $follow_up_db->type = 'COMMENTS';
            $follow_up_db->categories = "SPAM";
            $follow_up_db->reservation_id = $request->id;
            $follow_up_db->categories_reminder_user_create = auth()->user()->id;

            if($request->spam_remember):
                $follow_up_db->categories_reminder = $request->spam_remember_date;
            endif;
                        
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}