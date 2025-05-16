@php
    use Illuminate\Support\Str;
    $usersData = [];
    $accountingData = [
        "TOTAL" => 0,
        "TOTAL_SALE" => 0,
        "TOTAL_COMMISSION" => 0
    ];
    $users = auth()->user()->CallCenterAgent();
@endphp
@extends('layout.app')
@section('title') Reporte de comisiones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/reports/commissions.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/commissions.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>    
    <script src="{{ mix('/assets/js/sections/reports/commissions.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de comisiones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal',
                )
            ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="layout-columns" class=""><path fill="" fill-rule="evenodd" d="M7 5a2 2 0 00-2 2v10a2 2 0 002 2h1V5H7zm3 0v14h4V5h-4zm6 0v14h1a2 2 0 002-2V7a2 2 0 00-2-2h-1zM3 7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4V7z" clip-rule="evenodd"></path></svg> Administrar columnas',
                'titleAttr' => 'Administrar columnas',
                'className' => 'btn btn-primary __btn_columns',
                'attr' => array(
                    'data-title' =>  "Filtro de comisiones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#columnsModal',
                    'data-table' => 'commissions',// EL ID DE LA TABLA QUE VAMOS A OBTENER SUS HEADERS
                    'data-container' => 'columns', //EL ID DEL DIV DONDE IMPRIMIREMOS LOS CHECKBOX DE LOS HEADERS
                )                
            ),
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Ver graficas',
                'titleAttr' => 'Ver graficas',
                'className' => 'btn btn-primary',
                'attr' => array(
                    'data-title' =>  "Tabla de comisiones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#commissionsModal',
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
                
                <table id="dataCommissions" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">CANTIDAD</th>
                            <th class="text-center">VENDEDOR</th>
                            <th class="text-center">TIPO DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                            <th class="text-center">NOMBRE DEL CLIENTE</th>
                            <th class="text-center">FECHA DE SERVICIO</th>
                            <th class="text-center">ESTATUS DE SERVICIO</th>
                            <th class="text-center">ESTATUS DE OPERACIÓN</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN MXN</th>
                            <th class="text-center">TOTAL VENDIDO POR PRECIO DE SERVICIO</th>
                            {{-- <th class="text-center">TOTAL COMISIONADO POR PRECIO DE SERVICIO</th> --}}
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">MOTIVO DE CANCELACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($operations) >= 1)
                            @foreach($operations as $key => $operation)
                                @php
                                    $total_sales = ( $operation->currency == "USD" ? ($operation->total_sales * $exchange) : $operation->total_sales );
                                    $total_cost = ( $operation->currency == "USD" ? ($operation->cost * $exchange) : $operation->cost );

                                    if( !isset($usersData[Str::slug($operation->employee)]) ):
                                        $usersData[Str::slug($operation->employee)] = [
                                            'NAME' => ( !empty($operation->employee) ? $operation->employee : "SIN NOMBRE DE ASESOR DE CALL CENTER" ),
                                            'TOTAL' => 0,
                                            'USD' => 0,
                                            'MXN' => 0,
                                            'TOTAL_COMPLETED' => 0,
                                            'TOTAL_PENDING' => 0,
                                            'QUANTITY' => 0,
                                            'SETTINGS' => [
                                                'daily_goal' => 0,
                                                'type_commission' => 0,                                                
                                                'percentage' => 0,
                                                'targets' => [],
                                            ],
                                            'BOOKINGS' => [],
                                        ];
                                    endif;

                                    //TOTAL DE VENTA
                                    // APLICA COMO VENDIDO TODAS AQUELLAS RESERVAS QUE CUMPLAN CON LOS SIGUIENTES ESTATUS DE RESERVA, CONFIRMADO, CREDITO O CREDITO ABIERTO
                                    if( in_array( $operation->reservation_status, ['CONFIRMED', 'CREDIT', 'OPENCREDIT'] ) ){
                                        $accountingData['TOTAL'] = $total_sales;
                                        $accountingData['TOTAL_SALE'] = $total_cost;

                                        $usersData[Str::slug($operation->employee)]['TOTAL'] += $total_cost;
                                        $usersData[Str::slug($operation->employee)][$operation->currency] += $operation->cost;

                                        if( auth()->user()->serviceStatus($operation, "no_translate") == "COMPLETED" ){
                                            $usersData[Str::slug($operation->employee)]['TOTAL_COMPLETED'] += $total_cost;
                                            $accountingData['TOTAL_COMMISSION'] = $total_cost;
                                        }

                                        if( auth()->user()->serviceStatus($operation, "no_translate") == "PENDING" ){
                                            $usersData[Str::slug($operation->employee)]['TOTAL_PENDING'] += $total_cost;
                                        }

                                        $usersData[Str::slug($operation->employee)]['QUANTITY']++;

                                        if( !in_array($operation->reservation_id, $usersData[Str::slug($operation->employee)]['BOOKINGS']) ){                                     
                                            array_push($usersData[Str::slug($operation->employee)]['BOOKINGS'], $operation->reservation_id);
                                        }

                                        if( isset($usersData[Str::slug($operation->employee)]) ){
                                            $user = $users->where('id', $operation->employee_code)->first();
                                            $usersData[Str::slug($operation->employee)]['SETTINGS']['daily_goal'] = $user->daily_goal ?? 0;
                                            $usersData[Str::slug($operation->employee)]['SETTINGS']['type_commission'] = $user->type_commission ?? "target";
                                            $usersData[Str::slug($operation->employee)]['SETTINGS']['percentage'] = $user->percentage ?? 0;
                                            $usersData[Str::slug($operation->employee)]['SETTINGS']['targets'] = $user->target->object ?? [];
                                        }
                                    }
                                @endphp
                                <tr class="" data-nomenclatura="{{ $operation->final_service_type }}{{ $operation->op_type }}" data-reservation="{{ $operation->reservation_id }}" data-item="{{ $operation->id }}" data-operation="{{ $operation->final_service_type }}" data-service="{{ $operation->operation_type }}" data-type="{{ $operation->op_type }}" data-close_operation="">
                                    <td class="text-center">{{ $operation->reservation_id }}</td>
                                    <td class="text-center">{{ $operation->quantity }}</td>
                                    <td class="text-center">{{ $operation->employee }}</td>
                                    <td class="text-center"><span class="badge badge-{{ $operation->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermission(61))
                                            <a href="/reservations/detail/{{ $operation->reservation_id }}"><p class="mb-1">{{ $operation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $operation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $operation->site_name }}</td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($operation->reservation_status) }}">{{ auth()->user()->statusBooking($operation->reservation_status) }}</button></td>
                                    <td class="text-center">{{ $operation->full_name }}</td>
                                    <td class="text-center">{{ auth()->user()->setDateTime($operation, "date") }}</td>
                                    <td class="text-center"><?=auth()->user()->renderServiceStatus($operation)?></td>
                                    <td class="text-center"><?=auth()->user()->renderOperationStatus($operation)?></td>
                                    <td class="text-center">{{ number_format(($operation->total_sales),2) }}</td>
                                    <td class="text-center">{{ number_format($total_sales,2) }}</td>
                                    <td class="text-center">{{ number_format($total_cost,2) }}</td>
                                    {{-- <td class="text-center">{{ number_format(( auth()->user()->serviceStatus($operation, "no_translate") == "COMPLETED" ? $total_cost : 0 ),2) }}</td> --}}
                                    <td class="text-center">{{ $operation->currency }}</td>
                                    <td class="text-center">
                                        @if ( ($operation->reservation_status == "CANCELLED" && auth()->user()->serviceStatus($operation, "no_translate") == "CANCELLED") || ($operation->reservation_status != "CANCELLED" && auth()->user()->serviceStatus($operation, "no_translate") == "CANCELLED") )
                                            @if ( !empty($operation->cancellation_reason) )
                                                {{ $operation->cancellation_reason }}
                                            @else
                                                {{ $operation->cancellation_reason_one }} <br>
                                                {{ $operation->cancellation_reason_two }}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :users="$users" />
    <x-modals.reports.columns />
    <x-modals.reports.commissions :users="$usersData" />
@endsection

@push('Js')
    <script>
        let commissions = {
            dataUsers: @json(( isset($usersData) ? $usersData : [] )),
            dataChartUsers: function(){
                let object = [];
                const systems = Object.entries(this.dataUsers);
                systems.forEach( ([key, data]) => {
                    object.push(data);
                });
                return object;
            },            
            generateUniqueColors: function(dataLength) { // Generar colores únicos dinámicamente
                const colors = [];
                for (let i = 0; i < dataLength; i++) {
                    let color;
                    do {
                        color = `hsl(${Math.floor(Math.random() * 360)}, 70%, 70%)`;
                    } while (colors.includes(color)); // Asegurarse de que el color no se repita
                    colors.push(color);
                }
                return colors;
            },        
            renderChartSalesUsers: function(){
                _data = this.dataChartUsers();
                console.log(_data);
                

                if( document.getElementById('ChartSalesUsers') != null ){
                    new Chart(document.getElementById('ChartSalesUsers'), {
                        type: 'doughnut',
                        data: {
                            labels: _data.map(row => {
                                const total = _data.reduce((sum, item) => sum + item.QUANTITY, 0);
                                const percentage = ((row.QUANTITY / total) * 100).toFixed(2) + '%';
                                return `${row.NAME} (${percentage})`; // Agrega el porcentaje en la leyenda
                            }),
                            datasets: [
                                {
                                    data: _data.map(row => row.QUANTITY),
                                    borderWidth: 0, // Hace las líneas del gráfico más delgadas
                                    cutout: '70%' // Reduce el grosor del doughnut
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
                                datalabels: {
                                    display: false // Oculta los datalabels en el gráfico
                                }
                            }
                        },
                        plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                    });
                }
            },            
        };

        commissions.renderChartSalesUsers();
    </script>
@endpush