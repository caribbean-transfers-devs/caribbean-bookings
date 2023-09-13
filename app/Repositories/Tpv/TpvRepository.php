<?php

namespace App\Repositories\Tpv;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Traits\ApiTrait;

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
            return redirect('/tpv/new/'.$uuid);
        endif;
        
        $tpv = Session::get('tpv');
        $tpv[$uuid] = $this->empty();

        Session::put('tpv', $tpv);
        return redirect('/tpv/new/'.$uuid);
    }

    public function index($request)
    {
        $tpv = Session::get('tpv');
        if(!isset( $tpv[ $request->code ] )):
            return redirect('/');
        endif;
        $config = $tpv[ $request->code ];

        return view('tpv.index');
    }

    public function quote($request){

        $tpv = Session::get('tpv');
        if(!isset( $tpv[ $request->code ] )):
            return redirect('/');
        endif;
        $config = $tpv[ $request->code ];        
    }

    
}