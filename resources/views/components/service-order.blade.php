@props([
    'orderNumber'   => null,
    'createdAt'     => null,
    'serviceDate'   => null,
    'serviceType'   => null,
    'passengerName' => null,
    'pickupTime'    => null,
    'provider'      => null,
    'flight'        => null,
    'hotel'         => null,
    'room'          => null,
    'adults'        => null,
    'minors'        => null,
    'infants'       => null,
    'carSeat'       => null,
    'booster'       => null,
    'luggage'       => null,
    'lang'          => 'es',
])

@php
$t = $lang === 'en' ? [
    'doc_label'        => 'Service Order',
    'section_booking'  => 'Booking Information',
    'order_number'     => 'Order No.',
    'created_at'       => 'Created Date',
    'service_date'     => 'Service Date',
    'service_type'     => 'Service Type',
    'section_pax'      => 'Main Passenger',
    'passenger_name'   => 'Passenger Name',
    'pickup_time'      => 'Pickup Time',
    'pickup_suffix'    => 'hrs',
    'section_details'  => 'Service Details',
    'provider'         => 'Provider',
    'flight'           => 'Flight',
    'hotel'            => 'Hotel',
    'room'             => 'Room',
    'section_luggage'  => 'Passengers & Luggage',
    'adults'           => 'Adults',
    'minors'           => 'Minors',
    'infants'          => 'Infants',
    'car_seat'         => 'Car Seat',
    'booster'          => 'Booster',
    'luggage'          => 'Luggage',
    'section_notes'    => 'Important Notes',
    'footer_text'      => 'This document is a service order generated electronically by Caribbean Transfers.',
    'signature'        => 'Received By',
] : [
    'doc_label'        => 'Orden de Servicio',
    'section_booking'  => 'Información de la Reserva',
    'order_number'     => 'No. de Orden',
    'created_at'       => 'Fecha de Creación',
    'service_date'     => 'Fecha de Servicio',
    'service_type'     => 'Tipo de Servicio',
    'section_pax'      => 'Pasajero Principal',
    'passenger_name'   => 'Nombre del Pasajero',
    'pickup_time'      => 'Hora de Pickup',
    'pickup_suffix'    => 'hrs',
    'section_details'  => 'Detalles del Servicio',
    'provider'         => 'Proveedor',
    'flight'           => 'Vuelo',
    'hotel'            => 'Hotel',
    'room'             => 'Habitación',
    'section_luggage'  => 'Pasajeros y Equipaje',
    'adults'           => 'Adultos',
    'minors'           => 'Menores',
    'infants'          => 'Infantes',
    'car_seat'         => 'Silla de Auto',
    'booster'          => 'Booster',
    'luggage'          => 'Maletas',
    'section_notes'    => 'Notas Importantes',
    'footer_text'      => 'Este documento es una orden de servicio generada electrónicamente por Caribbean Transfers.',
    'signature'        => 'Firma de Recibido',
];
@endphp

<style>
    .so-wrap {
        width: 100%;
        background: #ffffff;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #2c2c2c;
    }

    .so-wrap table,
    .so-wrap td,
    .so-wrap th,
    .so-wrap div,
    .so-wrap span {
        box-sizing: border-box;
    }

    /* ── TOP ACCENT BAR ── */
    .so-top-bar {
        height: 4px;
        background: #f07c3c;
    }

    /* ── DARK HEADER ── */
    .so-header {
        background: #1e2461;
        padding: 20px 32px;
    }

    .so-header-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-header-logo-cell {
        vertical-align: middle;
        width: 200px;
    }


    .so-header-doc-cell {
        vertical-align: middle;
        text-align: right;
    }

    .so-doc-label {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: #f07c3c;
        display: block;
    }

    .so-doc-title {
        font-size: 18px;
        font-weight: 700;
        color: #ffffff;
        display: block;
        margin-top: 4px;
    }

    /* ── BODY ── */
    .so-body {
        padding: 28px 32px 48px 32px;
    }

    /* ── SECTION ── */
    .so-section {
        margin-bottom: 16px;
    }

    .so-section-title {
        display: block;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #ffffff;
        background: #1e2461;
        padding: 6px 14px;
    }

    .so-section-body {
        border: 1px solid #e8e8e8;
        border-top: none;
    }

    /* ── INFO DATA TABLE ── */
    .so-info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-info-table th {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #888;
        background: #f9f9f9;
        padding: 9px 14px;
        text-align: left;
        border-bottom: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;
        width: 25%;
    }

    .so-info-table th:last-child {
        border-right: none;
    }

    .so-info-table td {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a2e;
        padding: 12px 14px;
        border-right: 1px solid #e8e8e8;
        vertical-align: middle;
    }

    .so-info-table td:last-child {
        border-right: none;
    }

    /* ── SERVICE BADGE ── */
    .so-badge {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        color: #fff;
        background: #f07c3c;
        padding: 3px 10px;
        border-radius: 2px;
        letter-spacing: 0.3px;
    }

    /* ── PASSENGER BLOCK ── */
    .so-passenger-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-passenger-name-cell {
        vertical-align: middle;
        padding: 16px 18px;
    }

    .so-passenger-label {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #888;
        display: block;
        margin-bottom: 5px;
    }

    .so-passenger-name {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a2e;
    }

    .so-pickup-cell {
        vertical-align: middle;
        padding: 16px 18px;
        text-align: right;
        border-left: 1px solid #e8e8e8;
        width: 200px;
    }

    .so-pickup-label {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #888;
        display: block;
        margin-bottom: 5px;
    }

    .so-pickup-time {
        font-size: 26px;
        font-weight: 700;
        color: #f07c3c;
        line-height: 1;
    }

    .so-pickup-suffix {
        font-size: 12px;
        font-weight: 400;
        color: #aaa;
    }

    /* ── SERVICE DETAILS ── */
    .so-details-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-details-table th {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #888;
        background: #f9f9f9;
        padding: 9px 14px;
        text-align: left;
        border-bottom: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;
    }

    .so-details-table th:last-child {
        border-right: none;
    }

    .so-details-table th:nth-child(1) { width: 26%; }
    .so-details-table th:nth-child(2) { width: 20%; }
    .so-details-table th:nth-child(3) { width: 38%; }
    .so-details-table th:nth-child(4) { width: 16%; }

    .so-details-table td {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a2e;
        padding: 12px 14px;
        border-right: 1px solid #e8e8e8;
        vertical-align: middle;
    }

    .so-details-table td:last-child {
        border-right: none;
    }

    .so-detail-sub {
        font-size: 10px;
        font-weight: 400;
        color: #999;
        display: block;
        margin-top: 2px;
    }

    /* ── PAX & LUGGAGE TABLE ── */
    .so-pax-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-pax-table th {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #888;
        background: #f9f9f9;
        padding: 9px 6px;
        text-align: center;
        border-bottom: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;
        width: 16.66%;
    }

    .so-pax-table th:last-child {
        border-right: none;
    }

    .so-pax-table td {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a2e;
        padding: 14px 6px;
        text-align: center;
        border-top: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;
        vertical-align: middle;
    }

    .so-pax-table td:last-child {
        border-right: none;
    }

    .so-pax-zero {
        color: #ccc;
    }

    /* ── CONFIRMATION BLOCK ── */
    .so-confirm-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-confirm-table th {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #888;
        background: #f9f9f9;
        padding: 9px 14px;
        text-align: left;
        border-bottom: 1px solid #e8e8e8;
        border-right: 1px solid #e8e8e8;
    }

    .so-confirm-table th:last-child {
        border-right: none;
    }

    .so-confirm-table td {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a2e;
        padding: 12px 14px;
        border-right: 1px solid #e8e8e8;
        vertical-align: middle;
    }

    .so-confirm-table td:last-child {
        border-right: none;
    }

    .so-status-confirmed {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a2e;
    }

    /* ── NOTES ── */
    .so-notes-body {
        padding: 14px 18px 16px 18px;
        background: #f6f7fc;
        border: 1px solid #d8daf0;
        border-top: none;
    }

    .so-notes-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .so-notes-list li {
        font-size: 11px;
        color: #333;
        padding: 4px 0;
        line-height: 1.4;
    }

    .so-notes-marker {
        color: #f07c3c;
        font-size: 10px;
        margin-right: 8px;
    }

    /* ── FOOTER ── */
    .so-footer-table {
        width: 100%;
        border-collapse: collapse;
    }

    .so-footer-left {
        vertical-align: bottom;
    }

    .so-footer-text {
        font-size: 9px;
        color: #333;
        line-height: 1.6;
    }

    .so-footer-right {
        vertical-align: bottom;
        text-align: right;
        width: 300px;
    }

    .so-signature-line {
        border-top: 1px solid #ccc;
        margin-top: 60px;
        margin-bottom: 5px;
    }

    .so-signature-label {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #777;
    }
</style>

<div class="so-wrap">

    <div class="so-top-bar"></div>

    {{-- ─────────────────────────────────────────── --}}
    {{-- DARK HEADER                                  --}}
    {{-- ─────────────────────────────────────────── --}}
    <div class="so-header">
        <table class="so-header-table">
            <tr>
                <td class="so-header-logo-cell">
                    @php $logoB64 = base64_encode(file_get_contents(public_path('assets/img/logos/logo.png'))); @endphp
                    <img src="data:image/png;base64,{{ $logoB64 }}" style="width:190px; height:auto; display:block;" />
                </td>
                <td class="so-header-doc-cell">
                    <span class="so-doc-label">{{ $t['doc_label'] }}</span>
                    <span class="so-doc-title">{{ $orderNumber ? '# '.$orderNumber : '' }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="so-body">

        {{-- ─────────────────────────────────────────── --}}
        {{-- SECCIÓN 1 — INFORMACIÓN DE RESERVA          --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="so-section">
            <span class="so-section-title">{{ $t['section_booking'] }}</span>
            <div class="so-section-body">
                <table class="so-info-table">
                    <thead>
                        <tr>
                            <th>{{ $t['order_number'] }}</th>
                            <th>{{ $t['created_at'] }}</th>
                            <th>{{ $t['service_date'] }}</th>
                            <th>{{ $t['service_type'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $orderNumber ?: '-' }}</td>
                            <td>{{ $createdAt ?: '-' }}</td>
                            <td>{{ $serviceDate ?: '-' }}</td>
                            <td>
                                @if($serviceType)
                                    <span class="so-badge">{{ $serviceType }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─────────────────────────────────────────── --}}
        {{-- SECCIÓN 2 — PASAJERO                        --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="so-section">
            <span class="so-section-title">{{ $t['section_pax'] }}</span>
            <div class="so-section-body">
                <table class="so-passenger-table">
                    <tr>
                        <td class="so-passenger-name-cell">
                            <span class="so-passenger-label">{{ $t['passenger_name'] }}</span>
                            <span class="so-passenger-name">{{ $passengerName ?: '-' }}</span>
                        </td>
                        <td class="so-pickup-cell">
                            <span class="so-pickup-label">{{ $t['pickup_time'] }}</span>
                            @if($pickupTime)
                                <span class="so-pickup-time">{{ $pickupTime }} <span class="so-pickup-suffix">{{ $t['pickup_suffix'] }}</span></span>
                            @else
                                <span class="so-pickup-time">-</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ─────────────────────────────────────────── --}}
        {{-- SECCIÓN 3 — DETALLES DEL SERVICIO           --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="so-section">
            <span class="so-section-title">{{ $t['section_details'] }}</span>
            <div class="so-section-body">
                <table class="so-details-table">
                    <thead>
                        <tr>
                            <th>{{ $t['provider'] }}</th>
                            <th>{{ $t['flight'] }}</th>
                            <th>{{ $t['hotel'] }}</th>
                            <th>{{ $t['room'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $provider ?: '-' }}</td>
                            <td>{{ $flight ?: '-' }}</td>
                            <td>{{ $hotel ?: '-' }}</td>
                            <td>{{ $room ?: '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─────────────────────────────────────────── --}}
        {{-- SECCIÓN 4 — PASAJEROS Y EQUIPAJE            --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="so-section">
            <span class="so-section-title">{{ $t['section_luggage'] }}</span>
            <div class="so-section-body">
                <table class="so-pax-table">
                    <thead>
                        <tr>
                            <th>{{ $t['adults'] }}</th>
                            <th>{{ $t['minors'] }}</th>
                            <th>{{ $t['infants'] }}</th>
                            <th>{{ $t['car_seat'] }}</th>
                            <th>{{ $t['booster'] }}</th>
                            <th>{{ $t['luggage'] }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $pAdults  = $adults  ?? 0;
                            $pMinors  = $minors  ?? 0;
                            $pInfants = $infants ?? 0;
                            $pCarSeat = $carSeat ?? 0;
                            $pBooster = $booster ?? 0;
                            $pLuggage = $luggage ?? 0;
                        @endphp
                        <tr>
                            <td class="{{ $pAdults  == 0 ? 'so-pax-zero' : '' }}">{{ $pAdults }}</td>
                            <td class="{{ $pMinors  == 0 ? 'so-pax-zero' : '' }}">{{ $pMinors }}</td>
                            <td class="{{ $pInfants == 0 ? 'so-pax-zero' : '' }}">{{ $pInfants }}</td>
                            <td class="{{ $pCarSeat == 0 ? 'so-pax-zero' : '' }}">{{ $pCarSeat }}</td>
                            <td class="{{ $pBooster == 0 ? 'so-pax-zero' : '' }}">{{ $pBooster }}</td>
                            <td class="{{ $pLuggage == 0 ? 'so-pax-zero' : '' }}">{{ $pLuggage }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ─────────────────────────────────────────── --}}
        {{-- SECCIÓN 5 — NOTAS IMPORTANTES               --}}
        {{-- ─────────────────────────────────────────── --}}
        <div class="so-section">
            <span class="so-section-title">{{ $t['section_notes'] }}</span>
            <div class="so-notes-body">
                @php
                $hardcodedNotes = $lang === 'en' ? [
                    'Please arrive 10 minutes early at the designated meeting point in the arrivals area.',
                    'Present this voucher printed or on a mobile device to the assigned driver.',
                    'The service does not include tips for the driver; these are at the passenger\'s discretion.',
                    'If you cannot locate the driver, contact the emergency number immediately.',
                    'Rate applied according to the current agreement with the provider.',
                    'Schedule changes must be reported at least 4 hours in advance.',
                ] : [
                    'Llegar 10 minutos antes al punto de encuentro designado en el área de llegadas.',
                    'Presentar este voucher impreso o en dispositivo móvil al conductor asignado.',
                    'El servicio no incluye propinas al conductor; éstas quedan a criterio del pasajero.',
                    'En caso de no localizar al conductor, comunicarse de inmediato al número de emergencia.',
                    'Tarifa aplicada conforme al convenio vigente con el proveedor.',
                    'Cambios de horario deben notificarse con al menos 4 horas de anticipación.',
                ];
                @endphp
                <ul class="so-notes-list">
                    @foreach($hardcodedNotes as $note)
                        <li><span class="so-notes-marker">&gt;</span>{{ $note }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- ─────────────────────────────────────────── --}}
        {{-- FOOTER                                       --}}
        {{-- ─────────────────────────────────────────── --}}
        <table class="so-footer-table" style="margin-top: 28px;">
            <tr>
                <td class="so-footer-left">
                    <span class="so-footer-text">{{ $t['footer_text'] }}</span>
                </td>
                <td class="so-footer-right">
                    <div class="so-signature-line"></div>
                    <span class="so-signature-label">{{ $t['signature'] }}</span>
                </td>
            </tr>
        </table>

    </div>{{-- /so-body --}}

</div>{{-- /so-wrap --}}
