@php
    $users = [];
@endphp
@extends('layout.master')
@section('title') Comisiones @endsection

@push('up-stack')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        table thead th{ font-size: 8pt; }
        table tbody td{ font-size: 8pt; }
        .button_{ display: flex; justify-content: space-between; }
    </style>
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

    <script>
        $('#reservations_table').DataTable({
            dom: 'Bfrtip',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            },
            paging: false,
            ordering: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            "order": [[1, 'asc']] 
        });

        $(function() {
            const picker = new easepick.create({
                    element: "#lookup_date",        
                    css: [
                        'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                        'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                        'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                    ],
                    zIndex: 10,
                    plugins: ['RangePlugin'],
                });
        });
        function Search(){
            $("#btnSearch").text("Buscando....").attr("disabled", true);
            $("#formSearch").submit();
        }
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3 button_">
            Reporte de comisiones
            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>
        </h1>

        <div class="row">
            <div class="col-12 col-sm-12">

                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab">{{ date("Y-m-d", strtotime($search['init_date'])) }} al {{ date("Y-m-d", strtotime($search['end_date'])) }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-1" role="tabpanel">
                            <table id="reservations_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>                                                        
                                        <th>Fecha</th>
                                        <th>Sitio</th>
                                        <th>Código</th>
                                        <th>Estatus</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Pasajeros</th>
                                        <th>MXN</th>
                                        <th>USD</th>
                                        <th>Vendedor</th>
                                        <th>Método de pago</th>
                                        <th>Destino</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($items) >= 1)
                                        @foreach($items as $key => $value)
                                            @php                                                
                                                $status = $value->status;
                                                if(!isset( $users[ $value->employee ] )):
                                                    $users[ $value->employee ] = ['USD' => 0, 'MXN' => 0, 'QUANTITY' => 0];
                                                endif;

                                                if($value->currency == "USD"):
                                                    $users[ $value->employee ]['USD'] += $value->total_sales;
                                                endif;

                                                if($value->currency == "MXN"):
                                                    $users[ $value->employee ]['USD'] += $value->total_sales;
                                                endif;

                                                $users[ $value->employee ]['QUANTITY']++;
                                            @endphp
                                            <tr>
                                                <td>{{ date("m/d", strtotime($value->created_at)) }}</td>
                                                <td>{{ $value->site_name }}</td>
                                                <td><a href="/reservations/detail/{{ $value->reservation_id }}" target="_blank"> {{ $value->code }}</a></td>
                                                <td>                                                   
                                                    {{ $status }}
                                                </td>
                                                <td>{{ ucwords(strtolower($value->full_name)) }}</td>
                                                <td>{{ $value->service_name }}</td>
                                                <td>
                                                    @if ($value->is_round_trip == 1)
                                                        ROUND TRIP    
                                                    @else
                                                        {{ $value->final_service_type }}
                                                    @endif                                                    
                                                </td>
                                                <td>
                                                    @if( $value->currency == "MXN" )
                                                        {{ number_format($value->total_sales,2,".","") }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </td>
                                                <td>
                                                    @if( $value->currency == "USD" )
                                                        {{ number_format($value->total_sales,2,".","") }}
                                                    @else
                                                        0.00
                                                    @endif
                                                </td>
                                                <td>{{ $value->employee }}</td>
                                                <td>{{ $value->payment_type_name }}</td>
                                                <td>
                                                    @if( $value->zone_one_is_primary == 1 && $value->zone_two_is_primary == 0)
                                                    {{ $value->zone_two_name }} 
                                                    @endif
                                                    @if( $value->zone_one_is_primary == 0 && $value->zone_two_is_primary == 1)
                                                    {{ $value->zone_one_name }} 
                                                    @endif
                                                    @if( $value->zone_one_is_primary == 0 && $value->zone_two_is_primary == 0)
                                                        {{ $value->zone_one_name }} 
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            <hr>
                            <h3>Resumen</h3>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>                                                        
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>USD</th>
                                        <th>MXN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($users) >= 1)
                                        @foreach($users as $key => $value)
                                            <tr>
                                                <td>{{ $key }}</td>
                                                <td>{{ $value['QUANTITY'] }}</td>
                                                <td>{{ number_format($value['USD'],2) }}</td>
                                                <td>{{ number_format($value['MXN'],2) }}</td>                                                
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
@endsection

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
                    <div class="col-12 col-sm-12">
                        <label class="form-label" for="lookup_date">Seleccione el rango de fechas</label>
                        <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $search['init_date']." - ".$search['end_date'] }}">
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
