<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Zones;
use App\Models\ZonesPoints;

class ZonesRepository{

    public function index($request){
        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Zonas de agencias",
                "active" => true
            ),
        );
        return view('settings.zones.index',compact('breadcrumbs'));
    }
    
    public function zones($id){        
        $zones = Zones::where('destination_id',$id)->get();
        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Zonas",
                "active" => true
            ),
        );        
        return view('settings.zones.index', compact('zones','breadcrumbs'));
    }

    public function points($request){

        $data = DB::select("SELECT zon.id, zon.name, zp.latitude, zp.longitude
                                    FROM zones as zon
                                INNER JOIN zones_points as zp ON zp.zone_id = zon.id
                                WHERE zon.destination_id = :destination_id",
                                    [
                                        'destination_id' => $request->id,
                                ]);
        if(sizeof($data) <= 0):
            return response()->json([], 200);
        endif;

        $items = [];
        foreach($data as $key => $value):
            if( !isset($items[ $value->id ]) ):
                $items[ $value->id ] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "points" => []
                ];
            endif;

            $items[ $value->id ]['points'][] = [
                "lat" => $value->latitude,
                "lng" => $value->longitude,
            ];
        endforeach;

        return response()->json($items, 200);
    }

    public function setpoints($request){

        try {
            DB::beginTransaction();
            
            $delete = ZonesPoints::where('zone_id', $request->id)->delete();
            if(sizeof($request->coordinates) >= 1):
                foreach($request->coordinates as $key => $value):
                    $point = new ZonesPoints;
                    $point->zone_id = $request->id;
                    $point->latitude = $value['lat'];
                    $point->longitude = $value['lng'];
                    $point->save();                        
                endforeach;
            endif;

            DB::commit();

            return response()->json([
                'message' => 'Geocerca actualizada con éxito',
                'success' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error, contacte a soporte',
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }
}