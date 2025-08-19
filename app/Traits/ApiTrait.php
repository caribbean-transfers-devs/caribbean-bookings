<?php

namespace App\Traits;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

trait ApiTrait
{

    public static function empty(){
        $tpv = [
            "type" => "one-way",
            "start" => [
                "place" => "",
                "lat" => "",
                "lng" => "",
                "pickup" => date("Y-m-d H:i"),
            ],
            "end" => [
                "place" => "",
                "lat" => "",
                "lng" => "",
                "pickup" => NULL,
            ],
            "language" => "en",
            "passengers" => 1,
            "currency" => "USD",
            "rate_group" => "xLjDl18", //Grupo de tarifa por defecto...
        ];

        return $tpv;
    }

    public static function init(){
        $response = [
            "status" => false
        ];

        $data = self::sendRequest('/api/v1/oauth', 'POST', array('user' => 'api', 'secret' => '1234567890'));

        if(isset( $data['error'] )):
            $response['code'] = $data['error']['code'];
            $response['message'] = $data['error']['message'];
            return $response;
        endif;

        
        $response['status'] = true;
        $response['data'] = $data;
        return $response;
    }

    public static function checkToken($uuid = ''){
        
        if (!Session::has('tpv')):
            $token = self::init();
            // $tpv['token'] = [
            //     "token" => $token['data']['token'],
            //     "expires_in" => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $token['data']['expires_in'] . " seconds"))
            // ];
            $tpv[$uuid] = [
                "token" => [
                    "token" => $token['data']['token'],
                    "expires_in" => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $token['data']['expires_in'] . " seconds"))
                ],
                "data" => self::empty()
            ];

            Session::put('tpv', $tpv);
        else:
            $tpv = Session::get('tpv');
            if(isset( $tpv['token']['expires_in'] ) ):
                $nowDate = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s") . ' - 1440 minutes'));
                if($nowDate <= $tpv['token']['expires_in']):
                    $token = self::init();
                    $tpv[$uuid]['token'] = [
                        "token" => $token['data']['token'],
                        "expires_in" => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $token['data']['expires_in'] . " seconds"))
                    ];
                    $tpv[$uuid]['data'] = $tpv[$uuid]['data'];
                    Session::put('tpv', $tpv);
                endif;
            endif;
        endif;
    }

    public static function sendAutocomplete($keyword = '', $uuid = ''){
        self::checkToken($uuid);
        $tpv = Session::get('tpv')[$uuid];
        
        return self::sendRequest('/api/v1/autocomplete', 'POST', array('keyword' => $keyword), $tpv['token']['token']);
    }

    public static function makeQuote($data = [], $uuid = ''){
        self::checkToken($uuid);
        $tpv = Session::get('tpv')[$uuid];
        // dd($tpv['token']['token'], json_encode($data));
        return self::sendRequest('/api/v1/quote', 'POST', $data, $tpv['token']['token']);
    }

    public static function makeReservation($data = [], $uuid = ''){
        self::checkToken($uuid);
        $tpv = Session::get('tpv')[$uuid];

        return self::sendRequest('/api/v1/create', 'POST', $data, $tpv['token']['token']);
    }

    public static function makeTypesCancellations($data = []){
        return self::sendRequest('/api/v1/types/cancellations/get', 'GET', $data);
    }

    public static function sendRequest($end_point, $method = 'GET', $data = null, $token = null) {
        $url = 'https://api.caribbean-transfers.com'.$end_point;
        // $url = 'http://127.0.0.1:8001'.$end_point;
        $ch = curl_init($url);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        if ($method == 'GET') {
            if ($data) {
                $url .= '?' . http_build_query($data);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $headers = array(
            'Content-Type: application/json',
        );

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }
}