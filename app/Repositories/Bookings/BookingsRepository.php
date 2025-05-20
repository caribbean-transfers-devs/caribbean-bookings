<?php

namespace App\Repositories\Bookings;

//TRAITS
use App\Traits\ApiTrait2;

class BookingsRepository
{
    use ApiTrait2;

    public function ReservationDetail($request){
        if(!session()->has('reservation')):
            return redirect()->route('dashboard');
        endif;

        if(session()->has('reservation') && session()->get('reservation_time') < now()):
            session()->forget('reservation');
            session()->forget('reservation_time');
            return redirect()->route('dashboard');
        endif;

        $rez = session()->get('reservation');

        $payment_links = [
            "PAYPAL" => NULL,
            "STRIPE" => NULL,
        ];

        if($rez['payments']['total'] <= 0):
            $payment_data = [
                "type" => 'PAYPAL',
                "id" => $rez['config']['id'],
                "language" => app()->getLocale(),
                "success_url" => ( config('app.locale') == 'es' ? route('process.success.es',['locale' => config('app.locale')]) : route('process.success') ),
                "cancel_url" => ( config('app.locale') == 'es' ? route('process.cancel.es',['locale' => config('app.locale')]) : route('process.cancel') )   
            ];
            
            $paypal = ApiTrait2::paymentLink($payment_data);
            if(!isset( $paypal['error'] )):
                $payment_links['PAYPAL'] = $paypal['url'];
            endif;
            
            $payment_data = [
                "type" => 'STRIPE',
                "id" => $rez['config']['id'],
                "language" => app()->getLocale(),
                "success_url" => ( config('app.locale') == 'es' ? route('process.success.es',['locale' => config('app.locale')]) : route('process.success') ),
                "cancel_url" => ( config('app.locale') == 'es' ? route('process.cancel.es',['locale' => config('app.locale')]) : route('process.cancel') )   
            ];            
            $stripe = ApiTrait2::paymentLink($payment_data);
            if(!isset( $stripe['error'] )):
                $payment_links['STRIPE'] = $stripe['url'];
            endif;
        endif;          
        
        
        return view('process.my-reservation', ['rez' => $rez, 'payment_link' => $payment_links]);
    }
}