<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Session;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait2;

class ApiChecker
{
    use ApiTrait2;
    public function handle($request, Closure $next)
    {
        $this->handler();
        return $next($request); 
    }

    public function handler(){

        $auth = session()->get('auth');
        if( $auth == NULL ):
            $this->setToken();
        endif;

        if( $auth != NULL ):
            if($auth['expires_in'] < date("Y-m-d H:i:s")):
                $this->setToken();
            endif;
        endif;        

        $clientData = session()->get('client_data');
        if( $clientData == NULL ):
            Session::put( 'client_data', [
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'special_request' => '',
                'arrival_airline' => '',
                'arrival_flight_number' => '',
            ] );
        endif;       
        
        return true;
    }

    function setToken(){
        
        $token = ApiTrait2::init();

        $seconds = ($token['data']['expires_in'] - 86400);
        $new_date = date("Y-m-d H:i:s", strtotime( date("Y-m-d H:i:s")  . " +{$seconds} seconds"));

        Session::put('auth', [
            'token' => $token['data']['token'],
            'expires_in' => $new_date,
        ]);

    }
}