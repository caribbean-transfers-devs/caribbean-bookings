<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait OperationTrait
{

    use BookingTrait;

    //NOS AYUDA A COLOCAR LA CLASE, QUE TENDRA LA PREASIGNACION DEL SERVICIO
    public static function classServiceNumber($status = "ARRIVAL"){
        switch ($status) {
            case 'DEPARTURE':
                return 'primary';
                break;
            case 'TRANSFER':
                return 'info';
                break;
            default:
                return 'success';
                break;
        }        
    }

    //NOS AYUDA A SABER SI EL SERVICIO YA ESTA PREASIGNADO
    public static function validatePreassignment($service){
        $status = false;
        if( $service->op_type == "TYPE_ONE" && $service->op_one_preassignment != "" ){
            $status = true;
        }else if( $service->op_type == "TYPE_TWO" && $service->op_two_preassignment != "" ){
            $status = true;
        }
        return $status;
    }

    //TIPO DE SERVICIO EN OPERACIONES
    public static function typeService($status){
        switch ($status) {
            case 'DEPARTURE':
                return 'SALIDA';
                break;
            case 'TRANSFER':
                return 'TRASLADO';
                break;                              
            default:
                return 'LLEGADA';
                break;
        }        
    }

    public static function typePreassignment($service){
        if( $service->op_type == "TYPE_ONE" && $service->op_one_preassignment != "" ){
            return $service->op_one_preassignment;
        }else if( $service->op_type == "TYPE_TWO" && $service->op_two_preassignment != "" ){
            return $service->op_two_preassignment;
        }
    }

    public static function renderServicePreassignment($service){
        return '<button type="button" class="btn btn-'.( self::validatePreassignment($service) ? self::classServiceNumber($service->final_service_type) : 'danger' ).' btn-sm">'.( self::validatePreassignment($service) ? self::typePreassignment($service) : 'ADD' ).'</button>';
    }

    // ESTATUS CONFIRMACION
    public static function serviceStatusConfirmation($service){
        return ( $service->operation_type == 'arrival' ? $service->op_one_confirmation : $service->op_two_confirmation );
    }

    // BOTON DE CONFIRMACION
    
    public static function renderStatusConfirmation($service){
        $confirmation_type = self::serviceStatusConfirmation($service);
        $operation_type = "'".$service->operation_type."'";
        return '<button type="button" class="btn  btn-order btn-'.( self::serviceStatusConfirmation($service) == 0 ? 'warning' : 'success' ).'" onClick="updateConfirmation(event,'.$service->id.','.$operation_type.','.$confirmation_type.','.$service->reservation_id.')">'.( self::serviceStatusConfirmation($service) == 0 ? 'N' : 'Y' ).'</button>';
    }

    //COLOR AGENCY

    public static function setClassColorAgency(){
        
    }

    // ZONAS

    public static function classCutOffZone($service){
        $cut_off_zone = ( $service->op_type == "TYPE_ONE" ? $service->zone_one_cut_off : $service->zone_two_cut_off );
        return 'style="'.( $cut_off_zone >= 3 ? 'background-color:#e2a03f;color:#fff;' : ( $cut_off_zone >= 2 && $cut_off_zone < 3 ? 'background-color:#805dca;color:#fff;' : '' ) ).'"';
    }

    public static function setFrom($service, $type = "destination"){
        if ($type == "destination") {
            return ( $service->operation_type == 'arrival' ? $service->destination_name_from : $service->destination_name_to );
        }else{
            return ( $service->operation_type == 'arrival' ? $service->from_name : $service->to_name );
        }
        
    }

    public static function setTo($service, $type = "destination"){
        if ($type == "destination") {
            return ( $service->operation_type == 'arrival' ? $service->destination_name_to : $service->destination_name_from );
        }else{
            return ( $service->operation_type == 'arrival' ? $service->to_name : $service->from_name );
        }        
    }

    //SETEA LA FECHA O HORA, DEPENDIENDO DEL TIPE, DATE TIME
    public static function setDateTime($service, $type = "")
    {
        if ($type == "date") {
            return date("Y-m-d", strtotime(( $service->operation_type == 'arrival' ? $service->pickup_from : $service->pickup_to )));
        }else if ($type == "time"){
            return date("H:i", strtotime(( $service->operation_type == 'arrival' ? $service->pickup_from : $service->pickup_to )));
        }else{
            return ( $service->operation_type == 'arrival' ? $service->pickup_from : $service->pickup_to );
        }
    }

    //
    public static function serviceStatus($service, $action = "translate"){
        if( $action == "translate" ){
            return self::statusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_status : $service->two_service_status ));
        }else if( $action == "translate_name" ){
            return self::statusBooking($service);
        }else if( $action == "no_translate" ){
            return ( $service->op_type == "TYPE_ONE" ? $service->one_service_status : $service->two_service_status );
        }
    }

    //AQUI NOS MUESTRA EL BOTON CON EL ESTATUS DEL SERVICIO, CUANDO SE CERRO LA OPERACIÓN
    public static function renderServiceStatus($service){
        return '<button type="button" class="btn btn-'.self::classStatusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_status : $service->two_service_status ), 'OPERATION').'">'.self::statusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_status : $service->two_service_status )).'</button>';
    }

    //AQUI NOS MUESTRA EL BOTON CON LAS OPCIONES DE ESTATUS, CUANDO LA OPERACIÓN ESTA ABIERTA
    public static function renderServiceOptionsStatus($key,$service){
        // <div class="dropdown-divider"></div>
        // <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="CANCELLED" data-item="'. $service->id .'" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>        
        return '<div class="btn-group" role="group">
                    <button id="optionsBooking'.$key.$service->id.'" data-item="'.$key.$service->id.'" type="button" class="btn btn-'.self::classStatusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_status : $service->two_service_status ), 'OPERATION').' dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>'.self::statusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_status : $service->two_service_status )).'</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="optionsBooking'.$key.$service->id.'">
                        <a href="javascript:void(0);" class="dropdown-item serviceStatusUpdate" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="PENDING" data-item="'. $service->id .'" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                        <a href="javascript:void(0);" class="dropdown-item serviceStatusUpdate" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="COMPLETED" data-item="'. $service->id.'}" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                        <a href="javascript:void(0);" class="dropdown-item serviceStatusUpdate" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="NOSHOW" data-item="'. $service->id .'" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                    </div>                    
                </div>';
    } 

    //MOSTRAMOS EL NOMBRE DE LA UNIDAD, SELECCIONADA EN LA OPERCION
    public static function setOperationUnit($service){
        if( $service->op_type == "TYPE_ONE" && $service->vehicle_one_name != null ){
            return $service->vehicle_one_name." - ".$service->vehicle_name_one;
        }else if( $service->op_type == "TYPE_TWO" && $service->vehicle_two_name != null ){
            return $service->vehicle_two_name." - ".$service->vehicle_name_two;
        }else{
            return "Unidad no seleccionada";
        }
    }

    //MOSTRAMOS EL NOMBRE DEL VEHÍCULO, BASADO EN LA UNIDAD SELECCIONADA EN LA OPERACIÓN
    public static function setOperationVehicle($service){
        if( $service->op_type == "TYPE_ONE" && $service->vehicle_one_name != null ){
            return $service->vehicle_name_one;
        }else if( $service->op_type == "TYPE_TWO" && $service->vehicle_two_name != null ){
            return $service->vehicle_name_two;
        }else{
            return "Unidad no seleccionada";
        }        
        //return ( $service->op_type == "TYPE_ONE" ? $service->vehicle_name_one : $service->vehicle_name_two );
    }

    public static function setOperationDriver($service){
        if( $service->op_type == "TYPE_ONE" && $service->driver_one_name != null ){
            return $service->driver_one_name;
        }else if( $service->op_type == "TYPE_TWO" && $service->driver_two_name != null ){
            return $service->driver_two_name;
        }else{
            return "Conductor no seleccionado";
        }
    }

    public static function setOperationTime($service){
        if( $service->op_type == "TYPE_ONE" ){
            return date("H:i", strtotime($service->op_one_time_operation));
        }else if( $service->op_type == "TYPE_TWO" ){
            return date("H:i", strtotime($service->op_two_time_operation));
        }else{
            return "";
        }
    }

    public static function setOperatingCost($service){
        if( $service->op_type == "TYPE_ONE" ){
            return $service->op_one_operating_cost;
        }else if( $service->op_type == "TYPE_TWO" ){
            return $service->op_two_operating_cost;
        }else{
            return 0;
        }
    }
    
    public static function operationStatus($service, $action = "translate"){
        if( $action == "translate" ){
            return self::statusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_operation_status : $service->two_service_operation_status ));
        }else if( $action == "no_translate" ){
            return ( $service->op_type == "TYPE_ONE" ? $service->one_service_operation_status : $service->two_service_operation_status );
        }        
    }

    public static function renderOperationStatus($service){
        return '<button type="button" class="btn btn-'.self::classStatusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_operation_status : $service->two_service_operation_status ), 'OPERATION').'">'.self::statusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_operation_status : $service->two_service_operation_status )).'</button>';
    }

    public static function renderOperationOptionsStatus($key,$service){
        return '<div class="btn-group" role="group">
                    <button id="optionsOperation'.$key.$service->id.'" data-item="'.$key.$service->id.'" type="button" class="btn btn-'.self::classStatusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_operation_status : $service->two_service_operation_status ), 'OPERATION').' dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span>'.self::statusBooking(( $service->op_type == "TYPE_ONE" ? $service->one_service_operation_status : $service->two_service_operation_status )).'</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="optionsOperation'.$key.$service->id.'">
                        <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="PENDING" data-item="'. $service->id .'" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                        <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="E" data-item="'. $service->id.'}" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                        <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="C" data-item="'. $service->id .'" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="'. $service->final_service_type .'" data-service="'. $service->operation_type .'" data-type="'. $service->op_type .'" data-status="OK" data-item="'. $service->id .'" data-booking="'. $service->reservation_id .'" data-key="'. $key.$service->id .'"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                    </div>                    
                </div>';
    }    

    public static function commissionOperation($service){
        $payment = ( $service->site_id == 21 ? ( $service->currency == "USD" ? ( $service->total_sales * 16 ) : $service->total_sales ) : ( $service->op_type == "TYPE_ONE" ? $service->op_one_operating_cost : $service->op_two_operating_cost ) );
        $percentage = ( $service->site_id == 21 ? 0.04 : 0.05 );
        return ( $payment * $percentage );
    }
}