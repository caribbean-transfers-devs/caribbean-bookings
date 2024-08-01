@php
    use App\Traits\RoleTrait;
    $resume = [];
@endphp
@extends('layout.app')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('assets/js/sections/operations/managment.min.js') }}"></script>
    <script>
        if ( document.getElementById('lookup_date') != null ) {
            const picker = new easepick.create({
                element: "#lookup_date",
                css: [
                    'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                ],
                zIndex: 10,
            });   
        }
    </script>    
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => 'Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtro de reservaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            ),
            array(  
                'text' => 'Excel',
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
                <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th>Pickup</th>
                            <th>Sitio</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estatus Op.</th>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Vehículo</th>
                            <th>Pasajeros</th>
                            <th>Desde</th>
                            <th>Hacia</th>
                            <th>Pago</th>
                            <th>Total</th>
                            <th>Moneda</th>
                            <th></th>
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
                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                    <td>{{ $value->site_name }}</td>
                                    <td>{{ $value->final_service_type }}</td>
                                    <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                    <td>
                                        @if (RoleTrait::hasPermission(38))
                                            <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                        @else
                                            {{ $value->code }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $value->client_first_name }} {{ $value->client_last_name }}
                                        @if(!empty($value->reference))
                                            [{{ $value->reference }}]
                                        @endif
                                    </td>
                                    <td>{{ $value->service_name }}</td>
                                    <td class="text-center">{{ $value->passengers }}</td>
                                    <td>{{ $operation_from }}</td>
                                    <td>{{ $operation_to }}</td>
                                    <td class="text-center">{{ $value->status }}</td>
                                    <td class="text-end">{{ number_format(( $value->is_round_trip == 1 ? ( $payment / 2 ) : $payment ),2) }}</td>
                                    <td class="text-center">{{ $value->currency }}</td>
                                    <td>{{ !empty($value->cancellation_type_name) ? $value->cancellation_type_name : "NO SHOW" }}</td>
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

    <x-modals.reservations.reports :data="$date" />
@endsection