@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $users = auth()->user()->CallCenterAgent();
    $services = auth()->user()->Services();
    $websites = auth()->user()->Sites();
    $origins = auth()->user()->Origins();
    $reservation_status = auth()->user()->reservationStatus();
    $services_operation = auth()->user()->servicesOperation();
    $vehicles = auth()->user()->Vehicles();
    $zones = auth()->user()->Zones();
    $service_operation_status = auth()->user()->statusOperationService();
    $units = auth()->user()->Units(); //LAS UNIDADES DADAS DE ALT;
    $drivers = auth()->user()->Drivers();
    $operation_status = auth()->user()->statusOperation();
    $payment_status = auth()->user()->paymentStatus();
    $currencies = auth()->user()->Currencies();
    $methods = auth()->user()->Methods();
    $cancellations = auth()->user()->CancellationTypes();

    $zones = [];
@endphp
@extends('layout.app')
@section('title') Reporte De Operaciones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/reports/operations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/operations.min.css') }}" rel="stylesheet" >   
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{ mix('assets/js/sections/reports/operations.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de operaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal',
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

                @if(sizeof($operations) >= 1)
                    @foreach ($operations as $operation)
                        @php
                            if (!isset( $zones[auth()->user()->setFrom($operation, "destination")."-".auth()->user()->setTo($operation, "destination")] ) && auth()->user()->setOperatingCost($operation) > 0 ){
                                $zones[auth()->user()->setFrom($operation, "destination")."-".auth()->user()->setTo($operation, "destination")] = [
                                    "from" => auth()->user()->setFrom($operation, "destination"),
                                    "to" => auth()->user()->setTo($operation, "destination"),
                                    "cost_operation" => auth()->user()->setOperatingCost($operation),
                                ];
                            }
                        @endphp
                    @endforeach
                @endif

                <table id="dataOperations" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            {{-- <th class="text-center">ID</th>
                            <th class="text-center">CÓDIGO</th> --}}
                            <th class="text-center">ORIGEN</th>
                            <th class="text-center">DESTINO</th>
                            <th class="text-center">COSTO OPERATIVO</th>
                            {{-- <th class="text-center">ESTATUS</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($zones) >= 1)
                            @foreach ($zones as $zone)
                                <tr>
                                    {{-- <td class="text-center">{{ $operation->reservation_id }}</td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermission(61))
                                            <a href="/reservations/detail/{{ $operation->reservation_id }}"><p class="mb-1">{{ $operation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $operation->code }}</p>
                                        @endif                                        
                                    </td> --}}
                                    <td class="text-center">{{ $zone['from'] }}</td>
                                    <td class="text-center">{{ $zone['to'] }}</td>
                                    <td class="text-center">{{ $zone['cost_operation'] }}</td>
                                    {{-- <td class="text-center"><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($operation->reservation_status) }}">{{ auth()->user()->statusBooking($operation->reservation_status) }}</button></td> --}}
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" />
@endsection