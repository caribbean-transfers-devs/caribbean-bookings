<?php

namespace App\Repositories\Reservations;

use Dompdf\Dompdf;
use Dompdf\Options;

class ServiceOrderRepository
{
    public function createPDF($request)
    {
        $html = view('pdf.service-order')->render();

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
