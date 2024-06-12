@php
    use App\Traits\RoleTrait;
    $resumen = [];
@endphp
@extends('layout.custom')
@section('title') SPAM @endsection

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
    <script src="{{ mix('/assets/js/sections/operations/spam.min.js') }}"></script>
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
                            {{-- <th></th> --}}
                            <th>Sitio</th>
                            <th>Pickup</th>                           
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Operación</th>
                            <th>Código</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Vehículo</th>
                            <th>Pasajeros</th>
                            <th>Desde</th>
                            <th>Hacia</th>                                        
                            <th>Total</th>
                            <th>Moneda</th>                                        
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($items)>=1)
                            @foreach($items as $key => $value)
                                @if( in_array($value->final_service_type, ["ARRIVAL", "TRANSFER"]) )
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
                                        
                                        switch ($value->spam) {
                                            case 'PENDING':
                                                $spam = 'btn-secondary';
                                                break;
                                            case 'SENT':
                                                $spam = 'btn-info';
                                                break;
                                            case 'LATER':
                                                $spam = 'btn-warning';
                                                break;
                                            case 'CONFIRMED':
                                                $spam = 'btn-success';
                                                break;
                                            case 'REJECTED':
                                                $spam = 'btn-danger';
                                                break;
                                            default:
                                                $spam = 'btn-secondary';
                                                break;
                                        }

                                        if( !isset( $resumen[ $value->spam ] ) ):
                                            $resumen[ $value->spam ] = 0;
                                        endif;
                                        $resumen[ $value->spam ]++;
                                    @endphp
                                    <tr>
                                        {{-- <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn {{ $spam }}">{{ $value->spam }}</button>
                                                <button type="button" class="btn {{ $spam }} dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{$value->id}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    <span class="visually-hidden ">Toggle Dropdown</span>
                                                </button>                                                
                                                <div class="dropdown-menu" aria-labelledby="actions">
                                                    <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'PENDING','btn-secondary')">PENDING</a>
                                                    <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'SENT','btn-info')">SENT</a>
                                                    <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'LATER','btn-warning')">LATER</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'CONFIRMED','btn-success')">CONFIRMED</a>
                                                    <a class="dropdown-item" href="#" onClick="updateSpam(event,{{$value->id}},'REJECTED','btn-danger')">REJECTED</a>
                                                </div>
                                            </div>                                 
                                        </td> --}}
                                        <td>{{ $value->site_name }}</td>
                                        <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                        <td>{{ $value->final_service_type }}</td>
                                        <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                        <td>
                                            <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>                                                        
                                        </td>
                                        <td>{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                        <td>{{ trim($value->client_phone) }}</td>
                                        <td>{{ trim(strtolower($value->client_email)) }}</td>
                                        <td>{{ $value->service_name }}</td>
                                        <td class="text-center">{{ $value->passengers }}</td>
                                        <td>{{ $operation_from }}</td>
                                        <td>{{ $operation_to }}</td>                                                    
                                        <td class="text-end">{{ number_format($value->total_sales,2) }}</td>
                                        <td class="text-center">{{ $value->currency }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{-- <div class="mt-3 px-2">
                    <h6>Resumen de envío de SPAM</h6>
                    <h6 class="text-info small">Aqui encontrarás el resumen conversiones generadas por los agentes.</h6>                        
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>                                                        
                                    <th>Estatus</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resumen as $key => $value)
                                    <tr>
                                        <td class="text-start">{{ $key }}</td>
                                        <td>{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>


    @php
        // dump($date);
    @endphp
    <x-modals.reservations.reports :data="$date" />
@endsection