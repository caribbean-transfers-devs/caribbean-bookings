@php
    use App\Traits\RoleTrait;
    $resumen = [];
@endphp
@extends('layout.app')
@section('title') SPAM @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management_spam.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management_spam.min.css') }}" rel="stylesheet" >
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
                    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                    'className' => 'btn btn-primary __btn_create',
                    'attr' => array(
                        'data-title' =>  "Filtros de spam",
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#filterModal'
                    )
                )
            );

            array_push($buttons,
                array(
                    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar reporte de excel',
                    'className' => 'btn btn-primary',
                    'extend' => 'excelHtml5',
                )
            );
        }
        if (RoleTrait::hasPermission(70)){
            array_push($buttons,
                array(
                    'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar teléfonia de Box in plan',
                    'className' => 'btn btn-primary __btn_export',
                    'attr' => array(
                        'data-title' =>  "Generar reporte de excel",
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#filterModalExport'
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

                <div class="row layout-top-spacing mb-3">
                    <div class="col-md-12">
                        <ul class="nav nav-pills" id="animateLine" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="animated-underline-pending-tab" data-bs-toggle="tab" href="#animated-underline-pending" role="tab" aria-controls="animated-underline-pending" aria-selected="false" tabindex="-1"> Reservas pendientes</button>
                            </li>                
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="animated-underline-spam-tab" data-bs-toggle="tab" href="#animated-underline-spam" role="tab" aria-controls="animated-underline-spam" aria-selected="false" tabindex="-1"> Spam</button>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content" id="animateLineContent-4">
                    <div class="tab-pane fade show active" id="animated-underline-pending" role="tabpanel" aria-labelledby="animated-underline-badge-pending">                    
                        <table id="dataSpams" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                            <thead>
                                <tr>        
                                    <th class="text-center"></th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center"># Llamadas aceptadas</th>
                                    <th class="text-center">Sitio</th>
                                    <th class="text-center">Pickup</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Round Trip</th>
                                    <th class="text-center">Operación</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Cliente</th>
                                    <th class="text-center">Teléfono</th>
                                    <th class="text-center">Correo</th>
                                    <th class="text-center">Vehículo</th>
                                    <th class="text-center">Pasajeros</th>
                                    <th class="text-center">Desde</th>
                                    <th class="text-center">Hacia</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Moneda</th>
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
                                                    case 'ACCEPT':
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
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <button id="actions" type="button" class="btn {{ $spam }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{$value->id}}">
                                                            {{ $value->spam }}
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                        </button>                                                
                                                        <div class="dropdown-menu" aria-labelledby="actions">
                                                            <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$value->id}},'PENDING','btn-secondary')"><i class="flaticon-home-fill-1 mr-1"></i> PENDING</a>
                                                            <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$value->id}},'SENT','btn-info')"><i class="flaticon-home-fill-1 mr-1"></i> SENT</a>
                                                            <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$value->id}},'LATER','btn-warning')"><i class="flaticon-home-fill-1 mr-1"></i> LATER</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$value->id}},'CONFIRMED','btn-success')"><i class="flaticon-home-fill-1 mr-1"></i> CONFIRMED</a>
                                                            <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$value->id}},'REJECTED','btn-danger')"><i class="flaticon-home-fill-1 mr-1"></i> REJECTED</a>
                                                        </div>
                                                    </div>                                 
                                                </td>
                                                <td class="text-center">{{ $value->id }}</td>
                                                <td class="text-center">{{ $value->spam_count }}</td>                                        
                                                <td class="text-center">{{ $value->site_name }}</td>
                                                <td class="text-center">{{ date("H:i", strtotime($operation_pickup)) }}</td>
                                                <td class="text-center">{{ $value->final_service_type }}</td>
                                                <td class="text-center">
                                                    @if ( $value->is_round_trip == 1 )
                                                        Si
                                                    @else
                                                        No
                                                    @endif
                                                </td>
                                                <td class="text-center"><span class="badge badge-light-{{ $label }} mb-2 me-4">{{ $operation_status }}</span></td>
                                                <td class="text-center">
                                                    <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>                                                        
                                                </td>
                                                <td class="text-center">{{ $value->client_first_name }} {{ $value->client_last_name }}</td>
                                                <td class="text-center">{{ trim($value->client_phone) }}</td>
                                                <td class="text-center">{{ trim(strtolower($value->client_email)) }}</td>
                                                <td class="text-center">{{ $value->service_name }}</td>
                                                <td class="text-center" class="text-center">{{ $value->passengers }}</td>
                                                <td class="text-center">{{ $operation_from }}</td>
                                                <td class="text-center">{{ $operation_to }}</td>                                                    
                                                <td class="text-center">{{ number_format($value->total_sales,2) }}</td>
                                                <td class="text-center">{{ $value->currency }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>                        
                    </div>
                    <div class="tab-pane fade show active" id="animated-underline-spam" role="tabpanel" aria-labelledby="animated-underline-badge-spam">
                    </div>                    
                </div>

                
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

    <x-modals.filters.bookings :data="$data" />
    @if (RoleTrait::hasPermission(70))
        <x-modals.reservations.exports :data="$data" />
    @endif
@endsection