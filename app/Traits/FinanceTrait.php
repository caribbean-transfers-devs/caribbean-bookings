<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

//MODELS

trait FinanceTrait
{
    public function classStatusRefund($status = "REFUND_REQUESTED"){
        switch ($status) {
            case 'REFUND_MADE':
                return 'warning';
                break;
            case 'REFUND_COMPLETED':
                return 'success';
                break;        
            default:
                return 'danger';
                break;
        }
    }

    public function statusRefund($status = "REFUND_REQUESTED"){
        switch ($status) {
            case 'REFUND_MADE':
                return 'REEMBOLSO EFECTUADO';
                break;
            case 'REFUND_COMPLETED':
                return 'REEMBOLSO COMPLETO';
                break;
            default:
                return 'REEMBOLSO SOLICITADO';
                break;
        }
    }
}