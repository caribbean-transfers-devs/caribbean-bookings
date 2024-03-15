@php
    use App\Traits\RoleTrait;
    use App\Traits\Reports\PaymentsTrait;
    
    $resume = [
        'PENDIENTE' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        'CONFIRMADO' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
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
    
    <script src="{{ mix('/assets/js/views/reports/cash.min.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3 button_">
            Reporte de pagos en efectivo
            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>        
        </h1>
        
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab">Servicios</a></li>                        
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-1" role="tabpanel">
                            <h4 class="tab-title">Listado de reservaciones operadas el día {{ $date_search }}</h4>
                            <table id="reservations_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Pickup</th>
                                        <th></th>                                                     
                                        <th>Sitio</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Estatus Op.</th>
                                        <th>Código</th>
                                        <th>Cliente</th>
                                        <th>Vehículo</th>
                                        <th>Pasajeros</th>
                                        <th>Desde</th>
                                        <th>Hacia</th>
                                        <th>Pago</th>
                                        <th>Ventas</th>
                                        <th>Moneda</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($items)>=1)
                                        @foreach($items as $key => $value)
                                            @php                                                
                                                $show = false;
                                                $payments = PaymentsTrait::getPayments($value->reservation_id);
                                                if(sizeof($payments) >= 1):
                                                    foreach($payments as $keyP => $valueP):
                                                        if($valueP->payment_method == 'CASH'):
                                                            $show = true;
                                                        endif;
                                                    endforeach;
                                                else:
                                                    $show = true;
                                                endif;
                                            @endphp

                                            @if( $show )
                                                @php                                                
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
                                                    $confirmation_type = (( $value->payment_reconciled == 0 )? 1 : 0);

                                                    if( isset( $resume[ $value->status ] ) ):
                                                        $resume[ $value->status ][ $value->currency ] += $value->total_sales;
                                                        $resume[ $value->status ]['count']++;
                                                    endif;

                                                @endphp
                                                <tr>
                                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                                    <td>
                                                        <button class="btn btn-order {{ (($value->payment_reconciled == 0)? 'btn-warning':'btn-success')}}" onClick="updateConfirmation(event,{{$value->reservation_id}},{{$confirmation_type}})">
                                                            {{ (($value->payment_reconciled == 0)? 'N':'Y')}}
                                                        </button>
                                                    </td>
                                                    <td>{{ $value->site_name }}</td>
                                                    <td>{{ $value->final_service_type }}</td>
                                                    <td class="text-center"><span class="badge {{ $label }} rounded-pill">{{ $operation_status }}</span></td>
                                                    <td>
                                                        @if (RoleTrait::hasPermission(10))
                                                            <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                                        @else
                                                            {{ $value->code }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                                    <td>{{ $value->service_name }}</td>
                                                    <td class="text-center">{{ $value->passengers }}</td>
                                                    <td>{{ $operation_from }}</td>
                                                    <td>{{ $operation_to }}</td>
                                                    <td class="text-center">
                                                        @if($value->status == "PENDIENTE")
                                                            <span class="badge btn-secondary rounded-pill">Pendiente</span>
                                                        @endif
                                                        @if($value->status == "CONFIRMADO")
                                                            <span class="badge btn-success rounded-pill">Confirmado</span>
                                                        @endif
                                                    </td>
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
            <div class="col-12 col-sm-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Resumen por estatus</h4>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width:35%;">Estatus</th>
                                <th style="width:25%" class="text-center">Cantidad</th>
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Pendiente</td>
                                <td class="text-center">{{ $resume['PENDIENTE']['count'] }}</td>
                                <td class="text-end">{{ number_format($resume['PENDIENTE']['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($resume['PENDIENTE']['MXN'],2) }}</td>
                            </tr>
                            <tr>
                                <td>Confirmado</td>
                                <td class="text-center">{{ $resume['CONFIRMADO']['count'] }}</td>
                                <td class="text-end">{{ number_format($resume['CONFIRMADO']['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($resume['CONFIRMADO']['MXN'],2) }}</td>
                            </tr>
                        </tbody>
                    </table>
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
                        <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date_search }}">
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