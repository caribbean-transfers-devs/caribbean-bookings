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
            ],
            "end" => [
                "place" => "",
                "lat" => "",
                "lng" => "",
            ],
            "language" => "en",
            "passengers" => 1,
            "currency" => "USD",
            "pickup" => date("Y-m-d H:i"),
            "departure_pickup" => NULL,
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

    public static function checkToken(){

        if (!Session::has('tpv')):
            $token = self::init();
            $tpv['token'] = [
                "token" => $token['data']['token'],
                "expires_in" => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " + " . $token['data']['expires_in'] . " seconds"))
            ];
            Session::put('tpv', $tpv);
        else:
            $tpv = Session::get('tpv');
            Session::put('tpv', $tpv);
        endif;
    }

    public static function sendAutocomplete($keyword = ''){
        
        self::checkToken();
        //$data = self::sendRequest('/api/v1/autocomplete', 'POST', array('keyword' => $keyword));
    }

    public static function sendRequest($end_point, $method = 'GET', $data = null, $token = null) {
        $url = 'https://api.caribbean-transfers.com'.$end_point;
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