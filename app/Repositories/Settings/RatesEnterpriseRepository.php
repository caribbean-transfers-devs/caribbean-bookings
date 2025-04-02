<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Enterprise;
use App\Models\Zones;
use App\Models\DestinationService;
use App\Models\RatesEnterprise;

class RatesEnterpriseRepository{
    public function index($request){ 
        return view('settings.rates_enterprise.index', [
            'breadcrumbs' => [
                [
                    "route" => route('config.ratesEnterprise'),
                    "name" => "Tarifas de empresas",
                    "active" => true                    
                ]
            ],
            'enterprises' => Enterprise::where('is_external', 1)->whereNull('deleted_at')->get(),
        ]);
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

    public function getRatesEnterprise($request){
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
            "enterprise_id" => $request->enterprise_id,
        ];

        if($request->service_id != 0):
            $params['destination_service_id'] = $request->service_id;
        endif;

        $rates = DB::select("SELECT 
                                    ds.name as service_name, ds.price_type,
                                    rt.*,
                                    zoneOne.name as from_name,
	                                zoneTwo.name as to_name
                                FROM rates_enterprises as rt
                                    LEFT JOIN destination_services as ds ON ds.id = rt.destination_service_id
                                    LEFT JOIN enterprises as e ON e.id = rt.enterprise_id
                                    LEFT JOIN zones as zoneOne ON zoneOne.id = rt.zone_one
                                    LEFT JOIN zones as zoneTwo ON zoneTwo.id = rt.zone_two
                                WHERE rt.destination_id = :destination_id
                                AND ( (rt.zone_one = :zone_one AND rt.zone_two = :zone_two) OR ( rt.zone_one = :zone_three AND rt.zone_two = :zone_four )  ) 
                                AND e.id = :enterprise_id
                                {$query}", $params);

        $data = [
            "destination_data" => $request->destination_id,
            "from_data" => [],
            "to_data" => [],
            "enterprise_data" => [],
        ];
        
        if(sizeof($rates) <= 0 && $request->service_id != 0):                       
            $data['from_data'] = Zones::find($request->from_id)->toArray();
            $data['to_data'] = Zones::find($request->to_id)->toArray();
            $data['enterprise_data'] = Enterprise::find($request->enterprise_id)->toArray();
            $data['service_data'] = DestinationService::find($request->service_id)->toArray();
        endif;

        return view('settings.rates_enterprise.list', compact('rates','data'));
    }

    public function newRates($request)
    {
        try {
            DB::beginTransaction();

            $rate = new RatesEnterprise();
            $rate->enterprise_id = $request->enterprise_id;
            $rate->destination_service_id = $request->destination_service_id;
            $rate->destination_id = $request->destination_id;
            $rate->zone_one = $request->zone_one;
            $rate->zone_two = $request->zone_two;
            $rate->one_way = isset($request->one_way) ? $request->one_way : '0.00';
            // $rate->round_trip = isset($request->round_trip) ? $request->round_trip : '0.00';
            $rate->ow_12 = isset($request->ow_12) ? $request->ow_12 : '0.00';
            // $rate->rt_12 = isset($request->rt_12) ? $request->rt_12 : '0.00';
            $rate->ow_37 = isset($request->ow_37) ? $request->ow_37 : '0.00';
            // $rate->rt_37 = isset($request->rt_37) ? $request->rt_37 : '0.00';
            $rate->up_8_ow = isset($request->up_8_ow) ? $request->up_8_ow : '0.00';
            // $rate->up_8_rt = isset($request->up_8_rt) ? $request->up_8_rt : '0.00';
            $rate->operating_cost = isset($request->operating_cost) ? $request->operating_cost : '0.00';
            $rate->save();

            DB::commit();
            return response()->json([
                'message' => 'Tarifa agregada con éxito',
                'success' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteRates($request){
        try {
            DB::beginTransaction();
             
            $item = RatesEnterprise::find( $request->id );            
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
                'message' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateRates($request){

        try {
            DB::beginTransaction();
            foreach($request->price as $key => $value):
                $item = RatesEnterprise::find( $value['id'] );            
                if ($item) {
                    $item->one_way = (( isset($value['one_way']) && !empty($value['one_way']) )? $value['one_way'] : '0.00' );
                    // $item->round_trip = (( isset($value['round_trip']) && !empty($value['round_trip']) )? $value['round_trip'] : '0.00' );
                    $item->ow_12 = (( isset($value['ow_12']) && !empty($value['ow_12']) )? $value['ow_12'] : '0.00' );
                    // $item->rt_12 = (( isset($value['rt_12']) && !empty($value['rt_12']) )? $value['rt_12'] : '0.00' );
                    $item->ow_37 = (( isset($value['ow_37']) && !empty($value['ow_37']) )? $value['ow_37'] : '0.00' );
                    // $item->rt_37 = (( isset($value['rt_37']) && !empty($value['rt_37']) )? $value['rt_37'] : '0.00' );
                    $item->up_8_ow = (( isset($value['up_8_ow']) && !empty($value['up_8_ow']) )? $value['up_8_ow'] : '0.00' );
                    // $item->up_8_rt = (( isset($value['up_8_rt']) && !empty($value['up_8_rt']) )? $value['up_8_rt'] : '0.00' );
                    $item->operating_cost = (( isset($value['operating_cost']) && !empty($value['operating_cost']) )? $value['operating_cost'] : '0.00' );
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
                'message' => $e->getMessage(),
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }    
}