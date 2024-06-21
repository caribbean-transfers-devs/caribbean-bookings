<?php

namespace App\Repositories\Operation;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFile;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class SpamRepository
{
    public function index($request){
        // dd($request->input());
        //$date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        $data = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
        ];

        if(isset( $request->date ) && !empty( $request->date )){
            $tmp_date = explode(" - ", $request->date);
            $data['init'] = $tmp_date[0]." 00:00:00";
            $data['end'] = $tmp_date[1]." 23:59:59";            
        }

        // $search['init'] = $date[0]." 00:00:00";
        // $search['end'] = $date[1]." 23:59:59";

        $items = $this->querySpam($data);
        // dd($items);
        return view('operation.spam', compact('items','data'));
          
    }

    public function exportExcel($request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        $search = [];
        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";
        $search['language'] = $request->language;

        $items = $this->querySpam2($search);
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
                                    WHERE it.op_one_pickup BETWEEN :init_date_one AND :init_date_two
                                    AND rez.is_cancelled = 0
                                    AND rez.is_duplicated = 0
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
                                    WHERE it.op_two_pickup BETWEEN :init_date_three AND :init_date_four
                                    AND rez.is_cancelled = 0
                                    AND rez.is_duplicated = 0
                                    AND it.is_round_trip = 0
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

}