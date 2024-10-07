@php
    use App\Traits\RoleTrait;
    $resume = [
        'status' => [
            'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CONFIRMED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CANCELLED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        ]
    ];
    $sites = [];
    $destinations = [];
@endphp
@extends('layout.app')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/confirmation.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/confirmation.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>    
    <script src="{{ mix('assets/js/sections/operations/confirmation.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
        if (RoleTrait::hasPermission(41)){
            array_push($buttons,
                array(  
                    'text' => 'Filtrar',
                    'className' => 'btn btn-primary __btn_create',
                    'attr' => array(
                        'data-title' =>  "Filtro de reservaciones",
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#filterModal'
                    )
                )
            );
        }
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
                            <th></th>                                                
                            <th>Sitio</th>
                            <th>Pickup</th>
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
                            <th>Comsionable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($items)>=1)
                            @foreach($items as $key => $value)
                                @php
                                    $confirmation_type = $value->op_one_confirmation;
                                    if($value->operation_type == "departure"):
                                        $confirmation_type = $value->op_two_confirmation;
                                    endif;

                                    $payment = ( $value->total_sales - $value->total_payments );
                                    if($payment < 0) $payment = 0;

                                    $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                                    $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                    $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                    $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

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
                                @endphp
                                <tr>
                                    <td>
                                        @if (RoleTrait::hasPermission(40))
                                            <button class="btn btn-order {{ (($confirmation_type == 0)? 'btn-warning':'btn-success')}}" onClick="updateConfirmation(event,{{$value->id}},'{{$value->operation_type}}',{{$confirmation_type}},{{$value->reservation_id}})">
                                                {{ (($confirmation_type == 0)? 'N':'Y')}}
                                            </button>
                                        @endif
                                    </td>
                                    <td>{{ $value->site_name }}</td>
                                    <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                    <td>{{ $value->final_service_type }}</td>
                                    <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                    <td>
                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>                                                        
                                    </td>
                                    <td>{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                    <td>{{ $value->service_name }}</td>
                                    <td class="text-center">{{ $value->passengers }}</td>
                                    <td>{{ $operation_from }}</td>
                                    <td>{{ $operation_to }}</td>
                                    <td class="text-center">{{ $value->status }}</td>
                                    <td class="text-end">{{ number_format($payment,2) }}</td>
                                    <td class="text-center">{{ $value->currency }}</td>
                                    <td>
                                        <span class="badge badge-light-{{ $value->is_commissionable == 1 ? "success" : "danger" }}">{{ $value->is_commissionable == 1 ? "Comsionable" : "No comisionable" }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @php
        // dump($date_search);
    @endphp
    <x-modals.reservations.reports :data="$date" />
@endsection