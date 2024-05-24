<?php

namespace App\Traits\Reports;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait PaymentsTrait
{
    public static function getPayments($id){
        return DB::select("SELECT total, exchange_rate, payment_method, currency, operation, reference FROM payments WHERE reservation_id = :id ", [ "id" => $id ]);        
    }
}