@php
    $resume = [];
@endphp
@extends('layout.app')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/reports/cancellations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/cancellations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/sections/reports/cancellations.min.js') }}"></script>
@endpush

@section('content')
    @php
        // dump($request->input());
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de cancelaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
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
                <table id="dataCancellations" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">PICKUP</th>
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
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($items)>=1)
                            @foreach($items as $key => $value)                                
                                @php
                                    if( !isset( $resume[ $value->cancellation_type_id ] ) ):
                                        $resume[$value->cancellation_type_id] = [
                                            "total" => 0,
                                            "name" => $value->cancellation_type_name
                                        ];
                                    endif;

                                    $resume[$value->cancellation_type_id]['total']++;
                                    
                                    $payment = ( $value->total_sales - $value->total_payments );
                                    if($payment < 0) $payment = 0;

                                    $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                                    $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                    $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                    $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                                    if($operation_status != "CANCELLED"):
                                        continue;
                                    endif;
                                    
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

                                    $message_round_trip = ( $value->is_round_trip == 1 ? "Esta reservación es un Round Trip, con un total de ".$payment." ".$value->currency : "" );
                                @endphp
                                <tr style="{{ $value->is_round_trip == 1 ? 'background-color: #fcf5e9;' : '' }}" class="{{ $value->is_round_trip == 1 ? 'bs-tooltip' : '' }}" title="{{ $message_round_trip }}">
                                    <td class="text-center">{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                    <td class="text-center">{{ $value->site_name }}</td>
                                    <td class="text-center">{{ $value->final_service_type }}</td>
                                    <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                    <td class="text-center">
                                        @if (auth()->user()->hasPermission(61))
                                            <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                        @else
                                            {{ $value->code }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $value->client_first_name }} {{ $value->client_last_name }}
                                        @if(!empty($value->reference))
                                            [{{ $value->reference }}]
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $value->service_name }}</td>
                                    <td class="text-center" class="text-center">{{ $value->passengers }}</td>
                                    <td class="text-center">{{ $operation_from }}</td>
                                    <td class="text-center">{{ $operation_to }}</td>
                                    <td class="text-center">{{ $value->status }}</td>
                                    <td class="text-center">{{ number_format(( $value->is_round_trip == 1 ? ( $payment / 2 ) : $payment ),2) }}</td>
                                    <td class="text-center">{{ $value->currency }}</td>
                                    <td class="text-center">{{ !empty($value->cancellation_type_name) ? $value->cancellation_type_name : "NO SHOW" }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <div class="mt-3 px-2">
                    <h6>Resumen de Cancelaciones</h6>
                    <h6 class="text-info small">Aqui encontrarás el resumen de cancelaciones por tipo.</h6>                        
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>                                                        
                                    <th>Estatus</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resume as $key => $value)
                                    <tr>
                                        <td class="text-start">{{ $value['name'] }}</td>
                                        <td class="text-center">{{ $value['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" />
@endsection