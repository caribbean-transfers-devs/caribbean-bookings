<?php

namespace App\Repositories\Tpv;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Traits\ApiTrait;
use App\Models\Site;
use App\Models\UserRole;
use App\Models\User;

class TpvRepository
{
    use ApiTrait;

    public function handler($request){        
        Session::forget('tpv');
        $uuid = Str::uuid()->toString();

        if (!Session::has('tpv')):
            //Requerimos el Token y seteamos su tiempo de vida...
            $token = $this->init();
            if($token['status'] == false):
                return response()->json([
                    'success' => false,
                    'message' => '('.$token['code'].') '.$token['message'],
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR
                ]);
            endif;
            
            $tpv = [
                "token" => [
                    "token" => $token['data']['token'],
                    "expires_in" => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $token['data']['expires_in'] . " seconds"))
                ]      
            ];
            $tpv[$uuid] = $this->empty();
            Session::put('tpv', $tpv);
            return redirect('/tpv/edit/'.$uuid);
        endif;
        
        $tpv = Session::get('tpv');
        $tpv[$uuid] = $this->empty();

        Session::put('tpv', $tpv);
        return redirect('/tpv/edit/'.$uuid);
    }

    public function index($request){
        $tpv = Session::get('tpv');
        if(!isset( $tpv[ $request->code ] )):
            return redirect('/');
        endif;

        $config = [
            "code" => $request->code,
            "items" => $tpv[ $request->code ]
        ];

        return view('tpv.index', compact('config'));
    }

    public function quote($request){
        
        $tpv = Session::get('tpv');
        if(!isset( $tpv[ $request->code ] )):
            return response()->json([
                "message" => "Error en la sesión, genere la venta nuevamente",
                'error' => [
                    'session' => [
                        'Session dara is required'
                    ],
                ]
            ], Response::HTTP_BAD_REQUEST);
        endif;
            
        $tpv[ $request->code ]['type'] = ((isset($request->is_round_trip))? 'round-trip' : 'one-way' );
        $tpv[ $request->code ]['start']['place'] = $request->from_name;
        $tpv[ $request->code ]['start']['lat'] = $request->from_lat;
        $tpv[ $request->code ]['start']['lng'] = $request->from_lng;
        $tpv[ $request->code ]['start']['pickup'] = $request->pickup;
        $tpv[ $request->code ]['end']['place'] = $request->to_name;
        $tpv[ $request->code ]['end']['lat'] = $request->to_lat;
        $tpv[ $request->code ]['end']['lng'] = $request->to_lng;
        $tpv[ $request->code ]['end']['pickup'] = $request->pickup_departure;
        $tpv[ $request->code ]['language'] = $request->language;
        $tpv[ $request->code ]['passengers'] = $request->passengers;
        $tpv[ $request->code ]['currency'] = $request->currency;
        $tpv[ $request->code ]['rate_group'] = $request->rate_group;

        Session::put('tpv', $tpv);

        $quotation = $this->makeQuote($tpv[ $request->code ]);

        if(isset($quotation['error'])):
            return response()->json([
                "message" => "(".$quotation['error']['code'].") ".$quotation['error']['message'],
                'error' => [
                    'api' => [
                        $quotation['error']['message']
                    ],
                ]
            ], Response::HTTP_BAD_REQUEST);
        endif;

        $sites = Site::all();

        $users_ids = UserRole::where('role_id', 3)->orWhere('role_id',4)->pluck('user_id');
        $agents = User::whereIn('id', $users_ids)->get();        

        return view('tpv.form', compact('quotation','sites','agents'));
    }

    public function create($request){

        $data = [
            'service_token' => $request->service_token,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email_address' => $request->email_address,
            'phone' => str_replace(' ', '', $request->phone),
            'flight_number' => $request->flight_number,
            'special_request' => $request->special_request,            
            'site_id' => $request->site_id,
            'call_center_agent' => $request->call_center_agent,
        ];
        
        if($request->payment_method == "CASH"):            
            $data['pay_at_arrival'] = 1;
        endif;
        
        $rez = $this->makeReservation($data);

        if(isset($rez['error'])):
            return response()->json([
                "message" => "(".$rez['error']['code'].") ".$rez['error']['message'],
                'error' => [
                    'api' => [
                        $rez['error']['message']
                    ],
                ]
            ], Response::HTTP_BAD_REQUEST);
        endif;

        return response()->json($rez, Response::HTTP_OK);
    }
    
}