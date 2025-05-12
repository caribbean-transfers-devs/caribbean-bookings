<?php

namespace App\Repositories\Reservations;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\Reservation;
use App\Models\ReservationsMedia;

//TRAITS
use App\Traits\ApiTrait;
use App\Traits\FiltersTrait;

class DetailRepository
{
    use ApiTrait, FiltersTrait;
    
    // Constantes para estados de reservación
    private const STATUS_PENDING = 'PENDING';
    private const STATUS_CANCELLED = 'CANCELLED';
    private const STATUS_DUPLICATED = 'DUPLICATED';
    private const STATUS_OPEN_CREDIT = 'OPENCREDIT';
    private const STATUS_QUOTATION = 'QUOTATION';
    private const STATUS_PAY_AT_ARRIVAL = 'PAY_AT_ARRIVAL';
    private const STATUS_CREDIT = 'CREDIT';
    private const STATUS_CONFIRMED = 'CONFIRMED';

    //Estructura modular: Dividí el código en funciones más pequeñas y especializadas, 
    // cada una con una responsabilidad clara.    
    
    /**
     * Obtiene el detalle de una reservación
     *
     * @param mixed $request
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */    
    public function detail($request,$id)
    {
        // Configuración inicial del estado de la reserva
        $reservationData = [
            "status" => "PENDING", // Estado por defecto
            "total_sales" => 0,
            "total_payments" => 0,
        ];

        try {
            // Obtener la reserva con todas las relaciones necesarias
            $reservation = $this->getReservationWithRelations($id);
                
            // Calcular totales de ventas y pagos
            $this->calculateTotals($reservation, $reservationData);

            // Verificar si hay llegada o salida
            $reservationData['transfer_types'] = $this->detectArrivalDeparture($reservation);
            
            // Determinar el estado final de la reserva
            $reservationData['status'] = $this->determineReservationStatus($reservation, $reservationData);                        
            
            return $this->buildReservationDetailView($request, $id, $reservation, $reservationData);
        } catch (Exception $e) {
            return $this->buildErrorView($request, $id, $reservationData);
        }
    }

    // --------------------------
    // Funciones auxiliares
    // --------------------------

    /**
     * Obtiene la reservación con todas sus relaciones
     *
     * @param int $id
     * @return Reservation
     */    
    protected function getReservationWithRelations(int $id): Reservation
    {
        return Reservation::with([
            'destination.destination_services',
            // 'items' => function ($query) {
            //     $this->addItemsWithZoneInfo($query);
            // },
            'items' => $this->getItemsQuery(),            
            'sales',
            'callCenterAgent',
            'payments',
            'refunds.user',
            'followUps',
            'site',
            'cancellationType',
            'originSale'
        ])->find($id);
    }

    /**
     * Añade información de zonas a los items de reserva
     * Construye la consulta para los items de reservación
     *
     * @return \Closure
     */
    //addItemsWithZoneInfo($query)
    protected function getItemsQuery(): \Closure
    {
        return function ($query) {
            $query->join('zones as zone_one', 'zone_one.id', '=', 'reservations_items.from_zone')
                  ->join('zones as zone_two', 'zone_two.id', '=', 'reservations_items.to_zone')
                  ->select(
                        'reservations_items.*', 
                        'reservations_items.id as reservations_item_id', 
                        'zone_one.name as from_zone_name',
                        'zone_one.is_primary as is_primary_from',
                        'zone_two.name as to_zone_name',
                        'zone_two.is_primary as is_primary_to',
                        // Final Service Type para zone_one
                        DB::raw($this->getServiceTypeCase('zone_one', 'zone_two', 'final_service_type_one')),
                        // Final Service Type para zone_two
                        DB::raw($this->getServiceTypeCase('zone_two', 'zone_one', 'final_service_type_two'))
                  );
        };
    }

    /**
     * Genera la expresión CASE para determinar el tipo de servicio
     *
     * @param string $mainZone
     * @param string $otherZone
     * @param string $alias
     * @return string
     */    
    protected function getServiceTypeCase(string $mainZone, string $otherZone, string $alias): string
    {
        return "
            CASE 
                WHEN $mainZone.is_primary = 1 THEN 'ARRIVAL'
                WHEN $mainZone.is_primary = 0 AND $otherZone.is_primary = 1 THEN 'DEPARTURE'
                WHEN $mainZone.is_primary = 0 AND $otherZone.is_primary = 0 THEN 'TRANSFER'
                ELSE 'ARRIVAL'
            END AS $alias
        ";
    }

    /**
     * Calcula los totales de ventas y pagos
     *
     * @param Reservation $reservation
     * @param array &$data
     */    
    protected function calculateTotals(Reservation $reservation, array &$data): void
    {
        // Sumar ventas
        // foreach ($reservation->sales as $sale) {
        //     $data['total_sales'] += $sale->total;
        // }        
        $data['total_sales'] = $reservation->sales->sum('total');
        
        // Sumar pagos con conversión de moneda
        $data['total_payments'] = $reservation->payments->reduce(function ($carry, $payment) {
            if ($payment->operation == "multiplication") {
                return $carry + ($payment->total * $payment->exchange_rate);
            } elseif ($payment->operation == "division") {
                return $carry + ($payment->total / $payment->exchange_rate);
            }
            return $carry;
        }, 0);
    }

    /**
     * Detecta si hay ARRIVAL o DEPARTURE en los items
     *
     * @param Reservation $reservation
     * @return array
     */
    protected function detectArrivalDeparture(Reservation $reservation): array
    {
        $hasArrival = false;
        $hasDeparture = false;

        foreach ($reservation->items as $item) {
            if (
                isset($item->final_service_type_one) && $item->final_service_type_one === 'ARRIVAL' ||
                isset($item->final_service_type_two) && $item->final_service_type_two === 'ARRIVAL'
            ) {
                $hasArrival = true;
            }

            if (
                isset($item->final_service_type_one) && $item->final_service_type_one === 'DEPARTURE' ||
                isset($item->final_service_type_two) && $item->final_service_type_two === 'DEPARTURE'
            ) {
                $hasDeparture = true;
            }

            // Si ya tenemos ambos, salimos temprano
            if ($hasArrival && $hasDeparture) break;
        }

        return [
            'has_arrival' => $hasArrival,
            'has_departure' => $hasDeparture
        ];
    }

    /**
     * Determina el estado de la reserva basado en condiciones específicas
     *
     * @param Reservation $reservation
     * @param array $data
     * @return string
     */    
    protected function determineReservationStatus(Reservation $reservation, array $data): string
    {
        // Condiciones de crédito        
        $totalSales = round($data['total_sales'], 2);
        $totalPayments = round($data['total_payments'], 2);

        // Condiciones especiales que tienen prioridad
        if ($reservation->is_cancelled) return "CANCELLED";
        if ($reservation->is_duplicated) return "DUPLICATED";
        if ($reservation->open_credit) return "OPENCREDIT";
        if ($reservation->is_quotation) return "QUOTATION";
        if ($reservation->pay_at_arrival && round($totalPayments, 2) == 0) return "PAY_AT_ARRIVAL";
        if ($reservation->site->is_cxc && ( round($totalPayments, 2) == 0 || ( round($totalPayments, 2) <  round($totalSales,2) ) )) return "CREDIT";
        
        // Condición de balance
        $balance = round($data['total_sales']) - round($data['total_payments']);
        
        return $balance > 0 ? "PENDING" : "CONFIRMED";
    }

    /**
     * Construye la vista de detalle de reserva
     *
     * @param mixed $request
     * @param int $id
     * @param Reservation $reservation
     * @param array $data
     * @return \Illuminate\Contracts\View\View
     */    
    protected function buildReservationDetailView($request, int $id, Reservation $reservation, array $data)
    {
        return view('reservations.detail', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Detalle de reservación: ".$id,
                    "active" => true
                ]
            ],
            'reservation' => $reservation,
            'data' => $data,
            'types_cancellations' => ApiTrait::makeTypesCancellations(),
            'request' => $request->input(),
        ]);
    }

    /**
     * Construye la vista de error
     *
     * @param mixed $request
     * @param int $id
     * @param array $data
     * @return \Illuminate\Contracts\View\View
     */    
    protected function buildErrorView($request, int $id, array $data)
    {
        return view('reservations.detail', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Detalle de reservación: ".$id,
                    "active" => true
                ]
            ],
            'reservation' => [],
            'data' => $data,
            'types_cancellations' => [],
            'request' => $request->input(),
        ]);
    }    

    /**
     * Obtiene los medios asociados a una reservación
     *
     * @param mixed $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getMedia($request)
    {
        $query = ReservationsMedia::where('reservation_id', $request->id)
            ->orderBy('id', 'desc');

        if (isset($request->type)) {
            $query->where('type_media', 'OPERATION');
        }

        $media = $query->get();

        return view('reservations.media', compact('media'));
    }
}