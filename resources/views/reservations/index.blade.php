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
@extends('layout.app')
@section('title') Reservaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/sections/reservations/bookings.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => 'CSV',
                'extend' => 'csvHtml5',
                'titleAttr' => 'Exportar como CSV',
                'className' => 'btn btn-primary',
            ),
            array(
                'text' => 'Excel',
                'extend' => 'excelHtml5',
                'titleAttr' => 'Exportar como Excel',
                'className' => 'btn btn-primary',
            ),
        );
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-three">
                <div class="widget-heading">
                    <div class="">
                        <h5>Reservaciones - {{ date("Y-m-d", strtotime($data['init']))}} a {{ date("Y-m-d", strtotime($data['end']))}}</h5>
                    </div>
                    <div class="task-action">
                        <button class="btn btn-primary __btn_create" data-title="Filtro de reservaciones" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</button>
                    </div>
                </div>
                <div class="widget-content">
                    <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
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
                                                        <span class="badge badge-light-success">Confirmado</span>
                                                        @break
                                                    @case('PENDING')
                                                        <span class="badge badge-light-info">Pendiente</span>
                                                        @break
                                                    @default                                                            
                                                @endswitch
                                            @else
                                                    <span class="badge badge-light-danger">Cancelado</span>
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
        <div class="col-12 col-sm-4">
            <div class="widget widget-chart-three mb-3">
                <div class="widget-heading">
                    <div class="">
                        <h5>Resumen por estatus</h5>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table dt-table-hover">
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
                </div>
            </div>

            <div class="widget widget-chart-three mb-3">
                <div class="widget-heading">
                    <div class="">
                        <h5>Resumen por sitio</h5>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table dt-table-hover">
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
                </div>
            </div>

            <div class="widget widget-chart-three mb-3">
                <div class="widget-heading">
                    <div class="">
                        <h5>Resumen por destino</h5>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table dt-table-hover">
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

            <div class="widget widget-chart-three mb-3">
                <div class="widget-heading">
                    <div class="">
                        <h5>Resumen afiliados</h5>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="table-responsive">
                        <table class="table dt-table-hover">
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

    <x-modals.reports.modal :data="$data" :services="$services" :zones="$zones" :websites="$websites" />
@endsection