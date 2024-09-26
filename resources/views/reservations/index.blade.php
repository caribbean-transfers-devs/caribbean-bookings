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
                'text' => 'Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtro de reservaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            ),
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
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-9 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
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
                <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Referencia</th>
                            <th>Fecha/Hora</th>
                            <th>Sitio</th>
                            <th>Origen de venta</th>
                            <th>Estatus</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Pasajeros</th>
                            <th>Total</th>
                            <th>Moneda</th>
                            <th>Pendiente</th>
                            <th>Método</th>
                            <th>Origen</th>
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

                                            if(!isset( $destinations[$item->destination_name_to] )):
                                                $destinations[$item->destination_name_to] = [
                                                    'USD' => 0,
                                                    'MXN' => 0,
                                                    'count' => 0
                                                ];
                                            endif;
                                            $destinations[$item->destination_name_to][$item->currency] += $item->total_sales;
                                            $destinations[$item->destination_name_to]['count']++;
                                        endif;

                                    else:
                                        $resume['status']['CANCELLED'][$item->currency] += $item->total_sales;
                                        $resume['status']['CANCELLED']['count']++;
                                    endif;                                                
                                    $total_pending = $item->total_sales - $item->total_payments;
                                @endphp
                                <tr class="{{ ( $item->is_today != 0 ? 'bs-tooltip' : '' ) }}" title="{{ ( $item->is_today != 0 ? 'Es una reserva que se opera el mismo día en que se creo #: '.$item->reservation_id : '' ) }}" style="{{ ( $item->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}" data-reservation="{{ $item->reservation_id }}" data-is_round_trip="{{ $item->is_round_trip }}">
                                    <td>
                                        @php
                                            $codes_string = "";
                                            $codes = explode(",",$item->reservation_codes);
                                            foreach ($codes as $key => $code) {
                                                $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                            }
                                        @endphp
                                        @if (RoleTrait::hasPermission(38))
                                            <a href="/reservations/detail/{{ $item->reservation_id }}"><?=$codes_string?></a>
                                        @else
                                            <?=$codes_string?>
                                        @endif
                                    </td>
                                    <td>
                                        <?=( !empty($item->reference) ? '<p class="mb-1">'.$item->reference.'</p>' : '' )?>
                                        <span class="badge badge-light-{{ $item->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $item->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>
                                        
                                    </td>
                                    <td class="text-center">
                                        {{ date("Y-m-d", strtotime($item->created_at)) }}
                                        {{ "[".date("H:i", strtotime($item->created_at))."]" }}
                                    </td>
                                    <td>{{ $item->site_name }}</td>
                                    <td>{{ !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' }}</td>
                                    <td class="text-center">
                                        @if ($item->is_cancelled == 0)
                                            @if($item->open_credit == 1)
                                                <span class="badge badge-light-warning">Crédito Abierto</span>
                                            @else
                                                @switch($item->status)
                                                    @case('CONFIRMED')
                                                        <span class="badge badge-light-success">Confirmado</span>
                                                        @break
                                                    @case('PENDING')
                                                        <span class="badge badge-light-info">Pendiente</span>
                                                        @break
                                                    @default
                                                @endswitch
                                            @endif                                            
                                        @else
                                            <span class="badge badge-light-danger">Cancelado</span>
                                        @endif
                                    </td> 
                                    <td>
                                        <span>{{ $item->full_name }}</span>
                                    </td>
                                    <td>{{ $item->service_type_name }}</td>
                                    <td class="text-center">{{ $item->passengers }}</td>
                                    <td class="text-end">{{ $item->total_sales }}</td>
                                    <td class="text-center">{{ $item->currency }}</td>
                                    <td class="text-end" {{ (($total_pending < 0)? "style=color:green;font-weight:bold;":"") }}>{{ number_format(($total_pending),2) }}</td>
                                    <td class="text-center">{{ ((empty($item->payment_type_name))? 'CASH' : $item->payment_type_name ) }}</td>
                                    <td class="text-center">{{ $item->destination_name_from }}</td>
                                    <td class="text-center">{{ $item->destination_name_to }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-3 col-lg-12 col-12 ">
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

            <div class="widget widget-chart-three">
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

    <x-modals.reports.modal :data="$data" :services="$services" :zones="$zones" :websites="$websites" :originsales="$origin_sales" :istoday="1" />
@endsection