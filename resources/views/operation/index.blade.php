@php
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
    </style>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script>
        $('.table').DataTable({
            dom: 'Bfrtip',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            },
            paging: false,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') {                
                location.reload();
            }
        });

        let timeoutId;
        function resetTimer() {        
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            
            timeoutId = setTimeout(updateView, 300000); // 60,000 ms = 1 minuto
        }

        function updateView() {          
            console.log('La vista se ha actualizado...');
            location.reload();  
        }
        
        document.addEventListener('mousemove', resetTimer);
        document.addEventListener('keydown', resetTimer);
        resetTimer();
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3 button_">
            Operación            
        </h1>
        
        <div class="row">
            <div class="col-12 col-sm-12">
                
                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        @if(sizeof( $dates ))
                            @php
                                $cont = 0;
                            @endphp
                            @foreach ($dates as $key => $value)
                                @php
                                    $cont++;
                                @endphp
                                <li class="nav-item"><a class="nav-link {{ (($cont==1)?'active':'') }}" href="#tab-{{ $key }}" data-bs-toggle="tab" role="tab">{{ $key }}</a></li>                                
                            @endforeach
                        @endif
                    </ul>
                    <div class="tab-content">
                        
                        @if(sizeof( $dates ))
                            @php
                                $cont = 0;
                            @endphp
                            @foreach ($dates as $key => $value)
                                @php
                                    $cont++;
                                @endphp 
                                <div class="tab-pane {{ (($cont==1)?'active':'') }}" id="tab-{{ $key }}" role="tabpanel">
                                    <h4 class="tab-title">Operación de la fecha {{ $key }}</h4>                                
                                    <div class="table-responsive mt-3">
                                        <table id="reservations_table" class="table table-striped table-sm">
                                            <thead>
                                                <tr>                                                        
                                                    <th>Sitio</th>
                                                    <th class="text-center">Tipo</th>
                                                    <th>Código</th>
                                                    <th>Cliente</th>
                                                    <th>Pickup</th>
                                                    <th>Vehículo</th>
                                                    <th>Pasajeros</th>
                                                    <th>Desde</th>
                                                    <th>Hacia</th>
                                                    <th>Pago</th>
                                                    <th>Total</th>
                                                    <th>Moneda</th>
                                                    <th>Comentarios</th>
                                                </tr>
                                            </thead>
                                            <tbody>                                                
                                                @if(sizeof($value) >= 1) 
                                                    @foreach ($value as $valueItem)
                                                        @php
                                                            $payment = ( $valueItem->total_sales - $valueItem->total_payments );
                                                            if($payment < 0) $payment = 0;
                                                        @endphp                                                                                                        
                                                        <tr>
                                                            <td>{{ $valueItem->site_name }}</td>
                                                            <td>{{ $valueItem->final_service_type }}</td>
                                                            <td>{{ $valueItem->code }}</td>
                                                            <td>{{ $valueItem->client_first_name }} {{ $valueItem->client_last_name }}</td>
                                                            <td>{{ date("H:i", strtotime($valueItem->filtered_date)) }}</td>
                                                            <td>{{ $valueItem->service_name }}</td>
                                                            <td>{{ $valueItem->passengers }}</td>
                                                            <td>
                                                                @if($valueItem->operation_type == 'arrival')
                                                                    {{ $valueItem->from_name }}
                                                                @endif
                                                                @if($valueItem->operation_type == 'departure')
                                                                    {{ $valueItem->to_name }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($valueItem->operation_type == 'arrival')
                                                                    {{ $valueItem->to_name }}
                                                                @endif
                                                                @if($valueItem->operation_type == 'departure')
                                                                    {{ $valueItem->from_name }}
                                                                @endif                                                                
                                                            </td>
                                                            <td>{{ $valueItem->status }}</td>
                                                            <td class="text-end">{{ number_format($payment,2) }}</td>                                                            
                                                            <td class="text-center">{{ $valueItem->currency }}</td>
                                                            <td>{{ $valueItem->messages }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif                                                 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection