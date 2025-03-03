<?php

namespace App\Repositories\Tpv;

use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

//TRAITS
use App\Traits\ApiTrait2;

use Exception;

class TpvRepository2
{
    use ApiTrait2;

    public function create($request){
        $validator = Validator::make($request->all(), [
            'service_token' => 'required',
            'first_name' => 'required|max:75',
            'last_name' => 'max:75',
            'email' => 'required|email|max:85',
            'phone' => 'required',        
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'required_params', 
                    'message' =>  implode(', ', $validator->errors()->all())
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        $rez = $this->make($request);
        if(isset($rez['error'])):
            return response()->json($rez, Response::HTTP_BAD_REQUEST);
        endif;

        if(!isset($rez['error'])):
            Session::put( 'reservation', $rez);
            Session::put( 'reservation_time', now()->addMinutes(60));
            $rez['link'] = ( app()->getLocale() == "es" ? route('reservation.detail.es', ['locale' => config('app.locale')]) : route('reservation.detail') );
        endif;

        return response()->json($rez, Response::HTTP_OK);
    }
}