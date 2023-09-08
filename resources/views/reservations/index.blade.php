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
@section('title') Reservaciones @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="{{ mix('assets/js/views/reservationsIndex.js') }}"></script>    
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Reservaciones</h1>

        <div class="row">
            <div class="col-12 col-sm-9">
                <div class="card">                    
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table id="reservations_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Sitio</th>
                                        <th>Código</th>
                                        <th>Estatus</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Pasjaeros</th>
                                        <th>Total</th>
                                        <th>Moneda</th>
                                        <th>Pendiente</th>
                                        <th>Método</th>
                                        <th>Destino</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // echo "<pre>";
                                        // print_r($bookings);
                                        // die();
                                    @endphp
                                    @if(sizeof($bookings) >= 1)
                                        @foreach ($bookings as $item)
                                            @php                                                
                                                if($item->is_cancelled == 0):
                                                    if($item->pay_at_arrival == 1):
                                                        $item->status = "CONFIRMED";
                                                    endif;
                                                    $resume['status'][$item->status][$item->currency] += $item->total_sales;
                                                    $resume['status'][$item->status]['count']++;

                                                    //Si está confirmado, sumamos los totales por sitio...
                                                    if($item->status == "CONFIRMED"):
                                                        if(!isset( $sites[$item->site_name] )):
                                                            $sites[$item->site_name] = [
                                                                'USD' => 0,
                                                                'MXN' => 0,
                                                                'count' => 0
                                                            ];
                                                        endif;
                                                        $sites[$item->site_name][$item->currency] += $item->total_sales;
                                                        $sites[$item->site_name]['count']++;

                                                        if(!isset( $destinations[$item->destination_name] )):
                                                            $destinations[$item->destination_name] = [
                                                                'USD' => 0,
                                                                'MXN' => 0,
                                                                'count' => 0
                                                            ];
                                                        endif;
                                                        $destinations[$item->destination_name][$item->currency] += $item->total_sales;
                                                        $destinations[$item->destination_name]['count']++;

                                                    endif;

                                                else:
                                                    $resume['status']['CANCELLED'][$item->currency] += $item->total_sales;
                                                    $resume['status']['CANCELLED']['count']++;
                                                endif;                                                

                                            @endphp
                                            <tr>
                                                <td>{{ $item->site_name }}</td>
                                                <td>
                                                    <a href="reservations/detail/{{ $item->id }}"> {{ $item->reservation_codes }}</a>
                                                </td> 
                                                <td class="text-center">
                                                    @if ($item->is_cancelled == 0)                                                                                                   
                                                        @switch($item->status)
                                                            @case('CONFIRMED')
                                                                <span class="badge bg-success">Confirmado</span>
                                                                @break
                                                            @case('PENDING')
                                                                <span class="badge bg-info">Pendiente</span>
                                                                @break
                                                            @default                                                            
                                                        @endswitch
                                                    @else
                                                            <span class="badge bg-danger">Cancelado</span>
                                                    @endif
                                                </td> 
                                                <td>{{ $item->client_full_name }}</td>                                           
                                                <td>{{ $item->service_type_name }}</td>
                                                <td class="text-center">{{ $item->passengers }}</td>
                                                <td class="text-end">{{ $item->total_sales }}</td>
                                                <td class="text-center">{{ $item->currency }}</td>
                                                <td class="text-end">{{ number_format(($item->total_sales - $item->total_payments),2) }}</td>
                                                <td class="text-center">{{ ((empty($item->payment_type_name))? 'CASH' : $item->payment_type_name ) }}</td>
                                                <td class="text-center">{{ $item->destination_name }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-3">
                
                <div class="card">
                    <div class="card-header">
                        <h4>Resumen por estatus</h4>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width:35%;">Estatus</th>
                                <th style="width:25%">Cantidad</th>
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resume['status'] as $key => $value)
                            <tr>
                                <td>{{ strtolower( $key) }}</td>
                                <td class="text-center">{{ $value['count'] }}</td>
                                <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($value['MXN'],2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="card-header">
                        <hr>
                        <h4>Resumen por sitio</h4>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width:35%;">Estatus</th>
                                <th style="width:25%">Cantidad</th>
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sites as $key => $value)
                            <tr>
                                <td>{{ strtolower( $key) }}</td>
                                <td class="text-center">{{ $value['count'] }}</td>
                                <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($value['MXN'],2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="card-header">
                        <hr>
                        <h4>Resumen por destino</h4>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th style="width:35%;">Destino</th>
                                <th style="width:25%">Cantidad</th>
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($destinations as $key => $value)
                            <tr>
                                <td>{{ strtolower( $key) }}</td>
                                <td class="text-center">{{ $value['count'] }}</td>
                                <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($value['MXN'],2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>


            </div>
        </div>

    </div>

@endsection