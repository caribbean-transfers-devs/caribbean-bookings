<?php

namespace App\Http\Controllers\Tpv\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

//TRAITS
use App\Traits\ApiTrait2;

class QuoteController extends Controller
{
    use ApiTrait2;

    public function index(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:OW,RT',
            'currency' => 'required|in:USD,MXN',
            'language' => 'required|in:en,es',
            'from.name' => 'required|max:150',
            'from.lat' => 'required',
            'from.lng' => 'required',
            'from.pickupDate' => 'required|date_format:Y-m-d H:i',
            'to.name' => 'required|max:150',
            'to.lat' => 'required',
            'to.lng' => 'required',
            'to.pickupDate' => [
                'required_if:type,RT',
                'date_format:Y-m-d H:i',
            ],            
            'passengers' => 'required|integer|min:1|max:35',
            'service' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'required_params',
                    'message' =>  implode(', ', $validator->errors()->all())
                ]
            ], 500);
        }

        App::setLocale($request->language);
        $data = ApiTrait2::quote( $request->all() );
        return view('tpv._vehicles', compact('data'));
    }

    public function checkout(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:OW,RT',
            'currency' => 'required|in:USD,MXN',
            'language' => 'required|in:en,es',
            'from.name' => 'required|max:150',
            'from.lat' => 'required',
            'from.lng' => 'required',
            'from.pickupDate' => 'required|date_format:Y-m-d H:i',
            
            'to.name' => 'required|max:150',
            'to.lat' => 'required',
            'to.lng' => 'required',
            'to.pickupDate' => [
                'required_if:type,RT',
                'date_format:Y-m-d H:i',
            ],
            
            'passengers' => 'required|integer|min:1|max:35',
            'service' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'required_params',
                    'message' =>  implode(', ', $validator->errors()->all())
                ]
            ], 500);
        }

        $data = ApiTrait2::quote( $request->all() );
        
        if(isset( $data['error'] )):
            return response()->json([
                'error' => [
                    'code' => 'required_params',
                    'message' =>  implode(', ', $validator->errors()->all())
                ]
            ], 500);
        endif;

        $id = $request->id;        
        $item = array_filter($data['items'], function ($item) use ( $id ) {
            return $item['id'] == $id;
        });
        
        $item = array_values($item);

        $data['items'] = $item[0];

        return response()->json($data, 200);
    }
}