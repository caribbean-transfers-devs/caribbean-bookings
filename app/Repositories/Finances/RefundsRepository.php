<?php

namespace App\Repositories\Finances;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS

//TRAITS
use App\Traits\MethodsTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\PayPalTrait;
use App\Traits\StripeTrait;

class RefundsRepository
{
    use MethodsTrait, QueryTrait, FiltersTrait;

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

        $query = ' AND rez.site_id NOT IN(21,11) AND request_date_refund IS NOT NULL AND rez.request_date_refund BETWEEN :init AND :end ';
        $havingConditions = [];
        $queryHaving = '';
        $queryData = [
            'init' => $dates['init'] . " 00:00:00",
            'end' => $dates['end'] . " 23:59:59",
        ];
    
        // TIPO DE SERVICIO
        if (!empty($request->is_round_trip)) {
            $query .= MethodsTrait::buildFindInSetQuery($request->is_round_trip, 'is_round_trip', $queryData);
        }

        // SITIO
        if (!empty($request->site)) {
            $query .= " AND site.id IN (" . MethodsTrait::parseArrayQuery2($request->site) . ") ";
        }

        // ORIGEN DE VENTA
        if (!empty($request->origin)) {
            $query .= " AND (origin.id IN (" . MethodsTrait::parseArrayQuery2($request->origin) . ") OR origin.id IS NULL) ";
        }

        // ESTATUS DE RESERVACIÓN
        if (!empty($request->reservation_status)) {
            $havingConditions[] = " reservation_status IN (" . MethodsTrait::parseArrayQuery2($request->reservation_status, "single") . ") ";
        }

        // TIPO DE VEHÍCULO
        if (!empty($request->product_type)) {
            $query .= MethodsTrait::buildFindInSetQuery($request->product_type, 'service_type_id', $queryData);
        }

        // ZONAS
        foreach (['zone_one_id', 'zone_two_id'] as $zoneField) {
            if (!empty($request->$zoneField)) {
                $query .= MethodsTrait::buildFindInSetQuery($request->$zoneField, $zoneField, $queryData);
            }
        }

        // ESTATUS DE PAGO
        if (!empty($request->payment_status)) {
            $havingConditions[] = " payment_status IN (" . MethodsTrait::parseArrayQuery2($request->payment_status, "single") . ") ";
        }

        // MONEDA DE LA RESERVA
        if (!empty($request->currency)) {
            $query .= " AND rez.currency IN (" . MethodsTrait::parseArrayQuery2($request->currency, "single") . ") ";
        }

        // MÉTODO DE PAGO
        if (!empty($request->payment_method)) {
            $query .= MethodsTrait::buildFindInSetQuery($request->payment_method, 'payment_type_name', $queryData);
        }

        // COMISIONABLES
        if (!is_null($request->is_commissionable)) {
            $query .= " AND rez.is_commissionable = :is_commissionable ";
            $queryData['is_commissionable'] = $request->is_commissionable;
        }

        // MOTIVOS DE CANCELACIÓN
        if (!empty($request->cancellation_status)) {
            $query .= " AND tc.id IN (" . MethodsTrait::parseArrayQuery2($request->cancellation_status) . ") ";
        }

        // RESERVAS CON UN BALANCE
        if (!is_null($request->is_balance)) {
            $havingConditions[] = ($request->is_balance == 1) ? ' total_balance > 0 ' : ' total_balance <= 0 ';
        }

        // RESERVAS OPERADAS EL MISMO DÍA DE SU CREACIÓN
        if (!empty($request->is_today)) {
            $havingConditions[] = ' is_today != 0 ';
        }

        // FILTRO DE TEXTO
        if (!empty($request->filter_text)) {
            $query .= " AND (
                CONCAT(rez.client_first_name,' ',rez.client_last_name) LIKE :filter_text
                OR rez.client_phone LIKE :filter_text
                OR rez.client_email LIKE :filter_text
                OR rez.reference LIKE :filter_text
                OR it.code LIKE :filter_text
            )";
            $queryData['filter_text'] = '%' . $request->filter_text . '%';
        }

        // QUERY HAVING
        if (!empty($havingConditions)) {
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        $bookings = $this->queryBookings($query, $queryHaving, $queryData);

        return view('finances.refunds.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reembolsos del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'bookings' => $bookings,
            'exchange' => $this->Exchange(( date("Y-m-d", strtotime($data['init'])) ), ( date("Y-m-d", strtotime($data['end'])) )),
            'data' => $data,
        ]);
    }    
}