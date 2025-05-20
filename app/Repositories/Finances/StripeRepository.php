<?php

namespace App\Repositories\Finances;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS
use App\Models\Payment;

//TRAITS
use App\Traits\MethodsTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\PayPalTrait;
use App\Traits\StripeTrait;

class StripeRepository
{
    use MethodsTrait, FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        // Función auxiliar para obtener fechas seguras
        $dates = MethodsTrait::parseDateRange($request->date ?? '');

        $data = [
            "init" => $dates['init'],
            "end" => $dates['end'],
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
        ];

        $query = ' AND rez.site_id NOT IN(21,11) AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled = 0 AND rez.is_duplicated = 0';
        $havingConditions = []; $queryHaving = '';
        $queryData = [
            'init' => $dates['init'] . " 00:00:00",
            'end' => $dates['end'] . " 23:59:59",
        ];

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $query .= " AND rez.currency IN ($params) ";
        }

        //METODO DE PAGO
        $paramsPayment = "FIND_IN_SET('STRIPE', payment_type_name) > 0 OR ";
        $paramsPayment = rtrim($paramsPayment, ' OR ');
        $havingConditions[] = " (".$paramsPayment.") ";

        if( !empty($havingConditions) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        // dd($query, $queryHaving, $queryData);
        $conciliations = $this->queryConciliationStripe($query, $queryHaving, $queryData);

        return view('finances.stripe.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Conciliación de Stripe del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'conciliations' => $conciliations,
            'currencies' => $this->Currencies(),
            // 'exchange' => $this->Exchange(( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ), ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") )),
            'exchange' => "19",
            'data' => $data,
        ]);
    }    

    public function index2($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        // Función auxiliar para obtener fechas seguras
        $dates = MethodsTrait::parseDateRange($request->date ?? '');

        $data = [
            "init" => $dates['init'],
            "end" => $dates['end'],
            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "payment_status" => ( isset( $request->payment_status ) && !empty( $request->payment_status ) ? $request->payment_status : 0 ),
            "payment_method" => ( isset( $request->payment_method ) && !empty( $request->payment_method ) ? $request->payment_method : 0 ),
        ];

        $query = ' AND p.created_at IS NOT NULL AND p.deleted_at IS NULL AND p.created_at BETWEEN :init AND :end AND rez.site_id NOT IN(21,11) AND rez.is_cancelled = 0 AND rez.is_duplicated = 0';
        $havingConditions = []; $queryHaving = '';
        $queryData = [
            'init' => $dates['init'] . " 00:00:00",
            'end' => $dates['end'] . " 23:59:59",
        ];

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $query .= " AND rez.currency IN ($params) ";
        }

        //METODO DE PAGO
        if(isset( $request->payment_method ) && !empty( $request->payment_method )){
            $params = $this->parseArrayQuery($request->payment_method,"single");
            $query .= " AND p.payment_method IN ($params) ";
        }

        if( (isset( $request->payment_method ) && !empty( $request->payment_method )) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        // dd($query, $queryHaving, $queryData);
        $conciliations = $this->queryConciliation($query, $queryHaving, $queryData);

        return view('finances.stripe.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Conciliación de Stripe del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'conciliations' => $conciliations,
            'payment_status' => $this->paymentStatus(),
            'currencies' => $this->Currencies(),
            'exchange' => $this->Exchange(( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ), ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") )),
            'data' => $data,
            'request' => $request->input(),            
        ]);
    }        
}