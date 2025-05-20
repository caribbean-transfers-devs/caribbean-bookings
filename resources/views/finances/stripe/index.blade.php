@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    $resume = [
        'BOOKINGS' => [],
        'USD' => [
            'TOTAL' => 0,
            'QUANTITY' => 0
        ],
        'MXN' => [
            'TOTAL' => 0,
            'QUANTITY' => 0
        ],
        'CHARGED' => [
            'TOTAL' => 0,
            'QUANTITY' => 0            
        ],
        'PAID' => [
            'TOTAL' => 0,
            'QUANTITY' => 0            
        ],        
        // 'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        // 'PAID' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
    ];
@endphp
@extends('layout.app')
@section('title') Conciliación Stripe @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/finances/conciliations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/finances/conciliations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('/assets/js/sections/finances/conciliations.min.js') }}"></script>  
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Ayuda',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Ayuda de Stripe",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#helpStripeModal'
                )
            ),            
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtro de reservaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            ),
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> Conciliar Stripe',
                'className' => 'btn btn-primary btnConciliationStripe',
            ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="layout-columns" class=""><path fill="" fill-rule="evenodd" d="M7 5a2 2 0 00-2 2v10a2 2 0 002 2h1V5H7zm3 0v14h4V5h-4zm6 0v14h1a2 2 0 002-2V7a2 2 0 00-2-2h-1zM3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" clip-rule="evenodd"></path></svg> Administrar columnas',
                'titleAttr' => 'Administrar columnas',
                'className' => 'btn btn-primary __btn_columns',
                'attr' => array(
                    'data-title' =>  "Filtro de reservaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#columnsModal',
                    'data-table' => 'bookings',// EL ID DE LA TABLA QUE VAMOS A OBTENER SUS HEADERS
                    'data-container' => 'columns', //EL ID DEL DIV DONDE IMPRIMIREMOS LOS CHECKBOX DE LOS HEADERS                    
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
                'text' => 'Tipo de cambio: '.$exchange,
                'titleAttr' => 'Tipo de cambio',
                'className' => 'btn btn-warning',
            ),
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
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
                
                <table id="dataStripe" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">FECHA</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">ESTATUS</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">SERVICIO</th>
                            <th class="text-center">PAX</th>
                            <th class="text-center">DESTINO</th>
                            <th class="text-center">IMPORTE</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">METODO DE PAGO</th>
                            <th class="text-center">IMPORTE PESOS</th>
                            <th class="text-center">ID STRIPE / REFERENCIA</th>
                            <th class="text-center">FECHA DE COBRO STRIPE</th>
                            <th class="text-center">ESTATUS DE COBRO STRIPE</th>
                            <th class="text-center">TOTAL COBRADO EN STRIPE</th>
                            <th class="text-center">COMISIÓN DE STRIPE</th>
                            <th class="text-center">FECHA DE PAGO STRIPE</th>
                            <th class="text-center">ESTATUS DE PAGO STRIPE</th>
                            <th class="text-center">TOTAL PAGADO POR STRIPE</th>
                            <th class="text-center">TIENE REEMBOLSO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($conciliations)>=1)
                            @foreach($conciliations as $key => $item)
                                @php
                                    if( isset( $resume[ $item->currency ] ) ):
                                        $resume[ $item->currency ]['TOTAL'] += $item->total_payments_stripe;
                                        $resume[ $item->currency ]['QUANTITY']++;
                                    endif;

                                    if( $item->date_conciliation != NULL && isset( $resume[ 'CHARGED' ] ) ){
                                        $resume[ 'CHARGED' ]['TOTAL'] += $item->amount;
                                        $resume[ 'CHARGED' ]['QUANTITY']++;
                                    }

                                    if( $item->deposit_date != NULL && isset( $resume[ 'PAID' ] ) ){
                                        $resume[ 'PAID' ]['TOTAL'] += $item->amount;
                                        $resume[ 'PAID' ]['QUANTITY']++;
                                    }                                    
                                @endphp
                                {{-- @if ($item->reservation_id == 53769)
                                    @dump($item)
                                @endif --}}
                                <tr>
                                    <td class="text-center">{{ $item->reservation_id }}</td>
                                    <td class="text-center">{{ date("Y-m-d", strtotime($item->created_at)) }}</td>
                                    <td class="text-center">{{ $item->site_name }}</td>
                                    <td class="text-center">
                                        @php
                                            $codes_string = "";
                                            $codes = explode(",",$item->reservation_codes);
                                            foreach ($codes as $key => $code) {
                                                $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                            }
                                        @endphp
                                        @if (auth()->user()->hasPermission(61))
                                            <a href="/reservations/detail/{{ $item->reservation_id }}"><?=$codes_string?></a>
                                        @else
                                            <?=$codes_string?>
                                        @endif
                                    </td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($item->reservation_status) }}">{{ auth()->user()->statusBooking($item->reservation_status) }}</button></td>
                                    <td class="text-center">{{ $item->full_name }}</td>
                                    <td class="text-center">{{ $item->service_type_name }}</td>
                                    <td class="text-center">{{ $item->passengers }}</td>
                                    <td class="text-center">{{ $item->to_name }}</td>
                                    <td class="text-center">$ {{ number_format($item->total_payments_stripe, 2) }}</td>
                                    <td class="text-center">{{ $item->currency }}</td>
                                    <td class="text-center">{{ $item->payment_type_name }}</td>
                                    <td class="text-center">$ {{ number_format(( $item->currency == "MXN" ? $item->total_payments_stripe : ( $item->total_payments_stripe * $exchange ) ), 2) }}</td>
                                    <td class="text-center">
                                        @if ( !empty($item->reference_stripe) )
                                            <a href="javascript:void(0)" class="chargeInformationStripe" data-reference="{{ $item->reference_stripe }}">{{ $item->reference_stripe }}</a>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->date_conciliation != NULL ? date("Y-m-d", strtotime($item->date_conciliation)) : "SIN FECHA DE COBRO" }}</td>
                                    <td class="text-center" style="color:#fff;background-color:#{{ $item->date_conciliation != NULL ? '00ab55' : 'e7515a' }};">
                                        @if ($item->date_conciliation != NULL)
                                            COBRADO
                                        @else
                                            PENDIENTE DE COBRAR
                                        @endif
                                    </td>
                                    <td class="text-center">$ {{ number_format($item->amount, 2) }}</td>
                                    <td class="text-center">$ {{ number_format($item->total_fee, 2) }}</td>
                                    <td class="text-center">{{ $item->deposit_date != NULL ? date("Y-m-d", strtotime($item->deposit_date)) : "SIN FECHA DE PAGO " }}</td>
                                    <td class="text-center" style="color:#fff;background-color:#{{ $item->deposit_date != NULL ? '00ab55' : 'e7515a' }};">
                                        @if ($item->deposit_date != NULL)
                                            PAGADO
                                        @else
                                            PENDIENTE DE PAGO
                                        @endif
                                    </td>
                                    <td class="text-center">$ {{ number_format(( $item->total_net ), 2) }}</td>
                                    <td class="text-center">
                                        @if ( $item->is_refund > 0 )
                                            <button class="btn btn-success">Sí</button>
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
                                    <th class="text-center"></th>
                                    <th class="text-center">CANTIDAD</th>
                                    <th class="text-center">MONTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TOTAL EN DOLARES</td>
                                    <td class="text-center">{{ $resume['USD']['QUANTITY'] }}</td>
                                    <td class="text-center">{{ number_format($resume['USD']['TOTAL'],2) }}</td>
                                </tr>
                                <tr>
                                    <td>TOTAL EN PESOS</td>
                                    <td class="text-center">{{ $resume['MXN']['QUANTITY'] }}</td>
                                    <td class="text-center">{{ number_format($resume['MXN']['TOTAL'],2) }}</td>
                                </tr>
                                <tr>
                                    <td>TOTAL COBRADO EN STRIPE</td>
                                    <td class="text-center">{{ $resume['CHARGED']['QUANTITY'] }}</td>
                                    <td class="text-center">{{ number_format($resume['CHARGED']['TOTAL'],2) }}</td>
                                </tr>
                                <tr>
                                    <td>TOTAL PAGADO EN STRIPE</td>
                                    <td class="text-center">{{ $resume['PAID']['QUANTITY'] }}</td>
                                    <td class="text-center">{{ number_format($resume['PAID']['TOTAL'],2) }}</td>
                                </tr>                                
                            </tbody>
                        </table>
                    </div>
                </div>                
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :currencies="$currencies" />
    <x-modals.reports.columns />
    <x-modals.finances.charge_stripe />
    <x-modals.finances.help_stripe />
@endsection