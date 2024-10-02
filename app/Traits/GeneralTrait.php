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

trait GeneralTrait
{
    public function Enterprises(){
        return Enterprise::all();
    }

    //SON LA UNIDADES QUE SE ASIGNAN EN LA OPERACIÓN, PERO QUE SON CONSIDERADO COMO LOS VEHICULOS QUE TENEMOS
    public function Units(){
        return Vehicle::all();
    }

    public function Drivers(){
        return Driver::orderBy('names','ASC')->get();
    }

    public function Services(){
        return array(
            "0" => "One Way",
            "1" => "Round Trip",
        );
    }

    public function Sites(){
        return DB::select("SELECT id, name as site_name FROM sites ORDER BY site_name ASC");
    }

    public function Vehicles(){
        return DestinationService::all();
    }

    public function Zones(){
        return Zones::all();;
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

    public function CancellationTypes(){
        return CancellationTypes::where('status',1)->get();
    }

    public function Status(){
        return array(
            "CONFIRMED" => "Confirmado",
            "PENDING" => "Pendiente",
            "OPENCREDIT" => "Credito abierto",
            "CANCELLED" => "Cancelado",
            // "DUPLICATED" => "Duplicado",
        );
    }
    
    public function currencies(){
        return array(
            "USD" => "USD",
            "MXN" => "MXN",
        );
    }

    public function Methods(){
        return array(
            "CASH" => "CASH",
            "CARD" => "CARD",
            "PAYPAL" => "PAYPAL",
            "MIFEL" => "MIFEL",
        );
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

    public static function classStatusBooking($status = "CONFIRMED"){
        switch ($status) {
            case 'PENDING':
            case 'NOSHOW':
                return 'warning';
                break;
            case 'CANCELLED':
            case 'DUPLICATED':
                return 'danger';
                break;
            case 'OPENCREDIT':
                return 'info';
                break;
            default:
                return 'success';
                break;
        }
    }

    public static function statusBooking($status = "CONFIRMED"){
        switch ($status) {
            case 'PENDING':
                return 'Pendiente';
                break;
            case 'CANCELLED':
                return 'Cancelado';
                break;
            case 'DUPLICATED':
                return 'Duplicado';
                break;
            case 'NOSHOW':
                return 'No se presentó';
                break;                                
            case 'OPENCREDIT':
                return 'Credito abierto';
                break;
            case 'COMPLETED':
                return 'Completado';
                break;                
            default:
                return 'Confirmado';
                break;
        }
    }

    public static function parseServiceStatus( $data ){
        $span = "";
        $items = explode(',',$data);
        foreach ($items as $key => $item) {
            $span .= '<span class="badge badge-light-'.self::classStatusBooking($item).' mb-2">'.self::statusBooking($item).'</span>';
        }
        return $span;
    }
}