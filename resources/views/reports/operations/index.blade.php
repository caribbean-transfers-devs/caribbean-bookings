@php
    use App\Traits\FiltersTrait;
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $services = FiltersTrait::Services();
    $websites = FiltersTrait::Sites();
    $origins = FiltersTrait::Origins();
    $reservation_status = FiltersTrait::reservationStatus();
    $services_operation = FiltersTrait::servicesOperation();
    $vehicles = FiltersTrait::Vehicles();
    $zones = FiltersTrait::Zones();
    $service_operation_status = FiltersTrait::statusOperationService();
    $units = FiltersTrait::Units(); //LAS UNIDADES DADAS DE ALT;
    $drivers = FiltersTrait::Drivers();
    $operation_status = FiltersTrait::statusOperation();
    $payment_status = FiltersTrait::paymentStatus();
    $currencies = FiltersTrait::Currencies();
    $methods = FiltersTrait::Methods();
    $cancellations = FiltersTrait::CancellationTypes();

    $operationStatus = [
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
    ];///

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
    ];///

    $dataCurrency = [
        "total" => 0,
        "gran_total" => 0,
        "counter" => 0,
        "data" => []
    ];//

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
    ];///

    $dataServiceType = [
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
    
    $dataServiceTypeOperation = [
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
    ];///

    $dataDriver = [
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
        "commission" => 0,
        "counter" => 0,
        "data" => []        
    ];
    
    $dataUnit = [
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
        "operating_cost" => 0,
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
    ];//7
@endphp
@extends('layout.app')
@section('title') Reporte De Operaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/reports/operations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/operations.min.css') }}" rel="stylesheet" >   
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{ mix('assets/js/sections/reports/operations.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de operaciones",
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
            // array(
            //     'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Ver graficas',
            //     'titleAttr' => 'Ver graficas de operaciones',
            //     'className' => 'btn btn-primary __btn_chart',
            //     'attr' => array(
            //         'data-title' =>  "Grafica de ventas",
            //         'data-bs-toggle' => 'modal',
            //         'data-bs-target' => '#chartsModal',
            //     )
            // ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Ver graficas',
                'titleAttr' => 'Ver graficas de operaciones',
                'className' => 'btn btn-primary __btn_chart2',
                'attr' => array(
                    'data-title' =>  "Grafica de ventas",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#chartsModal2',
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

                <table id="dataOperations" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">TIPO DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">REFERENCIA</th>
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
                            <th class="text-center">PAGO AL LLEGAR</th>
                            <th class="text-center">COMISIÓNABLE</th> 
                            <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($operations) >= 1)
                            @foreach ($operations as $operation)
                                @php
                                    //ESTATUS
                                    if (!isset( $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")] ) ){
                                        $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")] = [
                                            "name" => OperationTrait::serviceStatus(OperationTrait::serviceStatus($operation,"no_translate"),"translate_name"),
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
                                    $operationStatus['total'] += $operation->service_cost;
                                    $operationStatus['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                    $operationStatus[$operation->currency]['total'] += $operation->service_cost;
                                    $operationStatus[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                    $operationStatus[$operation->currency]['counter']++;
                                    $operationStatus['counter']++;
                                    $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")]['total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                    $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                    $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")][$operation->currency]['total'] += $operation->service_cost;
                                    $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")][$operation->currency]['counter']++;
                                    $operationStatus['data'][OperationTrait::serviceStatus($operation,"no_translate")]['counter']++;

                                    //METODOS DE PAGO
                                    if (!isset( $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))] ) ){
                                        $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))] = [
                                            "name" => $operation->payment_type_name,
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
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        $dataMethodPayments['total'] += $operation->service_cost;
                                        $dataMethodPayments['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataMethodPayments[$operation->currency]['total'] += $operation->service_cost;
                                        $dataMethodPayments[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataMethodPayments[$operation->currency]['counter']++;
                                        $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))]['total'] += $operation->service_cost;
                                        $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))][$operation->currency]['counter']++;
                                        $dataMethodPayments['data'][strtoupper(Str::slug($operation->payment_type_name))]['counter']++;
                                        $dataMethodPayments['counter']++;
                                    }
                                    
                                    //SITIOS                                    
                                    if (!isset( $dataSites['data'][strtoupper(Str::slug($operation->site_name))] ) ){
                                        $dataSites['data'][strtoupper(Str::slug($operation->site_name))] = [
                                            "name" => $operation->site_name,
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
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        $dataSites['total'] += $operation->service_cost;
                                        $dataSites['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataSites[$operation->currency]['total'] += $operation->service_cost;
                                        $dataSites[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataSites[$operation->currency]['counter']++;
                                        $dataSites['data'][strtoupper(Str::slug($operation->site_name))]['total'] += $operation->service_cost;
                                        $dataSites['data'][strtoupper(Str::slug($operation->site_name))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataSites['data'][strtoupper(Str::slug($operation->site_name))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataSites['data'][strtoupper(Str::slug($operation->site_name))][$operation->currency]['counter']++;
                                        $dataSites['data'][strtoupper(Str::slug($operation->site_name))]['counter']++;
                                        $dataSites['counter']++;
                                    }

                                    //ORIGEN DE VENTA
                                    if (!isset( $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))] ) ){
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))] = [
                                            "name" => ( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' ),
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
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        $dataOriginSale['total'] += $operation->service_cost;
                                        $dataOriginSale['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataOriginSale[$operation->currency]['total'] += $operation->service_cost;
                                        $dataOriginSale[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataOriginSale[$operation->currency]['counter']++;
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))]['total'] += $operation->service_cost;
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))][$operation->currency]['counter']++;
                                        $dataOriginSale['data'][strtoupper(Str::slug(( !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' )))]['counter']++;
                                        $dataOriginSale['counter']++;
                                    }

                                    //MONEDAS
                                    if (!isset( $dataCurrency['data'][$operation->currency] ) ){
                                        $dataCurrency['data'][$operation->currency] = [
                                            "name" => $operation->currency,
                                            "total" => 0,
                                            "gran_total" => 0,
                                            "counter" => 0,
                                        ];
                                    }
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        $dataCurrency['total'] += $operation->service_cost;
                                        $dataCurrency['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataCurrency['data'][$operation->currency]['total'] += $operation->service_cost;
                                        $dataCurrency['data'][$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataCurrency['data'][$operation->currency]['counter']++;
                                        $dataCurrency['counter']++;
                                    }

                                    //CONDUCTORES DE OPERACION
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        if (!isset( $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))] ) ){
                                            $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))] = [
                                                "name" => OperationTrait::setOperationDriver($operation),
                                                "total" => 0,
                                                "gran_total" => 0,
                                                "units" => [],
                                                "USD" => [
                                                    "total" => 0,
                                                    "counter" => 0,
                                                ],
                                                "MXN" => [
                                                    "total" => 0,
                                                    "counter" => 0,
                                                ],
                                                "commission" => 0,
                                                "counter" => 0,                                            
                                            ];
                                        }
                                        $dataDriver['total'] += $operation->service_cost;
                                        $dataDriver['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataDriver[$operation->currency]['total'] += $operation->service_cost;
                                        $dataDriver[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataDriver[$operation->currency]['counter']++;
                                        $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))]['total'] += $operation->service_cost;
                                        $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );

                                        if( !in_array(OperationTrait::setOperationUnit($operation), $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))]['units']) ){
                                            array_push($dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))]['units'], OperationTrait::setOperationUnit($operation));
                                        }

                                        $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))][$operation->currency]['counter']++;
                                        $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))]['counter']++;
                                        $dataDriver['data'][strtoupper(Str::slug(OperationTrait::setOperationDriver($operation)))]['commission'] += OperationTrait::commissionOperation($operation);
                                        $dataDriver['commission'] += OperationTrait::commissionOperation($operation);
                                        $dataDriver['counter']++;
                                    }
                                    
                                    //UNIDADES DE OPERACION
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        if (!isset( $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))] ) ){
                                            $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))] = [
                                                "name" => OperationTrait::setOperationUnit($operation),
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
                                                "operating_cost" => 0,
                                                "counter" => 0,                                            
                                            ];
                                        }                                        
                                        $dataUnit['total'] += $operation->service_cost;
                                        $dataUnit['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataUnit[$operation->currency]['total'] += $operation->service_cost;
                                        $dataUnit[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataUnit[$operation->currency]['counter']++;
                                        $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))]['total'] += $operation->service_cost;
                                        $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))][$operation->currency]['counter']++;
                                        $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))]['counter']++;
                                        $dataUnit['data'][strtoupper(Str::slug(OperationTrait::setOperationUnit($operation)))]['operating_cost'] += OperationTrait::setOperatingCost($operation);
                                        $dataUnit['operating_cost'] += OperationTrait::setOperatingCost($operation);
                                        $dataUnit['counter']++;                                        
                                    }

                                    //TIPO DE SERVICIO
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        if (!isset( $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))] ) ){
                                            $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))] = [
                                                "name" => strtoupper($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'),
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
                                        $dataServiceType['total'] += $operation->service_cost;
                                        $dataServiceType['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataServiceType[$operation->currency]['total'] += $operation->service_cost;
                                        $dataServiceType[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataServiceType[$operation->currency]['counter']++;
                                        $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))]['total'] += $operation->service_cost;
                                        $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))][$operation->currency]['counter']++;
                                        $dataServiceType['data'][strtoupper(Str::slug($operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP'))]['counter']++;
                                        $dataServiceType['counter']++;
                                    }                                    

                                    //TIPO DE SERVICIO EN OPERACION
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){
                                        if (!isset( $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))] ) ){
                                            $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))] = [
                                                "name" => $operation->final_service_type,
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
                                        $dataServiceTypeOperation['total'] += $operation->service_cost;
                                        $dataServiceTypeOperation['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataServiceTypeOperation[$operation->currency]['total'] += $operation->service_cost;
                                        $dataServiceTypeOperation[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataServiceTypeOperation[$operation->currency]['counter']++;
                                        $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))]['total'] += $operation->service_cost;
                                        $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))][$operation->currency]['counter']++;
                                        $dataServiceTypeOperation['data'][strtoupper(Str::slug($operation->final_service_type))]['counter']++;
                                        $dataServiceTypeOperation['counter']++;
                                    }

                                    //VEHICULOS
                                    if( OperationTrait::serviceStatus($operation) == "COMPLETADO" && OperationTrait::operationStatus($operation) == "OK" ){                               
                                        if (!isset( $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))] ) ){
                                            $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))] = [
                                                "name" => OperationTrait::setOperationVehicle($operation),
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
                                                "codes" => [],
                                            ];
                                        }
                                        $dataVehicles['total'] += $operation->service_cost;
                                        $dataVehicles['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataVehicles[$operation->currency]['total'] += $operation->service_cost;
                                        $dataVehicles[$operation->currency]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataVehicles[$operation->currency]['counter']++;
                                        $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))]['total'] += $operation->service_cost;
                                        $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))]['gran_total'] += ( $operation->currency == "USD" ? ($operation->service_cost * $exchange) : $operation->service_cost );
                                        $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))][$operation->currency]['total'] += $operation->service_cost;
                                        $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))][$operation->currency]['counter']++;
                                        $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))]['counter']++;
                                        $dataVehicles['data'][strtoupper(Str::slug(OperationTrait::setOperationVehicle($operation)))]['codes'][] = $operation->code;
                                        $dataVehicles['counter']++;
                                    }
                                @endphp
                                <tr class="" data-nomenclatura="{{ $operation->final_service_type }}{{ $operation->op_type }}" data-reservation="{{ $operation->reservation_id }}" data-item="{{ $operation->id }}" data-operation="{{ $operation->final_service_type }}" data-service="{{ $operation->operation_type }}" data-type="{{ $operation->op_type }}" data-close_operation="">
                                    <td class="text-center">{{ $operation->reservation_id }}</td>
                                    <td class="text-center"><span class="badge badge-{{ $operation->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                                    <td class="text-center">
                                        @if (RoleTrait::hasPermission(38))
                                            <a href="/reservations/detail/{{ $operation->reservation_id }}"><p class="mb-1">{{ $operation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $operation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center"><?=( !empty($operation->reference) ? '<p class="mb-1">'.$operation->reference.'</p>' : '' )?></td>
                                    <td class="text-center">{{ date("Y-m-d", strtotime($operation->created_at)) }}</td>
                                    <td class="text-center">{{ date("H:i", strtotime($operation->created_at)) }}</td>
                                    <td class="text-center">{{ $operation->site_name }}</td>
                                    <td class="text-center">{{ !empty($operation->origin_code) ? $operation->origin_code : 'PAGINA WEB' }}</td>
                                    <td class="text-center">{{ $operation->campaign }}</td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($operation->reservation_status) }}">{{ BookingTrait::statusBooking($operation->reservation_status) }}</button></td>
                                    <td class="text-center"><?=OperationTrait::renderServicePreassignment($operation)?></td>
                                    <td class="text-center">{{ $operation->final_service_type }}</td>
                                    <td class="text-center">{{ $operation->full_name }}</td>
                                    <td class="text-center">{{ $operation->client_phone }}</td>
                                    <td class="text-center">{{ $operation->client_email }}</td>
                                    <td class="text-center">{{ $operation->service_type_name }}</td>
                                    <td class="text-center">{{ $operation->passengers }}</td>
                                    <td class="text-center">{{ OperationTrait::setFrom($operation, "destination") }}</td>
                                    <td class="text-center" <?=OperationTrait::classCutOffZone($operation)?>>{{ OperationTrait::setFrom($operation, "name") }}</td>
                                    <td class="text-center">{{ OperationTrait::setTo($operation, "destination") }}</td>
                                    <td class="text-center" <?=OperationTrait::classCutOffZone($operation)?>>{{ OperationTrait::setTo($operation, "name") }}</td>
                                    <td class="text-center">{{ OperationTrait::setDateTime($operation, "date") }}</td>
                                    <td class="text-center">{{ OperationTrait::setDateTime($operation, "time") }}</td>
                                    <td class="text-center"><?=OperationTrait::renderServiceStatus($operation)?></td>
                                    <td class="text-center"><?=OperationTrait::setOperationUnit($operation)?></td>
                                    <td class="text-center"><?=OperationTrait::setOperationDriver($operation)?></td>
                                    <td class="text-center"><?=OperationTrait::setOperationTime($operation)?></td>
                                    <td class="text-center"><?=OperationTrait::setOperatingCost($operation)?></td>
                                    <td class="text-center"><?=OperationTrait::renderOperationStatus($operation)?></td>
                                    <td class="text-center">{{ OperationTrait::commissionOperation($operation) }}</td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($operation)?>>{{ BookingTrait::statusPayment($operation->payment_status) }}</td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($operation)?>>{{ number_format(($operation->total_sales),2) }}</td>
                                    <td class="text-center" {{ (($operation->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($operation->total_balance,2) }}</td>
                                    <td class="text-center">{{ number_format($operation->service_cost,2) }}</td>
                                    <td class="text-center">{{ $operation->currency }}</td>
                                    <td class="text-center">{{ $operation->payment_type_name }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info __payment_info bs-tooltip" title="Ver informacón detallada de los pagos" data-reservation="{{ $operation->reservation_id }}"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></td>
                                    <td class="text-center">
                                        <button class="btn btn-{{ $operation->pay_at_arrival == 1 ? 'success' : 'danger' }}" type="button">{{ $operation->pay_at_arrival == 1 ? "SI" : "NO" }}</button>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-{{ $operation->is_commissionable == 1 ? 'success' : 'danger' }}" type="button">{{ $operation->is_commissionable == 1 ? "SI" : "NO" }}</button>
                                    </td>
                                    <td class="text-center">
                                        @if ( ($operation->reservation_status == "CANCELLED" && OperationTrait::serviceStatus($operation, "no_translate") == "CANCELLED") || ($operation->reservation_status != "CANCELLED" && OperationTrait::serviceStatus($operation, "no_translate") == "CANCELLED") )
                                            @if ( !empty($operation->cancellation_reason) )
                                                {{ $operation->cancellation_reason }}
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

    {{-- @dump($dataVehicles) --}}

    <x-modals.filters.bookings :data="$data" :services="$services" :vehicles="$vehicles" :reservationstatus="$reservation_status" :servicesoperation="$services_operation" :serviceoperationstatus="$service_operation_status" :units="$units" :drivers="$drivers" :operationstatus="$operation_status" :paymentstatus="$payment_status" :methods="$methods" :cancellations="$cancellations" :currencies="$currencies" :zones="$zones" :websites="$websites" :origins="$origins" :request="$request" />
    <x-modals.reports.columns />
    {{-- <x-modals.charts.sales :bookingsStatus="$operationStatus" :dataMethodPayments="$dataMethodPayments" :dataCurrency="$dataCurrency" :dataSites="$dataSites" :dataOriginSale="$dataOriginSale" :dataVehicles="$dataVehicles" :dataUnit="$dataUnit" :dataDriver="$dataDriver" :dataServiceTypeOperation="$dataServiceTypeOperation" /> --}}
    <x-modals.charts.sales2 :bookingsStatus="$operationStatus" :dataMethodPayments="$dataMethodPayments" :dataCurrency="$dataCurrency" :dataVehicles="$dataVehicles" :dataServiceType="$dataServiceType" :dataServiceTypeOperation="$dataServiceTypeOperation" :dataSites="$dataSites" :dataDriver="$dataDriver" :dataUnit="$dataUnit" :dataOriginSale="$dataOriginSale" />
    <x-modals.reservations.payments />
@endsection