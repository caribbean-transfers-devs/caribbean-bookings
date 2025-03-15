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
    <link href="{{ mix('/assets/css/sections/reports/cash.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/cash.min.css') }}" rel="stylesheet" >
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
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de pagos en efectivo",
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

                <table id="dataCash" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">PICKUP</th>
                            <th class="text-center"></th>                                                     
                            <th class="text-center">SITIO</th>
                            <th class="text-center">TIPO</th>
                            <th class="text-center">ESTATUS OPERACIÓN</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">VEHÍCULO</th>
                            <th class="text-center">PASAJEROS</th>
                            <th class="text-center">DESDE</th>
                            <th class="text-center">HACIA</th>
                            <th class="text-center">PAGO</th>
                            <th class="text-center">VENTAS</th>
                            <th class="text-center">MONEDA</th>
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
                                        <td class="text-center">{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-order {{ (($value->payment_reconciled == 0)? 'btn-warning':'btn-success')}}" onClick="updateConfirmation(event,{{$value->reservation_id}},{{$confirmation_type}})">
                                                {{ (($value->payment_reconciled == 0)? 'N':'Y')}}
                                            </button>
                                        </td>
                                        <td class="text-center">{{ $value->site_name }}</td>
                                        <td class="text-center">{{ $value->final_service_type }}</td>
                                        <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                        <td class="text-center">
                                            @if (RoleTrait::hasPermission(10))
                                                <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                            @else
                                                {{ $value->code }}
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                        <td class="text-center">{{ $value->service_name }}</td>
                                        <td class="text-center" class="text-center">{{ $value->passengers }}</td>
                                        <td class="text-center">{{ $operation_from }}</td>
                                        <td class="text-center">{{ $operation_to }}</td>
                                        <td class="text-center">
                                            @if($value->status == "PENDIENTE")
                                                <span class="badge btn-warning">Pendiente</span>
                                            @endif
                                            @if($value->status == "CONFIRMADO")
                                                <span class="badge btn-success">Confirmado</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ number_format($value->total_sales,2) }}</td>
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

    <x-modals.filters.bookings :data="$data" />
@endsection