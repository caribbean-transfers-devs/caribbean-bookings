<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\OriginSale;
use App\Models\Enterprise;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Zones;
use App\Models\DestinationService;
use App\Models\CancellationTypes;

trait FiltersTrait
{
    public function Enterprises(){
        return Enterprise::all();
    }

    //TIPO DE SERVICIO
    public function Services(){
        return array(
            "0" => "One Way",
            "1" => "Round Trip",
        );
    }

    //SITIOS O AGENCIAS
    public function Sites(){
        return DB::select("SELECT id, name as site_name FROM sites ORDER BY site_name ASC");
    }

    public function Origins(){
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
    public function reservationStatus(){
        return array(
            "CONFIRMED" => "Confirmado",
            "PENDING" => "Pendiente",
            "OPENCREDIT" => "Credito abierto",
            "CANCELLED" => "Cancelado",
            "DUPLICATED" => "Duplicado",
        );
    }

    //TIPO DE SERVICIO EN OPERACIÓN
    public function servicesOperation(){
        return array(
            "ARRIVAL" => "Llegada",
            "DEPARTURE" => "Salida",
            "TRANSFER" => "Transalado",
        );
    }

    //TIPO DE VEHÍCULO
    public function Vehicles(){
        return DestinationService::all();
    }

    //ZONAS DE ORIGEN Y DESTINO
    public function Zones(){
        return Zones::all();;
    }

    //ESTATUS DE SERVICIO
    public function statusOperationService(){
        return array(
            "PENDING" => "Pendiente",
            "COMPLETED" => "Completado",
            "NOSHOW" => "No se presentó",
            "CANCELLED" => "Cancelado",
        );
    }

    //SON LA UNIDADES QUE SE ASIGNAN EN LA OPERACIÓN, PERO QUE SON CONSIDERADO COMO LOS VEHICULOS QUE TENEMOS
    public function Units(){
        return Vehicle::all();
    }

    //CONDUCTOR
    public function Drivers(){
        return Driver::orderBy('names','ASC')->get();
    }

    //ESTATUS DE OPERACIÓN
    public function statusOperation(){
        return array(
            "PENDING" => "Pendiente",
            "E" => "E",
            "C" => "C",
            "OK" => "OK",
        );
    }    
    
    //ESTATUS DE PAGO DE RESERVACIÓN
    public function paymentStatus(){
        return array(
            "PAID" => "Pagado",
            "PENDING" => "Pendiente",
        );
    }

    //MONEDA DE RESERVACIÓN
    public function Currencies(){
        return array(
            "USD" => "USD",
            "MXN" => "MXN",
        );
    }
    
    //METODO DE PAGO DE RESERVACIÓN
    public function Methods(){
        return array(
            "CASH" => "CASH",
            "STRIPE" => "STRIPE",
            "PAYPAL" => "PAYPAL",
            "MIFEL" => "MIFEL",
        );
    }

    //MOTIVOS DE CANCELACIÓN
    public function CancellationTypes(){
        return CancellationTypes::where('status',1)->get();
    }

    public function parseArrayQuery($data, $marks = NULL){
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