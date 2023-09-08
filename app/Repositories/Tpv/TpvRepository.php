<?php

namespace App\Repositories\Tpv;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class TpvRepository
{
    public function handler($request){  
        $uuid = Str::uuid()->toString();
        if (!Session::has('tpv')):          
            Session::put('tpv', []);
        endif;

        $tpv = Session::get('tpv');
        $tpv[$uuid] =  [
            "type" => "one-way",
            "start" => [
                "place" => "",
                "lat" => "",
                "lng" => "",
            ],
            "end" => [
                "place" => "",
                "lat" => "",
                "lng" => "",
            ],
            "language" => "en",
            "passengers" => 1,
            "currency" => "USD",
            "rate_group" => "xLjDl18", //Grupo de tarifa por defecto...
        ];

        Session::put('tpv', $tpv);
        return redirect('/tpv/new/'.$uuid);
    }

    public function setQuote($request){

    }

    public function index($request)
    {
        $tpv = Session::get('tpv');
        if(!isset( $tpv[ $request->code ] )):
            return redirect('/');
        endif;

        // echo "<pre>";
        // print_r($tpv[ $request->code ]);
        // die();        
        return view('tpv.index');
    }
}