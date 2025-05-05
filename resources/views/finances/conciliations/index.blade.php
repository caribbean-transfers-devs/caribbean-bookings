@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;
    $conciliationPayments = [
        "total" => 0,
        "total_taxes" => 0,
        "total_received" => 0,
        "USD" => [
            "total" => 0,
            "total_taxes" => 0,
            "total_received" => 0,
            "quantity" => 0,
        ],
        "MXN" => [
            "total" => 0,
            "total_taxes" => 0,
            "total_received" => 0,
            "quantity" => 0,
        ],
        "quantity" => 0,
        "data" => []
    ];
@endphp
@extends('layout.app')
@section('title') Reporte De Conciliación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/finances/conciliations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/finances/conciliations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>    
    <script src="{{ mix('/assets/js/sections/finances/conciliations.min.js') }}"></script>
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
            // array(  
            //     'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> Conciliar PayPal',
            //     'className' => 'btn btn-primary __btn_conciliation_paypal',
            // ),
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> Conciliar Stripe',
                'className' => 'btn btn-primary __btn_conciliation_stripe',
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
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Ver graficas',
                'titleAttr' => 'Ver graficas',
                'className' => 'btn btn-primary',
                'attr' => array(
                    'id' => 'showLayer',
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
                
                <table id="dataConciliation" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">METODO DE PAGO</th>
                            <th class="text-center">CONCILIADO</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                            {{-- <th class="text-center">DESCRIPCIÓN DEL PAGO</th> --}}
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">TOTAL DE PAGO</th>
                            <th class="text-center">TOTAL DE COMISIÓN</th>
                            <th class="text-center">TOTAL RECIBIDO</th>
                            <th class="text-center">MONEDA DE PAGO</th>
                            <th class="text-center">COMENTARIO DE CONCILIACIÓN</th>
                            <th class="text-center">FECHA DE PAGO</th>
                            <th class="text-center">FECHA EN QUE SE RECIBIO EL PAGO</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">MONEDA DE RESERVACIÓN</th>                            
                            <th class="text-center">NOMBRE DEL CLIENTE</th>
                            <th class="text-center">TELÉFONO DEL CLIENTE</th>
                            <th class="text-center">CORREO DEL CLIENTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($conciliations)>=1)
                            @foreach($conciliations as $key => $conciliation)
                                @php
                                    if( $conciliation->is_conciliated == 1 ){
                                        if (!isset( $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))] )){
                                            $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))] = [
                                                "name" => strtoupper(Str::slug($conciliation->payment_method)),
                                                "total" => 0,
                                                "total_taxes" => 0,
                                                "total_received" => 0,
                                                "USD" => [
                                                    "total" => 0,
                                                    "total_taxes" => 0,
                                                    "total_received" => 0,
                                                    "quantity" => 0,
                                                ],
                                                "MXN" => [
                                                    "total" => 0,
                                                    "total_taxes" => 0,
                                                    "total_received" => 0,
                                                    "quantity" => 0,
                                                ],
                                                "quantity" => 0,
                                            ];
                                        }
                                        $conciliationPayments['total'] += ( $conciliation->currency_payment == "USD" ? ($conciliation->total * $exchange) : ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total / $conciliation->exchange_rate ) * $exchange) : $conciliation->total ) );
                                        $conciliationPayments['total_taxes'] += ( $conciliation->currency_payment == "USD" ? ($conciliation->total_fee * $exchange) : ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_fee / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_fee ) );
                                        $conciliationPayments['total_received'] += ( $conciliation->currency_payment == "USD" ? ($conciliation->total_net * $exchange) : ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_net / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_net ) );

                                        $conciliationPayments[$conciliation->currency_payment]['total'] += ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total / $conciliation->exchange_rate ) * $exchange) : $conciliation->total );
                                        $conciliationPayments[$conciliation->currency_payment]['total_taxes'] += ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_fee / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_fee );
                                        $conciliationPayments[$conciliation->currency_payment]['total_received'] += ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_net / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_net );
                                        $conciliationPayments[$conciliation->currency_payment]['quantity']++;

                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))]['total'] += ( $conciliation->currency_payment == "USD" ? ($conciliation->total * $exchange) : ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total / $conciliation->exchange_rate ) * $exchange) : $conciliation->total ) );
                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))]['total_taxes'] += ( $conciliation->currency_payment == "USD" ? ($conciliation->total_fee * $exchange) : ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_fee / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_fee ) );
                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))]['total_received'] += ( $conciliation->currency_payment == "USD" ? ($conciliation->total_net * $exchange) : ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_net / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_net ) );

                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))][$conciliation->currency_payment]['total'] += ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total / $conciliation->exchange_rate ) * $exchange) : $conciliation->total );
                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))][$conciliation->currency_payment]['total_taxes'] += ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_fee / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_fee );
                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))][$conciliation->currency_payment]['total_received'] += ( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? (( $conciliation->total_net / $conciliation->exchange_rate ) * $exchange) : $conciliation->total_net );
                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))][$conciliation->currency_payment]['quantity']++;

                                        $conciliationPayments['data'][strtoupper(Str::slug($conciliation->payment_method))]['quantity']++;
                                        $conciliationPayments['quantity']++;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $conciliation->reservation_id }}</td>
                                    <td class="text-center">
                                        {{ $conciliation->payment_method }}
                                        @if ( $conciliation->is_refund == 1 )
                                        <button class="btn btn-success" type="button">Tiene reembolso</button>
                                        @endif
                                    </td>
                                    <td class="text-center"><button class="btn btn-{{ $conciliation->is_conciliated == 1 ? 'success' : 'danger' }} __btn_conciliation bs-tooltip" data-reservation="{{ $conciliation->reservation_id }}" data-payment="{{ $conciliation->code_payment }}" data-currency="{{ $conciliation->currency_payment }}" title="{{ $conciliation->is_conciliated == 1 ? 'Click para ver la conciliación' : 'click para conciliar el pago' }}">{{ $conciliation->is_conciliated == 1 ? 'SÍ' : 'NO' }}</button></td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($conciliation->reservation_status) }}">{{ auth()->user()->statusBooking($conciliation->reservation_status) }}</button></td>
                                    {{-- <td class="text-center">{{ $conciliation->description }}</td> --}}
                                    <td class="text-center">{{ $conciliation->reference }}</td>
                                    <td class="text-center">{{ number_format(( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? $conciliation->total / $conciliation->exchange_rate : $conciliation->total ), 2) }}</td>

                                    <td class="text-center">{{ number_format(( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? $conciliation->total_fee / $conciliation->exchange_rate : $conciliation->total_fee ), 2) }}</td>
                                    <td class="text-center">{{ number_format(( $conciliation->payment_method == "STRIPE" && $conciliation->currency_payment == "MXN" && $conciliation->currency == "USD" ? $conciliation->total_net / $conciliation->exchange_rate : $conciliation->total_net ), 2) }}</td>

                                    <td class="text-center">{{ $conciliation->currency_payment }}</td>
                                    <td class="text-center">{{ $conciliation->conciliation_comment }}</td>
                                    <td class="text-center">
                                        {{ date("Y-m-d", strtotime($conciliation->created_payment)) }}
                                        [{{ date("H:m", strtotime($conciliation->created_payment)) }}]
                                    </td>
                                    <td class="text-center">
                                        {{ date("Y-m-d", strtotime($conciliation->conciliation_payment)) }}
                                        [{{ date("H:m", strtotime($conciliation->conciliation_payment)) }}]
                                    </td>                                    
                                    <td class="text-center">{{ number_format($conciliation->total_sales, 2) }}</td>
                                    <td class="text-center">{{ $conciliation->currency }}</td>                                    
                                    <td class="text-center">{{ $conciliation->full_name }}</td>
                                    <td class="text-center">{{ $conciliation->client_phone }}</td>
                                    <td class="text-center">{{ $conciliation->client_email }}</td>                                    
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
            <div class="btn_close">
                <button class="btn btn-primary" id="closeLayer">Cerrar</button>
            </div>
        </div>
        <div class="body-chart">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="col-lg-12 col-12">
                        <div class="row g-0">
                            <h5 class="col-12 text-left text-uppercase">Estadisiticas de pagos conciliados</h5>
                            <div class="col-lg-4 col-12">
                                <canvas class="" id="chartPaymentsConciliation"></canvas>
                            </div>
                            <div class="col-lg-8 col-12">
                                <table class="table table-chart table-chart-general">
                                    <thead>
                                        <tr>
                                            <th>METODO DE PAGO</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">TOTAL DE IMPUESTOS</th>
                                            <th class="text-center">TOTAL RECIBIDO</th>
                                            <th class="text-center">TOTAL DE USD</th>
                                            <th class="text-center">TOTAL DE MXN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($conciliationPayments['data'] as $keyP => $payment)
                                            <tr>
                                                <td>{{ $payment['name'] }}</td>
                                                <td class="text-center">{{ $payment['quantity'] }}</td>
                                                <td class="text-center">{{ number_format($payment['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($payment['total_taxes'],2) }}</td>
                                                <td class="text-center">{{ number_format($payment['total_received'],2) }}</td>
                                                <td class="text-center">{{ number_format($payment['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($payment['MXN']['total'],2) }}</td>                                                
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ $conciliationPayments['quantity'] }}</th>
                                            <th class="text-center">{{ number_format($conciliationPayments['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($conciliationPayments['total_taxes'],2) }}</th>
                                            <th class="text-center">{{ number_format($conciliationPayments['total_received'],2) }}</th>
                                            <th class="text-center">{{ number_format($conciliationPayments['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($conciliationPayments['MXN']['total'],2) }}</th> 
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :paymentstatus="$payment_status" :methods="$methods" :currencies="$currencies" :request="$request" />
    <x-modals.reports.columns />
    <x-modals.new_payment_conciliation />
@endsection

@push('Js')
    <script>
        let payments = {
            dataPaymentsConciliation: @json(( isset($conciliationPayments['data']) ? $conciliationPayments['data'] : [] )),
            dataChartPaymentsConciliation: function(){
                let object = [];
                const systems = Object.entries(this.dataPaymentsConciliation);
                systems.forEach( ([key, data]) => {
                    object.push(data);
                });
                return object;
            },
            renderChartPaymentsConciliation: function(){
                // Calcular el total de 'counter'
                const totalCount = payments.dataChartPaymentsConciliation().reduce((sum, system) => sum + system.quantity, 0);
                // Calcular el porcentaje de cada 'counter'
                const percentages = payments.dataChartPaymentsConciliation().map(site => ((site.quantity / totalCount) * 100).toFixed(2) + '%');

                if( document.getElementById('chartPaymentsConciliation') != null ){
                    new Chart(document.getElementById('chartPaymentsConciliation'), {
                        type: 'pie',
                        data: {
                            labels: payments.dataChartPaymentsConciliation().map(row => row.name),
                            datasets: [
                                {
                                    data: payments.dataChartPaymentsConciliation().map(row => row.quantity),
                                }
                            ]
                        },
                        options: {
                            responsive: true, // Hacer el gráfico responsivo
                            maintainAspectRatio: false, // Permitir que el gráfico ajuste su altura además de su ancho
                            plugins: {
                                legend: {
                                    display: true,  // Mostrar las etiquetas
                                    position: 'bottom', // Colocar las etiquetas debajo del gráfico
                                    labels: {
                                        padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                        boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                        font: {
                                            size: 12, // Tamaño de la fuente de los labels
                                            color: '#000' // Cambia el color de los labels a negro
                                        },
                                        color: '#000' // Asegura que el color de los labels sea negro
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        title: function(tooltipItems) {
                                            // Mostrar el nombre del sitio
                                            return tooltipItems[0].label;
                                        },
                                        label: function(tooltipItem) {
                                            // console.log(tooltipItem);
                                            const index = tooltipItem.dataIndex;
                                            const site = payments.dataChartPaymentsConciliation()[index];
                                            // Mostrar el monto en pesos y dólares junto con el porcentaje
                                            return [
                                                // `${site.name}:`,
                                                // `Porcentaje: ${percentages[index]}`,
                                                // `TOTAL DE VENTA: $ ${site.gran_total.toLocaleString()}`,
                                                // `TOTAL DE VENTA EN USD: $ ${site['accumulated']['USD'].total.toLocaleString()}`,
                                                // `TOTAL DE VENTA EN MXN: $ ${site['accumulated']['MXN'].total.toLocaleString()}`,
                                            ];
                                        }
                                    }
                                },
                                datalabels: {
                                    display: true,
                                    formatter: (value, context) => {
                                        const total = context.chart._metasets[0].total;
                                        const percentage = ((value / total) * 100).toFixed(2) + '%';
                                        return percentage; // Mostrar porcentaje en el gráfico
                                    },
                                    color: '#000',
                                    font: {
                                        weight: 'bold'
                                    },
                                    anchor: 'end',
                                    align: 'start'
                                }
                            }
                        },
                        plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                    });
                }
            },
        };

        console.log(payments.dataChartPaymentsConciliation());
        payments.renderChartPaymentsConciliation();
    </script>
@endpush