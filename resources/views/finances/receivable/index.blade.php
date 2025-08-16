@extends('layout.app')
@section('title') Cuentas Por cobrar @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/finances/receivables.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/finances/receivables.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/finances/receivables.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de cuentas por cobrar",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal',
                )
            ),
            // array(
            //     'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="layout-columns" class=""><path fill="" fill-rule="evenodd" d="M7 5a2 2 0 00-2 2v10a2 2 0 002 2h1V5H7zm3 0v14h4V5h-4zm6 0v14h1a2 2 0 002-2V7a2 2 0 00-2-2h-1zM3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" clip-rule="evenodd"></path></svg> Administrar columnas',
            //     'titleAttr' => 'Administrar columnas',
            //     'className' => 'btn btn-primary __btn_columns',
            //     'attr' => array(
            //         'data-title' =>  "Administrar columnas",
            //         'data-bs-toggle' => 'modal',
            //         'data-bs-target' => '#columnsModal',
            //         'data-table' => 'bookings',// EL ID DE LA TABLA QUE VAMOS A OBTENER SUS HEADERS
            //         'data-container' => 'columns', //EL ID DEL DIV DONDE IMPRIMIREMOS LOS CHECKBOX DE LOS HEADERS                    
            //     )                
            // ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Conciliar Reservas',
                'className' => 'btn btn-primary',
                'attr' => array(     
                    'id' => 'checkboxSelected',
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#ConciliationReservesCreditModal'
                )
            ),
            // array(
            //     'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar Excel',
            //     'extend' => 'excelHtml5',
            //     'titleAttr' => 'Exportar Excel',
            //     'className' => 'btn btn-primary',
            //     'exportOptions' => [
            //         'columns' => ':visible'  // Solo exporta las columnas visibles   
            //     ]
            // ),
            array(
                'text' => 'Tipo de cambio: '.$exchange,
                'titleAttr' => 'Tipo de cambio',
                'className' => 'btn btn-warning',
            ),
        );
    @endphp
    <div class="row layout-top-spacing" id="contentData">
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
                
                <table id="dataReceivable" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">
                                <div class="form-check form-check-primary">
                                    <input class="form-check-input chk-parent" type="checkbox" id="select-all">
                                </div>
                            </th> <!-- Checkbox para seleccionar todos -->
                            <th class="text-center">ESTATUS DE CONCILIACIÓN</th>
                            <th class="text-center">ACCIONES</th>
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
                                @php
                                    $color_status = '#e7515a';
                                    $text_status = 'PENDIENTE DE CONCILIAR PAGO';
                                    switch ($booking->credit_conciliation_status) {
                                        case 'pre-reconciled':
                                            $color_status = '#e2a03f';
                                            $text_status = 'PRE CONCILIADO';
                                            break;
                                        case 'reconciled':
                                            $color_status = '#00ab55';
                                            $text_status = 'CONCILIADO';
                                            break;
                                        case 'cxc':
                                            $color_status = '#805dca';
                                            $text_status = 'CXC';
                                            break;
                                        default:
                                            $color_status = '#e7515a';
                                            $text_status = 'PENDIENTE DE CONCILIAR PAGO';
                                            break;
                                    }
                                @endphp
                                <tr class="{{ ( $booking->is_today != 0 ? 'bs-tooltip' : '' ) }}" title="{{ ( $booking->is_today != 0 ? 'Es una reserva que se opera el mismo día en que se creo #: '.$booking->reservation_id : '' ) }}" style="{{ ( $booking->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}" data-reservation="{{ $booking->reservation_id }}" data-is_round_trip="{{ $booking->is_round_trip }}" {{ $booking->credit_conciliation_status }}>                                    
                                    <td class="text-center">
                                        @if ( $booking->credit_conciliation_status == null || $booking->credit_conciliation_status == "pre-reconciled" )
                                            <div class="form-check form-check-primary">
                                                <input class="form-check-input chk-chk row-check" type="checkbox" value="{{ $booking->reservation_id }}">
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center" style="background-color:{{ $color_status }}; color:#fff;">
                                        {{ $text_status }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $codes_string = "";
                                            $codes = explode(",",$booking->reservation_codes);
                                            foreach ($codes as $key => $code) {
                                                $codes_string .= '<p class="mb-1 text-white">'.$code.'</p>';
                                            }
                                        @endphp
                                        @if (auth()->user()->hasPermission(61))
                                            <a class="btn btn-dark w-100 mb-2" href="/reservations/detail/{{ $booking->reservation_id }}" target="_black"><?=$codes_string?></a>
                                        @else
                                            <button type="button" class="btn btn-dark w-100 mb-2"><?=$codes_string?></button>
                                        @endif
                                        @if ( $booking->credit_conciliation_status == null || $booking->credit_conciliation_status == "pre-reconciled" )
                                            <button type="button" class="btn btn-success __btn_conciliation_credit w-100 mb-2" data-reservation="{{ $booking->reservation_id }}" data-code="{{ $booking->credit_payment_ids }}">Click para conciliar pago</button>
                                        @endif
                                        <button type="button" class="btn btn-primary __show_reservation w-100" data-reservation="{{ $booking->reservation_id }}" data-bs-toggle="modal" data-bs-target="#viewProofsModal">VER EVIDENCIA</button>                                       
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $booking->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $booking->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span> <br>
                                        [{{ $booking->reservation_id }}]
                                    </td>
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
                                    <td class="text-center" <?=auth()->user()->classStatusPayment($booking)?>>{{ number_format(( $booking->total_payments_credit > 0 ? $booking->total_payments_credit : $booking->total_sales_credit ),2) }} {{ $booking->currency }}</td>
                                    <td class="text-center">{{ $booking->payment_type_name }}</td>
                                    <td class="text-center">
                                        @if ( !empty($booking->payment_details) )
                                            [{{ $booking->payment_details }}]
                                        @endif

                                        <p><strong class="text-uppercase">Referencia de agencia:</strong> {{ $booking->credit_references_agency  }}</p>
                                        <p><strong class="text-uppercase">Referencia de pago:</strong> {{ $booking->credit_references_payment  }}</p>
                                        <p><strong class="text-uppercase">Fecha de pre-conciliacion:</strong> {{ $booking->credit_conciliation_dates  }}</p>
                                        <p><strong class="text-uppercase">Comentarios de conciliación:</strong> {{ $booking->credit_comments  }}</p>
                                        <p><strong class="text-uppercase">Fecha de conciliacion:</strong> {{ $booking->credit_deposit_dates  }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :isSearch="1" :services="$services" />
    <x-modals.reports.columns />
    <x-modals.finances.proofs />
    <x-modals.finances.credit_conciliation />
    {{-- <x-modals.new_payment_conciliation /> --}}
@endsection