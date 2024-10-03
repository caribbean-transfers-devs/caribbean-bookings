@php
    use App\Traits\RoleTrait;    
@endphp

@extends('layout.master')
@section('title') Detalle @endsection

@push('up-stack')    
    <link href="{{ mix('/assets/css/reservations/detail.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/reservations/detail.min.css') }}" rel="stylesheet" >    
@endpush

@push('bootom-stack')
    <script>
        const rez_id = {{ $reservation->id }};
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=places"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

    <script src="{{ mix('assets/js/views/reservations/reservationsDetail.js') }}"></script>
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">Detalle de reservación</h1>            
        </div>

        <div class="row">
            @php
                // dump($reservation);
            @endphp
            <div class="col-xxl-3 col-xl-4 col-12"> 
                <div class="card">
                    <div class="card-header">
                        <div class="card-actions float-end">
                            @if (RoleTrait::hasPermission(11))
                            <div class="dropdown show">
                                <a href="#" data-bs-toggle="dropdown" data-bs-display="static">
                                    <i class="align-middle" data-feather="more-horizontal"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#" type="button" data-bs-toggle="modal" data-bs-target="#serviceClientModal">Editar</a>
                                </div>
                            </div>
                            @endif
                        </div>
                        <h5 class="card-title mb-0">{{ $reservation->site->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mt-2 mb-4">
                                <tbody>
                                    <tr>
                                        <th>Nombre</th>
                                        <td>{{ $reservation->client_first_name }} {{ $reservation->client_last_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>E-mail</th>
                                        <td>{{ $reservation->client_email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono</th>
                                        <td>{{ $reservation->client_phone }}</td>
                                    </tr>
                                    <tr>
                                        <th>Moneda</th>
                                        <td>{{ $reservation->currency }}</td>
                                    </tr>                                
                                    <tr>
                                        <th>Estatus</th>
                                        <td>
                                            @if ($data['status'] == "PENDING")
                                                <span class="badge bg-info">PENDING</span>
                                            @endif
                                            @if ($data['status'] == "CONFIRMED")
                                                <span class="badge bg-success">CONFIRMED</span>
                                            @endif
                                            @if ($data['status'] == "CANCELLED")
                                                <span class="badge bg-danger">CANCELLED</span>
                                            @endif
                                            @if ($data['status'] == "DUPLICATED")
                                                <span class="badge bg-danger">DUPLICADO</span>
                                            @endif                                                                             
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Unidad</th>
                                        <td>{{ $reservation->destination->name ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Creación</th>
                                        <td>{{ $reservation->created_at }}</td>
                                    </tr>
                                    <tr>
                                        <th>Referencia</th>
                                        <td>{{ $reservation->reference }}</td>
                                    </tr>
                                    @if( isset( $reservation->originSale->code ) )
                                        <tr>
                                            <th>Origen de venta</th>
                                            <td>{{ $reservation->originSale->code }}</td>
                                        </tr>                                    
                                    @endif                                    
                                    @if( $reservation->open_credit == 1 )
                                        <tr>
                                            <th>Crédito Abierto</th>
                                            <td><span class="badge bg-success">ACEPTADO</span></td>
                                        </tr>
                                    @endif
                                    @if( isset( $reservation->originSale->code ) )
                                        <tr>
                                            <th>Origen de venta</th>
                                            <td>{{ $reservation->originSale->code }}</td>
                                        </tr>                                    
                                    @endif
                                    @if( isset( $reservation->cancellationType->name_es ) )
                                        <tr>
                                            <th>Motivo de cancelación</th>
                                            <td>{{ $reservation->cancellationType->name_es }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if ( $reservation->callCenterAgent != null )
                            <div class="callcenter-x">
                                <div class="d-flex align-items-center mb-3 box zoom-in">
                                    <div class="flex-none w-10 h-10 overflow-hidden rounded-full image-fit">
                                        <img alt="Midone Tailwind HTML Admin Template" src="https://midone-vue.vercel.app/assets/profile-8-AMiR1yEM.jpg">
                                    </div>
                                    <div class="ms-4 me-auto">
                                        <div class="font-medium">{{ $reservation->callCenterAgent->name }}</div>
                                        <div class="text-slate-500 text-xs mt-0.5">Agente de Call Center</div>
                                    </div>
                                    {{-- <div class="text-success">+$50</div> --}}
                                </div>
                            </div>                            
                        @endif

                        @if (RoleTrait::hasPermission(25))
                            <strong>Actividad</strong>
                            <ul class="timeline mt-2 mb-0">
                                @foreach ($reservation->followUps as $followUp)
                                    <li class="timeline-item">
                                        <strong>[{{ $followUp->type }}]</strong>
                                        <span class="float-end text-muted text-sm">{{ date("Y/m/d H:i", strtotime($followUp->created_at)) }}</span>
                                        <p>{{ $followUp->text }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xxl-9 col-xl-8 col-12">
                <div class="controls">
                    @php
                        // dump( $reservation );
                    @endphp
                    @csrf
                    @if (RoleTrait::hasPermission(20))
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Re-envio de correo
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="sendMail('{{ $reservation->items->first()->code }}','{{ $reservation->client_email }}','es')">Español</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="sendMail('{{ $reservation->items->first()->code }}','{{ $reservation->client_email }}','en')">Inglés</a>
                        </div>
                    </div>
                    @endif
                    @if (RoleTrait::hasPermission(21))
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Enviar Mensaje
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">SMS</a>
                            <a class="dropdown-item" href="#">Whatsapp</a>
                        </div>
                    </div>
                    @endif
                    @if (RoleTrait::hasPermission(22))
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Invitación de pago
                            </button>
                            <div class="dropdown-menu" style="">
                                <a class="dropdown-item" href="#" onclick="sendInvitation(event, '{{ $reservation->id }}','en')">Enviar en inglés</a>
                                <a class="dropdown-item" href="#" onclick="sendInvitation(event, '{{ $reservation->id }}','es')">Enviar en español</a>
                            </div>
                        </div>
                    @endif
                    @if (RoleTrait::hasPermission(23))
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#reservationFollowModal"><i class="align-middle" data-feather="plus"></i> Seguimiento</button>
                    @endif
                    {{-- MOSTRARA EL BOTON DE ACTIVACION DE SERVICIO PLUS, SIEMPRE QUE LA RESERVA NO ESTA CANCELADA NI DUPLICADA --}}
                    @if (RoleTrait::hasPermission(94) && $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 && $reservation->is_advanced == 0 )
                        <button class="btn btn-success btn-sm" onclick="enablePlusService({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> Activar servicio plus</button>
                    @endif
                    @if (RoleTrait::hasPermission(24) && $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 )
                        <input type="hidden" value='{{ json_encode($types_cancellations) }}' id="types_cancellations">
                        <button class="btn btn-danger btn-sm" onclick="cancelReservation({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
                    @endif
                    @if (RoleTrait::hasPermission(24) && $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 )
                        <button class="btn btn-danger btn-sm" onclick="duplicatedReservation({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> Marcar como duplicado</button>
                    @endif
                    @if (RoleTrait::hasPermission(67) && ($reservation->is_cancelled == 1 || $reservation->is_duplicated == 1) )
                        <button class="btn btn-success btn-sm" onclick="enableReservation({{ $reservation->id }})"><i class="align-middle" data-feather="alert-circle"></i> Activar</button>
                    @endif
                    @if (RoleTrait::hasPermission(72) && $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 && $reservation->open_credit == 0 )
                        <button class="btn btn-warning btn-sm" onclick="openCredit({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> Crédito Abierto</button>
                    @endif

                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Copiar Link de pago
                        </button>
                        <div class="dropdown-menu" style="">
                            <a class="dropdown-item" href="#" onclick="copyPaymentLink(event, '{{ $reservation->items[0]['code'] }}', '{{ trim($reservation->client_email) }}', 'en')">Inglés</a>
                            <a class="dropdown-item" href="#" onclick="copyPaymentLink(event, '{{ $reservation->items[0]['code'] }}', '{{ trim($reservation->client_email) }}', 'es')">Español</a>
                        </div>
                    </div>
                </div>

                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#icon-tab-1" data-bs-toggle="tab" role="tab">
                                <i class="align-middle" data-feather="map-pin"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#icon-tab-2" data-bs-toggle="tab" role="tab">
                                <i class="align-middle" data-feather="shopping-bag"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#icon-tab-3" data-bs-toggle="tab" role="tab">
                                <i class="align-middle" data-feather="credit-card"></i>
                            </a>
                        </li>
                        @if (RoleTrait::hasPermission(65))
                            <li class="nav-item">
                                <a class="nav-link" href="#icon-tab-4" data-bs-toggle="tab" role="tab">
                                    <i class="align-middle" data-feather="camera"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="icon-tab-1" role="tabpanel">
                            <div class="d-flex">
                                @if (RoleTrait::hasPermission(12)) 
                                <!--<button class="btn btn-success float-end">
                                    <i class="align-middle" data-feather="plus"></i>
                                </button>-->
                                @endif
                            </div>                            
                            @foreach ($reservation->items as $item)
                                @php
                                    // dump( $item );
                                @endphp
                                <div class="services-container">
                                    <h3>{{ $item->code }}</h3>
                                    @if ( $reservation->is_advanced == 1 )
                                        <div class="check-bubble" data-bs-toggle="popover" title="Servicio plus" data-bs-content="incluye cancelación gratuita. bebidas de cortesia. cuponera de descuento. parada de cortesia">
                                            <span class="check-mark">✔</span>
                                        </div>
                                    @endif
                                    <div class="items-container">
                                        <div class="items">
                                            <div class="information_data">
                                                <p><strong>Tipo:</strong> {{ (( $item->is_round_trip == 1 )? 'Round Trip':'One Way') }}</p>
                                                <p><strong>Vehículo:</strong> {{ $item->destination_service->name }}</p>
                                                <p><strong>Pasajeros:</strong> {{ $item->passengers }}</p>
                                                <p><strong># de Vuelo:</strong> {{ $item->flught_number ?? 'N/A' }}</p>
                                            </div>
                                            <div class="actions mb-3">
                                                @if (RoleTrait::hasPermission(13))
                                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal" onclick="itemInfo({{ $item }})">Editar</button>
                                                @endif
                                                <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#arrivalConfirmationModal" onclick="getContactPoints({{ $item->reservations_item_id }}, {{ $item->destination_id }})">
                                                    Confirmacion de llegada
                                                </button>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Confirmacion de salida
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'en', 'departure')">Enviar en inglés</a>
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'es', 'departure')">Enviar en español</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Transfer recogida
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'en', 'transfer-pickup')">Enviar en inglés</a>
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'es', 'transfer-pickup')">Enviar en español</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Transfer regreso
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'en', 'transfer-return')">Enviar en inglés</a>
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'es', 'transfer-return')">Enviar en español</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="item-data">
                                            <table class="table table-striped table-sm">
                                                <thead>
                                                    <tr>
                                                        <td>Desde</td>
                                                        <td>Hacia</td>
                                                        <td>Pickup</td>
                                                        <td>Operación</td>
                                                        <td></td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <p><strong>Zona</strong>: {{ $item->origin->name }}</p>
                                                            <p><strong>Lugar</strong>: {{ $item->from_name }}</p>
                                                        </td>
                                                        <td>
                                                            <p><strong>Zona</strong>: {{ $item->destination->name }}</p>
                                                            <p><strong>Lugar</strong>: {{ $item->to_name }}</p>
                                                        </td>
                                                        <td>{{ date("Y/m/d H:i", strtotime( $item->op_one_pickup )) }}</td>
                                                        <td>
                                                            @if(false)
                                                                @switch($item->op_one_status)
                                                                    @case('PENDING')
                                                                        <span class="badge bg-secondary">PENDING</span>
                                                                    @break
                                                                    @case('CONFIRMED')
                                                                        <span class="badge bg-success">CONFIRMED</span>
                                                                    @break
                                                                    @case('NOSHOW')
                                                                        <span class="badge bg-warning">NOSHOW</span>
                                                                    @break
                                                                    @case('CANCELLED')
                                                                        <span class="badge bg-danger">CANCELLED</span>
                                                                    @break
                                                                    @default
                                                                @endswitch
                                                            @endif
                                                            <div class="btn-group btn-group-sm">
                                                                @if (RoleTrait::hasPermission(68))
                                                                    @php
                                                                        $btn_op_one_type = 'btn-secondary';
                                                                        switch ($item->op_one_status) {
                                                                            case 'PENDING':
                                                                                $btn_op_one_type = 'btn-secondary';
                                                                                break;
                                                                            case 'COMPLETED':
                                                                                $btn_op_one_type = 'bg-success';
                                                                                break;
                                                                            case 'NOSHOW':
                                                                                $btn_op_one_type = 'bg-warning';
                                                                                break;
                                                                            case 'CANCELLED':
                                                                                $btn_op_one_type = 'bg-danger';
                                                                                break;
                                                                        }
                                                                    @endphp
                                                                    @if ( $item->op_one_operation_close == 1 )
                                                                        <button data-bs-toggle="tooltip" data-bs-placement="top" title="Este es el estatus final asignado por operaciones" type="button" class="btn {{ $btn_op_one_type }}" style="color:white;">{{ $item->op_one_status }}</button>                                                                        
                                                                    @else
                                                                        <button type="button" class="btn {{ $btn_op_one_type }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">{{ $item->op_one_status }}</button>
                                                                        <div class="dropdown-menu" style="">
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event, 'arrival', 'PENDING', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Pendiente</a>
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event,  'arrival', 'COMPLETED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Completado</a>
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event, 'arrival', 'NOSHOW', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">No show</a>
                                                                            <hr>
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event, 'arrival', 'CANCELLED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Cancelado</a>
                                                                        </div>                                                                        
                                                                    @endif
                                                                @else
                                                                    @switch($item->op_one_status)
                                                                        @case('PENDING')
                                                                            <span class="badge bg-secondary">PENDING</span>
                                                                        @break
                                                                        @case('CONFIRMED')
                                                                            <span class="badge bg-success">CONFIRMED</span>
                                                                        @break
                                                                        @case('NOSHOW')
                                                                            <span class="badge bg-warning">NOSHOW</span>
                                                                        @break
                                                                        @case('CANCELLED')
                                                                            <span class="badge bg-danger">CANCELLED</span>
                                                                        @break
                                                                        @default
                                                                    @endswitch
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if(false)
                                                                <button class="btn btn-success" type="button"><i class="align-middle" data-feather="message-square"></i></button>
                                                            @endif
                                                            @if (RoleTrait::hasPermission(69))
                                                                <button class="btn {{ (($item->op_one_confirmation == 1)? 'btn-success':'btn-warning') }}" type="button" onclick="updateConfirmation(event, {{ $item->reservations_item_id }}, 'arrival', {{ (($item->op_one_confirmation == 0)? 0:1) }}, {{ $item->reservation_id }})"><i class="align-middle" data-feather="check-circle"></i></button>
                                                            @endif

                                                            <button data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->op_one_operation_close == 1 ? "El servicio se encuentra en una operación cerrada".( RoleTrait::hasPermission(92) ? ", da click si desea desbloquear el servicio del cierre de operación" : "" ) : "El servicio se encuentra en una operacón abierta" }}" class="btn btn-{{ $item->op_one_operation_close == 1 ? "danger" : "success" }} {{  RoleTrait::hasPermission(92) && $item->op_one_operation_close == 1 ? "unlock" : "" }}" type="button" data-id="{{ $item->reservations_item_id }}" data-type="arrival" data-rez_id="{{ $item->reservation_id }}"><i class="align-middle" data-feather="{{ $item->op_one_operation_close == 1 ? "lock" : "unlock" }}"></i></button>
                                                        </td>
                                                    </tr>

                                                    @if($item->is_round_trip == 1)
                                                        <tr>
                                                            <td>
                                                                <p><strong>Zona</strong>: {{ $item->destination->name }}</p>
                                                                <p><strong>Lugar</strong>: {{ $item->to_name }}</p>                                                                
                                                            </td>
                                                            <td>
                                                                <p><strong>Zona</strong>: {{ $item->origin->name }}</p>
                                                                <p><strong>Lugar</strong>: {{ $item->from_name }}</p>
                                                            </td>
                                                            <td>{{ date("Y/m/d H:i", strtotime( $item->op_two_pickup )) }}</td>
                                                            <td>
                                                                @if(false)
                                                                    @switch($item->op_two_status)
                                                                        @case('PENDING')
                                                                            <span class="badge bg-secondary">PENDING</span>
                                                                        @break
                                                                        @case('CONFIRMED')
                                                                            <span class="badge bg-success">CONFIRMED</span>
                                                                        @break
                                                                        @case('NOSHOW')
                                                                            <span class="badge bg-warning">NOSHOW</span>
                                                                        @break
                                                                        @case('CANCELLED')
                                                                            <span class="badge bg-danger">CANCELLED</span>
                                                                        @break
                                                                        @default
                                                                    @endswitch
                                                                @endif
                                                                <div class="btn-group btn-group-sm">
                                                                    @if (RoleTrait::hasPermission(68))
                                                                        @php
                                                                            $btn_op_two_type = 'btn-secondary';
                                                                            switch ($item->op_two_status) {
                                                                                case 'PENDING':
                                                                                    $btn_op_two_type = 'btn-secondary';
                                                                                    break;
                                                                                case 'COMPLETED':
                                                                                    $btn_op_two_type = 'bg-success';
                                                                                    break;
                                                                                case 'NOSHOW':
                                                                                    $btn_op_two_type = 'bg-warning';
                                                                                    break;
                                                                                case 'CANCELLED':
                                                                                    $btn_op_two_type = 'bg-danger';
                                                                                    break;
                                                                            }
                                                                        @endphp
                                                                        @if ( $item->op_two_operation_close == 1 )
                                                                            <button data-bs-toggle="tooltip" data-bs-placement="top" title="Este es el estatus final asignado por operaciones" type="button" class="btn {{ $btn_op_two_type }}" style="color:white;">{{ $item->op_two_status }}</button>    
                                                                        @else
                                                                            <button type="button" class="btn {{ $btn_op_two_type }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">{{ $item->op_two_status }}</button>
                                                                            <div class="dropdown-menu" style="">
                                                                                <a class="dropdown-item" href="#" onclick="setStatus(event, 'departure', 'PENDING', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Pendiente</a>
                                                                                <a class="dropdown-item" href="#" onclick="setStatus(event,  'departure', 'COMPLETED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Completado</a>
                                                                                <a class="dropdown-item" href="#" onclick="setStatus(event, 'departure', 'NOSHOW', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">No show</a>
                                                                                <hr>
                                                                                <a class="dropdown-item" href="#" onclick="setStatus(event, 'departure', 'CANCELLED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Cancelado</a>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        @switch($item->op_two_status)
                                                                            @case('PENDING')
                                                                                <span class="badge bg-secondary">PENDING</span>
                                                                            @break
                                                                            @case('CONFIRMED')
                                                                                <span class="badge bg-success">CONFIRMED</span>
                                                                            @break
                                                                            @case('NOSHOW')
                                                                                <span class="badge bg-warning">NOSHOW</span>
                                                                            @break
                                                                            @case('CANCELLED')
                                                                                <span class="badge bg-danger">CANCELLED</span>
                                                                            @break
                                                                            @default
                                                                        @endswitch
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if(false)
                                                                    <button class="btn btn-success" type="button"><i class="align-middle" data-feather="message-square"></i></button>
                                                                @endif
                                                                @if (RoleTrait::hasPermission(69))
                                                                    <button class="btn {{ (($item->op_two_confirmation == 1)? 'btn-success':'btn-warning') }}" type="button" onclick="updateConfirmation(event, {{ $item->reservations_item_id }}, 'departure', {{ (($item->op_two_confirmation == 0)? 0:1) }}, {{ $item->reservation_id }})"><i class="align-middle" data-feather="check-circle"></i></button>
                                                                @endif

                                                                <button data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $item->op_two_operation_close == 1 ? "El servicio se encuentra en una operación cerrada".( RoleTrait::hasPermission(92) ? ", da click si desea desbloquear el servicio del cierre de operación" : "" ) : "El servicio se encuentra en una operacón abierta" }}" class="btn btn-{{ $item->op_two_operation_close == 1 ? "danger" : "success" }} {{  RoleTrait::hasPermission(92) && $item->op_two_operation_close == 1 ? "unlock" : "" }}" type="button" data-id="{{ $item->reservations_item_id }}" data-type="departure" data-rez_id="{{ $item->reservation_id }}"><i class="align-middle" data-feather="{{ $item->op_two_operation_close == 1 ? "lock" : "unlock" }}"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>                                
                                </div>
                                
                                <input type="hidden" id="from_lat" value="{{ $item->from_lat }}">
                                <input type="hidden" id="from_lng" value="{{ $item->from_lng }}">
                                <input type="hidden" id="to_lat" value="{{ $item->to_lat }}">
                                <input type="hidden" id="to_lng" value="{{ $item->to_lng }}">
                            @endforeach
                        </div>
                        <div class="tab-pane" id="icon-tab-2" role="tabpanel">
                            <div class="d-flex">
                                <h4 class="flex-grow-1 tab-title">Ventas</h4> 
                                @if (RoleTrait::hasPermission(14))
                                <button class="btn btn-success float-end" type="button" data-bs-toggle="modal" data-bs-target="#serviceSalesModal">
                                    <i class="align-middle" data-feather="plus"></i>
                                </button>
                                @endif
                            </div>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th class="text-left">Descripción</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Vendedor</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->sales as $sale)
                                        <tr>
                                            <td>{{ $sale->type->name }}</td>
                                            <td class="text-left">{{ $sale->description }}</td>
                                            <td class="text-center">{{ $sale->quantity }}</td>
                                            <td class="text-center">{{ number_format($sale->total,2) }}</td>
                                            <td class="text-center">{{ $sale->callCenterAgent->name ?? 'System' }}</td>
                                            <td class="text-center">
                                                @if (RoleTrait::hasPermission(15))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#serviceSalesModal" onclick="getSale({{ $sale->id }})"><i class="align-middle" data-feather="edit-2"></i></a>
                                                @endif
                                                @if (RoleTrait::hasPermission(16))
                                                <a href="#" onclick="deleteSale({{ $sale->id }})"><i class="align-middle" data-feather="trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="icon-tab-3" role="tabpanel">
                            <div class="d-flex">
                                <h4 class="flex-grow-1 tab-title">Pagos</h4> 
                                @if (RoleTrait::hasPermission(14))
                                    <button class="btn btn-success float-end" type="button" data-bs-toggle="modal" data-bs-target="#servicePaymentsModal">
                                        <i class="align-middle" data-feather="plus"></i>
                                    </button>
                                @endif
                            </div>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Moneda</th>
                                        <th class="text-center">TC</th>
                                        <th class="text-start">Ref.</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->description }}</td>
                                            <td class="text-end">{{ number_format($payment->total) }}</td>
                                            <td class="text-center">{{ $payment->currency }}</td>
                                            <td class="text-end">{{ number_format($payment->exchange_rate) }}</td>
                                            <td class="text-start">{{ $payment->reference }}</td>
                                            <td class="text-center">
                                                @if (RoleTrait::hasPermission(15))
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#servicePaymentsModal" onclick="getPayment({{ $payment->id }})"><i class="align-middle" data-feather="edit-2"></i></a>
                                                @endif
                                                @if (RoleTrait::hasPermission(16))
                                                <a href="#" onclick="deletePayment({{ $payment->id }})"><i class="align-middle" data-feather="trash"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>
                        </div>
                        @if (RoleTrait::hasPermission(65))
                            <div class="tab-pane" id="icon-tab-4" role="tabpanel">                       
                                @if (RoleTrait::hasPermission(64))
                                    <form id="upload-form" class="dropzone" action="/reservations/upload">
                                        @csrf
                                        <input type="hidden" name="folder" value="{{ $reservation->id }}">
                                    </form>
                                @endif
                                @if (RoleTrait::hasPermission(65))
                                    <div class="image-listing" id="media-listing"></div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>        
        
    </div>

<!-- Modals -->
<x-modals.service_map />

<x-modals.edit_reservation_service :services=$services_types :zones=$zones />

<x-modals.new_sale_reservation :sellers=$sellers :types=$sales_types>
    <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
</x-modals.new_sale_reservation>

<x-modals.new_payment_reservation>
    <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
    <x-slot name="currency">{{ $reservation->currency }}</x-slot>
</x-modals.new_payment_reservation>

<x-modals.new_follow_reservation>
    <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
</x-modals.new_follow_reservation>

<x-modals.edit_reservation_details :reservation=$reservation :sites=$sites :origins=$origins />
<x-modals.reservations.confirmation :reservation=$reservation />

@endsection