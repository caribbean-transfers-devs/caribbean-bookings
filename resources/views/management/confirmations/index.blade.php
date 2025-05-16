@extends('layout.app')
@section('title') Gestión De Confirmaciónes @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management/confirmations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/confirmations.min.css') }}" rel="stylesheet" >
    <style>
        .bell-button {
            /* font-size: 24px; */
            border: none;
            background: none;
            cursor: pointer;
            position: relative;
            /* animation: ring 1s infinite ease-in-out; */
            transition: transform 0.3s;
        }
        .bell-button.active {
            animation: ring 1s infinite ease-in-out;
            box-shadow: 0 0 10px red, 0 0 20px red;
        }
        @keyframes ring {
            0% { transform: rotate(0); }
            15% { transform: rotate(-15deg); }
            30% { transform: rotate(15deg); }
            45% { transform: rotate(-10deg); }
            60% { transform: rotate(10deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0); }
        }
    </style>
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/management/confirmations.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de confirmaciones",
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

                <table id="dataConfirmations" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">INDICADORES</th>
                            <th class="text-center">ESTATUS DE CONFIRMACIÓN</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">PICKUP</th>
                            <th class="text-center">TIPO</th>
                            <th class="text-center">ESTATUS DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">VEHÍCULO</th>
                            <th class="text-center">PASAJEROS</th>
                            <th class="text-center">DESDE</th>
                            <th class="text-center">HACIA</th>
                            <th class="text-center">PAGO</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">COMISIÓNABLE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($confirmations)>=1)
                            @foreach($confirmations as $key => $confirmation)                           
                                <tr>
                                    <td class="text-center">{{ $confirmation->reservation_id }}</td>
                                    <td class="text-center">
                                        @if ( $confirmation->is_round_trip == 1 && $confirmation->final_service_type == "DEPARTURE" && ( $confirmation->one_service_status == "CANCELLED" || $confirmation->one_service_status == "NOSHOW" ) )
                                            <button class="btn btn-primary btn_operations active bell-button bs-tooltip" title="Por favor de confirmar el regreso con el cliente"> 
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>                                                    
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermission(40))
                                            <?=auth()->user()->renderStatusConfirmation($confirmation)?>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $confirmation->site_name }}</td>
                                    <td class="text-center">{{ auth()->user()->setDateTime($confirmation, "time") }}</td>
                                    <td class="text-center">{{ $confirmation->final_service_type }}</td>
                                    <td class="text-center"><?=auth()->user()->renderServiceStatusOP($confirmation)?></td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermission(61))
                                            <a href="/reservations/detail/{{ $confirmation->reservation_id }}"><p class="mb-1">{{ $confirmation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $confirmation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $confirmation->full_name }}</td>
                                    <td class="text-center">{{ $confirmation->service_type_name }}</td>
                                    <td class="text-center">{{ $confirmation->passengers }}</td>
                                    <td class="text-center">{{ auth()->user()->setFrom($confirmation, "name") }} {{ $confirmation->operation_type == 'arrival' && !empty($confirmation->flight_number) ? ' ('.$confirmation->flight_number.')' : '' }}</td>
                                    <td class="text-center">{{ auth()->user()->setTo($confirmation, "name") }}</td>
                                    <td class="text-center" <?=auth()->user()->classStatusPayment($confirmation)?>>{{ auth()->user()->statusPayment($confirmation->payment_status) }}</td>
                                    <td class="text-center">{{ number_format($confirmation->total_balance,2) }}</td>
                                    <td class="text-center">{{ $confirmation->currency }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-{{ $confirmation->is_commissionable == 1 ? 'success' : 'danger' }}" type="button">{{ $confirmation->is_commissionable == 1 ? "SI" : "NO" }}</button>
                                    </td>
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