@php
    $reservation_status = auth()->user()->reservationStatus();
    $service_operation_status = auth()->user()->statusOperationService();
    $currencies = auth()->user()->Currencies();

    $resume = [
        'BOOKINGS' => [],
        'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        'PAID' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
    ];
@endphp
@extends('layout.app')
@section('title') Reporte de Balances (Efectivo) @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/reports/cash.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/cash.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
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
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar Excel',
                'extend' => 'excelHtml5',
                'titleAttr' => 'Exportar Excel',
                'className' => 'btn btn-primary',
                'exportOptions' => [
                    'columns' => ':visible'  // Solo exporta las columnas visibles   
                ]
            ),
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-printer"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Imprimir',
                'extend' => 'print',
                'titleAttr' => 'Imprimir',
                'className' => 'btn btn-primary',
                'exportOptions' => [
                    'columns' => ':visible',  // Solo exporta las columnas visibles   
                    // 'modifier' => [
                    //     'page' => 'current' // Imprimir solo la página actual
                    // ]
                ]
            ),            
        );
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
                            <th class="text-center">ID</th>
                            <th class="text-center">FECHA DE SERVICIO</th>
                            <th class="text-center">TIPO DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>                            
                            <th class="text-center"># DE SERVICIO</th>
                            <th class="text-center">ESTATUS DE SERVICIO</th>
                            <th class="text-center">TIPO DE SERVICIO EN OPERACIÓN</th>
                            <th class="text-center">NOMBRE DEL CLIENTE</th>
                            <th class="text-center">DESDE</th>
                            <th class="text-center">HACIA</th>
                            <th class="text-center">DETALLES</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">TOTAL COBRADO EN EFECTIVO</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">ESTATUS DE PAGO</th>
                            <th class="text-center">ESTATUS DE CONCILIACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($items)>=1)
                            @foreach($items as $key => $operation)
                                @php
                                    if( isset( $resume[ $operation->payment_status ] ) && !in_array($operation->reservation_id, $resume['BOOKINGS']) ):
                                        $resume['BOOKINGS'][] = $operation->reservation_id;
                                        $resume[ $operation->payment_status ][ $operation->currency ] += ( $operation->cash_amount > 0 ? $operation->cash_amount : $operation->total_sales );
                                        $resume[ $operation->payment_status ]['count']++;
                                    endif;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $operation->reservation_id }}</td>
                                    <td class="text-center">{{ auth()->user()->setDateTime($operation, "date") }}</td>
                                    <td class="text-center"><span class="badge badge-{{ $operation->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermission(61))
                                            <a href="/reservations/detail/{{ $operation->reservation_id }}" target="_black"><p class="mb-1">{{ $operation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $operation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center"><?=( !empty($operation->reference) ? '<p class="mb-1">'.$operation->reference.'</p>' : '' )?></td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($operation->reservation_status) }}">{{ auth()->user()->statusBooking($operation->reservation_status) }}</button></td>                                    
                                    <td class="text-center"><?=auth()->user()->renderServicePreassignment($operation)?></td>
                                    <td class="text-center"><?=auth()->user()->renderServiceStatusOP($operation)?></td>
                                    <td class="text-center">{{ $operation->final_service_type }}</td>
                                    <td class="text-center">{{ $operation->full_name }}</td>
                                    <td class="text-center">{{ auth()->user()->setFrom($operation, "name") }}</td>
                                    <td class="text-center">{{ auth()->user()->setTo($operation, "name") }}</td>
                                    <td class="text-center">
                                        {{ $operation->op_one_comments }} <br>
                                        {{ $operation->op_two_comments }} <br>
                                        {{ $operation->cash_references }}
                                    </td>
                                    <td class="text-center">{{ number_format($operation->total_sales,2) }}</td>
                                    <td class="text-center">{{ number_format(( $operation->cash_amount > 0 ? $operation->cash_amount : $operation->total_sales ),2) }}</td>
                                    <td class="text-center">{{ $operation->currency }}</td>
                                    <td class="text-center" <?=auth()->user()->classStatusPayment($operation)?>>{{ auth()->user()->statusPayment($operation->payment_status) }}</td>
                                    <td class="text-center {{ auth()->user()->hasPermission(120) && $operation->cash_is_conciliated == 0 ? 'cashConciliation' : '' }}" data-code="{{ $operation->cash_payment_ids }}" data-statu="{{ $operation->cash_is_conciliated }}" style="cursor: pointer;background-color:#{{ $operation->cash_is_conciliated == 0 ? 'e7515a' : '00ab55' }};color:#fff;">
                                        @if ( $operation->cash_is_conciliated == 0 )
                                            Click para conciliar pago
                                        @endif
                                        
                                        @if ( $operation->cash_is_conciliated == 1 )
                                            Pago conciliado
                                        @endif                                        
                                    </td>
                                </tr>
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
                                    <th class="text-center">ESTATUS</th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">USD</th>
                                    <th class="text-center">MXN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PENDIENTES</td>
                                    <td class="text-center">{{ $resume['PENDING']['count'] }}</td>
                                    <td class="text-center">{{ number_format($resume['PENDING']['USD'],2) }}</td>
                                    <td class="text-center">{{ number_format($resume['PENDING']['MXN'],2) }}</td>
                                </tr>
                                <tr>
                                    <td>PAGADOS</td>
                                    <td class="text-center">{{ $resume['PAID']['count'] }}</td>
                                    <td class="text-center">{{ number_format($resume['PAID']['USD'],2) }}</td>
                                    <td class="text-center">{{ number_format($resume['PAID']['MXN'],2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" isSearch="1" :currencies="$currencies" :serviceoperationstatus="$service_operation_status" />
    <x-modals.finances.cash_conciliation />
@endsection