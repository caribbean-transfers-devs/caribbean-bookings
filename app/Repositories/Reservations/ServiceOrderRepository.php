<?php

namespace App\Repositories\Reservations;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\DB;

class ServiceOrderRepository
{
    /** Traducciones de tipo de servicio */
    private array $serviceTypeLabels = [
        'es' => ['ARRIVAL' => 'Llegada', 'DEPARTURE' => 'Salida', 'TRANSFER' => 'Traslado'],
        'en' => ['ARRIVAL' => 'Arrival', 'DEPARTURE' => 'Departure', 'TRANSFER' => 'Transfer'],
    ];

    public function createPDF($request)
    {
        if (empty($request->id)) {
            return response('ID de reserva requerido.', 400);
        }

        $type = $request->type ?? 'arrival'; // 'arrival' | 'departure'

        $items = $this->queryItems($request->id);

        if (empty($items)) {
            return response('Reserva no encontrada.', 404);
        }

        // Filtrar por tipo — misma lógica que CCForm
        if ($type === 'arrival') {
            $filtered = array_values(array_filter(
                $items,
                fn($i) => $i->final_service_type_one === 'ARRIVAL'
            ));
            $isRoundTripLeg = false;
        } else {
            // Items propios de salida/traslado
            $ownDeparture = array_values(array_filter(
                $items,
                fn($i) => in_array($i->final_service_type_one, ['DEPARTURE', 'TRANSFER'])
            ));
            // Items round-trip de llegada que tienen op_two (la salida)
            $roundTripLeg = array_values(array_filter(
                $items,
                fn($i) => $i->final_service_type_one === 'ARRIVAL'
                       && $i->is_round_trip == 1
                       && !empty($i->op_two_pickup)
            ));

            $filtered      = !empty($ownDeparture) ? $ownDeparture : $roundTripLeg;
            $isRoundTripLeg = empty($ownDeparture) && !empty($roundTripLeg);
        }

        if (empty($filtered)) {
            return response('No hay operaciones para este tipo de servicio.', 404);
        }

        $item = $filtered[0];

        // ── Idioma ──────────────────────────────────────────────────────────
        $lang = in_array($item->language, ['en', 'es']) ? $item->language : 'es';

        // ── Pickup y hotel según tipo ────────────────────────────────────────
        if ($type === 'arrival') {
            $pickup = $item->op_one_pickup;
            $hotel  = $item->to_name;    // destino de llegada = hotel
            $svcKey = 'ARRIVAL';
        } elseif ($isRoundTripLeg) {
            // Salida del round-trip: op_two del item de llegada
            $pickup = $item->op_two_pickup;
            $hotel  = $item->to_name;    // hotel donde estuvo = destino de la llegada
            $svcKey = 'DEPARTURE';
        } else {
            // Item de salida/traslado propio
            $pickup = $item->op_one_pickup;
            $hotel  = $item->from_name;  // origen de salida = hotel
            $svcKey = $item->final_service_type_one; // DEPARTURE o TRANSFER
        }

        // ── Fechas ───────────────────────────────────────────────────────────
        $pickupDate = $pickup ? date('d M Y', strtotime($pickup)) : null;
        $pickupTime = $pickup ? date('H:i', strtotime($pickup)) : null;
        $createdAt  = $item->reservation_created_at
            ? date('d M Y', strtotime($item->reservation_created_at))
            : null;

        // ── Proveedor: agencia con prioridad ─────────────────────────────────
        $provider = !empty($item->origin_sale_code)
            ? $item->origin_sale_code
            : $item->site_name;

        // ── Tipo de servicio traducido ────────────────────────────────────────
        $serviceType = $this->serviceTypeLabels[$lang][$svcKey] ?? $svcKey;

        // ── Parámetros del componente ─────────────────────────────────────────
        $params = [
            'orderNumber'   => $item->code,
            'createdAt'     => $createdAt,
            'serviceDate'   => $pickupDate,
            'serviceType'   => $serviceType,
            'passengerName' => trim($item->client_first_name . ' ' . $item->client_last_name),
            'pickupTime'    => $pickupTime,
            'provider'      => $provider,
            'flight'        => $item->flight_number ?: null,
            'hotel'         => $hotel,
            'room'          => null,
            'adults'        => (int) $item->passengers,
            'minors'        => 0,
            'infants'       => 0,
            'carSeat'       => 0,
            'booster'       => 0,
            'luggage'       => (int) $item->passengers,
            'lang'          => $lang,
        ];

        $html = view('pdf.service-order', $params)->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="orden-de-servicio.pdf"',
        ]);
    }

    private function queryItems(int $id): array
    {
        return DB::select("
            SELECT
                it.*,
                rez.client_first_name,
                rez.client_last_name,
                rez.language,
                rez.created_at  AS reservation_created_at,
                sit.name        AS site_name,
                os.code         AS origin_sale_code,
                CASE
                    WHEN zone_one.is_primary = 1                                             THEN 'ARRIVAL'
                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1                 THEN 'DEPARTURE'
                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0                 THEN 'TRANSFER'
                    ELSE 'ARRIVAL'
                END AS final_service_type_one,
                CASE
                    WHEN zone_two.is_primary = 1                                             THEN 'ARRIVAL'
                    WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1                 THEN 'DEPARTURE'
                    WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 0                 THEN 'TRANSFER'
                    ELSE 'DEPARTURE'
                END AS final_service_type_two
            FROM reservations_items AS it
                INNER JOIN reservations  AS rez      ON rez.id      = it.reservation_id
                INNER JOIN sites         AS sit      ON sit.id      = rez.site_id
                LEFT  JOIN origin_sales  AS os       ON os.id       = rez.origin_sale_id
                INNER JOIN zones         AS zone_one ON zone_one.id = it.from_zone
                INNER JOIN zones         AS zone_two ON zone_two.id = it.to_zone
            WHERE rez.id = :id
                AND rez.is_cancelled = 0
        ", ['id' => $id]);
    }
}
