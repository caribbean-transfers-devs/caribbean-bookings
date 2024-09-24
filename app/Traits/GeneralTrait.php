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
use App\Models\CancellationTypes;

trait GeneralTrait
{
    public function enterprises(){
        return Enterprise::all();
    }

    public function vehicles(){
        return Vehicle::all();
    }

    public function drivers(){
        return Driver::all();
    }    

    public function Sites(){
        return DB::select("SELECT 
                                id, 
                                name as site_name
                            FROM sites
                                ORDER BY site_name ASC");
    }

    public function Services(){
        // $services =  [];
        $services = DB::select("SELECT 
                                    ds.id,
                                    dest.name AS destination_name, 
                                    IFNULL(dest_trans.translation, ds.name) AS service_name
                                FROM destination_services AS ds
                                    INNER JOIN destinations AS dest ON dest.id = ds.destination_id
                                    LEFT JOIN destination_services_translate as dest_trans ON dest_trans.destination_services_id = ds.id AND dest_trans.lang = 'es'
                                ORDER BY ds.order ASC");

        // if(sizeof($query) >=1 ):
        //     foreach( $query as $key => $value ):
        //         if( !isset(  $services[ $value->destination_name ] ) ) $services[ $value->destination_name ] = [];
        //         $services[ $value->destination_name ][] = $value;
        //     endforeach;            
        // endif;

        return $services;
    }

    public function Zones(){
        $zones = [];
        $db_zones = DB::select("SELECT 
                                    dest.name AS destination_name, 
                                    z.id, z.name AS zone_name
                                FROM zones as z
                                    INNER JOIN destinations as dest ON dest.id = z.destination_id
                                ORDER BY z.name ASC");
        if(sizeof($db_zones) >=1 ):
            foreach( $db_zones as $key => $value ):
                if( !isset(  $zones[ $value->destination_name ] ) ) $zones[ $value->destination_name ] = [];
                $zones[ $value->destination_name ][] = $value;
            endforeach;            
        endif;
        return $zones;
    }

    public function Origins(){
        return OriginSale::All();
    }

    public function CancellationTypes(){
        return CancellationTypes::all();
    }

    public function Status(){
        return array(
            "CONFIRMED" => "Confirmado",
            "PENDING" => "Pendiente",
            "CANCELLED" => "Cancelado",
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

    public function parseArrayQuery($data){
        if( is_array($data) ){
            $string = implode(',', $data);
            return $string;
        }else{
            return $data;
        }
    }
}