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
@section('title') Gestión De Reservaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management/reservations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/reservations.min.css') }}" rel="stylesheet" >
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

                <table id="dataBookings" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">REFERENCIA</th>
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
                            <th class="text-center">PAGO AL LLEGAR</th>
                            <th class="text-center">CALIFICACIÓN</th>
                            <th class="text-center">COMISIÓNABLE</th>
                            <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($bookings) >= 1)
                            @foreach ($bookings as $item)
                                <tr class="{{ ( $item->is_today != 0 ? 'bs-tooltip' : '' ) }}" title="{{ ( $item->is_today != 0 ? 'Es una reserva que se opera el mismo día en que se creo #: '.$item->reservation_id : '' ) }}" style="{{ ( $item->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}" data-reservation="{{ $item->reservation_id }}" data-is_round_trip="{{ $item->is_round_trip }}">
                                    <td class="text-center">{{ $item->reservation_id }}</td>
                                    <td class="text-center">
                                        @php
                                            $codes_string = "";
                                            $codes = explode(",",$item->reservation_codes);
                                            foreach ($codes as $key => $code) {
                                                $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                            }
                                        @endphp
                                        @if (auth()->user()->hasPermission2(38))
                                            <a href="/reservations/detail/{{ $item->reservation_id }}"><?=$codes_string?></a>
                                        @else
                                            <?=$codes_string?>
                                        @endif
                                    </td>
                                    <td class="text-center"><?=( !empty($item->reference) ? '<p class="mb-1">'.$item->reference.'</p>' : '' )?></td>
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
                                    <td class="text-center">{{ $item->payment_type_name }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info __payment_info bs-tooltip" title="Ver informacón detallada de los pagos" data-reservation="{{ $item->reservation_id }}"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></td>
                                    <td class="text-center">
                                        <button class="btn btn-{{ $item->pay_at_arrival == 1 ? 'success' : 'danger' }}" type="button">{{ $item->pay_at_arrival == 1 ? "SI" : "NO" }}</button>
                                    </td>
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
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :isSearch="1" :vehicles="$vehicles" :reservationstatus="$reservation_status" :paymentstatus="$payment_status" :methods="$methods" :websites="$websites" :origins="$origins" :ispayarrival="1" :rating="1" :istoday="1" />
    <x-modals.reports.columns />
    <x-modals.reservations.payments />
@endsection