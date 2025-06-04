@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $users = auth()->user()->CallCenterAgent();
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
@section('title') Gestión De Reservaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management/reservations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/reservations.min.css') }}" rel="stylesheet" >
    <style>
        .bell-button {
            /* font-size: 24px; */
            border: none;
            background: none;
            cursor: pointer;
            position: relative;
            /* animation: ring 1s infinite ease-in-out; */
            transition: transform 0.3s;
        }
        .bell-button.active {
            animation: ring 1s infinite ease-in-out;
            box-shadow: 0 0 10px red, 0 0 20px red;
        }
        @keyframes ring {
            0% { transform: rotate(0); }
            15% { transform: rotate(-15deg); }
            30% { transform: rotate(15deg); }
            45% { transform: rotate(-10deg); }
            60% { transform: rotate(10deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0); }
        }        
    </style>
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/sections/management/reservations.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de reservaciones",
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
        );
    @endphp
    <div class="row layout-top-spacing">
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

        <div class="tab">
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#bookings" data-bs-toggle="tab" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin align-middle"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        Reservaciones
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#arrivals" data-bs-toggle="tab" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag align-middle"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                        Llegadas de operación
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#departures" data-bs-toggle="tab" role="tab">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card align-middle"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                        Salidas de operación
                    </a>
                </li>
            </ul>

            <div class="widget-content widget-content-area br-8">
                <div class="tab-content">
                    <div class="tab-pane active" id="bookings" role="tabpanel">
                        <table id="dataBookings" class="table table-bookings dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">INDICADORES</th>
                                    <th class="text-center">TIPO DE SERVICIO</th>
                                    <th class="text-center">CÓDIGO</th>
                                    <th class="text-center">REFERENCIA</th>
                                    <th class="text-center">VENDEDOR</th>
                                    <th class="text-center">FECHA</th>
                                    <th class="text-center">HORA</th>
                                    <th class="text-center">SITIO</th>                            
                                    <th class="text-center">ORIGEN DE VENTA</th>
                                    <th class="text-center">CAMPAÑA</th>
                                    <th class="text-center">ESTATUS</th>
                                    <th class="text-center">CLIENTE</th>
                                    <th class="text-center">VEHÍCULO</th>
                                    <th class="text-center">PAX</th>
                                    <th class="text-center">DESDE</th>                            
                                    <th class="text-center">HACIA</th>
                                    <th class="text-center">ESTATUS DE PAGO</th>
                                    <th class="text-center">TOTAL DE RESERVACIÓN</th>
                                    <th class="text-center">BALANCE</th>
                                    <th class="text-center">MONEDA</th>
                                    <th class="text-center">MÉTODO DE PAGO</th>
                                    <th class="text-center">CALIFICACIÓN</th>
                                    <th class="text-center">COMISIÓNABLE</th>
                                    <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($bookings) >= 1)
                                    @foreach ($bookings as $item)
                                        @php
                                            $background_identifier = "";
                                            if( $item->is_today != 0 ){
                                                $background_identifier = "background-color: #fb5607b8;";
                                            }

                                            if( $item->is_tomorrow != 0 ){
                                                $background_identifier = "background-color: #009688;";
                                            }                                            
                                        @endphp
                                        <tr class="{{ ( $item->is_today != 0 ? 'bs-tooltip' : '' ) }}" title="{{ ( $item->is_today != 0 ? 'Es una reserva que se opera el mismo día en que se creo #: '.$item->reservation_id : '' ) }}" style="{{ $background_identifier }}" data-reservation="{{ $item->reservation_id }}" data-is_round_trip="{{ $item->is_round_trip }}">
                                            <td class="text-center">{{ $item->reservation_id }}</td>
                                            <td class="text-center">
                                                @if ($item->is_same_day_round_trip)                                                                                                
                                                <button class="btn btn-warning active bell-button bs-tooltip" title="Servicion con misma fecha de llegada y salida"> 
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-truck"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                                                </button>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $item->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $item->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>

                                                @if ($item->is_last_minute == 1)
                                                    <span class="badge badge-secondary text-lowercase mt-1">Reserva de ultimo minuto</span>
                                                @endif
                                            </td>                                            
                                            <td class="text-center">
                                                @php
                                                    $codes_string = "";
                                                    $codes = explode(",",$item->reservation_codes);
                                                    foreach ($codes as $key => $code) {
                                                        $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                                    }
                                                @endphp
                                                @if (auth()->user()->hasPermission(61))
                                                    <a href="/reservations/detail/{{ $item->reservation_id }}"><?=$codes_string?></a>
                                                @else
                                                    <?=$codes_string?>
                                                @endif
                                            </td>
                                            <td class="text-center"><?=( !empty($item->reference) ? '<p class="mb-1">'.$item->reference.'</p>' : '' )?></td>
                                            <td class="text-center">{{ $item->employee ? $item->employee : 'System' }}</td>
                                            <td class="text-center">{{ date("Y-m-d", strtotime($item->created_at)) }}</td>
                                            <td class="text-center">{{ date("H:i", strtotime($item->created_at)) }}</td>
                                            <td class="text-center">{{ $item->site_name }}</td>
                                            <td class="text-center">{{ !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' }}</td>
                                            <td class="text-center">{{ $item->campaign }}</td>
                                            <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($item->reservation_status) }}">{{ auth()->user()->statusBooking($item->reservation_status) }}</button></td>
                                            <td class="text-center">{{ $item->full_name }}</td>
                                            <td class="text-center">{{ $item->service_type_name }}</td>
                                            <td class="text-center">{{ $item->passengers }}</td>                                    
                                            <td class="text-center">{{ $item->from_name }}</td>
                                            <td class="text-center">{{ $item->to_name }}</td>
                                            <td class="text-center" <?=auth()->user()->classStatusPayment($item)?>>{{ auth()->user()->statusPayment($item->payment_status) }}</td>
                                            <td class="text-center" <?=auth()->user()->classStatusPayment($item)?>>{{ number_format(($item->total_sales),2) }}</td>
                                            <td class="text-center" {{ (($item->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($item->total_balance,2) }}</td>                                
                                            <td class="text-center">{{ $item->currency }}</td>
                                            <td class="text-center">{{ $item->payment_type_name }}</td>
                                            <td class="text-center">
                                                @if ( $item->reserve_rating )
                                                    <button class="btn btn-{{ $item->reserve_rating == 1 ? 'success' : 'danger' }}" type="button"><?=$item->reserve_rating == 1 ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-down"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path></svg>'?></button>
                                                @endif
                                            </td>                                    
                                            <td class="text-center">
                                                <button class="btn btn-{{ $item->is_commissionable == 1 ? 'success' : 'danger' }}" type="button">{{ $item->is_commissionable == 1 ? "SI" : "NO" }}</button>
                                            </td>
                                            <td class="text-center">
                                                @if ( $item->reservation_status == "CANCELLED" )
                                                    @if ( !empty($item->cancellation_reason) )
                                                        {{ $item->cancellation_reason }}
                                                    @else
                                                        {{ "NO SHOW" }}
                                                    @endif
                                                @endif
                                            </td>                                    
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="arrivals" role="tabpanel">
                        <table id="dataArrivals" class="table table-arrivals dt-table-hover" style="width:100%" data-button='<?=json_encode([])?>'>
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>                                    
                                    <th class="text-center">ESTATUS DE CONFIRMACIÓN</th>
                                    <th class="text-center">TIPO DE SERVICIO</th>
                                    <th class="text-center">CÓDIGO</th>
                                    <th class="text-center">REFERENCIA</th>
                                    <th class="text-center">VENDEDOR</th>
                                    <th class="text-center">FECHA DE RESERVACIÓN</th>
                                    <th class="text-center">HORA DE RESERVACIÓN</th>
                                    <th class="text-center">SITIO</th>
                                    <th class="text-center">ORIGEN DE VENTA</th>
                                    <th class="text-center">CAMPAÑA</th>
                                    <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                                    <th class="text-center"># DE SERVICIO</th>
                                    <th class="text-center">TIPO DE SERVICIO EN OPERACIÓN</th>
                                    <th class="text-center">NOMBRE DEL CLIENTE</th>
                                    <th class="text-center">TELÉFONO DEL CLIENTE</th>
                                    <th class="text-center">CORREO DEL CLIENTE</th>
                                    <th class="text-center">VEHÍCULO</th>
                                    <th class="text-center">PAX</th>
                                    <th class="text-center">ORIGEN</th>
                                    <th class="text-center">DESDE</th>
                                    <th class="text-center">DESTINO</th>
                                    <th class="text-center">HACIA</th>
                                    <th class="text-center">FECHA DE SERVICIO</th>
                                    <th class="text-center">HORA DE SERVICIO</th>
                                    <th class="text-center">ESTATUS DE SERVICIO</th><!-- ESTATUS DE RESERVACION -->
                                    <th class="text-center">UNIDAD DE OPERACIÓN</th>
                                    <th class="text-center">CONDUCTOR DE OPERACIÓN</th>
                                    <th class="text-center">HORA DE OPERACIÓN</th>
                                    <th class="text-center">COSTO DE OPERACIÓN</th>
                                    <th class="text-center">ESTATUS DE OPERACIÓN</th>
                                    <th class="text-center">COMISIÓN CONDUCTOR</th>
                                    <th class="text-center">ESTATUS DE PAGO</th>
                                    <th class="text-center">TOTAL DE RESERVACIÓN</th>
                                    <th class="text-center">BALANCE</th>
                                    <th class="text-center">PRECIO POR SERVICIO</th>
                                    <th class="text-center">MONEDA</th>
                                    <th class="text-center">MÉTODO DE PAGO</th>
                                    <th class="text-center">COMISIÓNABLE</th> 
                                    <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                                    <th class="text-center">TIENE REEMBOLSO</th>
                                    <th class="text-center">CUANTAS SOLICITUDES</th>
                                    <th class="text-center">ESTATUS DE REEMBOLSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($arrivals) >= 1)
                                    @foreach ($arrivals as $arrival)
                                        <tr class="" data-nomenclatura="{{ $arrival->final_service_type }}{{ $arrival->op_type }}" data-reservation="{{ $arrival->reservation_id }}" data-item="{{ $arrival->id }}" data-operation="{{ $arrival->final_service_type }}" data-service="{{ $arrival->operation_type }}" data-type="{{ $arrival->op_type }}" data-close_operation="">
                                            <td class="text-center">{{ $arrival->reservation_id }}</td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermission(40))
                                                    <?=auth()->user()->renderStatusConfirmation($arrival)?>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $arrival->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $arrival->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>
                                                @if ($arrival->is_last_minute == 1)
                                                    <span class="badge badge-secondary text-lowercase mt-1">Reserva de ultimo minuto</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermission(61))
                                                    <a href="/reservations/detail/{{ $arrival->reservation_id }}"><p class="mb-1">{{ $arrival->code }}</p></a>
                                                @else
                                                    <p class="mb-1">{{ $arrival->code }}</p>
                                                @endif
                                            </td>
                                            <td class="text-center"><?=( !empty($arrival->reference) ? '<p class="mb-1">'.$arrival->reference.'</p>' : '' )?></td>
                                            <td class="text-center">{{ $arrival->employee ? $arrival->employee : 'System' }}</td>
                                            <td class="text-center">{{ date("Y-m-d", strtotime($arrival->created_at)) }}</td>
                                            <td class="text-center">{{ date("H:i", strtotime($arrival->created_at)) }}</td>
                                            <td class="text-center">{{ $arrival->site_name }}</td>
                                            <td class="text-center">{{ !empty($arrival->origin_code) ? $arrival->origin_code : 'PAGINA WEB' }}</td>
                                            <td class="text-center">{{ $arrival->campaign }}</td>
                                            <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($arrival->reservation_status) }}">{{ auth()->user()->statusBooking($arrival->reservation_status) }}</button></td>
                                            <td class="text-center"><?=auth()->user()->renderServicePreassignment($arrival)?></td>
                                            <td class="text-center">{{ $arrival->final_service_type }}</td>
                                            <td class="text-center">{{ $arrival->full_name }}</td>
                                            <td class="text-center">{{ $arrival->client_phone }}</td>
                                            <td class="text-center">{{ $arrival->client_email }}</td>
                                            <td class="text-center">{{ $arrival->service_type_name }}</td>
                                            <td class="text-center">{{ $arrival->passengers }}</td>
                                            <td class="text-center">{{ auth()->user()->setFrom($arrival, "destination") }}</td>
                                            <td class="text-center" <?=auth()->user()->classCutOffZone($arrival)?>>{{ auth()->user()->setFrom($arrival, "name") }}</td>
                                            <td class="text-center">{{ auth()->user()->setTo($arrival, "destination") }}</td>
                                            <td class="text-center" <?=auth()->user()->classCutOffZone($arrival)?>>{{ auth()->user()->setTo($arrival, "name") }}</td>
                                            <td class="text-center">{{ auth()->user()->setDateTime($arrival, "date") }}</td>
                                            <td class="text-center">{{ auth()->user()->setDateTime($arrival, "time") }}</td>
                                            <td class="text-center"><?=auth()->user()->renderServiceStatusOP($arrival)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperationUnit($arrival)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperationDriver($arrival)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperationTime($arrival)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperatingCost($arrival)?></td>
                                            <td class="text-center"><?=auth()->user()->renderOperationStatus($arrival)?></td>
                                            <td class="text-center">{{ auth()->user()->commissionOperation($arrival) }}</td>
                                            <td class="text-center" <?=auth()->user()->classStatusPayment($arrival)?>>{{ auth()->user()->statusPayment($arrival->payment_status) }}</td>
                                            <td class="text-center" <?=auth()->user()->classStatusPayment($arrival)?>>{{ number_format(($arrival->total_sales),2) }}</td>
                                            <td class="text-center" {{ (($arrival->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($arrival->total_balance,2) }}</td>
                                            <td class="text-center">{{ number_format($arrival->service_cost,2) }}</td>
                                            <td class="text-center">{{ $arrival->currency }}</td>
                                            <td class="text-center">{{ $arrival->payment_type_name }}</td>
                                            <td class="text-center">
                                                @if ( $arrival->is_commissionable == 1 )
                                                    <button class="btn btn-success" type="button">Sí</button>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( ($arrival->reservation_status == "CANCELLED" && auth()->user()->serviceStatus($arrival, "no_translate") == "CANCELLED") || ($arrival->reservation_status != "CANCELLED" && auth()->user()->serviceStatus($arrival, "no_translate") == "CANCELLED") )
                                                    @if ( !empty($arrival->cancellation_reason) )
                                                        {{ $arrival->cancellation_reason }}
                                                    @else
                                                        {{ "NO SHOW" }}  
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $arrival->has_refund_request )
                                                    <button class="btn btn-success" type="button">Sí</button>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $arrival->has_refund_request )
                                                    {{ $arrival->refund_request_count }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $arrival->has_refund_request )
                                                    <button class="btn btn-{{ auth()->user()->classStatusRefund($arrival->refund_status) }} btn-sm">{{ auth()->user()->statusRefund($arrival->refund_status) }}</button>
                                                @endif                                        
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="departures" role="tabpanel">
                        <table id="dataDepartures" class="table table-departures dt-table-hover" style="width:100%" data-button='<?=json_encode([])?>'>
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">ESTATUS DE CONFIRMACIÓN</th>
                                    <th class="text-center">TIPO DE SERVICIO</th>
                                    <th class="text-center">CÓDIGO</th>
                                    <th class="text-center">REFERENCIA</th>
                                    <th class="text-center">VENDEDOR</th>
                                    <th class="text-center">FECHA DE RESERVACIÓN</th>
                                    <th class="text-center">HORA DE RESERVACIÓN</th>
                                    <th class="text-center">SITIO</th>
                                    <th class="text-center">ORIGEN DE VENTA</th>
                                    <th class="text-center">CAMPAÑA</th>
                                    <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                                    <th class="text-center"># DE SERVICIO</th>
                                    <th class="text-center">TIPO DE SERVICIO EN OPERACIÓN</th>
                                    <th class="text-center">NOMBRE DEL CLIENTE</th>
                                    <th class="text-center">TELÉFONO DEL CLIENTE</th>
                                    <th class="text-center">CORREO DEL CLIENTE</th>
                                    <th class="text-center">VEHÍCULO</th>
                                    <th class="text-center">PAX</th>
                                    <th class="text-center">ORIGEN</th>
                                    <th class="text-center">DESDE</th>
                                    <th class="text-center">DESTINO</th>
                                    <th class="text-center">HACIA</th>
                                    <th class="text-center">FECHA DE SERVICIO</th>
                                    <th class="text-center">HORA DE SERVICIO</th>
                                    <th class="text-center">ESTATUS DE SERVICIO</th><!-- ESTATUS DE RESERVACION -->
                                    <th class="text-center">UNIDAD DE OPERACIÓN</th>
                                    <th class="text-center">CONDUCTOR DE OPERACIÓN</th>
                                    <th class="text-center">HORA DE OPERACIÓN</th>
                                    <th class="text-center">COSTO DE OPERACIÓN</th>
                                    <th class="text-center">ESTATUS DE OPERACIÓN</th>
                                    <th class="text-center">COMISIÓN CONDUCTOR</th>
                                    <th class="text-center">ESTATUS DE PAGO</th>
                                    <th class="text-center">TOTAL DE RESERVACIÓN</th>
                                    <th class="text-center">BALANCE</th>
                                    <th class="text-center">PRECIO POR SERVICIO</th>
                                    <th class="text-center">MONEDA</th>
                                    <th class="text-center">MÉTODO DE PAGO</th>
                                    <th class="text-center">COMISIÓNABLE</th> 
                                    <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                                    <th class="text-center">TIENE REEMBOLSO</th>
                                    <th class="text-center">CUANTAS SOLICITUDES</th>
                                    <th class="text-center">ESTATUS DE REEMBOLSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($departures) >= 1)
                                    @foreach ($departures as $departure)
                                        <tr class="" data-nomenclatura="{{ $departure->final_service_type }}{{ $departure->op_type }}" data-reservation="{{ $departure->reservation_id }}" data-item="{{ $departure->id }}" data-operation="{{ $departure->final_service_type }}" data-service="{{ $departure->operation_type }}" data-type="{{ $departure->op_type }}" data-close_operation="">
                                            <td class="text-center">{{ $departure->reservation_id }}</td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermission(40))
                                                    <?=auth()->user()->renderStatusConfirmation($departure)?>
                                                @endif
                                            </td>                                            
                                            <td class="text-center">
                                                <span class="badge badge-{{ $departure->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $departure->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>
                                                @if ($departure->is_last_minute == 1)
                                                    <span class="badge badge-secondary text-lowercase mt-1">Reserva de ultimo minuto</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermission(61))
                                                    <a href="/reservations/detail/{{ $departure->reservation_id }}"><p class="mb-1">{{ $departure->code }}</p></a>
                                                @else
                                                    <p class="mb-1">{{ $departure->code }}</p>
                                                @endif
                                            </td>
                                            <td class="text-center"><?=( !empty($departure->reference) ? '<p class="mb-1">'.$departure->reference.'</p>' : '' )?></td>
                                            <td class="text-center">{{ $departure->employee ? $departure->employee : 'System' }}</td>
                                            <td class="text-center">{{ date("Y-m-d", strtotime($departure->created_at)) }}</td>
                                            <td class="text-center">{{ date("H:i", strtotime($departure->created_at)) }}</td>
                                            <td class="text-center">{{ $departure->site_name }}</td>
                                            <td class="text-center">{{ !empty($departure->origin_code) ? $departure->origin_code : 'PAGINA WEB' }}</td>
                                            <td class="text-center">{{ $departure->campaign }}</td>
                                            <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($departure->reservation_status) }}">{{ auth()->user()->statusBooking($departure->reservation_status) }}</button></td>
                                            <td class="text-center"><?=auth()->user()->renderServicePreassignment($departure)?></td>
                                            <td class="text-center">{{ $departure->final_service_type }}</td>
                                            <td class="text-center">{{ $departure->full_name }}</td>
                                            <td class="text-center">{{ $departure->client_phone }}</td>
                                            <td class="text-center">{{ $departure->client_email }}</td>
                                            <td class="text-center">{{ $departure->service_type_name }}</td>
                                            <td class="text-center">{{ $departure->passengers }}</td>
                                            <td class="text-center">{{ auth()->user()->setFrom($departure, "destination") }}</td>
                                            <td class="text-center" <?=auth()->user()->classCutOffZone($departure)?>>{{ auth()->user()->setFrom($departure, "name") }}</td>
                                            <td class="text-center">{{ auth()->user()->setTo($departure, "destination") }}</td>
                                            <td class="text-center" <?=auth()->user()->classCutOffZone($departure)?>>{{ auth()->user()->setTo($departure, "name") }}</td>
                                            <td class="text-center">{{ auth()->user()->setDateTime($departure, "date") }}</td>
                                            <td class="text-center">{{ auth()->user()->setDateTime($departure, "time") }}</td>
                                            <td class="text-center"><?=auth()->user()->renderServiceStatusOP($departure)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperationUnit($departure)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperationDriver($departure)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperationTime($departure)?></td>
                                            <td class="text-center"><?=auth()->user()->setOperatingCost($departure)?></td>
                                            <td class="text-center"><?=auth()->user()->renderOperationStatus($departure)?></td>
                                            <td class="text-center">{{ auth()->user()->commissionOperation($departure) }}</td>
                                            <td class="text-center" <?=auth()->user()->classStatusPayment($departure)?>>{{ auth()->user()->statusPayment($departure->payment_status) }}</td>
                                            <td class="text-center" <?=auth()->user()->classStatusPayment($departure)?>>{{ number_format(($departure->total_sales),2) }}</td>
                                            <td class="text-center" {{ (($departure->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($departure->total_balance,2) }}</td>
                                            <td class="text-center">{{ number_format($departure->service_cost,2) }}</td>
                                            <td class="text-center">{{ $departure->currency }}</td>
                                            <td class="text-center">{{ $departure->payment_type_name }}</td>
                                            <td class="text-center">
                                                @if ( $departure->is_commissionable == 1 )
                                                    <button class="btn btn-success" type="button">Sí</button>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( ($departure->reservation_status == "CANCELLED" && auth()->user()->serviceStatus($departure, "no_translate") == "CANCELLED") || ($departure->reservation_status != "CANCELLED" && auth()->user()->serviceStatus($departure, "no_translate") == "CANCELLED") )
                                                    @if ( !empty($departure->cancellation_reason) )
                                                        {{ $departure->cancellation_reason }}
                                                    @else
                                                        {{ "NO SHOW" }}  
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $departure->has_refund_request )
                                                    <button class="btn btn-success" type="button">Sí</button>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $departure->has_refund_request )
                                                    {{ $departure->refund_request_count }}
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $departure->has_refund_request )
                                                    <button class="btn btn-{{ auth()->user()->classStatusRefund($departure->refund_status) }} btn-sm">{{ auth()->user()->statusRefund($departure->refund_status) }}</button>
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
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :isSearch="1" :users="$users" :vehicles="$vehicles" :reservationstatus="$reservation_status" :paymentstatus="$payment_status" :methods="$methods" :websites="$websites" :origins="$origins" :ispayarrival="1" :rating="1" :istoday="1" />
    <x-modals.reports.columns />
@endsection