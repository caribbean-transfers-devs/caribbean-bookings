@php
    use App\Traits\Reports\PaymentsTrait;
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
    <link href="{{ mix('/assets/css/reservations/index.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/reservations/index.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <script src="{{ mix('assets/js/views/reservations/reservationsIndex.js') }}"></script>

    <script>
        const choices = new Choices(document.getElementById('site'),{
            removeItemButton: true,
            loadingText: 'Cargando...',
            noResultsText: 'No se encontraron resultados',
            noChoicesText: 'No hay opciones para elegir',
            itemSelectText: 'Clic para elegir',
        });

        $('#reservations_table_sales').DataTable({
            dom: 'Bfrtip',
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
            },
            paging: false,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    </script>
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
                            <table id="reservations_table_sales" class="table table-striped table-sm">
                                <thead>
                                    <tr>                                        
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
                                                    if($item->status == "CONFIRMED" || $item->status == "PENDING"):
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
                                                $total_pending = $item->total_sales - $item->total_payments;
                                            @endphp
                                            <tr>
                                                <td>{{ $item->site_name }}</td>
                                                <td>
                                                    <a href="/reservations/detail/{{ $item->id }}"> {{ $item->reservation_codes }}</a>
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
                                                <td class="text-center">
                                                    @php
                                                            $payments = PaymentsTrait::getPayments($item->id);
                                                            if(sizeof( $payments ) >= 1):
                                                                foreach($payments as $keyP => $valueP):
                                                                    //dd();
                                                                    $total_ = 0;

                                                                    if($valueP->operation == "division"):
                                                                        $total_ = $valueP->total/$valueP->exchange_rate;
                                                                    else:
                                                                        $total_ = $valueP->total*$valueP->exchange_rate;
                                                                    endif;
                                                                    echo '['.$valueP->payment_method.' | '.$total_.' | '.$valueP->reference.']';
                                                                endforeach;
                                                            endif;                                                            
                                                    @endphp
                                                </td>
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
                            @php
                                $total = [
                                    "quantity" => 0,
                                    "USD" => 0,
                                    "MXN" => 0,
                                ];
                            @endphp
                            @foreach($sites as $key => $value)
                                @php
                                    $total['quantity'] += $value['count'];
                                    $total['USD'] += $value['USD'];
                                    $total['MXN'] += $value['MXN'];
                                @endphp
                            <tr>
                                <td>{{ strtolower( $key) }}</td>
                                <td class="text-center">{{ $value['count'] }}</td>
                                <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($value['MXN'],2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td class="text-center">{{ $total['quantity'] }}</td>
                                <td>{{ number_format($total['USD'],2) }}</td>
                                <td>{{ number_format($total['MXN'],2) }}</td>
                            </tr>
                        </tfoot>
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

<x-modals.reports.modal :data="$data" :services="$services" :zones="$zones" :websites="$websites" />    
@endsection