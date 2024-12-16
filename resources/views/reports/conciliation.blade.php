@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\Reports\PaymentsTrait;
@endphp
@extends('layout.app')
@section('title') Reporte de conciliación @endsection

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
            )
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
                
                <table id="dataConciliation" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">METODO DE PAGO</th>
                            <th class="text-center">DESCRIPCIÓN DEL PAGO</th>
                            <th class="text-center">REFERENCIA</th>
                            <th class="text-center">TOTAL DEL PAGO</th>
                            <th class="text-center">MONEDA DE PAGO</th>
                            <th class="text-center">FECHA DE PAGO</th>
                            <th class="text-center">CONCILIADO</th>                            
                            <th class="text-center">NOMBRE DEL CLIENTE</th>
                            <th class="text-center">TELÉFONO DEL CLIENTE</th>
                            <th class="text-center">CORREO DEL CLIENTE</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                            <th class="text-center">MONEDA DE RESERVACIÓN</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($conciliations)>=1)
                            @foreach($conciliations as $key => $conciliation)
                                @php
                                    dd($conciliation);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $conciliation->reservation_id }}</td>
                                    <td class="text-center">{{ $conciliation->payment_method }}</td>
                                    <td class="text-center">{{ $conciliation->description }}</td>
                                    <td class="text-center">{{ $conciliation->reference }}</td>
                                    <td class="text-center">{{ $conciliation->total }}</td>
                                    <td class="text-center">{{ $conciliation->currency_payment }}</td>
                                    <td class="text-center">{{ date("Y-m-d", strtotime($conciliation->created_payment)) }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-{{ $conciliation->is_conciliated == 1 ? 'success' : 'danger' }}">{{ $conciliation->is_conciliated == 1 ? 'SÍ' : 'NO' }}</button>
                                    </td>
                                    <td class="text-center">{{ $conciliation->full_name }}</td>
                                    <td class="text-center">{{ $conciliation->client_phone }}</td>
                                    <td class="text-center">{{ $conciliation->client_email }}</td>
                                    <td class="text-center"><button type="button" class="btn btn-"></button></td>
                                    <td class="text-center">{{ $conciliation->currency }}</td>
                                    <td class="text-center">0</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" :paymentstatus="$payment_status" :methods="$methods" :currencies="$currencies" :request="$request" />
@endsection