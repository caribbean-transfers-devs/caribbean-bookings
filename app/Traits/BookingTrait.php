<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait BookingTrait
{
    public static function classStatusBooking($status = "CONFIRMED", $section = "BOOKING"){
        switch ($status) {
            case 'PENDING':
            case 'NOSHOW':
            case 'C':
                return ( $status == "PENDING" && $section == "OPERATION" ? 'secondary' : 'warning' );
                break;
            case 'CANCELLED':
            case 'DUPLICATED':
                return 'danger';
                break;
            case 'OPENCREDIT':
            case 'E':
                return 'info';
                break;
            default:
                return 'success';
                break;
        }
    }

    public static function statusBooking($status = "CONFIRMED"){
        switch ($status) {
            case 'PENDING':
                return 'PENDIENTE';
                break;
            case 'CANCELLED':
                return 'CANCELADO';
                break;
            case 'DUPLICATED':
                return 'DUPLICADO';
                break;
            case 'NOSHOW':
                return 'NO SE PRESENTÓ';
                break;                                
            case 'OPENCREDIT':
                return 'CREDITO ABIERTO';
                break;
            case 'COMPLETED':
                return 'COMPLETADO';
                break;                
            default:
                return 'CONFIRMADO';
                break;
        }
    }

    public static function renderServiceStatus( $data ){
        $span = "";
        $items = explode(',',$data);
        foreach ($items as $key => $item) {
            $span .= '<button type="button" class="btn btn-'.self::classStatusBooking($item).' mb-2">'.self::statusBooking($item).'</button>';
        }
        return $span;
    }

    public static function classStatusPayment($service){
        return 'style="'.( $service->payment_status == "PAID" ? 'background-color:#00ab55;color:#fff;' : 'background-color:#e7515a;color:#fff;' ).'"';
    }

    public static function statusPayment($status = "PAID"){
        switch ($status) {
            case 'PENDING':
                return 'PENDIENTE';
                break;            
            default:
                return 'PAGADO';
                break;
        }
    }    
}