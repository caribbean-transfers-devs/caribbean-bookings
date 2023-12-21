@php
    use App\Traits\RoleTrait;
    $resume = [
        'status' => [
            'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CONFIRMED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CANCELLED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        ]
    ];
    $sites = [];
    $destinations = [];
@endphp
@extends('layout.master')
@section('title') Operación @endsection

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
    
    <script src="{{ mix('/assets/js/views/operation/confirmation.min.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3 button_">
            Gestión de operación
            @if (RoleTrait::hasPermission(41))
                <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>
            @endif
        </h1>
        
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab">Servicios</a></li>                        
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-1" role="tabpanel">
                            <h4 class="tab-title">Listado de reservaciones a operar</h4>
                            <table id="reservations_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>        
                                        <th></th>                                                
                                        <th>Sitio</th>
                                        <th>Pickup</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Estatus Op.</th>
                                        <th>Código</th>
                                        <th>Cliente</th>
                                        <th>Vehículo</th>
                                        <th>Pasajeros</th>
                                        <th>Desde</th>
                                        <th>Hacia</th>
                                        <th>Pago</th>
                                        <th>Total</th>
                                        <th>Moneda</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($items)>=1)
                                        @foreach($items as $key => $value)
                                            
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
                                                @endphp
                                                <tr>
                                                    <td data-value="{{ $confirmation_type }}">
                                                        @if (RoleTrait::hasPermission(40))
                                                            <button class="btn {{ (($confirmation_type == 0)? 'btn-warning':'btn-success')}}" onClick="updateConfirmation(event,{{$value->id}},'{{$value->operation_type}}',{{$confirmation_type}},{{$value->reservation_id}})">
                                                                <i class="align-middle" data-feather="check-circle"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td>{{ $value->site_name }}</td>
                                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                                    <td>{{ $value->final_service_type }}</td>
                                                    <td class="text-center"><span class="badge {{ $label }} rounded-pill">{{ $operation_status }}</span></td>
                                                    <td>
                                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>                                                        
                                                    </td>
                                                    <td>{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                                    <td>{{ $value->service_name }}</td>
                                                    <td class="text-center">{{ $value->passengers }}</td>
                                                    <td>{{ $operation_from }}</td>
                                                    <td>{{ $operation_to }}</td>
                                                    <td class="text-center">{{ $value->status }}</td>
                                                    <td class="text-end">{{ number_format($payment,2) }}</td>
                                                    <td class="text-center">{{ $value->currency }}</td>
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


@endsection