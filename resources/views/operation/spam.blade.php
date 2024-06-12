@php
    use App\Traits\RoleTrait;
    $resumen = [];
@endphp
@extends('layout.master')
@section('title') SPAM @endsection

@push('up-stack')
    <style>
        table thead th{
            font-size: 8pt;
        }
        table tbody td{
            font-size: 8pt;
        }
        .button_{
            display: flex;
            justify-content: space-between;
        }
        button.dropdown-toggle{
            font-size: 8pt !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    
    <script src="{{ mix('/assets/js/views/operation/spam.min.js') }}"></script>
    <script>
        var table = $('#reservations_table').DataTable({
            dom: 'Bfrtip',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            },
            paging: false,
            // buttons: [
            //     {
            //         extend: 'excelHtml5',
            //         text: 'Exportar a Excel',
            //         exportOptions: {
            //             columns: ':visible'
            //         }
            //     }
            // ],
            // columnDefs: [
            //     {
            //         targets: -1, // Aquí puedes ajustar qué columnas son visibles/invisibles
            //         visible: true
            //     }
            // ]
        });

        // Checkbox para seleccionar columnas
        // $('input.toggle-vis').on('change', function(e) {
        //     var column = table.column($(this).attr('data-column'));
        //     column.visible(!column.visible());
        // });
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between">
            <h1 class="h3 mb-3 button_">Gestión de envío de SPAM</h1>
            <div class="d-flex align-items-center gap-2">
                @if (RoleTrait::hasPermission(41))
                    <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>
                @endif
                @if (RoleTrait::hasPermission(70))
                    <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModalExport">Exportar Excel</a>
                @endif
            </div>
        </div>
                
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab">Servicios</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-1" role="tabpanel">
                            <h4 class="tab-title">Operación del día [ {{ $date }} ]</h4>
                            <table id="reservations_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Code</th>
                                        <th># Llamadas aceptadas</th>
                                        <th>Sitio</th>
                                        <th>Pickup</th>                           
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Operación</th>
                                        <th>Código</th>
                                        <th>Cliente</th>
                                        <th>Teléfono</th>
                                        <th>Correo</th>
                                        <th>Vehículo</th>
                                        <th>Pasajeros</th>
                                        <th>Desde</th>
                                        <th>Hacia</th>                                        
                                        <th>Total</th>
                                        <th>Moneda</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($items)>=1)
                                        @foreach($items as $key => $value)
                                            @if( in_array($value->final_service_type, ["ARRIVAL", "TRANSFER"]) )
                                                @php
                                                    $confirmation_type = $value->op_one_confirmation;
                                                    if($value->operation_type == "departure"):
                                                        $confirmation_type = $value->op_two_confirmation;
                                                    endif;

                                                    $payment = ( $value->total_sales - $value->total_payments );
                                                    if($payment < 0) $payment = 0;

                                                    $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                                                    $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                                    $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                                    $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                                                    switch ($operation_status) {
                                                        case 'PENDING':
                                                            $label = 'btn-secondary';
                                                            break;
                                                        case 'COMPLETED':
                                                            $label = 'btn-success';
                                                            break;
                                                        case 'NOSHOW':
                                                            $label = 'btn-warning';
                                                            break;
                                                        case 'CANCELLED':
                                                            $label = 'btn-danger';
                                                            break;
                                                        default:
                                                            $label = 'btn-secondary';
                                                            break;
                                                    }
                                                    
                                                    switch ($value->spam) {
                                                        case 'PENDING':
                                                            $spam = 'btn-secondary';
                                                            break;
                                                        case 'SENT':
                                                            $spam = 'btn-info';
                                                            break;
                                                        case 'LATER':
                                                            $spam = 'btn-warning';
                                                            break;
                                                        case 'CONFIRMED':
                                                            $spam = 'btn-success';
                                                            break;
                                                        case 'ACCEPT':
                                                            $spam = 'btn-success';
                                                            break;                                                            
                                                        case 'REJECTED':
                                                            $spam = 'btn-danger';
                                                            break;
                                                        default:
                                                            $spam = 'btn-secondary';
                                                            break;
                                                    }

                                                    if( !isset( $resumen[ $value->spam ] ) ):
                                                        $resumen[ $value->spam ] = 0;
                                                    endif;
                                                    $resumen[ $value->spam ]++;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn {{ $spam }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{$value->id}}">
                                                                {{ $value->spam }}
                                                            </button>
                                                            <div class="dropdown-menu" style="">
                                                                <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'PENDING','btn-secondary')">PENDING</a>
                                                                <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'SENT','btn-info')">SENT</a>
                                                                <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'LATER','btn-warning')">LATER</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'CONFIRMED','btn-success')">CONFIRMED</a>
                                                                <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'ACCEPT','btn-success')">ACCEPT</a>
                                                                <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'REJECTED','btn-danger')">REJECTED</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $value->id }}</td>
                                                    <td>{{ $value->spam_count }}</td>
                                                    <td>{{ $value->site_name }}</td>
                                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                                    <td>{{ $value->final_service_type }}</td>
                                                    <td class="text-center"><span class="badge {{ $label }} rounded-pill">{{ $operation_status }}</span></td>                                                    
                                                    <td>
                                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>                                                        
                                                    </td>
                                                    <td>{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                                    <td>{{ trim($value->client_phone) }}</td>
                                                    <td>{{ trim(strtolower($value->client_email)) }}</td>
                                                    <td>{{ $value->service_name }}</td>
                                                    <td class="text-center">{{ $value->passengers }}</td>
                                                    <td>{{ $operation_from }}</td>
                                                    <td>{{ $operation_to }}</td>                                                    
                                                    <td class="text-end">{{ number_format($value->total_sales,2) }}</td>
                                                    <td class="text-center">{{ $value->currency }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-4 col-xs-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Resumen de envío de SPAM</h5>
                        <h6 class="card-subtitle text-muted">Aqui encontrarás el resumen conversiones generadas por los agentes.</h6>
                    </div>
                    <div class="card-body text-center">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>                                                        
                                    <th>Estatus</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resumen as $key => $value)
                                    <tr>
                                        <td class="text-start">{{ $key }}</td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="filterModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtro de reservaciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row" action="" method="POST" id="formSearch">                    
                        @csrf
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date }}">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="Search()" id="btnSearch">Buscar</button>
                </div>
            </div>
        </div>
    </div>
    @if (RoleTrait::hasPermission(70))
        <div class="modal" tabindex="-1" id="filterModalExport">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Filtro de exportación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="row" action="" method="POST" id="formSearch">                    
                            @csrf
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="lookup_date2">Fecha</label>
                                <input type="text" name="date" id="lookup_date2" class="form-control" value="{{ $date }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="language">Idioma</label>
                                <select name="language" id="language" class="form-control">
                                    <option value="es">Español</option>
                                    <option value="en">Ingles</option>
                                </select>
                            </div>                        
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="generateExcel">Exportar</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection