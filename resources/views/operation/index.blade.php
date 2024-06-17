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
@extends('layout.app')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/operations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/operations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/operations/download.min.js') }}"></script>
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
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                @if(sizeof( $dates ))
                    @php
                        $cont = 0;
                    @endphp
                    @foreach ($dates as $key => $value)
                        @php
                            $cont++;
                        @endphp
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ ( $cont == 1 ) ? 'active' : '' }}" href="#tab-{{ $key }}" data-bs-toggle="tab" role="tab" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                {{ $key }}
                            </button>
                        </li>                        
                    @endforeach
                @endif
            </ul>
            <div class="tab-content" id="pills-tabContent">                
                @if(sizeof( $dates ))
                    @php
                        $cont = 0;
                    @endphp
                    @foreach ($dates as $key => $value)
                        @php
                            $cont++;
                        @endphp 
                        <div class="tab-pane fade {{ ( $cont == 1 ) ? 'show active' : '' }}" id="tab-{{ $key }}" role="tabpanel">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <div class="section general-info">
                                        <div class="info p-0">
                                            <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                                                <thead>
                                                    <tr>                                                        
                                                        <th>Sitio</th>
                                                        <th class="text-center">Tipo</th>
                                                        <th>Código</th>
                                                        <th>Cliente</th>
                                                        <th>Teléfono</th>
                                                        <th>Pickup</th>
                                                        <th>Vehículo</th>
                                                        <th>Pasajeros</th>
                                                        <th>Desde</th>
                                                        <th>Hacia</th>
                                                        <th>Pago</th>
                                                        <th>Total</th>
                                                        <th>Moneda</th>
                                                        <th>Comentarios</th>
                                                        <th>Ref.</th>
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
                                                                <td>{{ $valueItem->client_phone }}</td>
                                                                <td>{{ date("H:i", strtotime($valueItem->filtered_date)) }}</td>
                                                                <td>{{ $valueItem->service_name }}</td>
                                                                <td>{{ $valueItem->passengers }}</td>
                                                                <td>
                                                                    @if($valueItem->operation_type == 'arrival')
                                                                        {{ $valueItem->from_name }}
                                                                        @if(!empty($valueItem->flight_number))
                                                                            [{{ $valueItem->flight_number }}]
                                                                        @endif
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
                                                                <td>{{ $valueItem->reference }}</td>
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
                    @endforeach
                @endif

            </div>
        </div>
    </div>
@endsection