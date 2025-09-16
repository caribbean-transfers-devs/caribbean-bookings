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
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="row layout-spacing">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-tools bg-white p-2 d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <!-- Título y Select -->
                        <div class="tool-form w-md-50 d-flex align-items-center mb-2 mb-md-0">
                            <!-- Select para conciliación -->
                            <form class="form w-100" action="" method="POST" id="formSearch">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="payment_stripe" id="conciliationSelect" value="{{ isset($data['payment_stripe']) ? $data['payment_stripe'] : '' }}" class="form-control">
                                    <button class="btn btn-primary rounded-0" type="button" id="conciliationActionBtn">
                                        <i class="fas fa-hand-holding-usd me-2"></i>Conciliar Pago
                                    </button>
                                    <button class="btn btn-primary" type="submit" id="conciliationSearchBtn">
                                        <i class="fas fa-hand-holding-usd me-2"></i>Buscar conciliación
                                    </button>                                
                                </div>
                            </form>
                        </div>

                        @if (auth()->user()->hasPermission(61))
                            <div>
                                <button class="btn btn-primary _effect--ripple waves-effect waves-light" id="generateStripeAutomaticConciliationData">
                                    <i class="fa-solid fa-cash-register"></i>
                                </button>
                            </div>
                        @endif
                        <!-- Botón conciliación automática temporal -->
                        
                        <!-- Botones de Acción -->
                        <div class="d-flex flex-wrap gap-2">
                            <!-- Botón de Ayuda -->
                            <button class="btn btn-outline-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#helpStripeModal">
                                <i class="fas fa-question-circle me-2"></i>Ayuda
                            </button>
                            
                            <!-- Botón de Filtros -->
                            <button class="btn btn-outline-secondary d-flex align-items-center __btn_create" data-title="Filtro de conciliación" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="fas fa-filter me-2"></i>Filtros
                            </button>
                            
                            <!-- Botón de Conciliar Stripe -->
                            <button class="btn btn-success d-flex align-items-center btnConciliationStripe">
                                <i class="fas fa-exchange-alt me-2"></i>Conciliar Cobros Stripe
                            </button>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row layout-spacing">
            <!-- Resumen por moneda -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white p-2">
                        <h5 class="mb-0">Resumen por Moneda</h5>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            @foreach($resume['total'] as $key => $currency)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-{{ $currency['color'] }} shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-{{ $currency['color'] }} text-uppercase mb-1">
                                                        Total en {{ $key }}
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        ${{ number_format($currency['amount'], 2) }}
                                                    </div>
                                                    <small class="text-muted">{{ $currency['count'] }} transacciones</small>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-{{ $currency['icon'] }} fa-2x text-{{ $currency['color'] }}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado de transacciones -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white p-2">
                        <h5 class="mb-0">Estado de Transacciones</h5>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            @foreach($resume['status'] as $key => $status)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-{{ $status['color'] }} shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-{{ $status['color'] }} text-uppercase mb-1">
                                                        {{ $status['label'] }}
                                                    </div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                        ${{ number_format($status['amount'], 2) }}
                                                    </div>
                                                    <small class="text-muted">{{ $status['count'] }} transacciones</small>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-{{ $key === 'charged' || $key === 'paid' ? 'check-circle' : 'clock' }} fa-2x text-{{ $status['color'] }}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row layout-spacing">
            <!-- Comisiones y Neto -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white p-2">
                        <h5 class="mb-0">Comisiones Stripe</h5>
                    </div>
                    <div class="card-body p-2">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Total Comisiones
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${{ number_format($resume['fees']['amount'], 2) }}
                                        </div>
                                        <small class="text-muted">{{ $resume['fees']['count'] }} transacciones</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reembolsos -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-white p-2">
                        <h5 class="mb-0">Reembolsos</h5>
                    </div>
                    <div class="card-body p-2">
                        <div class="card border-left-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                            Total Reembolsos
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            ${{ number_format($resume['refunds']['amount'], 2) }}
                                        </div>
                                        <small class="text-muted">{{ $resume['refunds']['count'] }} transacciones</small>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exchange-alt fa-2x text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- listado de pagos de stripe --}}
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
                            <th class="text-center">IMPORTE DE VENTA</th>
                            <th class="text-center">IMPORTE COBRADO</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">METODO DE PAGO</th>
                            <th class="text-center">IMPORTE PESOS</th>
                            <th class="text-center">ID STRIPE / REFERENCIA</th>
                            <th class="text-center">FECHA DE COBRO STRIPE</th>
                            <th class="text-center">ESTATUS DE COBRO STRIPE</th>
                            <th class="text-center">TOTAL COBRADO EN STRIPE</th>
                            <th class="text-center">COMISIÓN DE STRIPE</th>

                            <th class="text-center">TOTAL A DEPOSITAR POR STRIPE</th>

                            <th class="text-center">FECHA DEPOSITADA AL BANCO</th>
                            <th class="text-center">ESTATUS DEL DEPOSITO BANCO</th>
                            <th class="text-center">TOTAL DEPOSITADO AL BANCO</th>
                            <th class="text-center">REFERENCIA DEL DEPOSITO AL BANCO POR STRIPE</th>
                            <th class="text-center">BANCO</th>
                            <th class="text-center">TIENE REEMBOLSO</th>
                            <th class="text-center">TIENE DISPUTA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($conciliations)>=1)
                            @foreach($conciliations as $key => $item)
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
                                    <td class="text-center">{{ $item->total_sales }}</td>
                                    <td class="text-center">$ {{ number_format($item->total_payments, 2) }}</td>
                                    <td class="text-center">{{ $item->currency }}</td>
                                    <td class="text-center">{{ $item->payment_type_name }}</td>
                                    <td class="text-center">$ {{ number_format(round($item->total_payments_stripe), 2) }}</td>
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
                                    <td class="text-center">$ {{ number_format($item->total_net, 2) }}</td>
                                    <td class="text-center">{{ $item->deposit_date != NULL ? date("Y-m-d", strtotime($item->deposit_date)) : "SIN FECHA DE PAGO " }}</td>
                                    <td class="text-center" style="color:#fff;background-color:#{{ $item->deposit_date != NULL ? '00ab55' : 'e7515a' }};">
                                        @if ($item->deposit_date != NULL)
                                            DEPOSITADO
                                        @else
                                            PENDIENTE DE DEPOSITO
                                        @endif
                                    </td>
                                    <td class="text-center">$ {{ number_format(( $item->total_final_net ), 2) }}</td>
                                    <td class="text-center">{{ $item->reference_conciliation }}</td>
                                    <td class="text-center">{{ $item->bank_name }}</td>
                                    <td class="text-center">
                                        @if ( $item->refunded > 0 )
                                            <button class="btn btn-success">Sí</button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ( $item->disputed > 0 )
                                            <button class="btn btn-success">Sí</button>
                                        @endif
                                    </td>                                    
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- listado de pagos de stripe pero sin codigo de referencia validos --}}
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
                {{-- <div class="table-responsive"> --}}
                    <table id="dataOthersReferences" class="table table-rendering dt-table-hover" style="width:100%">
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
                                <th class="text-center">IMPORTE DE VENTA</th>
                                <th class="text-center">IMPORTE COBRADO</th>
                                <th class="text-center">MONEDA</th>
                                <th class="text-center">METODO DE PAGO</th>
                                <th class="text-center">IMPORTE PESOS</th>
                                <th class="text-center">ID STRIPE / REFERENCIA</th>
                                <th class="text-center">FECHA DE COBRO STRIPE</th>
                                <th class="text-center">ESTATUS DE COBRO STRIPE</th>
                                <th class="text-center">TOTAL COBRADO EN STRIPE</th>
                                <th class="text-center">COMISIÓN DE STRIPE</th>
                                <th class="text-center">TOTAL A PAGAR POR STRIPE</th>
                                <th class="text-center">FECHA DE PAGO STRIPE</th>
                                <th class="text-center">ESTATUS DE PAGO STRIPE</th>
                                <th class="text-center">TOTAL PAGADO POR STRIPE</th>
                                <th class="text-center">REFERENCIA DE PAGADO POR STRIPE</th>
                                <th class="text-center">BANCO</th>
                                <th class="text-center">TIENE REEMBOLSO</th>
                                <th class="text-center">TIENE DISPUTA</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @if(sizeof($otherReferences)>=1)
                                @foreach($otherReferences as $key => $item)
                                    @php
                                        // Inicialización con punto y coma
                                        $total_payments = 0;

                                        // Verificar si $item tiene las propiedades necesarias
                                        if (isset($item->operation, $item->total_payments_stripe)) {
                                            switch ($item->operation) {
                                                case "multiplication":
                                                    $total_payments = round($item->total_payments_stripe * ($item->exchange_rate ?? 1));
                                                    break;
                                                case "division":
                                                    $total_payments = round($item->total_payments_stripe / ($item->exchange_rate ?? 1));
                                                    break;
                                                default:
                                                    $total_payments = round($item->total_payments_stripe);
                                            }
                                        }

                                        // Verificar y actualizar resumen por moneda
                                        if (isset($item->currency, $resume[$item->currency])) {
                                            $resume[$item->currency]['TOTAL'] += $item->total_payments_stripe ?? 0;
                                            $resume[$item->currency]['QUANTITY']++;
                                        }
                                    @endphp
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
                                        <td class="text-center">{{ $item->total_sales }}</td>
                                        <td class="text-center">$ {{ number_format($total_payments, 2) }}</td>
                                        <td class="text-center">{{ $item->currency }}</td>
                                        <td class="text-center">{{ $item->payment_type_name }}</td>
                                        <td class="text-center">$ {{ number_format(( $item->currency == "MXN" ? $total_payments : ( $total_payments * $item->exchange_rate ) ), 2) }}</td>
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
                                        <td class="text-center">$ {{ number_format($item->total_net, 2) }}</td>
                                        <td class="text-center">{{ $item->deposit_date != NULL ? date("Y-m-d", strtotime($item->deposit_date)) : "SIN FECHA DE PAGO " }}</td>
                                        <td class="text-center" style="color:#fff;background-color:#{{ $item->deposit_date != NULL ? '00ab55' : 'e7515a' }};">
                                            @if ($item->deposit_date != NULL)
                                                PAGADO
                                            @else
                                                PENDIENTE DE PAGO
                                            @endif
                                        </td>
                                        <td class="text-center">$ {{ number_format(( $item->total_final_net ), 2) }}</td>
                                        <td class="text-center">{{ $item->reference_conciliation }}</td>
                                        <td class="text-center">{{ $item->bank_name }}</td>
                                        <td class="text-center">
                                            @if ( $item->refunded > 0 )
                                                <button class="btn btn-success">Sí</button>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ( $item->disputed > 0 )
                                                <button class="btn btn-success">Sí</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                {{-- </div> --}}
            </div>
        </div>
    </div>


    <x-modals.filters.bookings :data="$data" :isSearch="1"  :currencies="$currencies" />
    <x-modals.reports.columns />
    <x-modals.finances.charge_stripe />
    <x-modals.finances.help_stripe />
    <x-modals.finances.automatic_conciliation_result />
@endsection