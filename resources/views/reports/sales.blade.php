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
@extends('layout.app')
@section('title') Reporte De Ventas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/report_sales.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/report_sales.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/reports/sales.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de ventas",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            ),            
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> CSV',
                'extend' => 'csvHtml5',
                'titleAttr' => 'Exportar como CSV',
                'className' => 'btn btn-primary',
            ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Excel',
                'extend' => 'excelHtml5',
                'titleAttr' => 'Exportar como Excel',
                'className' => 'btn btn-primary',
            ),
        );
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
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
                <table id="dataSales" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>                                        
                            <th class="text-center">SITIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">ESTATUS</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">SERVICIO</th>
                            <th class="text-center">PASAJEROS</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">PENDIENTE</th>
                            <th class="text-center">MÉTODO</th>
                            <th class="text-center">DESTINO</th>
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
                                    <td class="text-center">{{ $item->site_name }}</td>
                                    <td class="text-center">
                                        <a href="/reservations/detail/{{ $item->id }}"> {{ $item->reservation_codes }}</a>
                                    </td> 
                                    <td class="text-center">
                                        @if ($item->is_cancelled == 0)                                                                                                   
                                            @switch($item->status)
                                                @case('CONFIRMED')
                                                    <span class="badge badge-light-warning mb-2 me-4">Confirmado</span>
                                                    @break
                                                @case('PENDING')
                                                    <span class="badge badge-light-info mb-2 me-4">Pendiente</span>
                                                    @break
                                                @default                                                            
                                            @endswitch
                                        @else
                                                <span class="badge badge-light-danger mb-2 me-4">Cancelado</span>
                                        @endif
                                    </td> 
                                    <td class="text-center">{{ $item->client_full_name }}</td>                                           
                                    <td class="text-center">{{ $item->service_type_name }}</td>
                                    <td class="text-center">{{ $item->passengers }}</td>
                                    <td class="text-end">{{ $item->total_sales }}</td>
                                    <td class="text-center">{{ $item->currency }}</td>
                                    <td class="text-end" {{ (($total_pending < 0)? "style=color:green;font-weight:bold;":"") }}>{{ number_format(($total_pending),2) }}</td>
                                    <td class="text-center">
                                        @php
                                                $payments = PaymentsTrait::getPayments($item->id);
                                                if(sizeof( $payments ) >= 1):
                                                    foreach($payments as $keyP => $valueP):                                                        
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
                    </div>
                </div>
            </div>

            <div class="widget widget-chart-three">
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
        </div>
    </div>
    
    <x-modals.filters.bookings :data="$data" :isSearch="1"  :vehicles="$vehicles" :zones="$zones" :websites="$websites" />
@endsection