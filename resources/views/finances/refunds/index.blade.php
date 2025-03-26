@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $services = auth()->user()->Services();
    $websites = auth()->user()->Sites();
    $origins = auth()->user()->Origins();
    $reservation_status = auth()->user()->reservationStatus();
    $vehicles = auth()->user()->Vehicles();
    $zones = auth()->user()->Zones();
    $payment_status = auth()->user()->paymentStatus();
    $currencies = auth()->user()->Currencies();
    $methods = auth()->user()->Methods();
    $cancellations = auth()->user()->CancellationTypes();
@endphp
@extends('layout.app')
@section('title') Reembolsos @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/finances/refunds.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/finances/refunds.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{ mix('assets/js/sections/finances/refunds.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de ventas",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal',
                )
            ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="layout-columns" class=""><path fill="" fill-rule="evenodd" d="M7 5a2 2 0 00-2 2v10a2 2 0 002 2h1V5H7zm3 0v14h4V5h-4zm6 0v14h1a2 2 0 002-2V7a2 2 0 00-2-2h-1zM3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" clip-rule="evenodd"></path></svg> Administrar columnas',
                'titleAttr' => 'Administrar columnas',
                'className' => 'btn btn-primary __btn_columns',
                'attr' => array(
                    'data-title' =>  "Administrar columnas",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#columnsModal',
                    'data-table' => 'bookings',// EL ID DE LA TABLA QUE VAMOS A OBTENER SUS HEADERS
                    'data-container' => 'columns', //EL ID DEL DIV DONDE IMPRIMIREMOS LOS CHECKBOX DE LOS HEADERS                    
                )                
            ),        
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar Excel',
                'extend' => 'excelHtml5',
                'titleAttr' => 'Exportar Excel',
                'className' => 'btn btn-primary',
                'exportOptions' => [
                    'columns' => ':visible'  // Solo exporta las columnas visibles   
                ]
            ),
            array(
                'text' => 'Tipo de cambio: '.$exchange,
                'titleAttr' => 'Tipo de cambio',
                'className' => 'btn btn-warning',
            ),            
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
                @if ($errors->any())
                    <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <table id="dataRefunds" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ESTATUS DE REEMBOLSO</th>
                            <th class="text-center">ACCIONES</th>
                            <th class="text-center">MENSAJE DE REEMBOLSO</th>
                            <th class="text-center">MENSAJE DE RESPUESTA</th>
                            <th class="text-center">TIPO DE SERVICIO</th>                            
                            <th class="text-center">FECHA DE RESERVACIÓN</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                            <th class="text-center">FECHA DE SERVICIO</th>
                            <th class="text-center">ESTATUS DE SERVICIO(S)</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">MÉTODO DE PAGO</th>
                            <th class="text-center">INFORMACIÓN DE MÉTODO DE PAGO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($bookings) >= 1)
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td class="text-center">
                                        <button type="button" class="btn w-100 mb-2 btn-{{ auth()->user()->classStatusRefund($booking->status) }} {{ $booking->status == "REFUND_REQUESTED" ? 'danger' : 'success' }}">{{ auth()->user()->statusRefund($booking->status) }}</button>                                        
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $codes_string = "";
                                            $codes = explode(",",$booking->reservation_codes);
                                            foreach ($codes as $key => $code) {
                                                $codes_string .= '<p class="mb-1 text-white">'.$code.'</p>';
                                            }
                                        @endphp
                                        @if (auth()->user()->hasPermission(38))
                                            <a class="btn btn-dark w-100 mb-2" href="/reservations/detail/{{ $booking->reservation_id }}" target="_black"><?=$codes_string?></a>
                                        @else
                                            <button type="button" class="btn btn-dark w-100 mb-2"><?=$codes_string?></button>
                                        @endif
                                        @if ( $booking->status == "REFUND_REQUESTED" )
                                            <button type="button" class="btn btn-success __btn_refund w-100 mb-2" data-reservation="{{ $booking->reservation_id }}" data-refund="{{ $booking->id }}" data-type="APPLY_REFUND">Aplicar reembolso</button>
                                            <button type="button" class="btn btn-danger __btn_refund w-100 mb-2" data-reservation="{{ $booking->reservation_id }}" data-refund="{{ $booking->id }}" data-type="DECLINE_REFUND">Declinar reembolso</button>
                                        @endif
                                        <button type="button" class="btn btn-primary __show_reservation w-100" data-reservation="{{ $booking->reservation_id }}" data-bs-toggle="modal" data-bs-target="#viewProofsModal">VER EVIDENCIA</button>
                                    </td>                                    
                                    <td class="text-center">{{ $booking->message_refund }}</td>
                                    <td class="text-center">{{ $booking->response_message }}</td>
                                    <td class="text-center"><span class="badge badge-{{ $booking->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $booking->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                                    <td class="text-center">{{ date("Y-m-d", strtotime($booking->created_at)) }}</td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($booking->reservation_status) }}">{{ auth()->user()->statusBooking($booking->reservation_status) }}</button></td>
                                    <td class="text-center">
                                        @php
                                            $pickupFrom = $booking->pickup_from ?? ''; // Asegurar que la variable exista
                                            $pickupTo = $booking->pickup_to ?? ''; // Asegurar que la variable exista

                                            // Verificar si hay una coma para dividir, de lo contrario, ponerlo en un array con un único valor
                                            $pickupDatesFrom = strpos($pickupFrom, ',') !== false 
                                                ? array_map('trim', explode(',', $pickupFrom)) 
                                                : [$pickupFrom];

                                            // Verificar si hay una coma para dividir, de lo contrario, ponerlo en un array con un único valor
                                            $pickupDatesTo = strpos($pickupTo, ',') !== false 
                                                ? array_map('trim', explode(',', $pickupTo)) 
                                                : [$pickupTo];

                                            // dump($pickupDatesFrom, $pickupDatesTo);
                                            // $pickup_from = explode(',',$booking->pickup_from);                                            
                                            // $pickup_to = explode(',',$booking->pickup_to);
                                            // dump($booking->pickup_from);
                                        @endphp
                                        [{{ date("Y-m-d", strtotime($pickupDatesFrom[0])) }}] <br>
                                        @if ( $booking->is_round_trip != 0 )
                                            [{{ date("Y-m-d", strtotime($pickupDatesTo[0])) }}]
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <?=auth()->user()->renderServiceStatus($booking->one_service_status)?><br>
                                        @if ( $booking->is_round_trip != 0 )
                                            <?=auth()->user()->renderServiceStatus($booking->two_service_status)?>
                                        @endif
                                    </td>
                                    <td class="text-center" <?=auth()->user()->classStatusPayment($booking)?>>{{ number_format(($booking->total_sales),2) }} {{ $booking->currency }}</td>
                                    <td class="text-center">{{ $booking->payment_type_name }}</td>
                                    <td class="text-center">
                                        @if ( !empty($booking->payment_details) )
                                            [{{ $booking->payment_details }}]
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :isSearch="1" />
    <x-modals.reports.columns />
    <x-modals.finances.proofs />
    <x-modals.finances.refund_not_applicable />
    <x-modals.new_payment_conciliation />
@endsection