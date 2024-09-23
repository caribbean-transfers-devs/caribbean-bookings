<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\OriginSale;

trait GeneralTrait
{
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

    public function parseArrayQuery($data){
        if( is_array($data) ){
            $string = implode(',', $data);
            return $string;
        }else{
            return $data;
        }
    }

    // public function parseArrayQuery($data){
    //     if( is_array($data) ){
    //         // Filtrar para obtener solo valores numéricos
    //         $numericValues = array_filter($data, function($item) {
    //             return is_numeric($item);
    //         });
    //         // Unir los valores numéricos en una cadena separada por comas
    //         $string = implode(',', $numericValues);
    //         return $string;
    //     } else {
    //         return is_numeric($data) ? $data : null;
    //     }
    // }

    // public function parseArrayQuery($data){
    //     if( is_array($data) ){
    //         // Usamos array_map para agregar comillas dobles a cada elemento
    //         $string = implode(',', array_map(function($item) {
    //             return '"' . $item . '"';
    //         }, $data));
    //         return $string;
    //     } else {
    //         return $data;
    //     }
    // }    

}