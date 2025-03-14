<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response as ResponseFile;

//MODELS
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

class SpamRepository
{
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
        rit.from_name, rit.to_name, two_zone.is_primary, rit.passengers
                            FROM reservations_items as rit
                        INNER JOIN reservations as rez ON rez.id = rit.reservation_id
                        INNER JOIN zones as two_zone ON two_zone.id = rit.to_zone
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
                        WHERE rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND DATE(rit.op_one_pickup) = :filter_date AND rit.is_round_trip = 0 AND rit.op_one_status IN('PENDING','COMPLETED')
                        HAVING is_primary = 0",  $search);

        if($search):
            foreach($search as $key => $value):
                if( isset( $items[ $value->spam ] )):
                    $items[ $value->spam ]['total']++;
                    $items[ $value->spam ]['items'][] = $value;
                endif;
            endforeach;
        endif;

        if($partial == false):
            return view('components.html.management.spam.view', ['date' => $request->date, 'status' => $request->status, 'items' => $items]);
        else:
            return view('components.html.management.spam.partial', ['date' => $request->date, 'status' => $request->status, 'items' => $items]);
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
        
        $item = DB::select("SELECT 
                                rez.id as rez_id, 
                                rit.id as rit_id, 
                                rit.code, CONCAT(rez.client_first_name, ' ', rez.client_last_name) as client_full_name, 
                                rez.client_phone, 
                                rez.client_email, 
                                rit.spam, 
                                COALESCE(comments_.counter, 0) AS counter, 
                                comments_.last_date, 
                                comments_.last_user,
                                rit.from_name, 
                                rit.to_name, 
                                sit.name as site_name,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                rez.currency, rit.op_one_pickup, 
                                rit.op_one_status
                            FROM reservations_items as rit
                            INNER JOIN reservations as rez ON rez.id = rit.reservation_id
                            INNER JOIN sites as sit ON sit.id = rez.site_id
                            LEFT JOIN (
                                SELECT 
                                    reservation_id,  
                                    ROUND( COALESCE(SUM(total), 0), 2) as total_sales
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

        return view('components.html.management.spam.basic-information', ["item" => $item[0]]);
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

        return view('components.html.management.spam.history', [ "items" => $items ]);
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
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}