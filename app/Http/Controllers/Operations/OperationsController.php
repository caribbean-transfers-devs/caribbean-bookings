<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Enterprise;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;

use App\Traits\RoleTrait;
use Exception;

class OperationsController extends Controller
{
    
    public function index(Request $request){
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );

        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";

        $items = $this->querySpam($search);

        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Gestión de envío de operaciones",
                "active" => true
            ),
        );

        $vehicles = Vehicle::All();
        $drivers = Driver::All();

        return view('operation.operations', compact('items','date','breadcrumbs','vehicles','drivers'));
    }

    public function dataOperations(Request $request){
        //DECLARACIÓN DE VARIABLES
        $data["data"] = array();        
        $date = ( isset( $request->data->date ) ? $request->data->date : date("Y-m-d") );
        $search['init'] = $date." 00:00:00";
        $search['end'] = $date." 23:59:59";

        $items = $this->querySpam($search);

        //CONSULTAMOS LOS VEHICULOS Y VENDEDORES
        $vehicles = Vehicle::All();
        $drivers = Driver::All();

        if( sizeof($items)>=1 ):
            foreach($items as $key => $value):
                $payment = ( $value->total_sales - $value->total_payments );
                if($payment < 0) $payment = 0;

                $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status_operation : $value->op_two_status_operation );
                $operation_booking = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                $flag_comment = ( ($value->operation_type == 'arrival') && $value->op_one_comments != "" ? true : ( ($value->operation_type == 'departure') && $value->op_two_comments != "" ? true : false ) );
                $comment = (($value->operation_type == 'arrival')? $value->op_one_comments : $value->op_two_comments );

                switch ($operation_status) {
                    case 'PENDING':
                        $label = 'secondary';
                        break;
                    case 'E':
                        $label = 'info';
                        break;
                    case 'C':
                        $label = 'warning';
                        break;
                    case 'OK':
                        $label = 'success';
                        break;
                    default:
                        $label = 'secondary';
                        break;
                }

                switch ($operation_booking) {
                    case 'PENDING':
                        $label2 = 'secondary';
                        break;
                    case 'COMPLETED':
                        $label2 = 'success';
                        break;
                    case 'NOSHOW':
                        $label2 = 'warning';
                        break;
                    case 'CANCELLED':
                        $label2 = 'danger';
                        break;
                    default:
                        $label2 = 'secondary';
                        break;
                }

                $vehicle_items = "";
                if ( isset($vehicles) && count($vehicles) >= 1 ):
                    foreach ($vehicles as $vehicle):
                        $vehicle_items = '<option '.( isset($value->vehicle_id) && $value->vehicle_id == $vehicle->id ? 'selected' : '' ).' value="'.$vehicle->id.'">'.$vehicle->name.'</option>';
                    endforeach;
                endif;

                $driver_items = "";
                if ( isset($drivers) && count($drivers) >= 1 ):
                    foreach ($drivers as $driver):
                        $driver_items = '<option '.( isset($value->driver_id) && $value->driver_id == $driver->id ? 'selected' : '' ).' value="'.$driver->id.'">'.$driver->names.' '.$driver->surnames.'</option>';
                    endforeach;
                endif;

                $data["data"][] = array(
                    '',
                    ( $flag_comment ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square bs-popover" data-bs-container="body" data-bs-trigger="hover" data-bs-content="'.$comment.'"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>' : '' ),
                    date("H:i", strtotime($operation_pickup)),
                    $value->client_first_name.' '.$value->client_last_name . ( !empty($value->reference) ? '['.$value->reference.']' : '' ),
                    $value->final_service_type,
                    $value->passengers,
                    $operation_from,
                    $operation_to,
                    $value->site_name,
                    '
                        <select class="form-control vehicles " data-live-search="true" name="vehicle_id" id="vehicle_id" data-code="'.$value->id.'">
                            <option value="0">Selecciona un vehículo</option>
                            '.$vehicle_items.'
                        </select>
                    ',
                    '
                        <select class="form-control drivers " data-live-search="true" name="driver_id" id="driver_id" data-code="'.$value->id.'">
                            <option value="0">Selecciona un conductor</option>
                            '.$driver_items.'
                        </select>
                    ',
                    '
                        <div class="btn-group" role="group">
                            <button id="optionsOperation" type="button" class="btn btn-'.$label.' dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                '.$operation_status.'
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="optionsOperation">
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatusOperation(event, '.$value->operation_type.', "PENDING", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatusOperation(event, '.$value->operation_type.', "E", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatusOperation(event, '.$value->operation_type.', "C", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatusOperation(event, '.$value->operation_type.', "OK", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                            </div>
                        </div>                    
                    ',
                    '
                        <div class="btn-group" role="group">
                            <button id="optionsBooking" type="button" class="btn btn-'.$label2.' dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                '.$operation_booking.'
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="optionsBooking">
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatus(event, '.$value->operation_type.', "PENDING", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatus(event, '.$value->operation_type.', "COMPLETED", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatus(event, '.$value->operation_type.', "NOSHOW", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item" onclick="setStatus(event, '.$value->operation_type.', "CANCELLED", '.$value->id.', '.$value->reservation_id.')"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>                                                                
                            </div>
                        </div>                    
                    ',
                    ( RoleTrait::hasPermission(38) ? '<a href="/reservations/detail/'.$value->reservation_id.'">'.$value->code.'</a>' : $value->code ),
                    $value->service_name,
                    $value->status,
                    number_format($payment,2),
                    $value->currency,
                    ( !$flag_comment ? '<div class="btn btn-primary __open_modal_comment" data-bs-toggle="modal" data-bs-target="#messageModal" data-code="'.$value->id.'" data-type="'.$value->operation_type.'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></div>' : '' ),
                );
            endforeach;
        endif;

        return response()->json($data, 200);
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
                            'TYPE_ONE' as op_type,
                            '' as messages,
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
                                   GROUP BY it.id, rez.id, serv.id, sit.id, zone_one.id, zone_two.id
                                   UNION 
                                   SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_two_pickup as filtered_date, 'departure' as operation_type, sit.name as site_name, 'TYPE_TWO' as op_type, '' as messages,
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

    public function setVehicle(Request $request){
        try {
            DB::beginTransaction();
            //OBTENEMOS INFORMACION
            $item = ReservationsItem::find($request->reservation_item_id);
            $vehicle_current = Vehicle::find($item->vehicle_id);
            $vehicle_new = Vehicle::find($request->vehicle_id);

            //ACTUALIZAMOS INFORMACION
            $item->vehicle_id = $request->vehicle_id;
            $item->save();

            //CREAMOS UN LOG
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se asigno la unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" ).") por ".$vehicle_new->name. " al servicio: ".$item->id.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se asigno correctamente la unidad',
                'data' => array(
                    "item"  => $request->item,
                    "value"  => $request->vehicle_id,
                    "message" => "Se asigno la unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" ).") por ".$vehicle_new->name. " al servicio: ".$item->id.", por ".auth()->user()->name
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
            $item = ReservationsItem::find($request->reservation_item_id);
            $driver_current = Driver::find($item->driver_id);
            $driver_new = Driver::find($request->driver_id);

            //ACTUALIZAMOS INFORMACION
            $item->driver_id = $request->driver_id;
            $item->save();            

            //CREAMOS UN LOG
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se asigno al conductor (".( isset($driver_current->names) ? $driver_current->names." ".$driver_current->surnames : "NULL" ).") por ".$driver_new->names." ".$driver_new->surnames. " al servicio: ".$item->id.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se asigno correctamente el conductor',
                'data' => array(
                    "item"  => $request->item,
                    "value"  => $request->driver_id,                    
                    "message" => "Se asigno al conductor (".( isset($driver_current->names) ? $driver_current->names." ".$driver_current->surnames : "NULL" ).") por ".$driver_new->names." ".$driver_new->surnames. " al servicio: ".$item->id.", por ".auth()->user()->name        
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

    public function updateStatusOperation(Request $request){
        try {
            DB::beginTransaction();            
            $item = ReservationsItem::find($request->item_id);
            if($request->type == "arrival"):
                $item->op_one_status_operation = $request->status;
            endif;
            if($request->type == "departure"):
                $item->op_two_status_operation = $request->status;
            endif;
            $item->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Actualización de estatus de operación (".$request->type.") por ".$request->status;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Estatus de operación, actualizado con éxito',
                'data' => array(
                    "item"  => $request->item,
                    "value"  => $request->status,
                    "message" => "Actualización de estatus de operación (".$request->type.") por ".$request->status." al servicio: ".$item->id.", por ".auth()->user()->name
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
            $follow_up_db->text = "Actualización de estatus de reservación (".$request->type.") por ".$request->status;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
            $follow_up_db->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Estatus de reservación, actualizado con éxito',
                'data' => array(
                    "item"  => $request->item,
                    "value"  => $request->status,
                    "message" => "Actualización de estatus de reservación (".$request->type.") por ".$request->status." al servicio: ".$item->id.", por ".auth()->user()->name
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

    public function addComment(Request $request){
        try {
            DB::beginTransaction();
            $item = ReservationsItem::find($request->code);
            $action = ( ($request->type == "arrival" && $item->op_one_comments == "") || ($request->type == "departure" && $item->op_two_comments == "") ? "agrego" : ( ($request->type == "arrival" && $item->op_one_comments != "") || ($request->type == "departure" && $item->op_two_comments != "") ? "actualizo" : "" ) );
            if($request->type == "arrival"):
                $item->op_one_comments = $request->comment;
            endif;
            if($request->type == "departure"):
                $item->op_two_comments = $request->comment;
            endif;
            $item->save();
            
            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "Se agrego un comentario al servicio: ".$request->code.", por ".auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $item->reservation_id;
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
            $item = ReservationsItem::find($request->item_id);
            return response()->json([
                'success' => true,
                'message' => ( $request->type == "arrival" ? $item->op_one_comments : $item->op_two_comments ),
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

}
