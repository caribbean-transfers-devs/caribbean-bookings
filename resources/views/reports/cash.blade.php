@php
    use App\Traits\RoleTrait;
    use App\Traits\Reports\PaymentsTrait;
    
    $resume = [
        'PENDIENTE' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        'CONFIRMADO' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
    ];
    $sites = [];
    $destinations = [];
@endphp
@extends('layout.app')
@section('title') Operación @endsection

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
    <script src="{{ mix('/assets/js/sections/reports/cash.min.js') }}"></script>
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
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
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
                            <th>Pickup</th>
                            <th></th>                                                     
                            <th>Sitio</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estatus Op.</th>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Pasajeros</th>
                            <th>Desde</th>
                            <th>Hacia</th>
                            <th>Pago</th>
                            <th>Ventas</th>
                            <th>Moneda</th>                                        
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($items)>=1)
                            @foreach($items as $key => $value)
                                @php                                                
                                    $show = false;
                                    $payments = PaymentsTrait::getPayments($value->reservation_id);
                                    if(sizeof($payments) >= 1):
                                        foreach($payments as $keyP => $valueP):
                                            if($valueP->payment_method == 'CASH'):
                                                $show = true;
                                            endif;
                                        endforeach;
                                    else:
                                        $show = true;
                                    endif;
                                @endphp

                                @if( $show )
                                    @php                                                
                                        $payment = ( $value->total_sales - $value->total_payments );
                                        if($payment < 0) $payment = 0;

                                        $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                                        $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                        $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                        $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                                        switch ($operation_status) {
                                            case 'PENDING':
                                                $label = 'secondary';
                                                break;
                                            case 'COMPLETED':
                                                $label = 'success';
                                                break;
                                            case 'NOSHOW':
                                                $label = 'warning';
                                                break;
                                            case 'CANCELLED':
                                                $label = 'danger';
                                                break;
                                            default:
                                                $label = 'secondary';
                                                break;
                                        }
                                        $confirmation_type = (( $value->payment_reconciled == 0 )? 1 : 0);

                                        if( isset( $resume[ $value->status ] ) ):
                                            $resume[ $value->status ][ $value->currency ] += $value->total_sales;
                                            $resume[ $value->status ]['count']++;
                                        endif;

                                    @endphp
                                    <tr>
                                        <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                        <td>
                                            <button class="btn btn-order {{ (($value->payment_reconciled == 0)? 'btn-warning':'btn-success')}}" onClick="updateConfirmation(event,{{$value->reservation_id}},{{$confirmation_type}})">
                                                {{ (($value->payment_reconciled == 0)? 'N':'Y')}}
                                            </button>
                                        </td>
                                        <td>{{ $value->site_name }}</td>
                                        <td>{{ $value->final_service_type }}</td>
                                        <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                        <td>
                                            @if (RoleTrait::hasPermission(10))
                                                <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                            @else
                                                {{ $value->code }}
                                            @endif
                                        </td>
                                        <td>{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                        <td>{{ $value->service_name }}</td>
                                        <td class="text-center">{{ $value->passengers }}</td>
                                        <td>{{ $operation_from }}</td>
                                        <td>{{ $operation_to }}</td>
                                        <td class="text-center">
                                            @if($value->status == "PENDIENTE")
                                                <span class="badge btn-warning">Pendiente</span>
                                            @endif
                                            @if($value->status == "CONFIRMADO")
                                                <span class="badge btn-success">Confirmado</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($value->total_sales,2) }}</td>
                                        <td class="text-center">{{ $value->currency }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="mt-3 px-2">
                    <h5>Resumen por estatus</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:35%;">Estatus</th>
                                    <th style="width:25%" class="text-center">Cantidad</th>
                                    <th class="text-center">USD</th>
                                    <th class="text-center">MXN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pendiente</td>
                                    <td class="text-center">{{ $resume['PENDIENTE']['count'] }}</td>
                                    <td class="text-end">{{ number_format($resume['PENDIENTE']['USD'],2) }}</td>
                                    <td class="text-end">{{ number_format($resume['PENDIENTE']['MXN'],2) }}</td>
                                </tr>
                                <tr>
                                    <td>Confirmado</td>
                                    <td class="text-center">{{ $resume['CONFIRMADO']['count'] }}</td>
                                    <td class="text-end">{{ number_format($resume['CONFIRMADO']['USD'],2) }}</td>
                                    <td class="text-end">{{ number_format($resume['CONFIRMADO']['MXN'],2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        // dump($date_search);
    @endphp
    <x-modals.reservations.reports :data="$date_search" />
@endsection