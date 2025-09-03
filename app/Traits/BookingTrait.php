<?php

namespace App\Traits;
use Illuminate\Http\Response;

trait BookingTrait
{
    public function classStatusBooking($status = "CONFIRMED", $section = "BOOKING")
    {
        switch ($status) {
            case 'PAY_AT_ARRIVAL':
                return 'success-regular';
                break;
            case 'PENDING':
            case 'NOSHOW':
            case 'NOTOPERATED':
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
            case 'REFUND':
            case 'DISPUTE':
            case 'E':
                return 'info';
                break;
            case 'OPENCREDIT':
                return 'dark';
                break;
            case 'QUOTATION':
                return 'primary';
                break;
            case 'EXPIRED_QUOTATION':
                return 'COTIZACIÓN VENCIDA';
                break;
            default:
                return 'success';
                break;
        }
    }

    public function colorStatusBooking($status = "CONFIRMED")
    {
        switch ($status) {
            case 'PAY_AT_ARRIVAL':
                return '#22c7d5';
                break;            
            case 'PENDING':
                return '#e2a03f';
                break;
            case 'CANCELLED':
                return '#e7515a';
                break;
            case 'DUPLICATED':
                return '#805dca';
                break;
            case 'CREDIT':
                return '#2196f3';
                break;
            case 'OPENCREDIT':
                return '#3b3f5c';
                break;                
            case 'QUOTATION':
                return '#4361ee';
                break;                
            default:
                return '#00ab55';
                break;
        }
    }

    public function statusBooking($status = "CONFIRMED")
    {
        switch ($status) {
            case 'PAY_AT_ARRIVAL':
                return 'PAGO A LA LLEGADA';
                break;
            case 'PENDING':
                return 'PENDIENTE';
                break;                
            case 'CANCELLED':
                return 'CANCELADO';
                break;
            case 'CANCELLATION': //ES PARA LA GALERIA
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
                return 'ENVIADO';
                break;
            case 'C':
                return 'CONFIRMADO';
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
            case 'EXPIRED_QUOTATION':
                return 'COTIZACIÓN VENCIDA';
                break;
            case 'NOTOPERATED':
                return 'NO OPERADO';
                break;
            case 'DISPUTE':
                return 'DISPUTA';
                break;
            default:
                return 'CONFIRMADO';
                break;
        }
    }

    // RENDERIZA BOTOSNES DE LOS ESTATUS DE SERVICIOS
    public function renderServiceStatus( $data )
    {
        $span = "";
        $items = explode(',',$data);
        foreach ($items as $key => $item) {
            $span .= '<button type="button" class="btn btn-'.self::classStatusBooking($item).' mb-2">'.self::statusBooking($item).'</button>';
        }
        return $span;
    }

    public function renderCategoryPicture($status)
    {
        return '<button type="button" class="btn btn-'.self::classStatusBooking($status).' btn-sm">'.self::statusBooking($status).'</button>';
    }

    public function classStatusPayment($service)
    {
        return 'style="'.( $service->payment_status == "PAID" ? 'background-color:#00ab55;color:#fff;' : ( $service->payment_status == "PENDING" ? 'background-color:#e7515a;color:#fff;' : 'background-color:#2196f3;color:#fff;' ) ).'"';
    }

    public function statusPayment($status = "PAID")
    {
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

    public function colorPaymentMethods($method = "")
    {
        switch ($method) {
            case 'CREDIT':
                return '#3498db';
                break;
            case 'CASH':
                return '#2ecc71';
                break;
            case 'SANTANDER':
                return '#ec1c24';
                break;
            case 'STRIPE':
                return '#6772e5';
                break;
            case 'PAYPAL':
                return '#003087';
                break;
            default:
                return '#ff6b00';
                break;
        }        
    }
}