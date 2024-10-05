@php
    use App\Traits\RoleTrait;
    use App\Traits\Reports\PaymentsTrait;
    use App\Traits\BookingTrait;
    use Carbon\Carbon;
@endphp
@php
    $bookingsStatus = [
        'ACCUMULATED' => [
            "total" => 0,
            "USD" => [
                "total" => 0,
                "counter" => 0,
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0,
            ],
            "counter" => 0,
        ],
        'CONFIRMED' => [
            "total" => 0,
            "USD" => [
                "total" => 0,
                "counter" => 0,
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0,
            ],
            "counter" => 0,
        ],
        'PENDING' => [
            "total" => 0,
            "USD" => [
                "total" => 0,
                "counter" => 0,
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0,
            ],
            "counter" => 0,
        ],            
        'CANCELLED' => [
            "total" => 0,
            "USD" => [
                "total" => 0,
                "counter" => 0,
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0,
            ],
            "counter" => 0,
        ],
        'OPENCREDIT' => [
            "total" => 0,
            "USD" => [
                "total" => 0,
                "counter" => 0,
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0,
            ],
            "counter" => 0,
        ],
    ];
    $resume = [
        'status' => [
            'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CONFIRMED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CANCELLED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        ]
    ];
    $sites = [];
    $affiliates = [];
    $destinations = [];
    $resume_total = 0;
    $resume_total_mxn = 0;
    $resume_total_usd = 0;
@endphp
@extends('layout.app')
@section('title') Reservaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
    <style>
        .__payment_info{
            cursor: pointer;
            font-size: 20px;
        }
    </style>
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/sections/reservations/bookings.min.js') }}"></script>
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
                    'data-title' =>  "Filtro de reservaciones",
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
                <table id="bookings" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">TIPO DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">FECHA DE RESERVACIÓN</th>
                            <th class="text-center">HORA DE RESERVACIÓN</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">ORIGEN DE VENTA</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>
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
                            <th class="text-center">ESTATUS DE SERVICIO(S)</th>
                            <th class="text-center">ESTATUS DE PAGO</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">BALANCE</th>
                            <th class="text-center">COSTO POR SERVICIO</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">MÉTODO DE PAGO</th> 
                            <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($bookings) >= 1)
                            @foreach ($bookings as $item)
                                {{-- @dd($item); --}}
                                {{-- @dump($item) --}}

                                {{-- @if ( $item->reservation_id == 37165 )
                                    @dump($item)
                                @endif --}}
                                {{-- @if ( $item->status == "UNKNOWN" )
                                    @dd($item)
                                @endif --}}
                                @php
                                    $bookingsStatus['ACCUMULATED']['total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $bookingsStatus['ACCUMULATED'][$item->currency]['total'] += $item->total_sales;
                                    $bookingsStatus['ACCUMULATED'][$item->currency]['counter']++;
                                    $bookingsStatus['ACCUMULATED']['counter']++;

                                    if($item->is_cancelled == 0):
                                        // $resume['status'][$item->status][$item->currency] += $item->total_sales;
                                        // $resume['status'][$item->status]['count']++;
                                        // //Si está confirmado, sumamos los totales por sitio...
                                        // if($item->status == "CONFIRMED"):
                                        //     if(!isset( $sites[$item->site_name] )):
                                        //         $sites[$item->site_name] = [
                                        //             'USD' => 0,
                                        //             'MXN' => 0,
                                        //             'count' => 0
                                        //         ];
                                        //     endif;                                                        
                                        //     $sites[$item->site_name][$item->currency] += $item->total_sales;
                                        //     $sites[$item->site_name]['count']++;

                                        //     if($item->affiliate_id != 0 ):
                                        //         if(!isset( $affiliates[$item->site_name] )):
                                        //             $affiliates[$item->site_name] = [
                                        //                 'USD' => 0,
                                        //                 'MXN' => 0,
                                        //                 'count' => 0
                                        //             ];
                                        //         endif;
                                        //         $affiliates[$item->site_name][$item->currency] += $item->total_sales;
                                        //         $affiliates[$item->site_name]['count']++;
                                        //     endif;

                                        //     if(!isset( $destinations[$item->destination_name_to] )):
                                        //         $destinations[$item->destination_name_to] = [
                                        //             'USD' => 0,
                                        //             'MXN' => 0,
                                        //             'count' => 0
                                        //         ];
                                        //     endif;
                                        //     $destinations[$item->destination_name_to][$item->currency] += $item->total_sales;
                                        //     $destinations[$item->destination_name_to]['count']++;
                                        // endif;
                                    else:
                                        // $resume['status']['CANCELLED'][$item->currency] += $item->total_sales;
                                        // $resume['status']['CANCELLED']['count']++;
                                    endif;
                                @endphp
                                <tr class="{{ ( $item->is_today != 0 ? 'bs-tooltip' : '' ) }}" title="{{ ( $item->is_today != 0 ? 'Es una reserva que se opera el mismo día en que se creo #: '.$item->reservation_id : '' ) }}" style="{{ ( $item->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}" data-reservation="{{ $item->reservation_id }}" data-is_round_trip="{{ $item->is_round_trip }}">
                                    <td class="text-center"><span class="badge badge-{{ $item->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $item->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                                    <td class="text-center">
                                        @php
                                            $codes_string = "";
                                            $codes = explode(",",$item->reservation_codes);
                                            foreach ($codes as $key => $code) {
                                                $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                            }
                                        @endphp
                                        @if (RoleTrait::hasPermission(38))
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
                                    <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($item->reservation_status) }}">{{ BookingTrait::statusBooking($item->reservation_status) }}</button></td>
                                    <td class="text-center">{{ $item->full_name }}</td>
                                    <td class="text-center">{{ $item->client_phone }}</td>
                                    <td class="text-center">{{ $item->client_email }}</td>
                                    <td class="text-center">{{ $item->service_type_name }}</td>
                                    <td class="text-center">{{ $item->passengers }}</td>
                                    <td class="text-center">{{ $item->destination_name_from }}</td>
                                    <td class="text-center">{{ $item->from_name }}</td>
                                    <td class="text-center">{{ $item->destination_name_to }}</td>
                                    <td class="text-center">{{ $item->to_name }}</td>
                                    <td class="text-center">
                                        [{{ date("Y-m-d", strtotime($item->pickup_from)) }}]
                                        @if ( $item->is_round_trip != 0 )
                                            [{{ date("Y-m-d", strtotime($item->pickup_to)) }}]
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        [{{ date("H:i", strtotime($item->pickup_from)) }}] <br>
                                        @if ( $item->is_round_trip != 0 )
                                            [{{ date("H:i", strtotime($item->pickup_to)) }}]
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <?=BookingTrait::renderServiceStatus($item->one_service_status)?><br>
                                        @if ( $item->is_round_trip != 0 )
                                            <?=BookingTrait::renderServiceStatus($item->two_service_status)?>
                                        @endif
                                    </td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($item)?>>{{ BookingTrait::statusPayment($item->payment_status) }}</td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($item)?>>{{ number_format(($item->total_sales),2) }}</td>
                                    <td class="text-center" {{ (($item->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($item->total_balance,2) }}</td>
                                    <td class="text-center">{{ number_format(($item->is_round_trip != 0 ? ( $item->total_sales / 2 ) : $item->total_sales),2) }}</td>
                                    <td class="text-center">{{ $item->currency }}</td>
                                    <td class="text-center">{{ $item->payment_type_name }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info __payment_info bs-tooltip" title="Ver informacón detallada de los pagos" data-reservation="{{ $item->reservation_id }}"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></td>
                                    <td class="text-center">{{ $item->cancellation_reason }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :isSearch="1" :services="$services" :vehicles="$vehicles" :reservationstatus="$reservation_status" :paymentstatus="$payment_status" :methods="$methods" :cancellations="$cancellations" :currencies="$currencies" :zones="$zones" :websites="$websites" :origins="$origins" :istoday="1" :isbalance="1" />
    <x-modals.reports.columns />
    <x-modals.reservations.payments />
@endsection