<?php

namespace App\Repositories\Finances;

use Exception;
use Illuminate\Http\Response;
use Carbon\Carbon;

//MODELS

//TRAITS
use App\Traits\MethodsTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class ReceivablesRepository
{
    use MethodsTrait, FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite de memoria
        set_time_limit(120); // Aumenta el tiempo de ejecución a 120 segundos
    
        // Función auxiliar para obtener fechas seguras
        $dates = MethodsTrait::parseDateRange($request->date ?? '');
        
        // Parámetros iniciales
        $data = [
            "init" => $dates['init'],
            "end" => $dates['end'],
            "filter_text" => $request->filter_text ?? null,
            "is_round_trip" => $request->is_round_trip ?? null,
            "site" => $request->site ?? 0,
            "origin" => $request->origin ?? null,
            "reservation_status" => $request->reservation_status ?? 0,
            "product_type" => $request->product_type ?? 0,
            "zone_one_id" => $request->zone_one_id ?? 0,
            "zone_two_id" => $request->zone_two_id ?? 0,
            "currency" => $request->currency ?? 0,
            "payment_status" => $request->payment_status ?? 0,
            "payment_method" => $request->payment_method ?? 0,
            "is_commissionable" => $request->is_commissionable ?? null,
            "is_pay_at_arrival" => $request->is_pay_at_arrival ?? null,
            "cancellation_status" => $request->cancellation_status ?? 0,
            "is_balance" => $request->is_balance ?? null,
            "is_today" => $request->is_today ?? 0,
            "is_duplicated" => $request->is_duplicated ?? 0,
            "is_agency" => $request->is_agency ?? 0,
        ];

        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end AND site.is_cxc = 1 ';
        $havingConditions = [];
        $queryHaving = '';
        $queryData = [
            'init' => $dates['init'] . " 00:00:00",
            'end' => $dates['end'] . " 23:59:59",
        ];

        // FILTRO DE TEXTO
        if (!empty($request->filter_text)) {
            $queryData = [];
            $query  = " AND (
                ( CONCAT(rez.client_first_name,' ',rez.client_last_name) like '%".$data['filter_text']."%') OR
                ( rez.client_phone like '%".$data['filter_text']."%') OR
                ( rez.client_email like '%".$data['filter_text']."%') OR
                ( rez.reference like '%".$data['filter_text']."%') OR
                ( it.code like '%".$data['filter_text']."%' )
            )";
        }

        // QUERY HAVING
        if (!empty($havingConditions)) {
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        $bookings = $this->queryBookings($query, $queryHaving, $queryData);

        return view('finances.receivable.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Cuentas por cobrar del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'bookings' => $bookings,
            'services' => $this->Services(),
            'websites' => $this->Sites(),
            'origins' => $this->Origins(),
            'reservation_status' => $this->reservationStatus(),
            'vehicles' => $this->Vehicles(),
            'zones' => $this->Zones(),
            'payment_status' => $this->paymentStatus(),
            'currencies' => $this->Currencies(),
            'methods' => $this->Methods(),
            'cancellations' => $this->CancellationTypes(),
            'exchange' => $this->Exchange(( date("Y-m-d", strtotime($data['init'])) ), ( date("Y-m-d", strtotime($data['end'])) )),
            'data' => $data,
        ]);        
    }    
}