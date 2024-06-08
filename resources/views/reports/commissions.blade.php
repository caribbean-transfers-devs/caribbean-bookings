@php
    $users = [];
    $exchange_rate = 16.50;
@endphp
@extends('layout.app')
@section('title') Comisiones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
    <style>
/* Ocultar el "Powered by PayPal" */
.paypal-button-context iframe + div {
    display: none;
}

/* Asegurar que el contenedor se renderiza */
#paypal-button-container {
    display: block;
}
    </style>
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
                'extend' => 'copyHtml5',
                'text' => 'Copiar',
                'titleAttr' => 'Copiar datos',
                'className' => 'btn btn-primary',
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
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
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
                                        $users[ $value->employee ]['MXN'] += $value->total_sales;
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
                <div class="mt-3 px-2">
                    <h5>Resumen</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>                                                        
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>USD</th>
                                    <th>MXN</th>
                                    <th>Total</th>
                                    <th>Comisión</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($users) >= 1)
                                    @foreach($users as $key => $value)
                                        @php
                                            $total = ($value['USD'] * $exchange_rate) + $value['MXN'];
                                            $commission = 0;
                                            if($total >= 50000 && $total <= 74999):
                                                //$commission = 2500;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 75000 && $total <= 99999):
                                                //$commission = 3750;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 100000 && $total <= 124999):
                                                //$commission = 6250;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 125000 && $total <= 174999):
                                                //$commission = 8750;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 200000):
                                                //$commission = 100000;
                                                $commission = 0.05 * $total;
                                            endif;
                                            
                                        @endphp
                                        <tr>
                                            <td>{{ $key }}</td>
                                            <td>{{ $value['QUANTITY'] }}</td>
                                            <td>{{ number_format($value['USD'],2) }}</td>
                                            <td>{{ number_format($value['MXN'],2) }}</td>
                                            <td>{{ number_format($total,2) }}</td>
                                            <td>{{ number_format($commission,2) }}</td>                                       
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
    <div id="paypal-button-container"></div>

    <x-modals.reservations.reports :data="$search" />
@endsection

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    
    <script src="https://www.paypal.com/sdk/js?client-id=ASGgN8z0Yc7y1F0e5E9wd46AEg1eLYrlo2DjeNVC5y4CeV2AeEoEcMJs9Wa3EabanstiseLJNZ3FK6KE&currency=MXN&funding-eligibility=card" sync></script>
    
    <script>
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color:  'blue',
                shape:  'rect',
                disableMaxWidth: true,
                tagline: false,
                displayOnly: ['credit'],
            },            
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '0.01'  // Reemplaza con el monto que deseas cobrar
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Transaction completed by ' + details.payer.name.given_name);
                    // Redireccionar o actualizar el UI según sea necesario
                });
            },
        }).render('#paypal-button-container');
    </script>
    <script src="{{ mix('/assets/js/sections/reports/commissions.min.js') }}"></script>

@endpush