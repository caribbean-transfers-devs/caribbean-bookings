@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\Reports\PaymentsTrait;
@endphp
@extends('layout.app')
@section('title') Reporte De Conciliación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/report_conciliation.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/report_conciliation.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/reports/conciliation.min.js') }}"></script>
    <script>
        document.getElementById('showLayer').addEventListener('click', function() {
            document.getElementById('layer').classList.add('active');
        });

        document.getElementById('closeLayer').addEventListener('click', function() {
            document.getElementById('layer').classList.remove('active');
        });
    </script>    
@endpush

@section('content')
    @php
        $buttons = array(
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
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> Conciliar pagos de PayPal',
                'className' => 'btn btn-primary __btn_conciliation_paypal',
            ),
            // array(
            //     'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Ver graficas',
            //     'titleAttr' => 'Ver graficas',
            //     'className' => 'btn btn-primary',
            //     'attr' => array(
            //         'id' => 'showLayer',
            //     )
            // ),
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
                
                <table id="dataConciliation" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">METODO DE PAGO</th>
                            <th class="text-center">DESCRIPCIÓN DEL PAGO</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">TOTAL DE PAGO</th>
                            <th class="text-center">TOTAL DE COMISIÓN</th>
                            <th class="text-center">TOTAL RECIBIDO</th>
                            <th class="text-center">MONEDA DE PAGO</th>
                            <th class="text-center">COMENTARIO DE CONCILIACIÓN</th>
                            <th class="text-center">FECHA DE PAGO</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">MONEDA DE RESERVACIÓN</th>
                            <th class="text-center">CONCILIADO</th>
                            <th class="text-center">NOMBRE DEL CLIENTE</th>
                            <th class="text-center">TELÉFONO DEL CLIENTE</th>
                            <th class="text-center">CORREO DEL CLIENTE</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>                            

                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($conciliations)>=1)
                            @foreach($conciliations as $key => $conciliation)
                                @php
                                    // dd($conciliation);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $conciliation->reservation_id }}</td>
                                    <td class="text-center">{{ $conciliation->payment_method }}</td>
                                    <td class="text-center">{{ $conciliation->description }}</td>
                                    <td class="text-center">{{ $conciliation->reference }}</td>
                                    <td class="text-center">{{ number_format($conciliation->total, 2) }}</td>

                                    <td class="text-center">{{ number_format($conciliation->payment_method == "PAYPAL"  ? $conciliation->total_fee : 0, 2) }}</td>
                                    <td class="text-center">{{ number_format($conciliation->payment_method == "PAYPAL"  ? $conciliation->total_net : 0, 2) }}</td>                                    

                                    <td class="text-center">{{ $conciliation->currency_payment }}</td>
                                    <td class="text-center">{{ $conciliation->conciliation_comment }}</td>
                                    <td class="text-center">
                                        {{ date("Y-m-d", strtotime($conciliation->created_payment)) }}
                                        [{{ date("H:m", strtotime($conciliation->created_payment)) }}]
                                    </td>
                                    <td class="text-center">{{ number_format($conciliation->total_sales, 2) }}</td>
                                    <td class="text-center">{{ $conciliation->currency }}</td>
                                    <td class="text-center"><button class="btn btn-{{ $conciliation->is_conciliated == 1 ? 'success' : 'danger' }} __btn_conciliation bs-tooltip" data-reservation="{{ $conciliation->reservation_id }}" data-payment="{{ $conciliation->code_payment }}" data-currency="{{ $conciliation->currency_payment }}" title="{{ $conciliation->is_conciliated == 1 ? 'Click para ver la conciliación' : 'click para conciliar el pago' }}">{{ $conciliation->is_conciliated == 1 ? 'SÍ' : 'NO' }}</button></td>
                                    <td class="text-center">{{ $conciliation->full_name }}</td>
                                    <td class="text-center">{{ $conciliation->client_phone }}</td>
                                    <td class="text-center">{{ $conciliation->client_email }}</td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($conciliation->reservation_status) }}">{{ BookingTrait::statusBooking($conciliation->reservation_status) }}</button></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="layer" id="layer">
        <div class="header-chart d-flex justify-content-between">
            <div class="gran_total">
            </div>
            <div class="btn_close">
                <button class="btn btn-primary" id="closeLayer">Cerrar</button>
            </div>
        </div>
        <div class="body-chart">
            <div class="row">
                <div class="col-lg-8 col-12">
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">ESTADISTICAS POR ESTATUS</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="chartSale" id="chartSaleStatus"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart table-chart-general mb-3">
                                    <thead>
                                        <tr>
                                            <th>ESTATUS</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>

                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>ESTATUS</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>                                
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por metodo de pago</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="chartSale" id="chartSaleMethodPayments"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>METODO DE PAGO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por sitio</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="" id="chartSaleSites"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>SITIO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por destino</h5>
                            <div class="col-lg-5 col-12">
                                <canvas class="" id="chartSaleDestinations"></canvas>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>DESTINO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>                    
                </div>
                <div class="col-lg-4 col-12">
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por moneda</h5>
                            <div class="col-lg-12 col-12">
                                <canvas class="" id="chartSaleCurrencies"></canvas>
                            </div>
                            <div class="col-lg-12 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>MONEDA</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">TOTAL</th>                                
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por origen de venta</h5>
                            <div class="col-lg-12 col-12">
                                <canvas class="" id="chartSaleOrigins"></canvas>
                            </div>
                            <div class="col-lg-12 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>ORIGEN</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">estadisticas por vehículo</h5>
                            <div class="col-lg-12 col-12">
                                <canvas class="" id="chartSaleVehicles"></canvas>
                            </div>
                            <div class="col-lg-12 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>VEHÍCULO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>    

    <x-modals.filters.bookings :data="$data" :paymentstatus="$payment_status" :methods="$methods" :currencies="$currencies" :request="$request" />
    <x-modals.new_payment_conciliation />    
@endsection