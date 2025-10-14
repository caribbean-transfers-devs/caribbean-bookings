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

    private $months = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];        

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        // Inicializar el resumen
        $resume = [
            'total' => [
                'USD' => ['amount' => 0, 'count' => 0, 'icon' => 'usd', 'color' => 'success'],
                'MXN' => ['amount' => 0, 'count' => 0, 'icon' => 'money-bill', 'color' => 'info'],
            ],
            'status' => [
                'charged' => ['amount' => 0, 'count' => 0, 'label' => 'Cobrado', 'color' => 'primary'],
                'pending_charge' => ['amount' => 0, 'count' => 0, 'label' => 'Pendiente cobro', 'color' => 'warning'],
                'paid' => ['amount' => 0, 'count' => 0, 'label' => 'Pagado', 'color' => 'success'],
                'pending_payment' => ['amount' => 0, 'count' => 0, 'label' => 'Pendiente pago', 'color' => 'danger'],
            ],
            'fees' => [
                'count' => 0,
                'amount' => 0,
            ],
            'refunds' => [
                'count' => 0,
                'amount' => 0,
            ]
        ];

        // Función auxiliar para obtener fechas seguras
        $dates = MethodsTrait::parseDateRange($request->date ?? '');

        $data = [
            "init"              =>  $dates['init'],
            "end"               =>  $dates['end'],
            "filter_text"       =>  $request->filter_text ?? NULL,
            "payment_stripe"    =>  $request->payment_stripe ?? NULL,
            "currency"          =>  $request->currency ?? 0,
        ];

        $queryData = ['method' => 'STRIPE'];
        $queryDate = '';

        if (empty($request->filter_text) && empty($request->payment_stripe)) {
            $queryDate = ' AND rez.created_at BETWEEN :init AND :end ';
            $queryData['init'] = $dates['init'] . " 00:00:00";
            $queryData['end'] = $dates['end'] . " 23:59:59";
        }
        
        $query = $queryDate . ' AND rez.site_id NOT IN(21,11) AND rez.is_duplicated = 0 AND p.payment_method = :method AND p.created_at IS NOT NULL AND p.deleted_at IS NULL AND (p.reference IS NOT NULL AND p.reference != "") ';
        $havingConditions = []; $queryHaving = '';

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $query .= " AND rez.currency IN ($params) ";
        }

        if(isset( $request->filter_text ) && !empty( $request->filter_text )){
            $query .= " AND p.reference LIKE :filter_text ";
            $queryData['filter_text'] = "%" . $request->filter_text . "%";
        }        

        if(isset( $request->payment_stripe ) && !empty( $request->payment_stripe )){
            $query .= " AND p.reference_conciliation LIKE :payment_stripe ";
            $queryData['payment_stripe'] = "%" . $request->payment_stripe . "%";
        }        

        if( !empty($havingConditions) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }        

        // dd($query, $queryHaving, $queryData);
        $conciliations = $this->queryConciliation($query, $queryHaving, $queryData);

        // Procesar los datos
        foreach ($conciliations as $item) {
            // Calcular montos según la operación
            $amount = $this->calculateAmount($item);            
            $item->total_payments = $amount;
            $prefix = substr($item->reference_stripe, 0, 3);

            if( in_array($prefix, ['pi_', 'py_', 'ch_']) ){
                // Totales por moneda
                if (isset($resume['total'][$item->currency])) {
                    $resume['total'][$item->currency]['amount'] += $amount;
                    $resume['total'][$item->currency]['count']++;
                }
                
                // Estado de cobro
                if ($item->date_conciliation) {
                    $resume['status']['charged']['amount'] += $item->amount;
                    $resume['status']['charged']['count']++;
                } else {
                    $resume['status']['pending_charge']['amount'] += $item->total_payments_stripe;
                    $resume['status']['pending_charge']['count']++;
                }
                
                // Estado de pago
                if ($item->deposit_date) {
                    $resume['status']['paid']['amount'] += $item->total_net ?? 0;
                    $resume['status']['paid']['count']++;
                } else {
                    $resume['status']['pending_payment']['amount'] += $item->total_net ?? 0;
                    $resume['status']['pending_payment']['count']++;
                }
                
                // Comisiones 
                if ($item->total_fee) {
                    $resume['fees']['count']++;
                }            
                $resume['fees']['amount'] += $item->total_fee ?? 0;
                
                // Reembolsos
                if ($item->is_refund > 0) {
                    $resume['refunds']['count']++;
                }
            }
        }

        $filteredConciliations = collect($conciliations)->filter(function ($item) {
            if (empty($item->reference_stripe)) return true;
                        
            $prefix = substr($item->reference_stripe, 0, 3);
            return in_array($prefix, ['pi_', 'py_', 'ch_', 're_']);
        });

        $otherReferences = collect($conciliations)->reject(function ($item) {
            if (empty($item->reference_stripe)) return false;

            $prefix = substr($item->reference_stripe, 0, 3);
            return in_array($prefix, ['pi_', 'py_', 'ch_', 're_']);
        });

        $refunds = collect($conciliations)->filter(function ($item) {
            return $item->is_refund;
        });

        return view('finances.stripe.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Conciliación de Stripe del <strong>" . date("d", strtotime($data['init'])) . " al ". date("d", strtotime($data['end'])) .  " de " . $this->months[str_replace("0","",date("m", strtotime($data['init'])))] ."</strong>",
                    // "name" => "Conciliación de Stripe del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'conciliations' => $filteredConciliations,
            'otherReferences' => $otherReferences,
            'refunds' => $refunds,
            'currencies' => $this->Currencies(),
            // 'exchange' => $this->Exchange(( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ), ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") )),
            'exchange' => "19",
            'data' => $data,
            'resume' => $resume,            
        ]);
    }

    private function calculateAmount($item)
    {
        if (!isset($item->total_payments_stripe)) return 0;
        
        switch ($item->operation) {
            case "multiplication":
                return round($item->total_payments_stripe * ($item->exchange_rate ?? 1));
            case "division":
                return round($item->total_payments_stripe / ($item->exchange_rate ?? 1));
            default:
                return round($item->total_payments_stripe);
        }
    }    
}