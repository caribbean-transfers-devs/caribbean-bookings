<?php
namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Settings\ZonesRepository;
use App\Http\Controllers\Controller;

class ZonesController extends Controller
{   
    public function index(Request $request, ZonesRepository $zone){        
        //dd($request);
        return $zone->index($request);        
    }

    public function getZones(Request $request, ZonesRepository $zone, $id){
        if (!$request->id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        return $zone->zones($id);       
    }

    public function getPoints(Request $request, ZonesRepository $zone){
        if (!$request->id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'destination_id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        if (!$request->zone_id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'zone_id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        return $zone->points($request);
    }

    public function setPoints(Request $request, ZonesRepository $zone){
        if (!$request->id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }
        return $zone->setpoints($request);
    }
}