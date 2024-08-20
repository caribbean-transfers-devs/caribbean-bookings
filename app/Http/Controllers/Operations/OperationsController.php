<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFile;

use App\Models\Enterprise;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\Sale;
use App\Models\ReservationFollowUp;
use App\Models\Zones;
use App\Models\Destination;
use App\Models\DestinationService;

use App\Traits\RoleTrait;
use App\Traits\CodeTrait;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OperationsController extends Controller
{

    use CodeTrait, RoleTrait;
    // if(!$this->hasPermission(39)){
    //     abort(403, 'NO TIENE AUTORIZACIÓN.');
    // }    

    public function index(Request $request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        $nexDate = date('Y-m-d', strtotime($request->date . ' +1 day'));

        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";

        $items = $this->queryBookings($search);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Gestión de envío de operaciones",
                "active" => true
            ),
        );

        $vehicles = Vehicle::All();
        $drivers = Driver::orderBy('names','ASC')->get();
        $zones = Zones::all();
        $services =  DestinationService::all();
        $websites = DB::select("SELECT id, name as site_name FROM sites ORDER BY site_name ASC");

        return view('operation.operations', compact('items', 'date', 'nexDate', 'breadcrumbs', 'vehicles', 'drivers', 'zones', 'services', 'websites'));
    }

    public function preassignments(Request $request){
        try {
            //DECLARAMOS VARIABLES
            $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
            $search['init'] = $date." 00:00:00";
            $search['end'] = $date." 23:59:59";
            $arrival_counter = 1;
            $transfer_counter = 1;
            $departure_counter = 1;
    
            //CONSULTAMOS SERVICIOS
            $items = $this->queryBookings($search);
    
            //RECORREMOS LOS SERVICIOS PARA PODER REALISAR LA PREASIGNACION
            if( sizeof($items)>=1 ):
                foreach($items as $key => $item):
                    $preassignment = "";
                    $service = ReservationsItem::find($item->id);
                    if( $item->final_service_type == 'ARRIVAL' ){
                        $preassignment = "L".$arrival_counter;
                        $service->op_one_preassignment = $preassignment;
                        $arrival_counter ++;
                    }

                    if( $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $preassignment = "T".$transfer_counter;
                        $service->op_one_preassignment = $preassignment;
                        $transfer_counter ++;
                    }
                    if( $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $preassignment = "T".$transfer_counter;
                        $service->op_two_preassignment = $preassignment;
                        $transfer_counter ++;
                    }

                    if( $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $preassignment = "S".$departure_counter;
                        $service->op_one_preassignment = $preassignment;
                        $departure_counter ++;
                    }
                    if( $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $preassignment = "S".$departure_counter;
                        $service->op_two_preassignment = $preassignment;
                        $departure_counter ++;
                    }
                    $service->save();

                    //CREAMOS UN LOG
                    $follow_up_db = new ReservationFollowUp;
                    $follow_up_db->name = auth()->user()->name;
                    $follow_up_db->text = "Se pre-asigno de (NULL) por ".$preassignment. " al servicio: ".$item->id.", por ".auth()->user()->name;
                    $follow_up_db->type = 'HISTORY';
                    $follow_up_db->reservation_id = $item->reservation_id;
                    $follow_up_db->save();
                endforeach;
            endif;

            return response()->json([
                'success' => true,
                'message' => 'Se pre-asignaron los servicios de manera correcta...',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function closeOperation(Request $request){
        try {
            //DECLARAMOS VARIABLES
            $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
            $search['init'] = $date." 00:00:00";
            $search['end'] = $date." 23:59:59";
    
            //CONSULTAMOS SERVICIOS
            $items = $this->queryBookings($search);
    
            //RECORREMOS LOS SERVICIOS PARA PODER REALISAR LA PREASIGNACION
            if( sizeof($items)>=1 ):
                foreach($items as $key => $item):
                    $service = ReservationsItem::find($item->id);
                    if( $item->final_service_type == 'ARRIVAL' ){
                        $service->op_one_operation_close = 1;
                    }

                    if( $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $service->op_one_operation_close = 1;
                    }
                    if( $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $service->op_two_operation_close = 1;
                    }

                    if( $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $service->op_one_operation_close = 1;
                    }
                    if( $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $service->op_two_operation_close = 1;
                    }
                    $service->save();
                endforeach;
            endif;

            return response()->json([
                'success' => true,
                'message' => 'Se cerro la operación correctamente',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function queryBookings($search){
        return  DB::select("SELECT 
                                rez.id as reservation_id,
                                CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name,
                                rez.*, 
                                it.*, 
                                serv.name as service_name, 
                                it.op_one_pickup as filtered_date, 
                                'arrival' as operation_type,
                                sit.name as site_name,
                                'TYPE_ONE' as op_type,
                                CASE WHEN upload.reservation_id IS NOT NULL THEN 1 ELSE 0 END as pictures,
                                CASE WHEN rfu.reservation_id IS NOT NULL THEN 1 ELSE 0 END as messages,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                CASE
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                ELSE 'CONFIRMED'
                                END AS status,
                                zone_one.id as zone_one_id,
                                zone_one.name as zone_one_name, 
                                zone_one.is_primary as zone_one_is_primary, 
                                zone_one.cut_off as zone_one_cut_off,
                                zone_two.id as zone_two_id, 
                                zone_two.name as zone_two_name, 
                                zone_two.is_primary as zone_two_is_primary, 
                                zone_two.cut_off as zone_two_cut_off,
                                CASE 
                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type,
                                vehicle_one.name as vehicle_one_name,
                                vehicle_two.name as vehicle_two_name,
                                CONCAT(driver_one.names,' ',driver_one.surnames) as driver_one_name,
                                CONCAT(driver_two.names,' ',driver_two.surnames) as driver_two_name
                            FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone

                                LEFT OUTER JOIN vehicles as vehicle_one ON vehicle_one.id = it.vehicle_id_one
                                LEFT OUTER JOIN vehicles as vehicle_two ON vehicle_two.id = it.vehicle_id_two
                                LEFT OUTER JOIN drivers as driver_one ON driver_one.id = it.driver_id_one
                                LEFT OUTER JOIN drivers as driver_two ON driver_two.id = it.driver_id_two
                                LEFT OUTER JOIN reservations_media as upload ON upload.reservation_id = rez.id
                                LEFT JOIN reservations_follow_up as rfu ON rfu.reservation_id = rez.id AND rfu.type IN ('CLIENT', 'OPERATION')

                                LEFT JOIN (
                                    SELECT 
                                        reservation_id,  
                                        ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                    FROM sales
                                        WHERE deleted_at IS NULL
                                    GROUP BY reservation_id
                                ) as s ON s.reservation_id = rez.id
                                LEFT JOIN (
                                        SELECT 
                                            reservation_id,
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
                            GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id

                                UNION 
                                   SELECT 
                                   rez.id as reservation_id,
                                   CONCAT(rez.client_first_name, ' ', rez.client_last_name) as full_name,
                                   rez.*, 
                                   it.*, 
                                   serv.name as service_name, 
                                   it.op_two_pickup as filtered_date, 
                                   'departure' as operation_type, 
                                   sit.name as site_name, 
                                   'TYPE_TWO' as op_type, 
                                   CASE WHEN upload.reservation_id IS NOT NULL THEN 1 ELSE 0 END as pictures,
                                    CASE WHEN rfu.reservation_id IS NOT NULL THEN 1 ELSE 0 END as messages,
                                    COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                    COALESCE(SUM(p.total_payments), 0) as total_payments,
                                    CASE
                                            WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                            ELSE 'CONFIRMADO'
                                    END AS status,
                                    zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary, zone_one.cut_off as zone_one_cut_off,
                                    zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary, zone_two.cut_off as zone_two_cut_off,
                                    CASE
                                        WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1  THEN 'DEPARTURE'
                                        WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                    END AS final_service_type,
                                    vehicle_one.name as vehicle_one_name,
                                    vehicle_two.name as vehicle_two_name,
                                    CONCAT(driver_one.names,' ',driver_one.surnames) as driver_one_name,
                                    CONCAT(driver_two.names,' ',driver_two.surnames) as driver_two_name
                                   FROM reservations_items as it
                                   INNER JOIN reservations as rez ON rez.id = it.reservation_id                                   
                                   INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                   INNER JOIN sites as sit ON sit.id = rez.site_id
                                   INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                   INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                   LEFT OUTER JOIN vehicles as vehicle_one ON vehicle_one.id = it.vehicle_id_one
                                   LEFT OUTER JOIN vehicles as vehicle_two ON vehicle_two.id = it.vehicle_id_two
                                   LEFT OUTER JOIN drivers as driver_one ON driver_one.id = it.driver_id_one
                                   LEFT OUTER JOIN drivers as driver_two ON driver_two.id = it.driver_id_two
                                   LEFT OUTER JOIN  reservations_media as upload ON upload.reservation_id = rez.id
                                   LEFT JOIN reservations_follow_up as rfu ON rfu.reservation_id = rez.id AND rfu.type IN ('CLIENT', 'OPERATION')
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
                                   AND it.is_round_trip = 1
                                   GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                   ORDER BY filtered_date ASC",[
                                       "init_date_one" => $search['init'],
                                       "init_date_two" => $search['end'],
                                       "init_date_three" => $search['init'],
                                       "init_date_four" => $search['end'],
                                   ]);
    }

    public function preassignment(Request $request){
        try {
            DB::beginTransaction();
            //DECLARAMOS VARIABLES
            $search['init'] = $request->date." 00:00:00";
            $search['end'] = $request->date." 23:59:59";
            $arrival_counter = 0;
            $transfer_counter = 0;
            $departure_counter = 0;

            //CONSULTAMOS SERVICIOS
            $items = $this->queryBookings($search);

            //RECORREMOS LOS SERVICIOS PARA PODER INDENTIFICAR LA CONTINUIDAD DE LA PREASIGNACION
            if( sizeof($items)>=1 ):
                foreach($items as $key => $item):
                    if( $item->final_service_type == 'ARRIVAL' && $item->op_one_preassignment != "" ){
                        $arrival_counter ++;
                    }

                    if( $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) && $item->op_one_preassignment != "" ){
                        $transfer_counter ++;
                    }
                    if( $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) && $item->op_two_preassignment != "" ){
                        $transfer_counter ++;
                    }                    

                    if( $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) && $item->op_one_preassignment != "" ){
                        $departure_counter ++;
                    }                    
                    if( $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 1 ) && $item->op_two_preassignment != "" ){
                        $departure_counter ++;
                    }
                endforeach;
            endif;
                        
            $preassignment = "";
            //OBTENEMOS INFORMACION DEL SERVICIO
            $service = ReservationsItem::find($request->reservation_item);
            // dd($request, $service);
            //REALIZAMOS LA PREASIGNACION DEPENDIENDO DE LA OPERACION
            if( $request->operation == 'ARRIVAL' ){
                $preassignment = "L".( $arrival_counter == 0 ? 1 : ($arrival_counter + 1) );
                $service->op_one_preassignment = $preassignment;
            }

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $preassignment = "T".( $transfer_counter == 0 ? 1 : ($transfer_counter + 1) );
                $service->op_one_preassignment = $preassignment;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $preassignment = "T".( $transfer_counter == 0 ? 1 : ($transfer_counter + 1) );
                $service->op_two_preassignment = $preassignment;
            }

            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $preassignment = "S".( $departure_counter == 0 ? 1 : ($departure_counter + 1) );
                $service->op_one_preassignment = $preassignment;
                $departure_counter ++;
            }            
            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $preassignment = "S".( $departure_counter == 0 ? 1 : ($departure_counter + 1) );
                $service->op_two_preassignment = $preassignment;
                $departure_counter ++;
            }               
            $service->save();

            //CREAMOS UN LOG
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se pre-asigno de (NULL) por ".$preassignment. " al servicio: ".$service->id.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se pre-asigno el servicio de manera correcta...',
                'data' => array(
                    "item"  => $request->id,
                    "operation"  => $request->operation,
                    "value"  => $preassignment,
                    "message" => "Se pre-asigno de (NULL) por ".$preassignment. " al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //SETEMOS LA UNIDAD E INGRESAMOS EL MONTO OPERATIVO, DEL SERVICIO
    public function setVehicle(Request $request){
        try {
            DB::beginTransaction();
            //OBTENEMOS INFORMACION
            $service = ReservationsItem::find($request->reservation_item);            
            $vehicle_new = Vehicle::find($request->vehicle_id);

            //ACTUALIZAMOS INFORMACION         
            if( $request->operation == 'ARRIVAL' ){
                $vehicle_current = Vehicle::find($service->vehicle_id_one);
                $service->vehicle_id_one = $request->vehicle_id;
                $service->op_one_operating_cost = $request->operating_cost;
            }

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $vehicle_current = Vehicle::find($service->vehicle_id_one);
                $service->vehicle_id_one = $request->vehicle_id;
                $service->op_one_operating_cost = $request->operating_cost;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $vehicle_current = Vehicle::find($service->vehicle_id_two);
                $service->vehicle_id_two = $request->vehicle_id;
                $service->op_two_operating_cost = $request->operating_cost;
            }          

            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $vehicle_current = Vehicle::find($service->vehicle_id_one);
                $service->vehicle_id_one = $request->vehicle_id;
                $service->op_one_operating_cost = $request->operating_cost;
            }
            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $vehicle_current = Vehicle::find($service->vehicle_id_two);
                $service->vehicle_id_two = $request->vehicle_id;
                $service->op_two_operating_cost = $request->operating_cost;
            }
            $service->save();

            //CREAMOS UN LOG
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" ).") por ".$vehicle_new->name. " al servicio: ".$service->id.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de costo operativo por ".$request->value. " al servicio: ".$service->id.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se asigno correctamente la unidad',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->vehicle_id,
                    "name" => $vehicle_new->name,
                    "cost"  => $request->operating_cost,
                    "message" => "Actualización de unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" ).") por ".$vehicle_new->name. " y costo de operación ".$request->operating_cost." al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function setDriver(Request $request){
        try {
            DB::beginTransaction();
            //OBTENEMOS INFORMACION
            $service = ReservationsItem::find($request->reservation_item);        
            $driver_new = Driver::find($request->driver_id);

            //ACTUALIZAMOS INFORMACION
            if( $request->operation == 'ARRIVAL' ){
                $driver_current = Driver::find($service->driver_id_one);
                $service->driver_id_one = $request->driver_id;
            }

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_one);
                $service->driver_id_one = $request->driver_id;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_two);
                $service->driver_id_two = $request->driver_id;
            }

            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_one);
                $service->driver_id_one = $request->driver_id;
            }            
            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_two);
                $service->driver_id_two = $request->driver_id;
            }            
            $service->save();            

            //CREAMOS UN LOG
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se asigno al conductor (".( isset($driver_current->names) ? $driver_current->names." ".$driver_current->surnames : "NULL" ).") por ".$driver_new->names." ".$driver_new->surnames. " al servicio: ".$service->id.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se asigno correctamente el conductor',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->driver_id,
                    "name" => $driver_new->names." ".$driver_new->surnames,
                    "message" => "Se asigno al conductor (".( isset($driver_current->names) ? $driver_current->names." ".$driver_current->surnames : "NULL" ).") por ".$driver_new->names." ".$driver_new->surnames. " al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //ACTUALIZAMOS EL ESTATUS E INGRESAMOS LA HORA OPERATIVA, DEL SERVICIO
    public function updateStatusOperation(Request $request){
        try {
            DB::beginTransaction();            
            $service = ReservationsItem::find($request->item_id);

            if( $request->operation == "ARRIVAL" ):
                $service->op_one_status_operation = $request->status;
                ( isset($request->time) ? $service->op_one_time_operation = $request->time : "" );
            endif;

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $service->op_one_status_operation = $request->status;
                ( isset($request->time) ? $service->op_one_time_operation = $request->time : "" );
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $service->op_two_status_operation = $request->status;
                ( isset($request->time) ? $service->op_two_time_operation = $request->time : "" );
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_status_operation = $request->status;
                ( isset($request->time) ? $service->op_one_time_operation = $request->time : "" );
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_status_operation = $request->status;
                ( isset($request->time) ? $service->op_two_time_operation = $request->time : "" );
            endif;
            $service->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de estatus de operación (".$request->operation.") por ".$request->status.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de hora operación (".$request->operation.") por ".$request->time.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Estatus de operación, actualizado con éxito',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->status,
                    "time"  => ( $request->time != "" ? date("H:i", strtotime($request->time)) : NULL ),
                    "message" => "Actualización de estatus y horario de operación (".$request->type.") por ".$request->status." y ".$request->time." al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatusBooking(Request $request){
        try {
            DB::beginTransaction();
            $service = ReservationsItem::find($request->item_id);

            if( $request->operation == "ARRIVAL" ):
                $service->op_one_status = $request->status;
            endif;

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $service->op_one_status = $request->status;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $service->op_two_status = $request->status;
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_status = $request->status;
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_status = $request->status;
            endif;
            $service->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de estatus de reservación (".$request->operation.") por ".$request->status;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Estatus de reservación, actualizado con éxito',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->status,
                    "message" => "Actualización de estatus de reservación (".$request->type.") por ".$request->status." al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createService(Request $request){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'reference' => 'required|string|max:255',
                'site_id' => 'required|integer|exists:sites,id',
                'language' => 'required|in:en,es',

                'client_first_name' => 'required|string|max:255',
                'client_last_name' => 'required|string|max:255',

                'from_zone_id' => 'required|integer|exists:zones,id',
                'from_name' => 'required|string|max:255',
                'to_zone_id' => 'required|integer|exists:zones,id',
                'to_name' => 'required|string|max:255',

                'passengers' => 'required|integer|max:255',
                'departure_date' => 'required|date_format:Y-m-d H:i',
                'destination_service_id' => 'required|integer|exists:destination_services,id',
                // 'comments' => 'string|max:255',

                'sold_in_currency' => 'required|in:MXN,USD',
                'total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => [
                        'code' => 'required_params',
                        'message' =>  $validator->errors()->all() 
                    ],
                    'message' => $validator->errors()->all()
                ], Response::HTTP_BAD_REQUEST);
            }

            //VALIDAMOS SI LA REFERENCIA YA EXISTE
            $duplicated_reservation = Reservation::where('reference', $request->reference)->count();
            if( $duplicated_reservation ) {
                return response()->json([
                    'errors' => [
                        'code' => 'required_params',
                    ],
                    'message' => 'Ese folio ya ha sido registrado',
                ], Response::HTTP_BAD_REQUEST); 
            }

            //FORMATEAMOS LA FECHA DEL SERVICIO PARA PODER VER SI ACTUALIZAREMOS LA TABLA
            // Crear una instancia de DateTime a partir de la cadena de fecha y hora
            $dateTime = new DateTime($request->departure_date);            
            // Obtener solo la fecha en el formato deseado
            $departure_date = $dateTime->format('Y-m-d');
            $departure_date_today = ( $departure_date == date('Y-m-d') ? true : false );

    
            $default_destination_id = 1; // Considerando que el id corresponde a: "Cancún"
            $destination = Destination::find( $default_destination_id );
    
            $from_coordinates = $this->getLatLngByZoneId( $request->from_zone_id );
            $to_coordinates = $this->getLatLngByZoneId( $request->to_zone_id );
    
            $from_lat = $from_coordinates['lat'];
            $from_lng = $from_coordinates['lng'];
            $to_lat = $to_coordinates['lat'];
            $to_lng = $to_coordinates['lng'];
    
            $from_zone = Zones::find( $request->from_zone_id );
            $to_zone = Zones::find( $request->to_zone_id );
    
            $destination_service = DestinationService::find( $request->destination_service_id );
            if( !$destination_service ){
                return response()->json([
                    'errors' => [
                        'code' => 'required_params',
                    ],                
                    'message' => 'No se encontró el vehículo',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Creando reservación
            $reservation = new Reservation;
            $reservation->client_first_name = $request->client_first_name;
            $reservation->client_last_name = $request->client_last_name;
            $reservation->client_email = $request->client_email ? $request->client_email : null;
            $reservation->client_phone = $request->client_phone ? $request->client_phone : null;
            $reservation->currency = $request->sold_in_currency;
            $reservation->language = $request->language;
            $reservation->rate_group = '0B842B8C';
            $reservation->is_commissionable = 0;
            $reservation->pay_at_arrival = 1;
            $reservation->site_id = $request->site_id;
            $reservation->destination_id = $destination ? $default_destination_id : null;
            $reservation->user_id = auth()->user()->id;
            $reservation->reference = $request->reference;
            $reservation->created_at = Carbon::now();
            $reservation->updated_at = Carbon::now();
            $reservation->comments = $request->comments;
            $reservation->is_complete = ( $request->site_id == 11 || $request->site_id == 21 ? 0 : 1 );
            $reservation->save();

            // Creando follow_up
            $follow_up = new ReservationFollowUp();
            $follow_up->reservation_id = $reservation->id;
            $follow_up->name = auth()->user()->name;
            $follow_up->text = 'Se capturó la venta por: '.auth()->user()->name;
            $follow_up->type = 'HISTORY';
            $follow_up->save();

            $item = new ReservationsItem();
            $item->reservation_id = $reservation->id;
            $item->code = $this->generateCode();
            $item->destination_service_id = $request->destination_service_id;
            $item->from_name = $request->from_name ? $request->from_name : $from_zone->name;
            $item->from_lat = $from_lat;
            $item->from_lng = $from_lng;
            $item->from_zone = $request->from_zone_id;
            $item->to_name = $request->to_name ? $request->to_name : $to_zone->name;
            $item->to_lat = $to_lat;
            $item->to_lng = $to_lng;
            $item->to_zone = $request->to_zone_id;
            $item->distance_time = $to_zone->time ? $this->timeToSeconds( $to_zone->time ) : 0;
            $item->distance_km = $to_zone->distance ? $to_zone->distance : '';
            $item->is_round_trip = 0;
            $item->passengers = $request->passengers;
            $item->op_one_status = 'PENDING';
            $item->op_one_pickup = $request->departure_date;
            $item->op_two_status = 'PENDING';
            $item->created_at = Carbon::now();
            $item->updated_at = Carbon::now();
            $item->save();

            // Creando Sale
            $sale = new Sale();
            $sale->reservation_id = $reservation->id;
            $sale->description = $destination_service->name . ' | ' . 'One Way';
            $sale->quantity = 1;
            $sale->total = $request->total;
            $sale->created_at = Carbon::now();
            $sale->updated_at = Carbon::now();
            $sale->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'date' => $departure_date,
                'today' => $departure_date_today,
                'message' => 'Se agrego servicio correctamente, para '.(  $departure_date_today ? " el día de hoy, es necesario recargar la pagina para actualizar la información " : " la fecha ".$departure_date ),
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => 'Internal Server'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addComment(Request $request){
        try {
            DB::beginTransaction();
            $service = ReservationsItem::find($request->code);
            $action = ( ( ( $request->type == "ARRIVAL" ) && $service->op_one_comments == "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) && $service->op_one_comments == "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) && $service->op_two_comments == "" ) ? "agrego" : ( ( ( $request->type == "ARRIVAL" ) && $service->op_one_comments != "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) && $service->op_one_comments != "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) && $service->op_two_comments != "" ) ? "actualizo" : "" ) );

            if( $request->operation == "ARRIVAL" ):
                $service->op_one_comments = $request->comment;
            endif;

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $service->op_one_comments = $request->comment;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $service->op_two_comments = $request->comment;
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_comments = $request->comment;
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_comments = $request->comment;
            endif;
            $service->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se agrego un comentario al servicio: ".$request->code.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $service->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se '.$action.' comentario exitosamente...',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->comment,
                    "status"  => 1,
                    "message" => "Se agrego un comentario al servicio: ".$request->code.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getComment(Request $request){
        try {
            //DECLARACION DE VARIABLES
            $message = "";
            $service = ReservationsItem::find($request->item_id);

            if( $request->operation == "ARRIVAL" ):
                $message = $service->op_one_comments;
            endif;

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $message = $service->op_one_comments;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $message = $service->op_two_comments;
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $message = $service->op_one_comments;
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $message = $service->op_two_comments;
            endif;            

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request){
        try {
            //DECLARACION DE VARIABLES
            $message = $this->getMessages($request->code);

            return response()->json([
                'success' => ( !empty($message) ? true : false ),
                'message' => $message,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getDataCustomer(Request $request){
        try {
            //DECLARACION DE VARIABLES
            $booking = Reservation::where('id',$request->code)->first();

            return response()->json([
                'success' => ( !empty($booking) ? true : false ),
                'data' => $booking,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }    

    private function timeToSeconds($time) {
        $parts = explode(' ', $time);
        
        $hours = 0;
        $minutes = 0;
        
        foreach ($parts as $key => $part) {
            if ($part == 'H') {
                $hours = (int)$parts[$key - 1];
            } elseif ($part == 'Min') {
                $minutes = (int)$parts[$key - 1];
            }
        }
        
        $seconds = $hours * 3600 + $minutes * 60;
        
        return $seconds;
    }

    private function getLatLngByZoneId($zone_id) {
        $equivalences = [
            1 => [
                'lat' => 21.0442754,
                'lng' => -86.8772972,
            ],
            2 => [
                'lat' => 21.135166,
                'lng' => -86.746224,
            ],
            3 => [
                'lat' => 21.1831607,
                'lng' => -86.8087541,
            ],
            4 => [
                'lat' => 21.2217215,
                'lng' => -86.8029101,
            ],
            5 => [
                'lat' => 20.8471632,
                'lng' => -86.8803245,
            ],
            6 => [
                'lat' => 20.644799,
                'lng' => -87.0917467,
            ],
            7 => [
                'lat' => 21.0815015,
                'lng' => -86.8546508,
            ],
            8 => [
                'lat' => 20.5067138,
                'lng' => -87.2386847,
            ],
            9 => [
                'lat' => 20.4027428,
                'lng' => -87.3193673,
            ],
            10 => [
                'lat' => 20.214244,
                'lng' => -87.4559179,
            ],
            11 => [
                'lat' => 20.187102,
                'lng' => -87.443475,
            ],
            12 => [
                'lat' => 20.3618852,
                'lng' => -87.3327632,
            ],
            13 => [
                'lat' => 20.7612258,
                'lng' => -86.9612859,
            ],
            14 => [
                'lat' => 20.8704582,
                'lng' => -87.0702105,
            ],
            15 => [
                'lat' => 20.0311617,
                'lng' => -87.4780201,
            ],
            16 => [
                'lat' => 20.689586,
                'lng' => -88.2047133,
            ],
            17 => [
                'lat' => 21.4323185,
                'lng' => -87.3375753,
            ],
            18 => [
                'lat' => 20.6787816,
                'lng' => -88.5733424,
            ],
            19 => [
                'lat' => 20.9776327,
                'lng' => -89.6322621,
            ],
            20 => [
                'lat' => 18.526777,
                'lng' => -88.3300811,
            ],
            21 => [
                'lat' => 21.2440641,
                'lng' => -86.8119526,
            ],
            22 => [
                'lat' => 20.1695036,
                'lng' => -87.6847257,
            ],
            23 => [
                'lat' => 20.199593,
                'lng' => -87.49902,
            ],
        ];

        if( !isset($equivalences[$zone_id]) ) return ['lat' => '', 'lng' => ''];

        $lat = $equivalences[$zone_id]['lat'];
        $lng = $equivalences[$zone_id]['lng'];

        return ['lat' => $lat, 'lng' => $lng];
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
                $xHTML .= '[('.$value->type.') '. $value->text.'] <br>';
            endforeach;
        endif;

        return $xHTML;
    }

    public function exportExcelBoard(Request $request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";

        $items = $this->queryBookings($search);

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Rellenar con datos
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Código');
        $sheet->setCellValue('C1', 'Hora');
        $sheet->setCellValue('D1', 'Cliente');
        $sheet->setCellValue('E1', 'Tipo de servicios');
        $sheet->setCellValue('F1', 'Folio');
        $sheet->setCellValue('G1', 'Pax');
        $sheet->setCellValue('H1', 'Origen');
        $sheet->setCellValue('I1', 'Destino');
        $sheet->setCellValue('J1', 'Agencia');
        $sheet->setCellValue('K1', 'Unidad');
        $sheet->setCellValue('L1', 'Conductor');
        $sheet->setCellValue('M1', 'Estatus de operación');
        $sheet->setCellValue('N1', 'Hora de operación');
        $sheet->setCellValue('O1', 'Costo operativo');
        $sheet->setCellValue('P1', 'Estatus de reservación');
        $sheet->setCellValue('Q1', 'Vehículo');
        $sheet->setCellValue('R1', 'Estatus de pago');
        $sheet->setCellValue('S1', 'Total');
        $sheet->setCellValue('T1', 'Moneda');
        $sheet->setCellValue('U1', 'Tipo');
        $sheet->setCellValue('V1', 'Mensajes');

        $count = 2;

        foreach( $items as $key => $item ){
            // $payment = ( $item->total_sales - $item->total_payments );
            // if($payment < 0) $payment = 0;
            $payment = $item->total_sales;
            $messages_text = $this->getMessages($item->reservation_id);
            $message = ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_comments : $item->op_two_comments );
            if( !empty($message) ){
                $messages_text .= $message;
            }

            $flag_preassignment = ( ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ) && $item->op_one_preassignment != "" ? true : ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && ( $item->is_round_trip == 1 ) && $item->op_two_preassignment != "" ? true : false ) );
            $preassignment = ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_preassignment : $item->op_two_preassignment );

            $status_operation = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_status_operation : $item->op_two_status_operation );
            $time_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_time_operation : $item->op_two_time_operation );
            $cost_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_operating_cost : $item->op_two_operating_cost );
            $status_booking =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_status : $item->op_two_status );            

            $operation_pickup = (($item->operation_type == 'arrival')? $item->op_one_pickup : $item->op_two_pickup );
            $operation_from = (($item->operation_type == 'arrival')? $item->from_name.((!empty($item->flight_number))? ' ('.$item->flight_number.')' :'')  : $item->to_name );
            $operation_to = (($item->operation_type == 'arrival')? $item->to_name : $item->from_name );
            $vehicle_d = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Vehicle::find($item->vehicle_id_one) : Vehicle::find($item->vehicle_id_two) );
            $driver_d =  ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Driver::find($item->driver_id_one) : Driver::find($item->driver_id_two) );


            $sheet->setCellValue('A'.strval($count), ( $preassignment != "" ? $preassignment : "ADD" ));
            $sheet->setCellValue('B'.strval($count), $item->code);
            $sheet->setCellValue('C'.strval($count), date("H:i", strtotime($operation_pickup)));
            $sheet->setCellValue('D'.strval($count), $item->client_first_name.' '.$item->client_last_name);
            $sheet->setCellValue('E'.strval($count), $item->final_service_type);
            $sheet->setCellValue('F'.strval($count), $item->reference);
            $sheet->setCellValue('G'.strval($count), $item->passengers);
            $sheet->setCellValue('H'.strval($count), $operation_from);
            $sheet->setCellValue('I'.strval($count), $operation_to);
            $sheet->setCellValue('J'.strval($count), $item->site_name);
            $sheet->setCellValue('K'.strval($count), ( isset($vehicle_d->name) ? $vehicle_d->name : 'Selecciona vehículo' ));
            $sheet->setCellValue('L'.strval($count), ( isset($driver_d->names) ? $driver_d->names.' '.$driver_d->surnames : 'Selecciona conductor' ));
            $sheet->setCellValue('M'.strval($count), $status_operation);
            $sheet->setCellValue('N'.strval($count), ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation);
            $sheet->setCellValue('O'.strval($count), $cost_operation);
            $sheet->setCellValue('P'.strval($count), $status_booking);
            $sheet->setCellValue('Q'.strval($count), $item->service_name);
            $sheet->setCellValue('R'.strval($count), $item->status);
            $sheet->setCellValue('S'.strval($count), number_format(( $item->is_round_trip == 1 ? ( $payment / 2 ) : $payment ),2));
            $sheet->setCellValue('T'.strval($count), $item->currency);
            $sheet->setCellValue('U'.strval($count), ( $item->is_round_trip == 1 ? 'Round Trip' : 'One Way' ));
            $sheet->setCellValue('V'.strval($count), $messages_text);
            $count = $count + 1;
        }

        // Crear un escritor de archivos Excel
        $writer = new Xlsx($spreadsheet);

        // Crear una respuesta HTTP para la descarga del archivo
        $fileName = 'operation_board_'.$request->date.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return ResponseFile::download($temp_file, $fileName)->deleteFileAfterSend(true);        
    }

    public function exportExcelBoardCommision(Request $request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";

        $items = $this->queryBookings($search);

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Rellenar con datos
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Hora');
        $sheet->setCellValue('C1', 'Cliente');
        $sheet->setCellValue('D1', 'Tipo de servicios');
        $sheet->setCellValue('E1', 'Folio');
        $sheet->setCellValue('F1', 'Pax');
        $sheet->setCellValue('G1', 'Origen');
        $sheet->setCellValue('H1', 'Destino');
        $sheet->setCellValue('I1', 'Agencia');
        $sheet->setCellValue('J1', 'Unidad');
        $sheet->setCellValue('K1', 'Conductor');
        $sheet->setCellValue('L1', 'Hora de operación');
        $sheet->setCellValue('M1', 'Costo operativo');
        $sheet->setCellValue('N1', 'Total');
        $sheet->setCellValue('O1', 'Comisión');
        $sheet->setCellValue('P1', 'Monenda Comisión');

        $count = 2;

        foreach( $items as $key => $item ){
            // $payment = ( $item->total_sales - $item->total_payments );
            // if($payment < 0) $payment = 0;
            $payment = ( $item->site_id == 21 ? ( $item->currency == "USD" ? ( $item->total_sales * 16 ) : $item->total_sales ) : $item->total_sales );

            $preassignment = ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_preassignment : $item->op_two_preassignment );

            $status_operation = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_status_operation : $item->op_two_status_operation );
            $time_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_time_operation : $item->op_two_time_operation );
            $cost_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_operating_cost : $item->op_two_operating_cost );
            $status_booking =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_status : $item->op_two_status );            

            $operation_pickup = (($item->operation_type == 'arrival')? $item->op_one_pickup : $item->op_two_pickup );
            $operation_from = (($item->operation_type == 'arrival')? $item->from_name.((!empty($item->flight_number))? ' ('.$item->flight_number.')' :'')  : $item->to_name );
            $operation_to = (($item->operation_type == 'arrival')? $item->to_name : $item->from_name );
            $vehicle_d = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Vehicle::find($item->vehicle_id_one) : Vehicle::find($item->vehicle_id_two) );
            $driver_d =  ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Driver::find($item->driver_id_one) : Driver::find($item->driver_id_two) );
            $porcentaje = ( $item->site_id == 21 ? 0.04 : 0.05 );
            $commission = ( $item->site_id == 21 ? ( $payment * $porcentaje ) : ( $cost_operation * $porcentaje ) );


            $sheet->setCellValue('A'.strval($count), ( $preassignment != "" ? $preassignment : "ADD" ));
            $sheet->setCellValue('B'.strval($count), date("H:i", strtotime($operation_pickup)));
            $sheet->setCellValue('C'.strval($count), $item->client_first_name.' '.$item->client_last_name);
            $sheet->setCellValue('D'.strval($count), $item->final_service_type);
            $sheet->setCellValue('E'.strval($count), $item->reference);
            $sheet->setCellValue('F'.strval($count), $item->passengers);
            $sheet->setCellValue('G'.strval($count), $operation_from);
            $sheet->setCellValue('H'.strval($count), $operation_to);
            $sheet->setCellValue('I'.strval($count), $item->site_name);
            $sheet->setCellValue('J'.strval($count), ( isset($vehicle_d->name) ? $vehicle_d->name : 'Selecciona vehículo' ));
            $sheet->setCellValue('K'.strval($count), ( isset($driver_d->names) ? $driver_d->names.' '.$driver_d->surnames : 'Selecciona conductor' ));
            $sheet->setCellValue('L'.strval($count), ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation);
            $sheet->setCellValue('M'.strval($count), number_format($cost_operation,2));
            $sheet->setCellValue('N'.strval($count), number_format($payment,2));
            $sheet->setCellValue('O'.strval($count), $commission);
            $sheet->setCellValue('P'.strval($count), 'MXN');
            $count = $count + 1;
        }

        // Crear un escritor de archivos Excel
        $writer = new Xlsx($spreadsheet);

        // Crear una respuesta HTTP para la descarga del archivo
        $fileName = 'operation_board_comission_'.$request->date.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return ResponseFile::download($temp_file, $fileName)->deleteFileAfterSend(true);        
    }    
}
