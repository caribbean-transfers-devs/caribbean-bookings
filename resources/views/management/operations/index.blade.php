@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;    
    $arrivalTimeGroup = [];
    $departureTimeGroup = [];
    $generalTimeGroup = []; // Arreglo para agrupar los datos

    $websites = auth()->user()->Sites();
    $reservation_status = auth()->user()->reservationStatus();
    $vehicles = auth()->user()->Vehicles();
    $zones = auth()->user()->Zones();
    $units = auth()->user()->Units(); //LAS UNIDADES DADAS DE ALTA
    $units2 = auth()->user()->Units('active'); //LAS UNIDADES DADAS DE ALTA
    $drivers = auth()->user()->Drivers();
    $drivers2 = auth()->user()->Drivers('active');
@endphp
@extends('layout.custom')
@section('title') Gestión De Operaciones @endsection

@push('Css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link href="{{ mix('/assets/css/sections/management/operations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/operations.min.css') }}" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <style>
        .tab .nav-tabs .nav-link {
            background: transparent;
            color: #343a40;
            padding: .75rem 1rem;
            border: 0;
        }
        .tab .nav-tabs .nav-link.active {
            background: #fff;
            color: #343a40;
        }

        /**/
        .dt-buttons .btn svg{
            width: 18px;
            height: 18px;
        }

        /*ESTILOS PARA IDENTIFICAR LOS COLORES DE AGENCIAS ESPECIFICAS*/
        .agency_29{
            background-color: #FE7A1F !important;
        }
        .agency_30{
            background-color: #00467e !important;
        }
        .is_open{
            background-color: #e2a03f !important;
        }

        .agency_29 td,
        .is_open td{
            color: #000000 !important;
        }
        .agency_30 td{
            color: #ffffff !important;
        }

        /*ESTILO DE CAMPANA*/
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdn.socket.io/4.4.1/socket.io.min.js"></script>
    <script src="{{ mix('assets/js/sections/management/operations.min.js') }}"></script>
    <script>
        document.getElementById('showLayer').addEventListener('click', function() {
            document.getElementById('layer').classList.add('active');
        });

        document.getElementById('closeLayer').addEventListener('click', function() {
            document.getElementById('layer').classList.remove('active');
        });
    </script>
@endpush

@section('content')
    @php
        $total_close = 0;
        $buttons = array();
        if( sizeof($items) >= 1 ):
            foreach ($items as $operation) {
                $close_operation = ( ( ( $operation->final_service_type == 'ARRIVAL' || $operation->final_service_type == 'TRANSFER' || $operation->final_service_type == 'DEPARTURE' ) && $operation->op_type == "TYPE_ONE" && ( $operation->is_round_trip == 0 || $operation->is_round_trip == 1 ) ) ? $operation->op_one_operation_close : $operation->op_two_operation_close );
                ( $close_operation == 1 ? $total_close++ : "" );
            }
        endif;
    @endphp
    @if (auth()->user()->hasPermission2(79))
        <input type="hidden" class="" id="permission_reps" value="true" required>
    @endif

    @if ( sizeof($items) >= 1 )
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="alert alert-arrow-right alert-icon-right alert-light-{{ sizeof($items) == $total_close ? 'success' : 'danger' }} mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
                {{ sizeof($items) == $total_close ? 'La operación ya se encuentra cerrada' : 'La operación esta activa' }}.
            </div>
        </div>
    @endif
    <input type="hidden" value='{{ json_encode($types_cancellations) }}' id="types_cancellations">

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 layout-spacing">
        <div class="btn-group" role="group" aria-label="options">
            <button type="button" class="btn btn-primary" data-title="Filtros de operación" data-bs-toggle="modal" data-bs-target="#filterModal"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros</button>
            <button type="button" class="btn btn-primary __btn_columns" title="Administrar columnas" data-title="columnas de operaciones" data-bs-toggle="modal" data-bs-target="#columnsModal" data-table="bookings" data-container="columns"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="layout-columns" class=""><path fill="" fill-rule="evenodd" d="M7 5a2 2 0 00-2 2v10a2 2 0 002 2h1V5H7zm3 0v14h4V5h-4zm6 0v14h1a2 2 0 002-2V7a2 2 0 00-2-2h-1zM3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" clip-rule="evenodd"></path></svg> Administrar columnas</button>
            <button type="button" class="btn btn-primary" title="Ver graficas" id="showLayer"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Ver graficas</button>

            @if ( auth()->user()->hasPermission2(80) )
                <button type="button" class="btn btn-primary" data-title="Agregar nuevo servicio" data-bs-toggle="modal" data-bs-target="#operationModal"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Agregar nuevo servicio</button>
            @endif            
            @if ( auth()->user()->hasPermission2(82) )
                <button type="button" class="btn btn-primary" id="btn_preassignment"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Pre-asignación</button>
            @endif
            @if ( auth()->user()->hasPermission2(83) )
                <button type="button" class="btn btn-primary" id="btn_dowload_operation"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Descargar operación</button>
            @endif
            @if ( auth()->user()->hasPermission2(84) )
                <button type="button" class="btn btn-primary" id="btn_dowload_operation_comission"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Descargar comisiones de operación</button>
            @endif
            <button type="button" class="btn btn-danger" id="getSchedules"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-watch"><circle cx="12" cy="12" r="7"></circle><polyline points="12 9 12 12 13.5 13.5"></polyline><path d="M16.51 17.35l-.35 3.83a2 2 0 0 1-2 1.82H9.83a2 2 0 0 1-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 0 1 9.83 1h4.35a2 2 0 0 1 2 1.82l.35 3.83"></path></svg> Ver horario de conductores</button>
            @if ( auth()->user()->hasPermission2(85) )
                <button type="button" class="btn btn-danger" id="btn_close_operation"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> Cerrar operación</button>
            @endif
        </div>        
    </div>

    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 layout-spacing">
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

            {{-- <div class="row layout-top-spacing mb-3">
                <div class="col-md-12">
                    <ul class="nav nav-pills" id="animateLine" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="animated-underline-private-tab" data-bs-toggle="tab" href="#animated-underline-private" role="tab" aria-controls="animated-underline-private" aria-selected="false" tabindex="-1"> Operación privada</button>
                        </li>                
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="animated-underline-shared-tab" data-bs-toggle="tab" href="#animated-underline-shared" role="tab" aria-controls="animated-underline-shared" aria-selected="false" tabindex="-1"> Operación compartida</button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content" id="animateLineContent-4">
                <div class="tab-pane fade show active" id="animated-underline-private" role="tabpanel" aria-labelledby="animated-underline-badge-private">                    
                </div>
            </div> --}}

            <table id="dataManagementOperations" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">INDICADORES</th>
                        <th class="text-center">HORA</th>
                        <th class="text-center">CLIENTE</th>
                        <th class="text-center">TIPO DE SERVICIO</th>
                        <th class="text-center">PAX</th>
                        <th class="text-center">ORIGEN</th>
                        <th class="text-center">DESTINO</th>
                        <th class="text-center">AGENCIA</th>
                        <th class="text-center">UNIDAD</th>
                        <th class="text-center">CONDUCTOR</th>
                        <th class="text-center">ESTATUS DE OPERACIÓN</th>
                        <th class="text-center">HORA OPERACIÓN</th>
                        <th class="text-center">COSTO OPERATIVO</th>
                        <th class="text-center">ESTATUS DE SERVICIO</th>
                        <th class="text-center">CÓDIGO</th>
                        <th class="text-center">VEHÍCULO</th>
                        <th class="text-center">PAGO</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">MONEDA</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($items)>=1)
                        @foreach($items as $key => $value)
                            @php
                                // dump($value);
                                //DECLARAMOS VARIABLES DE IDENTIFICADORES
                                //SABER SI SON ARRIVAL, DEPARTURE O TRANSFER, MEDIANTE UN COLOR DE FONDO
                                $background_color = "background-color: #".( $value->final_service_type == 'ARRIVAL' ? "ddf5f0" : ( $value->final_service_type == 'TRANSFER' ? "f2eafa" : "dbe0f9" ) ).";";
                                // $color = "color: #".( $value->site_code == 29 || $value->site_code == 30 ? "FFFFFF" : "515365" ).";";
                                $class_agency = ( $value->site_code == 29 || $value->site_code == 30 ? "agency_".$value->site_code : ( $value->is_open ? "is_open" : "" ) );

                                //PREASIGNACION
                                $flag_preassignment = ( ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ) && $value->op_one_preassignment != "" ? true : ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && ( $value->is_round_trip == 1 ) && $value->op_two_preassignment != "" ? true : false ) );
                                $flag_comment =       ( ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ) && $value->op_one_comments != "" ? true : ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && ( $value->is_round_trip == 1 ) && $value->op_two_comments != "" ? true : false ) );

                                $preassignment = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_preassignment : $value->op_two_preassignment );
                                $comment =       ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_comments : $value->op_two_comments );

                                $vehicle_d = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->vehicle_id_one : $value->vehicle_id_two );
                                $driver_d =  ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->driver_id_one : $value->driver_id_two );                                
                                $close_operation = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_operation_close : $value->op_two_operation_close );

                                //LOGISTICA PARA GRAFICAS
                                    // Obtener la hora formateada
                                    $time = date("H:i", strtotime(auth()->user()->setDateTime($value, "null"))); //EXTRAEMOS LA HORA DE LA FECHA
                                    $hour = date("H", strtotime(auth()->user()->setDateTime($value, "null"))); //EXTRAEMOS LA HORA                                
                                    $minutes = date("i", strtotime(auth()->user()->setDateTime($value, "null"))); //EXTRAEMOS LOS SEGUNDOS

                                    // Agrupar por intervalo de 15 minutos
                                    if ($minutes < 15) {
                                        $index = $hour . ':00';
                                    } elseif ($minutes < 30) {
                                        $index = $hour . ':15';
                                    } elseif ($minutes < 45) {
                                        $index = $hour . ':30';
                                    } else {
                                        $index = $hour . ':45';
                                    }

                                    // Agregar al arreglo agrupado
                                    if( $value->final_service_type == "ARRIVAL" ){
                                        if ( !isset($arrivalTimeGroup[$hour]) ) {
                                            $arrivalTimeGroup[$hour] = [
                                                'name' => $hour,
                                                'quantity' => 0,  // Contador
                                            ];
                                        }
                                        $arrivalTimeGroup[$hour]['quantity']++;                                        
                                    }
                                    if( $value->final_service_type == "DEPARTURE" || $value->final_service_type == "TRANSFER" ){
                                        if ( !isset($departureTimeGroup[$hour]) ) {
                                            $departureTimeGroup[$hour] = [
                                                'name' => $hour,
                                                'quantity' => 0,  // Contador
                                            ];
                                        }
                                        $departureTimeGroup[$hour]['quantity']++;
                                    }
                                    if( $value->final_service_type == "ARRIVAL" || $value->final_service_type == "DEPARTURE" || $value->final_service_type == "TRANSFER" ){
                                        // Inicializar el índice si no existe
                                        if (!isset($generalTimeGroup[$hour])) {
                                            $generalTimeGroup[$hour] = [
                                                'name' => $hour,
                                                'quantity' => 0,  // Contador
                                            ];
                                        }
                                        $generalTimeGroup[$hour]['quantity']++;
                                    }
                            @endphp
                            <tr class="item-{{ $key.$value->id }} {{ $class_agency }}" id="item-{{ $key.$value->id }}" data-payment-method="{{ $value->payment_type_name }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-close_operation="{{ $close_operation }}" style="{{ $background_color }}">
                                <td class="text-center">
                                    @if ( $flag_preassignment )
                                        <button type="button" class="btn btn-<?=( $value->final_service_type == 'ARRIVAL' ? 'success' : ( $value->final_service_type == 'DEPARTURE' ? 'primary' : 'info' ) )?> btn_operations text-uppercase {{ auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 ? 'disabled' : '' }}">{{ $preassignment }}</button>
                                    @else
                                        <button type="button" class="btn btn-danger text-uppercase btn_operations {{ auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 ? 'disabled' : 'add_preassignment' }}" id="btn_preassignment_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}">ADD</button>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-2 w-100">
                                        <div class="d-flex flex-column gap-2 comment-default">
                                            @if ( !empty($value->messages) )
                                                <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip w-100" title="Ver historial de reservación" data-type="history" data-code="{{ $value->reservation_id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                </div>
                                            @endif
                                            <div class="btn btn-primary btn_operations __open_modal_customer bs-tooltip w-100" title="Ver datos del cliente" data-code="{{ $value->reservation_id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                            </div>
                                            @if ( $value->is_round_trip == 1 && $value->final_service_type == "DEPARTURE" && ( $value->one_service_status == "CANCELLED" || $value->one_service_status == "NOSHOW" ) )
                                                <div class="btn btn-primary btn_operations active notifications bell-button bs-tooltip w-100" data-json="<?=json_encode($value)?>" onclick="notificationInfo({{ json_encode($value) }})" title="Por favor de confirmar el regreso con el cliente"> 
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>                                                    
                                                </div>                                                
                                            @endif
                                        </div>
                                        <div class="comment_new" id="comment_new_{{ $key.$value->id }}">
                                            @if ( $flag_comment )
                                                <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip w-100" title="Ver mensaje de operaciones" data-type="comment" data-comment="{{ $comment }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="upload_new" id="upload_new_{{ $key.$value->id }}">
                                            @if ( !empty($value->pictures) )
                                                <div class="btn btn-primary btn_operations __open_modal_media bs-tooltip w-100" title="Esta reservación tiene imagenes" data-code="{{ $value->reservation_id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                </div>
                                            @endif
                                        </div>                                        
                                    </div>
                                </td>
                                <td class="text-center">{{ auth()->user()->setDateTime($value, "time") }}</td>
                                <td class="text-center">
                                    <span>{{ $value->full_name }}</span>
                                    @if(!empty($value->reference))
                                        [{{ $value->reference }}]
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $value->final_service_type }}
                                    @if ( $value->final_service_type == "ARRIVAL" )
                                        <span class="badge badge-{{ $value->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $value->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $value->passengers }}</td>
                                <td class="text-center" <?=auth()->user()->classCutOffZone($value)?>>{{ auth()->user()->setFrom($value, "name") }} {{ $value->operation_type == 'arrival' && !empty($value->flight_number) ? ' ('.$value->flight_number.')' : '' }}</td>
                                <td class="text-center">{{ auth()->user()->setTo($value, "name") }}</td>
                                <td class="text-center">{{ $value->site_name }}</td>
                                <td class="text-center" data-order="{{ ( $vehicle_d != NULL ) ? $vehicle_d : 0 }}" data-name="{{ auth()->user()->setOperationUnit($value) }}">
                                    @if ( auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 )
                                        {{ auth()->user()->setOperationUnit($value) }}
                                    @else
                                        <select class="form-control vehicles selectpicker" data-live-search="true" id="vehicle_id_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}">
                                            <option value="0">Selecciona un vehículo</option>
                                            @if ( isset($units2) && count($units2) >= 1 )
                                                @foreach ($units2 as $unit)
                                                    <option {{ ( $vehicle_d != NULL && $vehicle_d == $unit->id ) ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->destination_service->name }} - {{ $unit->enterprise->names }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </td>
                                <td class="text-center" data-order="{{ ( $driver_d != NULL ) ? $driver_d : 0 }}" data-name="{{ auth()->user()->setOperationDriver($value) }}">
                                    @if ( auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 )
                                        {{  auth()->user()->setOperationDriver($value) }}
                                    @else
                                        <select class="form-control drivers selectpicker" data-live-search="true" id="driver_id_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}">
                                            <option value="0">Selecciona un conductor</option>
                                            @if ( isset($drivers2) && count($drivers2) >= 1 )
                                                @foreach ($drivers2 as $driver)
                                                    <option {{ ( $driver_d != NULL && $driver_d == $driver->id ) ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ( auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 )
                                        <?=auth()->user()->renderOperationStatus($value)?>
                                    @else
                                        <?=auth()->user()->renderOperationOptionsStatus($key,$value)?>
                                    @endif
                                </td>
                                <td class="text-center"><?=auth()->user()->setOperationTime($value)?></td>
                                <td class="text-center"><?=auth()->user()->setOperatingCost($value)?></td>
                                <td class="text-center">
                                    @if ( auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 )
                                        <?=auth()->user()->renderServiceStatus($value)?>
                                    @else
                                        <?=auth()->user()->renderServiceOptionsStatus($key,$value)?>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (auth()->user()->hasPermission2(38))
                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                    @else
                                        {{ $value->code }}
                                    @endif
                                </td>
                                <td class="text-center" style="{{ ( $value->service_type_name == "Suburban" ? 'background-color:#e2a03f;color:#fff;' : '' ) }}">{{ $value->service_type_name }}</td>
                                <td class="text-center" <?=auth()->user()->classStatusPayment($value)?>>{{ auth()->user()->statusPayment($value->payment_status) }}</td>
                                <td class="text-center" >{{ number_format(( $value->pay_at_arrival == 1 || ( $value->pay_at_arrival == 0 && $value->payment_status == "PENDING" ) ? $value->total_sales : 0 ),2) }}</td>
                                <td class="text-center">{{ $value->currency }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="btn btn-primary btn_operations {{ auth()->user()->hasPermission2(78) || auth()->user()->hasPermission2(79) || $close_operation == 1 ? 'disabled' : '__open_modal_comment' }} bs-tooltip" title="{{ ( $flag_comment ) ? 'Modificar comentario' : 'Agregar comentario' }}" id="btn_add_modal_{{ $key.$value->id }}" data-status="{{ ( $flag_comment ) ? 1 : 0 }}" data-reservation="{{ $value->reservation_id }}" data-id="{{ $key.$value->id }}" data-code="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-type="{{ $value->op_type }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        </div>
                                        <div class="btn btn-primary btn_operations extract_whatsapp bs-tooltip" title="Ver información para enviar por whatsApp" id="extract_whatsapp{{ $key.$value->id }}" data-bs-toggle="modal" data-bs-target="#operationWhatsAppModal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                        </div>
                                        @if ( $value->final_service_type == "ARRIVAL" )
                                            <div class="btn btn-primary btn_operations extract_confirmation bs-tooltip" title="Ver información de confirmación" id="extract_confirmation{{ $key.$value->id }}" data-bs-toggle="modal" data-language="{{ $value->language }}" data-bs-target="#confirmationModal">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="layer" id="layer">
        <div class="header-chart d-flex justify-content-between">
            <div class="btn_close">                
                <button class="btn btn-primary" id="closeLayer">Cerrar</button>
            </div>
        </div>
        <div class="body-chart">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <h5 class="col-12 text-left text-uppercase">Gráfica general de servicios por hora</h5>
                    <canvas class="chartSale" id="chartGeneral" height="150"></canvas>
                </div>
                <hr>
                <div class="col-lg-6 col-12">
                    <h5 class="col-12 text-left text-uppercase">Gráfica de llegadas por hora</h5>
                    <canvas class="" id="chartArrival" height="200"></canvas>
                </div>
                <div class="col-lg-6 col-12">
                    <h5 class="col-12 text-left text-uppercase">Gráfica de salidas por hora</h5>
                    <canvas class="" id="chartDeparture" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :websites="$websites" :units="$units" :drivers="$drivers" :reservationstatus="$reservation_status" />
    <x-modals.reports.columns />

    <x-modals.management.operations.schedules />

    <x-modals.reservations.operation_create :websites="$websites" :zones="$zones" :vehicles="$vehicles" />
    <x-modals.reservations.comments /> <!-- MODAL PARA PODER AGREGAR COMENTARIO DE OPERACION Y IMAGENES -->
    <x-modals.reservations.operation_messages_history /> <!-- HISTORIAL DE MENSAJES DE LA RESERVACION -->
    <x-modals.reservations.operation_media_history /> <!-- HISTORIAL DE MEDIA DE LA RESERVACION -->
    <x-modals.reservations.operation_data_customer /> <!-- INFORMACIÓN DEL CLIENTE -->
    <x-modals.reservations.operation_confirmations />
    <x-modals.reservations.operation_data_whatsapp />    
@endsection

@push('Js')
    <script>
        let graphics = {
            dataGeneral: @json(( isset($generalTimeGroup) ? $generalTimeGroup : [] )),
            dataArrival: @json(( isset($arrivalTimeGroup) ? $arrivalTimeGroup : [] )),
            dataDeparture: @json(( isset($departureTimeGroup) ? $departureTimeGroup : [] )),
            dataChartGeneral: function(){
                let object = [];                    
                // Obtiene y ordena las claves en orden ascendente
                const generalKeys = Object.keys(this.dataGeneral).sort((a, b) => {
                    if (!isNaN(a) && !isNaN(b)) {
                        // Si son numéricos, los convierte para ordenar correctamente
                        return parseFloat(a) - parseFloat(b);
                    }
                    // Si no son numéricos, usa orden lexicográfico
                    return a.localeCompare(b);
                });
                generalKeys.forEach( (key) => {
                    object.push(this.dataGeneral[key]);
                });
                // const systems = Object.entries(this.dataArrival);
                // systems.forEach( ([key, data]) => {
                //     // console.log(key);
                //     // console.log(data);
                //     object.push(data);
                // });
                return object;
            },
            dataChartArrival: function(){
                let object = [];                    
                // Obtiene y ordena las claves en orden ascendente
                const generalKeys = Object.keys(this.dataArrival).sort((a, b) => {
                    if (!isNaN(a) && !isNaN(b)) {
                        // Si son numéricos, los convierte para ordenar correctamente
                        return parseFloat(a) - parseFloat(b);
                    }
                    // Si no son numéricos, usa orden lexicográfico
                    return a.localeCompare(b);
                });
                generalKeys.forEach( (key) => {
                    object.push(this.dataArrival[key]);
                });
                return object;
            },
            dataChartDeparture: function(){
                let object = [];                    
                // Obtiene y ordena las claves en orden ascendente
                const generalKeys = Object.keys(this.dataDeparture).sort((a, b) => {
                    if (!isNaN(a) && !isNaN(b)) {
                        // Si son numéricos, los convierte para ordenar correctamente
                        return parseFloat(a) - parseFloat(b);
                    }
                    // Si no son numéricos, usa orden lexicográfico
                    return a.localeCompare(b);
                });
                generalKeys.forEach( (key) => {
                    object.push(this.dataDeparture[key]);
                });
                return object;
            },
        };

        // console.log(graphics.dataChartGeneral());

        if( document.getElementById('chartGeneral') != null ){
            // Tu arreglo de datos
            const dataGeneral = graphics.dataChartGeneral();
            // Extraer etiquetas (eje X) y datos (eje Y)
            const labelsG = dataGeneral.map(item => item.name);
            const quantitiesG = dataGeneral.map(item => item.quantity);
            // Configuración de la gráfica
            const ctxG = document.getElementById('chartGeneral');
            const myChartG = new Chart(ctxG, {
                type: 'bar', // Tipo de gráfica
                data: {
                    labels: labelsG, // Etiquetas del eje X
                    datasets: [{
                        label: 'Cantidad',
                        data: quantitiesG, // Datos del eje Y
                        // backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de fondo
                        // borderColor: 'rgba(75, 192, 192, 1)', // Color del borde
                        // borderWidth: 1 // Grosor del borde
                    }]
                },
                options: {
                    responsive: true, // Se adapta al tamaño del contenedor
                    maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda
                            display: false  // Oculta la leyenda
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true // Iniciar eje Y en 0
                        }
                    }
                }
            });
        }

        if( document.getElementById('chartArrival') != null ){
            // Tu arreglo de datos
            const dataArrival = graphics.dataChartArrival();
            // Extraer etiquetas (eje X) y datos (eje Y)
            const labelsA = dataArrival.map(item => item.name);
            const quantitiesA = dataArrival.map(item => item.quantity);
            // Configuración de la gráfica
            const ctxA = document.getElementById('chartArrival');
            const myChartA = new Chart(ctxA, {
                type: 'bar', // Tipo de gráfica
                data: {
                    labels: labelsA, // Etiquetas del eje X
                    datasets: [{
                        label: 'Cantidad',
                        data: quantitiesA, // Datos del eje Y
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de fondo
                        borderColor: 'rgba(75, 192, 192, 1)', // Color del borde
                        borderWidth: 1 // Grosor del borde
                    }]
                },
                options: {
                    responsive: true, // Se adapta al tamaño del contenedor
                    maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda
                            display: false  // Oculta la leyenda
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true // Iniciar eje Y en 0
                        }
                    }
                }
            });
        }
        
        if( document.getElementById('chartDeparture') != null ){
            // Tu arreglo de datos
            const dataDeparture = graphics.dataChartDeparture();
            // Extraer etiquetas (eje X) y datos (eje Y)
            const labelsD = dataDeparture.map(item => item.name);
            const quantitiesD = dataDeparture.map(item => item.quantity);
            // Configuración de la gráfica
            const ctxD = document.getElementById('chartDeparture');
            const myChartD = new Chart(ctxD, {
                type: 'bar', // Tipo de gráfica
                data: {
                    labels: labelsD, // Etiquetas del eje X
                    datasets: [{
                        label: 'Cantidad',
                        data: quantitiesD, // Datos del eje Y
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Color de fondo
                        borderColor: 'rgba(75, 192, 192, 1)', // Color del borde
                        borderWidth: 1 // Grosor del borde
                    }]
                },
                options: {
                    responsive: true, // Se adapta al tamaño del contenedor
                    maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                    plugins: {
                        legend: {
                            position: 'top', // Posición de la leyenda
                            display: false  // Oculta la leyenda
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true // Iniciar eje Y en 0
                        }
                    }
                }
            });
        }
    </script>
@endpush