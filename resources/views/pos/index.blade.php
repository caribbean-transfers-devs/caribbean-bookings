@php
    use App\Traits\RoleTrait;
    use Carbon\Carbon;
@endphp
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
    function getShift($created_at) {
        $time = Carbon::parse($created_at)->format('H:i');

        if ($time >= '08:30' && $time <= '15:00') {
            return 'Matutino';
        } elseif ($time >= '15:01' && $time <= '21:00') {
            return 'Vespertino';
        } else {
            return 'Guardia';
        }
    }
@endphp
@extends('layout.app')
@section('title') Ventas capturadas @endsection

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
    <script src="{{ mix('assets/js/sections/pos/sales.min.js') }}"></script>
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
                        <h5>Ventas capturadas - {{ date("Y-m-d", strtotime($data['init']))}} a {{ date("Y-m-d", strtotime($data['end']))}}</h5>
                    </div>
                    <div class="task-action">
                        <button class="btn btn-primary __btn_create" data-title="Filtro de reservaciones" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</button>  
                    </div>
                </div>
                <div class="widget-content">
                    <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Vendedor</th>
                                <th>Terminal</th>
                                <th>Folio</th>
                                <th>Venta</th>
                                <th>Moneda</th>
                                <th>Zona</th>
                                <th>Unidad</th>
                                <th>Moneda2</th>
                                <th>Pax</th>
                                <th>Servicio</th>
                                <th>Turno</th>
                                <th>Hora</th>
                                <th>Observaciones</th>
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
                                        <td>
                                            @if(RoleTrait::hasPermission(53))
                                                <a href="punto-de-venta/detail/{{ $item->id }}">{{ substr($item->created_at, 0, 10) }}</a>
                                            @else
                                                {{ substr($item->created_at, 0, 10) }}
                                            @endif
                                        </td>
                                        <td>{{ $item->vendor }}</td>                                           
                                        <td>{{ $item->terminal ? str_replace('T', 'Terminal ', $item->terminal) : 'No se capturó la terminal' }}</td>
                                        <td>{{ $item->reference }}</td>
                                        <td>{{ $item->total_sales }}</td>
                                        <td>{{ $item->currency }}</td>
                                        <td>{{ $item->destination_name }}</td>
                                        <td>{{ $item->service_type_name }}</td>
                                        <td>{{ ((empty($item->payment_type_name))? 'Efectivo' : str_replace(['CARD', 'CASH'], ['Tarjeta', 'Efectivo'], $item->payment_type_name) ) }}</td>
                                        <td>{{ $item->passengers }}</td>
                                        <td>{{ (( $item->site_id == 21 ) ? 'Llegada':'Salida') }}</td>
                                        <td>{{ getShift($item->created_at) }}</td>
                                        <td>{{ substr($item->created_at, -8, 5) }}</td>
                                        <td>{{ $item->comments }}</td>
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
                                    <th style="width:35%;">Destino</th>
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
        </div>
    </div>

    <x-modals.reports.modal :data="$data" :services="$services" :zones="$zones" :websites="$websites" />    
@endsection