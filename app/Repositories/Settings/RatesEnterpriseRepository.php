<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Enterprise;
use App\Models\Zones;
use App\Models\DestinationService;
use App\Models\RatesTransfer;

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
}