<?php

namespace App\Repositories\Reservations;

use Dompdf\Dompdf;
use Dompdf\Options;

class ServiceOrderRepository
{
    public function createPDF($request)
    {
        $params = [
            'orderNumber'   => $request->orderNumber,
            'createdAt'     => $request->createdAt,
            'serviceDate'   => $request->serviceDate,
            'serviceType'   => $request->serviceType,
            'passengerName' => $request->passengerName,
            'pickupTime'    => $request->pickupTime,
            'provider'      => $request->provider,
            'flight'        => $request->flight ?: null,
            'hotel'         => $request->hotel,
            'room'          => null,
            'adults'        => (int) ($request->adults ?? 0),
            'minors'        => 0,
            'infants'       => 0,
            'carSeat'       => 0,
            'booster'       => 0,
            'luggage'       => (int) ($request->luggage ?? 0),
            'lang'          => in_array($request->lang, ['en', 'es']) ? $request->lang : 'es',
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
}
