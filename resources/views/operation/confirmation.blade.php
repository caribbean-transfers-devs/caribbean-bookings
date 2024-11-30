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
@section('title') Confirmaciónes @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management_confirmation.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management_confirmation.min.css') }}" rel="stylesheet" >
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
                    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                    'className' => 'btn btn-primary __btn_create',
                    'attr' => array(
                        'data-title' =>  "Filtros de confirmaciones",
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#filterModal'
                    )
                )
            );
        }
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
                            <th class="text-center"></th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">PICKUP</th>
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
                            <th class="text-center">COMISIONABLE</th>
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
                                    <td class="text-center">
                                        @if (RoleTrait::hasPermission(40))
                                            <button class="btn btn-order {{ (($confirmation_type == 0)? 'btn-warning':'btn-success')}}" onClick="updateConfirmation(event,{{$value->id}},'{{$value->operation_type}}',{{$confirmation_type}},{{$value->reservation_id}})">
                                                {{ (($confirmation_type == 0)? 'N':'Y')}}
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $value->site_name }}</td>
                                    <td class="text-center">{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                    <td class="text-center">{{ $value->final_service_type }}</td>
                                    <td class="text-center"><span class="badge badge-light-{{ $label }}">{{ $operation_status }}</span></td>
                                    <td class="text-center">
                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>                                                        
                                    </td>
                                    <td class="text-center">{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                    <td class="text-center">{{ $value->service_name }}</td>
                                    <td class="text-center" class="text-center">{{ $value->passengers }}</td>
                                    <td class="text-center">{{ $operation_from }}</td>
                                    <td class="text-center">{{ $operation_to }}</td>
                                    <td class="text-center">{{ $value->status }}</td>
                                    <td class="text-center">{{ number_format($payment,2) }}</td>
                                    <td class="text-center">{{ $value->currency }}</td>
                                    <td class="text-center">
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

    <x-modals.filters.bookings :data="$date" />
@endsection