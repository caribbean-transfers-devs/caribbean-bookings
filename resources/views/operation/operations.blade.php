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
        // dd($items);
        $buttons = array();
        // dump($buttons);
    @endphp
    @if (RoleTrait::hasPermission(79))
        <input type="hidden" class="" id="permission_reps" value="true" required>
    @endif
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div id="filters" class="accordion">
            <div class="card">
                <div class="card-header" id="headingOne1">
                    <section class="mb-0 mt-0">
                        <div role="menu" class="" data-bs-toggle="collapse" data-bs-target="#defaultAccordionOne" aria-expanded="true" aria-controls="defaultAccordionOne">
                            Filtros <div class="icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                        </div>
                    </section>
                </div>
                <div id="defaultAccordionOne" class="collapse show" aria-labelledby="headingOne1" data-bs-parent="#filters">
                    <div class="card-body">
                        <form action="" class="row" method="POST" id="formSearch">
                            @csrf
                            <input type="hidden" id="lookup_date_next" value="{{ $nexDate }}" required>

                            <div class="col-12 col-sm-12 mb-3">
                                <div class="row">
                                    @if (RoleTrait::hasPermission(80))
                                        <div class="col-12 col-sm-2 align-self-end">
                                            <button type="button" class="btn btn-primary btn-lg w-100" id="btn_addservice">Agregar nuevo servicio</button>
                                        </div>
                                    @endif

                                    @if (RoleTrait::hasPermission(82))
                                        <div class="col-12 col-sm-2 align-self-end">
                                            <button type="button" class="btn btn-primary btn-lg w-100" id="btn_preassignment">Pre-asignación</button>
                                        </div>
                                    @endif

                                    @if (RoleTrait::hasPermission(83))
                                        <div class="col-12 col-sm-2 align-self-end">
                                            <button type="button" class="btn btn-primary btn-lg w-100" id="btn_dowload_operation">Descargar operación</button>
                                        </div>
                                    @endif

                                    @if (RoleTrait::hasPermission(84))
                                        <div class="col-12 col-sm-2 align-self-end">
                                            <button type="button" class="btn btn-primary btn-lg w-100" id="btn_dowload_operation_comission">Descargar comisiones de operación</button>
                                        </div>
                                    @endif

                                    @if (RoleTrait::hasPermission(85))
                                        <div class="col-12 col-sm-2 align-self-end">
                                            <button type="button" class="btn btn-danger btn-lg w-100" id="btn_close_operation">Cerrar operación</button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-sm-4 mb-3 mb-lg-0">
                                <label class="form-label" for="lookup_date">Fecha de creación</label>
                                <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date }}">
                            </div>
                            <div class="col-12 col-sm-2 align-self-end">
                                <button type="submit" class="btn btn-primary btn-lg btn-filter w-100">Filtrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
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
            <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>' data-action="dataOperations">
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
                    @if(sizeof($items)>=1)
                        @foreach($items as $key => $value)
                            @php
                                //DECLARAMOS VARIABLES DE IDENTIFICADORES
                                //SABER SI SON ARRIVAL, DEPARTURE O TRANSFER, MEDIANTE UN COLOR DE FONDO
                                $background_color = "background-color: #".( $value->final_service_type == 'ARRIVAL' ? "ddf5f0" : ( $value->final_service_type == 'TRANSFER' ? "f2eafa" : "dbe0f9" ) ).";";
                                //SABER EL NIVEL DE CUT OFF
                                $cut_off_zone = ( $value->final_service_type == 'ARRIVAL' || ( ( $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->zone_one_cut_off : $value->zone_two_cut_off );

                                // $payment = ( $value->total_sales - $value->total_payments );
                                // if($payment < 0) $payment = 0;
                                // $payment = $value->total_sales;

                                //PREASIGNACION
                                $flag_preassignment = ( ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ) && $value->op_one_preassignment != "" ? true : ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && ( $value->is_round_trip == 1 ) && $value->op_two_preassignment != "" ? true : false ) );
                                $flag_comment =       ( ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ) && $value->op_one_comments != "" ? true : ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && ( $value->is_round_trip == 1 ) && $value->op_two_comments != "" ? true : false ) );

                                $preassignment = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_preassignment : $value->op_two_preassignment );
                                $comment =       ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_comments : $value->op_two_comments );

                                //ESTATUS
                                $status_operation = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_status_operation : $value->op_two_status_operation );
                                $time_operation =   ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_time_operation : $value->op_two_time_operation );
                                $cost_operation =   ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_operating_cost : $value->op_two_operating_cost );
                                $status_booking =   ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_status : $value->op_two_status );

                                $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                                $vehicle_d = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->vehicle_id_one : $value->vehicle_id_two );
                                $driver_d =  ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->driver_id_one : $value->driver_id_two );                                
                                $close_operation = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? $value->op_one_operation_close : $value->op_two_operation_close );

                                $vehicle_name = ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? ( $value->vehicle_one_name != null ? $value->vehicle_one_name : 'Selecciona un vehículo' ) : ( $value->vehicle_two_name != null ? $value->vehicle_two_name : 'Selecciona un vehículo' ) );
                                $driver_name =  ( ( ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || $value->final_service_type == 'DEPARTURE' ) && $value->op_type == "TYPE_ONE" && ( $value->is_round_trip == 0 || $value->is_round_trip == 1 ) ) ? ( $value->driver_one_name != null ? $value->driver_one_name : 'Selecciona un conductor' ) : ( $value->driver_two_name != null ? $value->driver_two_name : 'Selecciona un conductor' ) );

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
                            {{-- @php
                                if ($value->reservation_id == "33404") {
                                    dump($value);
                                }
                            @endphp --}}
                            <tr class="item-{{ $key.$value->id }}" id="item-{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-close_operation="{{ $close_operation }}" style="{{ $background_color }}">
                                <td>
                                    @if ( $flag_preassignment )
                                        <button type="button" class="btn btn-<?=( $value->final_service_type == 'ARRIVAL' ? 'success' : ( $value->final_service_type == 'DEPARTURE' ? 'primary' : 'info' ) )?> btn_operations text-uppercase {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">{{ $preassignment }}</button>
                                    @else
                                        <button type="button" class="btn btn-danger text-uppercase btn_operations {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : 'add_preassignment' }}" id="btn_preassignment_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}">ADD</button>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 w-100">
                                        <div class="d-flex gap-2 comment-default">
                                            @if ( !empty($value->messages) )
                                                <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver historial de reservación" data-type="history" data-code="{{ $value->reservation_id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                </div>
                                            @endif
                                            <div class="btn btn-primary btn_operations __open_modal_customer bs-tooltip" title="Ver datos del cliente" data-code="{{ $value->reservation_id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                            </div>
                                        </div>
                                        <div class="comment_new" id="comment_new_{{ $key.$value->id }}">
                                            @if ( $flag_comment )
                                                <div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver mensaje de operaciones" data-type="comment" data-comment="{{ $comment }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="upload_new" id="upload_new_{{ $key.$value->id }}">
                                            @if ( !empty($value->pictures) )
                                                <div class="btn btn-primary btn_operations bs-tooltip" title="Esta reservación tiene imagenes">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                </div>
                                            @endif
                                        </div>                                        
                                    </div>
                                </td>
                                <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>                                    
                                <td>
                                    <span>{{ $value->client_first_name }} {{ $value->client_last_name }}</span>
                                    @if(!empty($value->reference))
                                        [{{ $value->reference }}]
                                    @endif
                                </td>
                                <td>{{ $value->final_service_type }}</td>
                                <td class="text-center">{{ $value->passengers }}</td>
                                <td style="{{ ( $cut_off_zone >= 3 ? 'background-color:#e2a03f;color:#fff;' : ( $cut_off_zone >= 2 && $cut_off_zone < 3 ? 'background-color:#805dca;color:#fff;' : '' ) ) }}">{{ $operation_from }}</td>
                                <td>{{ $operation_to }}</td>
                                <td>{{ $value->site_name }}</td>
                                <td data-order="{{ ( $vehicle_d != NULL ) ? $vehicle_d : 0 }}" data-name="{{ $vehicle_name }}">
                                    @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                        {{ $vehicle_name }}
                                    @else
                                        <select class="form-control vehicles selectpicker" data-live-search="true" id="vehicle_id_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}">
                                            <option value="0">Selecciona un vehículo</option>
                                            @if ( isset($vehicles) && count($vehicles) >= 1 )
                                                @foreach ($vehicles as $vehicle)
                                                    <option {{ ( $vehicle_d != NULL && $vehicle_d == $vehicle->id ) ? 'selected' : '' }} value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->destination_service->name }} - {{ $vehicle->enterprise->names }}</option>
                                                @endforeach
                                            @endif
                                        </select>                                        
                                    @endif
                                </td>
                                <td data-order="{{ ( $driver_d != NULL ) ? $driver_d : 0 }}" data-name="{{ $driver_name }}">
                                    @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                        {{ $driver_name }}
                                    @else
                                        <select class="form-control drivers selectpicker" data-live-search="true" id="driver_id_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-reservation="{{ $value->reservation_id }}" data-item="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}">
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
                                            <button id="optionsOperation{{ $key.$value->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle btn_status_action {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">
                                                <span>{{ $status_operation }}</span>
                                            </button>
                                        </div>                                      
                                    @else
                                        <div class="btn-group" role="group">
                                            <button id="optionsOperation{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span>{{ $status_operation }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="optionsOperation{{ $key.$value->id }}">
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="PENDING" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="E" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="C" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="OK" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">{{ ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation }}</td>
                                <td class="text-center">{{ $cost_operation }}</td>
                                <td class="text-center">
                                    @if ( RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 )
                                        <div class="btn-group" role="group">
                                            <button id="optionsBooking{{ $key.$value->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle btn_status_action {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '' }}">
                                                <span>{{ $status_booking }}</span>
                                            </button>
                                        </div>
                                    @else
                                        <div class="btn-group" role="group">
                                            <button id="optionsBooking{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle btn_status_action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span>{{ $status_booking }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="optionsBooking{{ $key.$value->id }}">
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="PENDING" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="COMPLETED" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="NOSHOW" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-service="{{ $value->operation_type }}" data-type="{{ $value->op_type }}" data-status="CANCELLED" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if (RoleTrait::hasPermission(38))
                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                    @else
                                        {{ $value->code }}
                                    @endif
                                </td>
                                <td style="{{ ( $value->service_name == "Suburban" ? 'background-color:#e2a03f;color:#fff;' : '' ) }}">{{ $value->service_name }}</td>
                                <td class="text-center" style="{{ ( $value->status == "PENDING" ? 'background-color:#e7515a;' : 'background-color:#00ab55;' ) }}color:#fff;">{{ $value->status }}</td>
                                <td class="text-end" style="{{ ( $value->status == "PENDING" ? 'background-color:#e7515a;' : 'background-color:#00ab55;' ) }}color:#fff;">{{ number_format($value->total_sales,2) }}</td>
                                <td class="text-center">{{ $value->currency }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-3">
                                        <div class="btn btn-primary btn_operations {{ RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) || $close_operation == 1 ? 'disabled' : '__open_modal_comment' }} bs-tooltip" title="{{ ( $flag_comment ) ? 'Modificar comentario' : 'Agregar comentario' }}" id="btn_add_modal_{{ $key.$value->id }}" data-status="{{ ( $flag_comment ) ? 1 : 0 }}" data-reservation="{{ $value->reservation_id }}" data-id="{{ $key.$value->id }}" data-code="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" data-type="{{ $value->op_type }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        </div>
                                        <div class="btn btn-primary btn_operations extract_whatsapp bs-tooltip" title="Ver información para enviar por whatsApp" id="extract_whatsapp{{ $key.$value->id }}" data-bs-toggle="modal" data-bs-target="#operationWhatsAppModal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <x-modals.reservations.operation_create :websites="$websites" :zones="$zones" :services="$services" />
    <x-modals.reservations.comments />
    <x-modals.reservations.operation_messages_history />
    <x-modals.reservations.operation_data_customer />
    <x-modals.reservations.operation_data_whatsapp />    
@endsection