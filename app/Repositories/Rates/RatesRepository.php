<?php

namespace App\Repositories\Rates;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\RatesGroup;
use App\Models\Zones;
use App\Models\DestinationService;
use App\Models\RatesTransfer;

class RatesRepository{
    public function index($request){        
        $rate_groups = RatesGroup::all();
        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => "Tarifas",
                "active" => true
            ),
        );        
        return view('configs.rates', compact('rate_groups','breadcrumbs'));
    }

    public function items($request){   
        $data = [
            "zones" => [],
            "services" => []
        ];
        
        if (!$request->id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }
        
        $data['zones'] = Zones::where('destination_id', $request->id)->get();
        $data['services'] = DestinationService::select('id', 'name')->where('destination_id', $request->id)->get();

        return response()->json($data, Response::HTTP_OK);
        
    }

    public function getRates($request){
        $query = '';
        if($request->service_id != 0):
            $query = 'AND rt.destination_service_id = :destination_service_id';
        endif;

        $params = [
            "destination_id" => $request->destination_id,
            "zone_one" => $request->from_id,
            "zone_two" => $request->to_id,
            "zone_three" => $request->to_id,
            "zone_four" => $request->from_id,
            "rate_group" => $request->rate_group,            
        ];

        if($request->service_id != 0):
            $params['destination_service_id'] = $request->service_id;
        endif;

        $rates = DB::select("SELECT 
                                    ds.name as service_name, ds.price_type,
                                    rt.*,
                                    zoneOne.name as from_name,
	                                zoneTwo.name as to_name
                                FROM rates_transfers as rt
                                    LEFT JOIN destination_services as ds ON ds.id = rt.destination_service_id
                                    LEFT JOIN rates_groups as rg ON rg.id = rt.rate_group_id
                                    LEFT JOIN zones as zoneOne ON zoneOne.id = rt.zone_one
                                    LEFT JOIN zones as zoneTwo ON zoneTwo.id = rt.zone_one
                                WHERE rt.destination_id = :destination_id
                                AND ( (rt.zone_one = :zone_one AND rt.zone_two = :zone_two) OR ( rt.zone_one = :zone_three AND rt.zone_two = :zone_four )  ) 
                                AND rg.id = :rate_group
                                {$query}", $params);

        $data = [
            "destination_data" => $request->destination_id,
            "from_data" => [],
            "to_data" => [],
            "rate_group_data" => [],
        ];
        
        if(sizeof($rates) <= 0 && $request->service_id != 0):                       
            $data['from_data'] = Zones::find($request->from_id)->toArray();
            $data['to_data'] = Zones::find($request->to_id)->toArray();
            $data['rate_group_data'] = RatesGroup::find($request->rate_group)->toArray();
            $data['service_data'] = DestinationService::find($request->service_id)->toArray();
        endif;

        return view('configs.rates_list', compact('rates','data'));
    }

    public function newRates($request){
            
        try {
            DB::beginTransaction();

            $rate = new RatesTransfer();
            $rate->rate_group_id = $request->rate_group_id;
            $rate->destination_service_id = $request->destination_service_id;
            $rate->destination_id = $request->destination_id;
            $rate->zone_one = $request->zone_one;
            $rate->zone_two = $request->zone_two;
            $rate->one_way = isset($request->one_way) ? $request->one_way : '0.00';
            $rate->round_trip = isset($request->round_trip) ? $request->round_trip : '0.00';
            $rate->ow_12 = isset($request->ow_12) ? $request->ow_12 : '0.00';
            $rate->rt_12 = isset($request->rt_12) ? $request->rt_12 : '0.00';
            $rate->ow_37 = isset($request->ow_37) ? $request->ow_37 : '0.00';
            $rate->rt_37 = isset($request->rt_37) ? $request->rt_37 : '0.00';
            $rate->up_8_ow = isset($request->up_8_ow) ? $request->up_8_ow : '0.00';
            $rate->up_8_rt = isset($request->up_8_rt) ? $request->up_8_rt : '0.00';           
            $rate->save();

            DB::commit();

            return response()->json([
                'message' => 'Tarifa agregada con éxito',
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

    public function deleteRates($request){
        try {
            DB::beginTransaction();
             
            $item = RatesTransfer::find( $request->id );            
            if ($item) {                
                $item->delete();                
            }

            DB::commit();
            return response()->json([
                'message' => 'Tarifa eliminada con éxito',
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

    public function updateRates($request){

        try {
            DB::beginTransaction();
            foreach($request->price as $key => $value):
                $item = RatesTransfer::find( $value['id'] );            
                if ($item) {
                    $item->one_way = (( isset($value['one_way']) && !empty($value['one_way']) )? $value['one_way'] : '0.00' );
                    $item->round_trip = (( isset($value['round_trip']) && !empty($value['round_trip']) )? $value['round_trip'] : '0.00' );
                    $item->ow_12 = (( isset($value['ow_12']) && !empty($value['ow_12']) )? $value['ow_12'] : '0.00' );
                    $item->rt_12 = (( isset($value['rt_12']) && !empty($value['rt_12']) )? $value['rt_12'] : '0.00' );
                    $item->ow_37 = (( isset($value['ow_37']) && !empty($value['ow_37']) )? $value['ow_37'] : '0.00' );
                    $item->rt_37 = (( isset($value['rt_37']) && !empty($value['rt_37']) )? $value['rt_37'] : '0.00' );
                    $item->up_8_ow = (( isset($value['up_8_ow']) && !empty($value['up_8_ow']) )? $value['up_8_ow'] : '0.00' );
                    $item->up_8_rt = (( isset($value['up_8_rt']) && !empty($value['up_8_rt']) )? $value['up_8_rt'] : '0.00' );
                    $item->save();
                }
            endforeach;
            DB::commit();

            return response()->json([
                'message' => 'Tarifas actualizadas con éxito',
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