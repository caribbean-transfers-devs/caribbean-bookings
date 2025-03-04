<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\User;
use App\Models\OriginSale;
use App\Models\Enterprise;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Zones;
use App\Models\DestinationService;
use App\Models\CancellationTypes;
use App\Models\ExchangeRateReport;
use App\Models\ExchangeRateCommission;
use App\Models\SalesType;
use App\Models\ContactPoints;

trait FiltersTrait
{

    //NOS TRAE EL TIPO DE CAMBIO DEPENDIENDO DE LA FECHA, PARA LOS REPORTES
    public static function Exchange($in, $end){
        $in = ( isset($in) ? $in : date('Y-m-d') );
        $end = ( isset($in) ? $in : date('Y-m-d') );
        $report = ExchangeRateReport::where('status', 1)->where('date_init', '<=', $in)
                                ->where('date_end', '>=', $end)
                                ->first();

        if ($report) {
            return $report->exchange; // Ejemplo: 25.50
        } else {
            return 18;
        }
    }

    public static function ExchangeCommission($in, $end){
        $in = ( isset($in) ? $in : date('Y-m-d') );
        $end = ( isset($in) ? $in : date('Y-m-d') );
        $report = ExchangeRateCommission::where('status', 1)->where('date_init', '<=', $in)
                                ->where('date_end', '>=', $end)
                                ->first();

        if ($report) {
            return $report->exchange; // Ejemplo: 25.50
        } else {
            return 16.5;
        }
    }

    public static function PercentageCommissionInvestment(){
        return 20; // Ejemplo: 20.00
    }

    public static function Users(){
        return User::where('status', 1)->get();
    }

    //NOS TRAE LOS AGENTES DE CALL CENTER
    public static function CallCenterAgent(){
        return User::where('is_commission', 1)->with('target')->get();
    }

    public static function Enterprises(){
        return Enterprise::all();
    }

    //TIPO DE SERVICIO
    public static function Services(){
        return array(
            "0" => "One Way",
            "1" => "Round Trip",
        );
    }

    //SITIOS O AGENCIAS
    public static function Sites(){
        return DB::select("SELECT id, name, type_site FROM sites ORDER BY name ASC");
    }

    public static function Origins(){
        $origins[] = (object) array(
            "id" => 0,
            "code" => "PAGINA WEB"
        );        
        $data = OriginSale::All();
        if( !empty($data) ){
            foreach ($data as $key => $value) {
                $origins[] = $value;
            }
        }
        
        // dd($origins);        
        return $origins;
    }

    //ESTATUS DE RESERVACIÓN
    public static function reservationStatus(){
        return array(
            "CONFIRMED" => "Confirmado",
            "PENDING" => "Pendiente",
            "CREDIT" => "Crédito",
            "OPENCREDIT" => "Credito abierto",
            "CANCELLED" => "Cancelado",
            "DUPLICATED" => "Duplicado",
            "QUOTATION" => "Cotización",
        );
    }

    //TIPO DE SERVICIO EN OPERACIÓN
    public static function servicesOperation(){
        return array(
            "ARRIVAL" => "Llegada",
            "DEPARTURE" => "Salida",
            "TRANSFER" => "Transalado",
        );
    }

    //TIPO DE VEHÍCULO
    public static function Vehicles(){
        return DestinationService::all();
    }

    //ZONAS DE ORIGEN Y DESTINO
    public static function Zones($destination_id = NULL){
        if( $destination_id ){
            return Zones::where('destination_id', $destination_id)->get();
        }else{
            return Zones::all();
        }        
    }

    //ESTATUS DE SERVICIO
    public static function statusOperationService(){
        return array(
            "PENDING" => "Pendiente",
            "COMPLETED" => "Completado",
            "NOSHOW" => "No se presentó",
            "CANCELLED" => "Cancelado",
        );
    }

    //SON LA UNIDADES QUE SE ASIGNAN EN LA OPERACIÓN, PERO QUE SON CONSIDERADOS COMO LOS VEHICULOS QUE TENEMOS
    // SI LE MANDAMOS EL PARAMERO ACTION COMO FILTERS, NOS TRAE TODAS LAS UNIDADES SI IMPORTAR QUE ESTEN INACTIVAS
    // SI LE MANDAMOS EL PARAMERO ACTION COMO DIFERENTE DE FILTERS, NOS TRAE TODAS LAS UNIDADES QUE SOLO ESTEN ACTIVAS
    public static function Units($action = "filters"){
        if( $action == "filters" ){
            return Vehicle::all();
        }else{
            return Vehicle::where('status',1)->get();
        }
    }

    //CONDUCTOR
    public static function Drivers($action = "filters"){
        if( $action == "filters" ){
            return Driver::orderBy('names','ASC')->get();
        }else{
            return Driver::where('status',1)->orderBy('names','ASC')->get();
        }
    }

    //ESTATUS DE OPERACIÓN
    public static function statusOperation(){
        return array(
            "PENDING" => "Pendiente",
            "E" => "E",
            "C" => "C",
            "OK" => "OK",
        );
    }    
    
    //ESTATUS DE PAGO DE RESERVACIÓN
    public static function paymentStatus(){
        return array(
            "PAID" => "Pagado",
            "PENDING" => "Pendiente",
        );
    }

    //MONEDA DE RESERVACIÓN
    public static function Currencies(){
        return array(
            "USD" => "USD",
            "MXN" => "MXN",
        );
    }
    
    //METODO DE PAGO DE RESERVACIÓN
    public static function Methods(){
        return array(
            "CREDIT" => "CREDITO",
            "CASH" => "EFECTIVO",
            "STRIPE" => "STRIPE",
            "PAYPAL" => "PAYPAL",
            "MIFEL" => "MIFEL",
        );
    }

    //MOTIVOS DE CANCELACIÓN
    public static function CancellationTypes(){
        return CancellationTypes::where('status',1)->get();
    }

    public static function TypeSales()
    {
        return SalesType::all();
    }

    public static function ContactPoints($destination_id = NULL){
        return ContactPoints::where('destination_id', $destination_id )->get();
    }

    public static function parseArrayQuery($data, $marks = NULL){
        if (is_array($data)) {
            $filteredData = array_filter($data, function($value) {
                return $value !== NULL && $value !== 0;
            });
            
            // Envuelve cada valor del array en comillas simples
            $string = implode(',', array_map(function($value) use ($marks) {
                if( $marks != NULL && $marks == "single" ){
                    return "'" . $value . "'";
                }else if( $marks != NULL && $marks == "single" ){
                    return '"' . $value . '"';
                }else{
                    return $value;   
                }
            }, $filteredData));
            return $string;
        } else {
            return "'" . $data . "'";
        }
    }
}