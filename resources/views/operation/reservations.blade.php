@php        
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    $bookingsStatus = [
        "total" => 0,
        "gran_total" => 0,
        "USD" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "counter" => 0,
        "data" => []
    ];

    $dataMethodPayments = [
        "total" => 0,
        "gran_total" => 0,
        "USD" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "counter" => 0,
        "data" => []
    ];    

    $dataSites = [
        "total" => 0,
        "gran_total" => 0,
        "USD" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "counter" => 0,
        "data" => []
    ];

    $dataDestinations = [
        "total" => 0,
        "gran_total" => 0,
        "USD" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "counter" => 0,
        "data" => []
    ];

    $dataCurrency = [
        "total" => 0,
        "gran_total" => 0,
        "counter" => 0,
        "data" => []
    ];

    $dataVehicles = [
        "total" => 0,
        "gran_total" => 0,
        "USD" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "counter" => 0,
        "data" => []
    ];

    $dataOriginSale = [
        "total" => 0,
        "gran_total" => 0,
        "USD" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "gran_total" => 0,
            "counter" => 0,
        ],
        "counter" => 0,
        "data" => []
    ];
@endphp
@extends('layout.app')
@section('title') Reservaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management_reservations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management_reservations.min.css') }}" rel="stylesheet" >
    <style>
        .__payment_info{
            cursor: pointer;
            font-size: 20px;
        }

        /* Estilo para la capa */
        .layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            color: white;
            opacity: 0;
            visibility: hidden;            
            transition: opacity 0.5s ease, visibility 0.5s ease;
            z-index: 1000;
        }

        .layer.active {
            opacity: 1;
            visibility: visible;
        }

        .header-chart,
        .body-chart{
            width: 100%;
            padding: 15px;
        }

        table.table-chart {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            color: #000000;
            text-align: left;
            min-width: 400px;
        }
        .table-chart thead tr,
        .table-chart tfoot tr{
            background-color: #009879;
            color: white;
            font-weight: bold;            
        }

        .table-chart th,
        .table-chart td {
            padding: 12px 15px !important;
            border: 1px solid #ddd;
        }
        .table-chart tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        /* Hover en las filas */
        .table-chart tbody tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        /* Resaltar la fila seleccionada */
        .table-chart tbody tr.active-row {
            font-weight: bold;
            color: #009879;
        }

        .gran_total .btn{
            font-size: 1.5em; /* Tamaño general más grande para el total */
            color: #000; /* Color negro para el texto */
        }

        .gran_total .btn strong {
            font-size: 1em; /* El texto "TOTAL" se mantiene en el mismo tamaño */
            margin-right: 10px; /* Espacio entre "TOTAL:" y el monto */
        }

        .gran_total .btn span {
            display: inline-flex;
            /* align-items: flex-end; */
            align-items: flex-start;
        }

        .gran_total .btn span::after {
            content: ' MXN'; /* Moneda */
            font-size: 0.65em; /* Texto más pequeño para la moneda */
            margin-left: 5px; /* Espacio entre el monto y la moneda */
            vertical-align: top; /* Alinea la moneda a la parte superior */
            position: relative;
            top: -0.2em; /* Ajuste fino para elevar ligeramente la moneda */
        }

        .fixed-header {
            position: fixed;
            top: 108px;
            z-index: 1000;
            width: 100%;
            background-color: rgba(234, 241, 255, 0.74); /* Asegúrate de que el fondo sea blanco o del color de la tabla */
        }
    </style>
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{ mix('assets/js/sections/operations/reservations.min.js') }}"></script>
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
                <table id="bookings" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">HORA</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">ORIGEN DE VENTA</th>
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
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($bookings) >= 1)
                            @foreach ($bookings as $item)
                                @php
                                    //ESTATUS
                                    if (!isset( $bookingsStatus['data'][$item->reservation_status] ) ){
                                        $bookingsStatus['data'][$item->reservation_status] = [
                                            "name" => BookingTrait::statusBooking($item->reservation_status),
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "USD" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "MXN" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $bookingsStatus['total'] += $item->total_sales;
                                    $bookingsStatus['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $bookingsStatus[$item->currency]['total'] += $item->total_sales;
                                    $bookingsStatus[$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $bookingsStatus[$item->currency]['counter']++;
                                    $bookingsStatus['counter']++;
                                    $bookingsStatus['data'][$item->reservation_status]['total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $bookingsStatus['data'][$item->reservation_status]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $bookingsStatus['data'][$item->reservation_status][$item->currency]['total'] += $item->total_sales;
                                    $bookingsStatus['data'][$item->reservation_status][$item->currency]['counter']++;
                                    $bookingsStatus['data'][$item->reservation_status]['counter']++;

                                    //METODOS DE PAGO
                                    if (!isset( $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))] ) ){
                                        $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))] = [
                                            "name" => $item->payment_type_name,
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "USD" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "MXN" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $dataMethodPayments['total'] += $item->total_sales;
                                    $dataMethodPayments['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataMethodPayments[$item->currency]['total'] += $item->total_sales;
                                    $dataMethodPayments[$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataMethodPayments[$item->currency]['counter']++;
                                    $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))]['total'] += $item->total_sales;
                                    $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))][$item->currency]['total'] += $item->total_sales;
                                    $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))][$item->currency]['counter']++;
                                    $dataMethodPayments['data'][strtoupper(Str::slug($item->payment_type_name))]['counter']++;
                                    $dataMethodPayments['counter']++;

                                    //SITIOS                                    
                                    if (!isset( $dataSites['data'][strtoupper(Str::slug($item->site_name))] ) ){
                                        $dataSites['data'][strtoupper(Str::slug($item->site_name))] = [
                                            "name" => strtoupper($item->site_name),
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "USD" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "MXN" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $dataSites['total'] += $item->total_sales;
                                    $dataSites['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataSites[$item->currency]['total'] += $item->total_sales;
                                    $dataSites[$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataSites[$item->currency]['counter']++;
                                    $dataSites['data'][strtoupper(Str::slug($item->site_name))]['total'] += $item->total_sales;
                                    $dataSites['data'][strtoupper(Str::slug($item->site_name))]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataSites['data'][strtoupper(Str::slug($item->site_name))][$item->currency]['total'] += $item->total_sales;
                                    $dataSites['data'][strtoupper(Str::slug($item->site_name))][$item->currency]['counter']++;
                                    $dataSites['data'][strtoupper(Str::slug($item->site_name))]['counter']++;
                                    $dataSites['counter']++;

                                    //DESTINOS                                    
                                    if (!isset( $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))] ) ){
                                        $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))] = [
                                            "name" => strtoupper($item->destination_name_to),
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "USD" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "MXN" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $dataDestinations['total'] += $item->total_sales;
                                    $dataDestinations['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataDestinations[$item->currency]['total'] += $item->total_sales;
                                    $dataDestinations[$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataDestinations[$item->currency]['counter']++;
                                    $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))]['total'] += $item->total_sales;
                                    $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))][$item->currency]['total'] += $item->total_sales;
                                    $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))][$item->currency]['counter']++;
                                    $dataDestinations['data'][strtoupper(Str::slug($item->destination_name_to))]['counter']++;
                                    $dataDestinations['counter']++;

                                    //MONEDAS
                                    if (!isset( $dataCurrency['data'][$item->currency] ) ){
                                        $dataCurrency['data'][$item->currency] = [
                                            "name" => $item->currency,
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $dataCurrency['total'] += $item->total_sales;
                                    $dataCurrency['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataCurrency['data'][$item->currency]['total'] += $item->total_sales;
                                    $dataCurrency['data'][$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataCurrency['data'][$item->currency]['counter']++;
                                    $dataCurrency['counter']++;

                                    //VEHICULOS                                    
                                    if (!isset( $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))] ) ){
                                        $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))] = [
                                            "name" => strtoupper($item->service_type_name),
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "USD" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "MXN" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $dataVehicles['total'] += $item->total_sales;
                                    $dataVehicles['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataVehicles[$item->currency]['total'] += $item->total_sales;
                                    $dataVehicles[$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataVehicles[$item->currency]['counter']++;
                                    $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))]['total'] += $item->total_sales;
                                    $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))][$item->currency]['total'] += $item->total_sales;
                                    $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))][$item->currency]['counter']++;
                                    $dataVehicles['data'][strtoupper(Str::slug($item->service_type_name))]['counter']++;
                                    $dataVehicles['counter']++;

                                    //ORIGEN DE VENTA
                                    if (!isset( $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))] ) ){
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))] = [
                                            "name" => ( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' ),
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "USD" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "MXN" => [
                                                "total" => 0,
                                                "counter" => 0,
                                            ],
                                            "counter" => 0,                                            
                                        ];
                                    }
                                    $dataOriginSale['total'] += $item->total_sales;
                                    $dataOriginSale['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataOriginSale[$item->currency]['total'] += $item->total_sales;
                                    $dataOriginSale[$item->currency]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataOriginSale[$item->currency]['counter']++;
                                    $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))]['total'] += $item->total_sales;
                                    $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))]['gran_total'] += ( $item->currency == "USD" ? ($item->total_sales * 18) : $item->total_sales );
                                    $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))][$item->currency]['total'] += $item->total_sales;
                                    $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))][$item->currency]['counter']++;
                                    $dataOriginSale['data'][strtoupper(Str::slug(( !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' )))]['counter']++;
                                    $dataOriginSale['counter']++;
                                @endphp
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
                                    <td class="text-center">{{ $item->service_type_name }}</td>
                                    <td class="text-center">{{ $item->passengers }}</td>                                    
                                    <td class="text-center">{{ $item->from_name }}</td>
                                    <td class="text-center">{{ $item->to_name }}</td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($item)?>>{{ BookingTrait::statusPayment($item->payment_status) }}</td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($item)?>>{{ number_format(($item->total_sales),2) }}</td>
                                    <td class="text-center" {{ (($item->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($item->total_balance,2) }}</td>                                
                                    <td class="text-center">{{ $item->currency }}</td>
                                    <td class="text-center">{{ $item->payment_type_name }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info __payment_info bs-tooltip" title="Ver informacón detallada de los pagos" data-reservation="{{ $item->reservation_id }}"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>                    
            </div>
        </div>
    </div>

    <div class="layer" id="layer">
        <div class="header-chart d-flex justify-content-between">
            <div class="gran_total">
                @foreach ($bookingsStatus['data'] as $key => $status)
                    <div class="btn btn-{{ BookingTrait::classStatusBooking($key) }}">
                        <span><strong>TOTAL {{ $status['name'] }}:</strong> $ {{ number_format($status['gran_total'],2) }}</span>
                    </div>
                @endforeach
            </div>
            <div>
                <button class="btn btn-primary" id="closeLayer">Cerrar</button>
            </div>
        </div>
        <div class="body-chart">
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">ESTADISTICAS POR ESTATUS</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="chartSale" id="chartSaleStatus"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>ESTATUS</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookingsStatus['data'] as $keyStatus => $status )
                                            <tr>
                                                <th>{{ $status['name'] }}</th>
                                                <td class="text-center">{{ number_format($status['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $status['counter'] }}</td>
                                                <td class="text-center">{{ number_format($status['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($status['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($bookingsStatus['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $bookingsStatus['counter'] }}</th>
                                            <th class="text-center">{{ number_format($bookingsStatus['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($bookingsStatus['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por metodo de pago</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="chartSale" id="chartSaleMethodPayments"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>METODO DE PAGO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataMethodPayments['data'] as $keyMethod => $method )
                                            <tr>
                                                <th>{{ $method['name'] }}</th>
                                                <td class="text-center">{{ number_format($method['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $method['counter'] }}</td>
                                                <td class="text-center">{{ number_format($method['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($method['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataMethodPayments['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataMethodPayments['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataMethodPayments['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataMethodPayments['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por sitio</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="" id="chartSaleSites"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>SITIO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataSites['data'] as $keySite => $site )
                                            <tr>
                                                <th>{{ $site['name'] }}</th>
                                                <td class="text-center">{{ number_format($site['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $site['counter'] }}</td>
                                                <td class="text-center">{{ number_format($site['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($site['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataSites['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataSites['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataSites['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataSites['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por destino</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="" id="chartSaleDestinations"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>DESTINO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataDestinations['data'] as $keyDestination => $destination )
                                            <tr>
                                                <th>{{ $destination['name'] }}</th>
                                                <td class="text-center">{{ number_format($destination['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $destination['counter'] }}</td>
                                                <td class="text-center">{{ number_format($destination['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($destination['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataDestinations['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataDestinations['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="col-lg-4 col-12">
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por moneda</h5>
                            <div class="col-lg-12 col-12">
                                <canvas class="" id="chartSaleCurrencies"></canvas>
                            </div>
                            <div class="col-lg-12 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>MONEDA</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">TOTAL</th>                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataCurrency['data'] as $keyCurrency => $currency )
                                            <tr>
                                                <th>{{ $currency['name'] }}</th>
                                                <td class="text-center">{{ number_format($currency['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $currency['counter'] }}</td>
                                                <td class="text-center">{{ number_format($currency['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataCurrency['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataCurrency['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataCurrency['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por origen de venta</h5>
                            <div class="col-lg-12 col-12">
                                <canvas class="" id="chartSaleOrigins"></canvas>
                            </div>
                            <div class="col-lg-12 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>ORIGEN</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataOriginSale['data'] as $keyOrigin => $origin )
                                            <tr>
                                                <th>{{ $origin['name'] }}</th>
                                                <td class="text-center">{{ number_format($origin['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $origin['counter'] }}</td>
                                                <td class="text-center">{{ number_format($origin['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($origin['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataOriginSale['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por vehículo</h5>
                            <div class="col-lg-12 col-12">
                                <canvas class="" id="chartSaleVehicles"></canvas>
                            </div>
                            <div class="col-lg-12 col-12">
                                <table class="table table-chart">
                                    <thead>
                                        <tr>
                                            <th>VEHÍCULO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataVehicles['data'] as $keyVehicle => $vehicle )
                                            <tr>
                                                <th>{{ $vehicle['name'] }}</th>
                                                <td class="text-center">{{ number_format($vehicle['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $vehicle['counter'] }}</td>
                                                <td class="text-center">{{ number_format($vehicle['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($vehicle['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataVehicles['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataVehicles['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataVehicles['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataVehicles['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :isSearch="1" :vehicles="$vehicles" :reservationstatus="$reservation_status" :paymentstatus="$payment_status" :methods="$methods" :websites="$websites" :origins="$origins" :istoday="1" />
    <x-modals.reports.columns />
    <x-modals.reservations.payments />
@endsection