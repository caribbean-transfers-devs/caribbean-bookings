@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.custom')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/operations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/operations.min.css') }}" rel="stylesheet" >
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
        .agency_29{
            background-color: #FE7A1F !important;
        }
        .agency_30{
            background-color: #00467e !important;
        }

        .agency_29 td{
            color: #000000 !important;
        }
        .agency_30 td{
            color: #ffffff !important;
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
    <script src="https://cdn.socket.io/4.4.1/socket.io.min.js"></script>
    <script src="{{ mix('assets/js/sections/operations/operations.min.js') }}"></script>
@endpush

@section('content')
    @php
        $total_close = 0;
        $buttons = [];        
        if( sizeof($privates) >= 1 || sizeof($shareds) >= 1 ):
            $operations = array_merge($privates, $shareds);
            foreach ($operations as $operation) {
                $close_operation = ( ( ( $operation->final_service_type == 'ARRIVAL' || $operation->final_service_type == 'TRANSFER' || $operation->final_service_type == 'DEPARTURE' ) && $operation->op_type == "TYPE_ONE" && ( $operation->is_round_trip == 0 || $operation->is_round_trip == 1 ) ) ? $operation->op_one_operation_close : $operation->op_two_operation_close );
                ( $close_operation == 1 ? $total_close++ : "" );
            }
        endif;
    @endphp
    @if (RoleTrait::hasPermission(79))
        <input type="hidden" class="" id="permission_reps" value="true" required>
    @endif

    @if ( sizeof($operations) >= 1 )
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="alert alert-arrow-right alert-icon-right alert-light-{{ sizeof($operations) == $total_close ? 'success' : 'danger' }} mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
                {{ sizeof($operations) == $total_close ? 'La operación ya se encuentra cerrada' : 'La operación esta activa' }}.
            </div>
        </div>
    @endif

    {{-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 layout-spacing"> --}}
        {{-- <div class="widget-content widget-content-area br-8"> --}}
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

            <div class="layout-top-spacing">
                <div class="col-md-12">                        
                    <div class="card">
                        <div class="card-body p-2">
                            <button class="btn btn-primary" data-title="Filtros de operación" data-bs-toggle="modal" data-bs-target="#filtersOperationModal">Filtros</button>
                            @if (RoleTrait::hasPermission(80))
                                <button class="btn btn-primary" data-title="Agregar nuevo servicio" data-bs-toggle="modal" data-bs-target="#operationModal">Agregar nuevo servicio</button>
                            @endif
                            @if (RoleTrait::hasPermission(82))
                                <button class="btn btn-primary" id="btn_preassignment">Pre-asignación</button>
                            @endif
                            @if (RoleTrait::hasPermission(83))
                                <button class="btn btn-primary" id="btn_dowload_operation">Descargar operación</button>
                            @endif
                            @if (RoleTrait::hasPermission(84))
                                <button class="btn btn-primary" id="btn_dowload_operation_comission">Descargar comisiones de operación</button>
                            @endif
                            @if (RoleTrait::hasPermission(85))
                                <button class="btn btn-danger" id="btn_close_operation">Cerrar operación</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="layout-top-spacing mb-3">
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
                    <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                        <div class="section general-info">
                            <div class="row info">
                                <table id="operation-private" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>INDICADORES</th>
                                            <th>HORA</th>
                                            <th>CLIENTE</th>
                                            <th class="text-center">TIPO DE SERVICIO</th>
                                            <th>PAX</th>
                                            <th>ORIGEN</th>
                                            <th>DESTINO</th>
                                            <th>AGENCIA</th>
                                            <th>UNIDAD</th>
                                            <th>CONDUCTOR</th>
                                            <th class="text-center">ESTATUS OPERACIÓN</th>
                                            <th class="text-center">HORA OPERACIÓN</th>
                                            <th class="text-center">COSTO OPERATIVO</th>
                                            <th class="text-center">ESTATUS RESERVACIÓN</th>
                                            <th>CÓDIGO</th>
                                            <th>VEHÍCULO</th>
                                            <th>PAGO</th>
                                            <th>TOTAL</th>
                                            <th>MONEDA</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if( sizeof($privates) >= 1 )
                                            @foreach($privates as $key => $private)
                                                @php
                                                    // dump($private);
                                                    //DECLARAMOS VARIABLES DE IDENTIFICADORES
                                                    //SABER SI SON ARRIVAL, DEPARTURE O TRANSFER, MEDIANTE UN COLOR DE FONDO
                                                    $background_color = "background-color: #".( $private->final_service_type == 'ARRIVAL' ? "ddf5f0" : ( $private->final_service_type == 'TRANSFER' ? "f2eafa" : "dbe0f9" ) ).";";
                                                    // $color = "color: #".( $private->site_code == 29 || $private->site_code == 30 ? "FFFFFF" : "515365" ).";";
                                                    $class_agency = ( $private->site_code == 29 || $private->site_code == 30 ?  "agency_".$private->site_code : "" );
                    
                                                    //SABER EL NIVEL DE CUT OFF
                                                    $cut_off_zone = ( $private->final_service_type == 'ARRIVAL' || ( ( $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->zone_one_cut_off : $private->zone_two_cut_off );
                    
                                                    // $payment = ( $private->total_sales - $private->total_payments );
                                                    // if($payment < 0) $payment = 0;
                                                    // $payment = $private->total_sales;
                    
                                                    //PREASIGNACION
                                                    $flag_preassignment = ( ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ) && $private->op_one_preassignment != "" ? true : ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && ( $private->is_round_trip == 1 ) && $private->op_two_preassignment != "" ? true : false ) );
                                                    $flag_comment =       ( ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ) && $private->op_one_comments != "" ? true : ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && ( $private->is_round_trip == 1 ) && $private->op_two_comments != "" ? true : false ) );
                    
                                                    $preassignment = ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->op_one_preassignment : $private->op_two_preassignment );
                                                    $comment =       ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->op_one_comments : $private->op_two_comments );
                    
                                                    //ESTATUS
                                                    $status_operation = ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->one_service_operation_status : $private->two_service_operation_status );
                                                    $time_operation =   ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->op_one_time_operation : $private->op_two_time_operation );
                                                    $cost_operation =   ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->op_one_operating_cost : $private->op_two_operating_cost );
                                                    $status_booking =   ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->one_service_status : $private->two_service_status );
                    
                                                    $operation_pickup = (($private->operation_type == 'arrival')? $private->pickup_from : $private->pickup_to );
                                                    $operation_from = (($private->operation_type == 'arrival')? $private->from_name.( (!empty($private->flight_number)) ? ' ('.$private->flight_number.')' : '' )  : $private->to_name );
                                                    $operation_to = (($private->operation_type == 'arrival')? $private->to_name : $private->from_name );
                    
                                                    $vehicle_d = ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->vehicle_id_one : $private->vehicle_id_two );
                                                    $driver_d =  ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->driver_id_one : $private->driver_id_two );                                
                                                    $close_operation = ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? $private->op_one_operation_close : $private->op_two_operation_close );
                    
                                                    $vehicle_name = ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? ( $private->vehicle_one_name != null ? $private->vehicle_one_name : 'Selecciona un vehículo' ) : ( $private->vehicle_two_name != null ? $private->vehicle_two_name : 'Selecciona un vehículo' ) );
                                                    $driver_name =  ( ( ( $private->final_service_type == 'ARRIVAL' || $private->final_service_type == 'TRANSFER' || $private->final_service_type == 'DEPARTURE' ) && $private->op_type == "TYPE_ONE" && ( $private->is_round_trip == 0 || $private->is_round_trip == 1 ) ) ? ( $private->driver_one_name != null ? $private->driver_one_name : 'Selecciona un conductor' ) : ( $private->driver_two_name != null ? $private->driver_two_name : 'Selecciona un conductor' ) );
                    
                                                    switch ($status_operation) {
                                                        case 'E':
                                                            $label = 'info';
                                                            break;
                                                        case 'C':
                                                            $label = 'warning';
                                                            break;
                                                        case 'OK':
                                                            $label = 'success';
                                                            break;
                                                        default:
                                                            $label = 'secondary';
                                                            break;
                                                    }
                    
                                                    switch ($status_booking) {
                                                        case 'COMPLETED':
                                                            $label2 = 'success';
                                                            break;
                                                        case 'NOSHOW':
                                                            $label2 = 'warning';
                                                            break;
                                                        case 'CANCELLED':
                                                            $label2 = 'danger';
                                                            break;
                                                        default:
                                                            $label2 = 'secondary';
                                                            break;
                                                    }
                                                @endphp
                                                <tr class="item-{{ $key.$private->id }} {{ $class_agency }}" id="item-{{ $key.$private->id }}" data-payment-method="{{ $private->payment_type_name }}" data-reservation="{{ $private->reservation_id }}" data-item="{{ $private->id }}" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-close_operation="{{ $close_operation }}" style="{{ $background_color }}">
                                                    <td>
                                                        @if ( $flag_preassignment )
                                                            <button type="button" class="btn btn-<?=( $private->final_service_type == 'ARRIVAL' ? 'success' : ( $private->final_service_type == 'DEPARTURE' ? 'primary' : 'info' ) )?> btn_operations text-uppercase {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">{{ $preassignment }}</button>
                                                        @else
                                                            <button type="button" class="btn btn-danger text-uppercase btn_operations {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : 'add_preassignment' }}" id="btn_preassignment_{{ $key.$private->id }}" data-id="{{ $key.$private->id }}" data-reservation="{{ $private->reservation_id }}" data-item="{{ $private->id }}" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}">ADD</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2 w-100">
                                                            <div class="d-flex gap-2 comment-default">
                                                                @if ( !empty($private->messages) )
                                                                    <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver historial de reservación" data-type="history" data-code="{{ $private->reservation_id }}">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                                    </div>
                                                                @endif
                                                                <div class="btn btn-primary btn_operations __open_modal_customer bs-tooltip" title="Ver datos del cliente" data-code="{{ $private->reservation_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                                </div>
                                                            </div>
                                                            <div class="comment_new" id="comment_new_{{ $key.$private->id }}">
                                                                @if ( $flag_comment )
                                                                    <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver mensaje de operaciones" data-type="comment" data-comment="{{ $comment }}">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="upload_new" id="upload_new_{{ $key.$private->id }}">
                                                                @if ( !empty($private->pictures) )
                                                                    <div class="btn btn-primary btn_operations bs-tooltip" title="Esta reservación tiene imagenes">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                                    </div>
                                                                @endif
                                                            </div>                                        
                                                        </div>
                                                    </td>
                                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>                                    
                                                    <td>
                                                        <span>{{ $private->full_name }}</span>
                                                        @if(!empty($private->reference))
                                                            [{{ $private->reference }}]
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $private->final_service_type }}
                                                        @if ( $private->final_service_type == "ARRIVAL" )
                                                            <span class="badge badge-{{ $private->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $private->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $private->passengers }}</td>
                                                    <td style="{{ ( $cut_off_zone >= 3 ? 'background-color:#e2a03f;color:#fff;' : ( $cut_off_zone >= 2 && $cut_off_zone < 3 ? 'background-color:#805dca;color:#fff;' : '' ) ) }}">{{ $operation_from }}</td>
                                                    <td>{{ $operation_to }}</td>
                                                    <td>{{ $private->site_name }}</td>
                                                    <td data-order="{{ ( $vehicle_d != NULL ) ? $vehicle_d : 0 }}" data-name="{{ $vehicle_name }}">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            {{ $vehicle_name }}
                                                        @else
                                                            <select class="form-control vehicles selectpicker" data-live-search="true" id="vehicle_id_{{ $key.$private->id }}" data-id="{{ $key.$private->id }}" data-reservation="{{ $private->reservation_id }}" data-item="{{ $private->id }}" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}">
                                                                <option value="0">Selecciona un vehículo</option>
                                                                @if ( isset($vehicles) && count($vehicles) >= 1 )
                                                                    @foreach ($units as $unit)
                                                                        <option {{ ( $vehicle_d != NULL && $vehicle_d == $unit->id ) ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->destination_service->name }} - {{ $unit->enterprise->names }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        @endif
                                                    </td>
                                                    <td data-order="{{ ( $driver_d != NULL ) ? $driver_d : 0 }}" data-name="{{ $driver_name }}">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            {{ $driver_name }}
                                                        @else
                                                            <select class="form-control drivers selectpicker" data-live-search="true" id="driver_id_{{ $key.$private->id }}" data-id="{{ $key.$private->id }}" data-reservation="{{ $private->reservation_id }}" data-item="{{ $private->id }}" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}">
                                                                <option value="0">Selecciona un conductor</option>
                                                                @if ( isset($drivers) && count($drivers) >= 1 )
                                                                    @foreach ($drivers as $driver)
                                                                        <option {{ ( $driver_d != NULL && $driver_d == $driver->id ) ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsOperation{{ $key.$private->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle btn_status_action {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">
                                                                    <span>{{ $status_operation }}</span>
                                                                </button>
                                                            </div>                                      
                                                        @else
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsOperation{{ $key.$private->id }}" data-item="{{ $key.$private->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span>{{ $status_operation }}</span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="optionsOperation{{ $key.$private->id }}">
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="PENDING" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="E" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="C" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="OK" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation }}</td>
                                                    <td class="text-center">{{ $cost_operation }}</td>
                                                    <td class="text-center">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsBooking{{ $key.$private->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle btn_status_action {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">
                                                                    <span>{{ $status_booking }}</span>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsBooking{{ $key.$private->id }}" data-item="{{ $key.$private->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span>{{ $status_booking }}</span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="optionsBooking{{ $key.$private->id }}">
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="PENDING" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="COMPLETED" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="NOSHOW" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $private->final_service_type }}" data-service="{{ $private->operation_type }}" data-type="{{ $private->op_type }}" data-status="CANCELLED" data-item="{{ $private->id }}" data-booking="{{ $private->reservation_id }}" data-key="{{ $key.$private->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (RoleTrait::hasPermission(38))
                                                            <a href="/reservations/detail/{{ $private->reservation_id }}">{{ $private->code }}</a>
                                                        @else
                                                            {{ $private->code }}
                                                        @endif
                                                    </td>
                                                    <td style="{{ ( $private->service_type_name == "Suburban" ? 'background-color:#e2a03f;color:#fff;' : '' ) }}">{{ $private->service_type_name }}</td>
                                                    <td class="text-center" style="{{ ( $private->reservation_status == "PENDING" || $private->reservation_status == "PENDIENTE" || ( $private->reservation_status == "CONFIRMADO" && $private->payment_type_name == "CASH" ) ? 'background-color:#e7515a;' : 'background-color:#00ab55;' ) }}color:#fff;">{{ $private->reservation_status }}</td>
                                                    <td class="text-end" style="{{ ( $private->reservation_status == "PENDING" || $private->reservation_status == "PENDIENTE" || ( $private->reservation_status == "CONFIRMADO" && $private->payment_type_name == "CASH" ) ? 'background-color:#e7515a;' : 'background-color:#00ab55;' ) }}color:#fff;">{{ number_format($private->total_sales,2) }}</td>
                                                    <td class="text-center">{{ $private->currency }}</td>
                                                    <td class="text-center">
                                                        <div class="d-flex gap-3">
                                                            <div class="btn btn-primary btn_operations {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '__open_modal_comment' }} bs-tooltip" title="{{ ( $flag_comment ) ? 'Modificar comentario' : 'Agregar comentario' }}" id="btn_add_modal_{{ $key.$private->id }}" data-status="{{ ( $flag_comment ) ? 1 : 0 }}" data-reservation="{{ $private->reservation_id }}" data-id="{{ $key.$private->id }}" data-code="{{ $private->id }}" data-operation="{{ $private->final_service_type }}" data-type="{{ $private->op_type }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                            </div>
                                                            <div class="btn btn-primary btn_operations extract_whatsapp bs-tooltip" title="Ver información para enviar por whatsApp" id="extract_whatsapp{{ $key.$private->id }}" data-bs-toggle="modal" data-bs-target="#operationWhatsAppModal">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                                            </div>
                                                            @if ( $private->final_service_type == "ARRIVAL" )
                                                                <div class="btn btn-primary btn_operations extract_confirmation bs-tooltip" title="Ver información de confirmación" id="extract_confirmation{{ $key.$private->id }}" data-bs-toggle="modal" data-language="{{ $private->language }}" data-bs-target="#confirmationModal">
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
                    </div>
                </div>
                <div class="tab-pane fade" id="animated-underline-shared" role="tabpanel" aria-labelledby="animated-underline-badge-shared">
                    <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                        <div class="section general-info">
                            <div class="row info">
                                <table id="operation-shared" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>INDICADORES</th>
                                            <th>HORA</th>
                                            <th>CLIENTE</th>
                                            <th class="text-center">TIPO DE SERVICIO</th>
                                            <th>PAX</th>
                                            <th>ORIGEN</th>
                                            <th>DESTINO</th>
                                            <th>AGENCIA</th>
                                            <th>UNIDAD</th>
                                            <th>CONDUCTOR</th>
                                            <th class="text-center">ESTATUS OPERACIÓN</th>
                                            <th class="text-center">HORA OPERACIÓN</th>
                                            <th class="text-center">COSTO OPERATIVO</th>
                                            <th class="text-center">ESTATUS RESERVACIÓN</th>
                                            <th>CÓDIGO</th>
                                            <th>VEHÍCULO</th>
                                            <th>PAGO</th>
                                            <th>TOTAL</th>
                                            <th>MONEDA</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if( sizeof($shareds) >=1 )
                                            @foreach($shareds as $key => $shared)
                                                @php
                                                    // dump($shared);
                                                    //DECLARAMOS VARIABLES DE IDENTIFICADORES
                                                    //SABER SI SON ARRIVAL, DEPARTURE O TRANSFER, MEDIANTE UN COLOR DE FONDO
                                                    $background_color = "background-color: #".( $shared->final_service_type == 'ARRIVAL' ? "ddf5f0" : ( $shared->final_service_type == 'TRANSFER' ? "f2eafa" : "dbe0f9" ) ).";";
                                                    // $color = "color: #".( $shared->site_code == 29 || $shared->site_code == 30 ? "FFFFFF" : "515365" ).";";
                                                    $class_agency = ( $shared->site_code == 29 || $shared->site_code == 30 ?  "agency_".$shared->site_code : "" );
                    
                                                    //SABER EL NIVEL DE CUT OFF
                                                    $cut_off_zone = ( $shared->final_service_type == 'ARRIVAL' || ( ( $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->zone_one_cut_off : $shared->zone_two_cut_off );
                    
                                                    // $payment = ( $shared->total_sales - $shared->total_payments );
                                                    // if($payment < 0) $payment = 0;
                                                    // $payment = $shared->total_sales;
                    
                                                    //PREASIGNACION
                                                    $flag_preassignment = ( ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ) && $shared->op_one_preassignment != "" ? true : ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && ( $shared->is_round_trip == 1 ) && $shared->op_two_preassignment != "" ? true : false ) );
                                                    $flag_comment =       ( ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ) && $shared->op_one_comments != "" ? true : ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && ( $shared->is_round_trip == 1 ) && $shared->op_two_comments != "" ? true : false ) );
                    
                                                    $preassignment = ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->op_one_preassignment : $shared->op_two_preassignment );
                                                    $comment =       ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->op_one_comments : $shared->op_two_comments );
                    
                                                    //ESTATUS
                                                    $status_operation = ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->one_service_operation_status : $shared->two_service_operation_status );
                                                    $time_operation =   ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->op_one_time_operation : $shared->op_two_time_operation );
                                                    $cost_operation =   ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->op_one_operating_cost : $shared->op_two_operating_cost );
                                                    $status_booking =   ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->one_service_status : $shared->two_service_status );
                    
                                                    $operation_pickup = (($shared->operation_type == 'arrival')? $shared->pickup_from : $shared->pickup_to );
                                                    $operation_from = (($shared->operation_type == 'arrival')? $shared->from_name.( (!empty($shared->flight_number)) ? ' ('.$shared->flight_number.')' : '' )  : $shared->to_name );
                                                    $operation_to = (($shared->operation_type == 'arrival')? $shared->to_name : $shared->from_name );
                    
                                                    $vehicle_d = ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->vehicle_id_one : $shared->vehicle_id_two );
                                                    $driver_d =  ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->driver_id_one : $shared->driver_id_two );                                
                                                    $close_operation = ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? $shared->op_one_operation_close : $shared->op_two_operation_close );
                    
                                                    $vehicle_name = ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? ( $shared->vehicle_one_name != null ? $shared->vehicle_one_name : 'Selecciona un vehículo' ) : ( $shared->vehicle_two_name != null ? $shared->vehicle_two_name : 'Selecciona un vehículo' ) );
                                                    $driver_name =  ( ( ( $shared->final_service_type == 'ARRIVAL' || $shared->final_service_type == 'TRANSFER' || $shared->final_service_type == 'DEPARTURE' ) && $shared->op_type == "TYPE_ONE" && ( $shared->is_round_trip == 0 || $shared->is_round_trip == 1 ) ) ? ( $shared->driver_one_name != null ? $shared->driver_one_name : 'Selecciona un conductor' ) : ( $shared->driver_two_name != null ? $shared->driver_two_name : 'Selecciona un conductor' ) );
                    
                                                    switch ($status_operation) {
                                                        case 'E':
                                                            $label = 'info';
                                                            break;
                                                        case 'C':
                                                            $label = 'warning';
                                                            break;
                                                        case 'OK':
                                                            $label = 'success';
                                                            break;
                                                        default:
                                                            $label = 'secondary';
                                                            break;
                                                    }
                    
                                                    switch ($status_booking) {
                                                        case 'COMPLETED':
                                                            $label2 = 'success';
                                                            break;
                                                        case 'NOSHOW':
                                                            $label2 = 'warning';
                                                            break;
                                                        case 'CANCELLED':
                                                            $label2 = 'danger';
                                                            break;
                                                        default:
                                                            $label2 = 'secondary';
                                                            break;
                                                    }
                                                @endphp
                                                <tr class="item-{{ $key.$shared->id }} {{ $class_agency }}" id="item-{{ $key.$shared->id }}" data-payment-method="{{ $shared->payment_type_name }}" data-reservation="{{ $shared->reservation_id }}" data-item="{{ $shared->id }}" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-close_operation="{{ $close_operation }}" style="{{ $background_color }}">
                                                    <td>
                                                        @if ( $flag_preassignment )
                                                            <button type="button" class="btn btn-<?=( $shared->final_service_type == 'ARRIVAL' ? 'success' : ( $shared->final_service_type == 'DEPARTURE' ? 'primary' : 'info' ) )?> btn_operations text-uppercase {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">{{ $preassignment }}</button>
                                                        @else
                                                            <button type="button" class="btn btn-danger text-uppercase btn_operations {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : 'add_preassignment' }}" id="btn_preassignment_{{ $key.$shared->id }}" data-id="{{ $key.$shared->id }}" data-reservation="{{ $shared->reservation_id }}" data-item="{{ $shared->id }}" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}">ADD</button>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-2 w-100">
                                                            <div class="d-flex gap-2 comment-default">
                                                                @if ( !empty($shared->messages) )
                                                                    <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver historial de reservación" data-type="history" data-code="{{ $shared->reservation_id }}">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                                    </div>
                                                                @endif
                                                                <div class="btn btn-primary btn_operations __open_modal_customer bs-tooltip" title="Ver datos del cliente" data-code="{{ $shared->reservation_id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                                                </div>
                                                            </div>
                                                            <div class="comment_new" id="comment_new_{{ $key.$shared->id }}">
                                                                @if ( $flag_comment )
                                                                    <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver mensaje de operaciones" data-type="comment" data-comment="{{ $comment }}">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="upload_new" id="upload_new_{{ $key.$shared->id }}">
                                                                @if ( !empty($shared->pictures) )
                                                                    <div class="btn btn-primary btn_operations bs-tooltip" title="Esta reservación tiene imagenes">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                                    </div>
                                                                @endif
                                                            </div>                                        
                                                        </div>
                                                    </td>
                                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>                                    
                                                    <td>
                                                        <span>{{ $shared->full_name }}</span>
                                                        @if(!empty($shared->reference))
                                                            [{{ $shared->reference }}]
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $shared->final_service_type }}
                                                        @if ( $shared->final_service_type == "ARRIVAL" )
                                                            <span class="badge badge-{{ $shared->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $shared->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $shared->passengers }}</td>
                                                    <td style="{{ ( $cut_off_zone >= 3 ? 'background-color:#e2a03f;color:#fff;' : ( $cut_off_zone >= 2 && $cut_off_zone < 3 ? 'background-color:#805dca;color:#fff;' : '' ) ) }}">{{ $operation_from }}</td>
                                                    <td>{{ $operation_to }}</td>
                                                    <td>{{ $shared->site_name }}</td>
                                                    <td data-order="{{ ( $vehicle_d != NULL ) ? $vehicle_d : 0 }}" data-name="{{ $vehicle_name }}">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            {{ $vehicle_name }}
                                                        @else
                                                            <select class="form-control vehicles selectpicker" data-live-search="true" id="vehicle_id_{{ $key.$shared->id }}" data-id="{{ $key.$shared->id }}" data-reservation="{{ $shared->reservation_id }}" data-item="{{ $shared->id }}" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}">
                                                                <option value="0">Selecciona un vehículo</option>
                                                                @if ( isset($vehicles) && count($vehicles) >= 1 )
                                                                    @foreach ($units as $unit)
                                                                        <option {{ ( $vehicle_d != NULL && $vehicle_d == $unit->id ) ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->destination_service->name }} - {{ $unit->enterprise->names }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        @endif
                                                    </td>
                                                    <td data-order="{{ ( $driver_d != NULL ) ? $driver_d : 0 }}" data-name="{{ $driver_name }}">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            {{ $driver_name }}
                                                        @else
                                                            <select class="form-control drivers selectpicker" data-live-search="true" id="driver_id_{{ $key.$shared->id }}" data-id="{{ $key.$shared->id }}" data-reservation="{{ $shared->reservation_id }}" data-item="{{ $shared->id }}" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}">
                                                                <option value="0">Selecciona un conductor</option>
                                                                @if ( isset($drivers) && count($drivers) >= 1 )
                                                                    @foreach ($drivers as $driver)
                                                                        <option {{ ( $driver_d != NULL && $driver_d == $driver->id ) ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsOperation{{ $key.$shared->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle btn_status_action {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">
                                                                    <span>{{ $status_operation }}</span>
                                                                </button>
                                                            </div>                                      
                                                        @else
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsOperation{{ $key.$shared->id }}" data-item="{{ $key.$shared->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span>{{ $status_operation }}</span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="optionsOperation{{ $key.$shared->id }}">
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="PENDING" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="E" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="C" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="OK" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation }}</td>
                                                    <td class="text-center">{{ $cost_operation }}</td>
                                                    <td class="text-center">
                                                        @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsBooking{{ $key.$shared->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle btn_status_action {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">
                                                                    <span>{{ $status_booking }}</span>
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div class="btn-group" role="group">
                                                                <button id="optionsBooking{{ $key.$shared->id }}" data-item="{{ $key.$shared->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span>{{ $status_booking }}</span>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                                </button>
                                                                <div class="dropdown-menu" aria-labelledby="optionsBooking{{ $key.$shared->id }}">
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="PENDING" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="COMPLETED" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="NOSHOW" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $shared->final_service_type }}" data-service="{{ $shared->operation_type }}" data-type="{{ $shared->op_type }}" data-status="CANCELLED" data-item="{{ $shared->id }}" data-booking="{{ $shared->reservation_id }}" data-key="{{ $key.$shared->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (RoleTrait::hasPermission(38))
                                                            <a href="/reservations/detail/{{ $shared->reservation_id }}">{{ $shared->code }}</a>
                                                        @else
                                                            {{ $shared->code }}
                                                        @endif
                                                    </td>
                                                    <td style="{{ ( $shared->service_type_name == "Suburban" ? 'background-color:#e2a03f;color:#fff;' : '' ) }}">{{ $shared->service_type_name }}</td>
                                                    <td class="text-center" style="{{ ( $shared->reservation_status == "PENDING" || $shared->reservation_status == "PENDIENTE" || ( $shared->reservation_status == "CONFIRMADO" && $shared->payment_type_name == "CASH" ) ? 'background-color:#e7515a;' : 'background-color:#00ab55;' ) }}color:#fff;">{{ $shared->reservation_status }}</td>
                                                    <td class="text-end" style="{{ ( $shared->reservation_status == "PENDING" || $shared->reservation_status == "PENDIENTE" || ( $shared->reservation_status == "CONFIRMADO" && $shared->payment_type_name == "CASH" ) ? 'background-color:#e7515a;' : 'background-color:#00ab55;' ) }}color:#fff;">{{ number_format($shared->total_sales,2) }}</td>
                                                    <td class="text-center">{{ $shared->currency }}</td>
                                                    <td class="text-center">
                                                        <div class="d-flex gap-3">
                                                            <div class="btn btn-primary btn_operations {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '__open_modal_comment' }} bs-tooltip" title="{{ ( $flag_comment ) ? 'Modificar comentario' : 'Agregar comentario' }}" id="btn_add_modal_{{ $key.$shared->id }}" data-status="{{ ( $flag_comment ) ? 1 : 0 }}" data-reservation="{{ $shared->reservation_id }}" data-id="{{ $key.$shared->id }}" data-code="{{ $shared->id }}" data-operation="{{ $shared->final_service_type }}" data-type="{{ $shared->op_type }}">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                            </div>
                                                            <div class="btn btn-primary btn_operations extract_whatsapp bs-tooltip" title="Ver información para enviar por whatsApp" id="extract_whatsapp{{ $key.$shared->id }}" data-bs-toggle="modal" data-bs-target="#operationWhatsAppModal">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                                            </div>
                                                            @if ( $shared->final_service_type == "ARRIVAL" )
                                                                <div class="btn btn-primary btn_operations extract_confirmation bs-tooltip" title="Ver información de confirmación" id="extract_confirmation{{ $key.$shared->id }}" data-bs-toggle="modal" data-language="{{ $shared->language }}" data-bs-target="#confirmationModal">
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
                    </div>                    
                </div>                
            </div>
        {{-- </div> --}}
    {{-- </div> --}}

    <x-modals.operation.filters :data="$data" :nexDate="$nexDate" :date="$date" :websites="$websites" :units="$units" :drivers="$drivers" />
    <x-modals.reservations.operation_create :websites="$websites" :zones="$zones" :vehicles="$vehicles" />
    <x-modals.reservations.comments />
    <x-modals.reservations.operation_messages_history />
    <x-modals.reservations.operation_data_customer />
    <x-modals.reservations.operation_confirmations />
    <x-modals.reservations.operation_data_whatsapp />
@endsection