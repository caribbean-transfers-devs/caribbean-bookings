@php
    $resume = [
        'status' => [
            'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CONFIRMED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CANCELLED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        ]
    ];
    $sites = [];
    $affiliates = [];
    $destinations = [];
    $resume_total = 0;
    $resume_total_mxn = 0;
    $resume_total_usd = 0;
@endphp
@extends('layout.master')
@section('title') Reservaciones @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/reservations/index.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/reservations/index.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/views/reservations/reservationsIndex.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        @php
            // echo "<pre>";
            // print_r($data);
            // die();
        @endphp
        <h1 class="h3 mb-3 button_">
            Reservaciones - {{ date("Y-m-d", strtotime($data['init']))}} a {{ date("Y-m-d", strtotime($data['end']))}}
            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>  
        </h1>
        
        <div class="row">
            <div class="col-12 col-sm-9">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table id="reservations_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Sitio</th>
                                        <th>Código</th>
                                        <th>Estatus</th>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Pasajeros</th>
                                        <th>Total</th>
                                        <th>Moneda</th>
                                        <th>Pendiente</th>
                                        <th>Método</th>
                                        <th>Destino</th>
                                    </tr>
                                </thead>
                                <tbody>
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

                                                        if($item->affiliate_id != 0 ):
                                                            if(!isset( $affiliates[$item->site_name] )):
                                                                $affiliates[$item->site_name] = [
                                                                    'USD' => 0,
                                                                    'MXN' => 0,
                                                                    'count' => 0
                                                                ];
                                                            endif;
                                                            $affiliates[$item->site_name][$item->currency] += $item->total_sales;
                                                            $affiliates[$item->site_name]['count']++;
                                                        endif;

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
                                                $total_pending = $item->total_sales - $item->total_payments;
                                            @endphp
                                            <tr>
                                                <td class="text-end">
                                                    @if($item->is_today >= 1)
                                                        <i class="align-middle me-2" data-feather="alert-circle"></i>
                                                    @endif
                                                </td>
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
                                                <td class="text-end" {{ (($total_pending < 0)? "style=color:green;font-weight:bold;":"") }}>{{ number_format(($total_pending),2) }}</td>
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
                    <div class="table-responsive">
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
                                @php
                                    if( $key == "PENDING" || $key == "CONFIRMED" ):
                                        $resume_total = $resume_total + $value['count'];
                                        $resume_total_mxn = $resume_total_mxn + $value['USD'];
                                        $resume_total_usd = $resume_total_usd + $value['MXN'];
                                    endif;
                                @endphp
                                <tr>
                                    <td>{{ strtolower( $key) }}</td>
                                    <td class="text-center">{{ $value['count'] }}</td>
                                    <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                    <td class="text-end">{{ number_format($value['MXN'],2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfooter>
                                <tr>
                                    <td>Total</td>
                                    <td class="text-center">{{ $resume_total }}</td>                                    
                                    <td class="text-end">{{ $resume_total_mxn }}</td>
                                    <td class="text-end">{{ $resume_total_usd }}</td>
                                </tr>
                            </tfooter>                             
                        </table>
                    </div>

                    <div class="card-header">
                        <hr>
                        <h4>Resumen por sitio</h4>
                    </div>
                    <div class="table-responsive">
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
                    </div>

                    <div class="card-header">
                        <hr>
                        <h4>Resumen por destino</h4>
                    </div>
                    <div class="table-responsive">
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

                    <div class="card-header">
                        <hr>
                        <h4>Resumen afiliados</h4>
                    </div>
                    <div class="table-responsive">
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
                                @foreach($affiliates as $key => $value)
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

    </div>

<x-modals.reservations.listing :data="$data" :services="$services" :zones="$zones" :websites="$websites" />
@endsection