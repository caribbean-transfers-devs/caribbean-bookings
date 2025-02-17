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
            case 'CANCELLATION':
                return 'danger';
                break;
            case 'DUPLICATED':
            case 'DUPLICATE':
            case 'OPERATION':
                return 'secondary';
                break;
            case 'CREDIT':
            case 'OPENCREDIT':
            case 'REFUND':
            case 'E':
                return 'info';
                break;
            case 'QUOTATION':
                return 'primary';
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
            case 'CANCELLATION':
                return 'CANCELACIÓN';
                break;
            case 'CREDIT':
                return 'CRÉDITO';
                break;
            case 'DUPLICATED':
            case 'DUPLICATE':
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
            case 'E':
                return 'E';
                break;
            case 'C':
                return 'C';
                break;                
            case 'OK':
                return 'OK';
                break;
            case 'GENERAL':
                return 'GENERAL';
                break;
            case 'OPERATION':
                return 'OPERACIÓN';
                break;
            case 'REFUND':
                return 'REEMBOLSO';
                break;
            case 'QUOTATION':
                return 'COTIZACIÓN';
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

    public static function renderCategoryPicture($status){
        return '<button type="button" class="btn btn-'.self::classStatusBooking($status).' btn-sm">'.self::statusBooking($status).'</button>';
    }

    public static function classStatusPayment($service){
        return 'style="'.( $service->payment_status == "PAID" ? 'background-color:#00ab55;color:#fff;' : 'background-color:#e7515a;color:#fff;' ).'"';
    }

    public static function statusPayment($status = "PAID"){
        switch ($status) {
            case 'PENDING':
                return 'PENDIENTE';
                break;
            case 'CREDIT':
                return 'CREDITO';
                break;
            default:            
                return 'PAGADO';
                break;
        }
    }    
}