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
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=places"></script>
    <script src="{{ mix('assets/js/views/reservations/reservationsDetail.js') }}"></script>
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">Detalle de reservación</h1>            
        </div>

        <div class="row">

            <div class="col-xl-4">
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
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unidad</th>
                                    <td>{{ $reservation->destination->name }}</td>
                                </tr>
                                <tr>
                                    <th>Creación</th>
                                    <td>{{ $reservation->created_at }}</td>
                                </tr>
                                <tr>
                                    <th>Referencia</th>
                                    <td>{{ $reservation->reference }}</td>
                                </tr>
                            </tbody>
                        </table>
                        @if (RoleTrait::hasPermission(25))
                        <strong>Actividad</strong>

                        <ul class="timeline mt-2 mb-0">
                            @foreach ($reservation->followUps as $followUp)
                                <li class="timeline-item">
                                    <strong>[{{ $followUp->type }}]</strong>
                                    @php
                                        $time = $followUp->created_at->diffInMinutes(now());
                                        if($time > 90){
                                            $time /= 60;
                                            $time = number_format($time, 0, '.', '');
                                            $time .= ' hours';
                                        }else if($time > 1440){
                                            $time /= 1440;
                                            $time = number_format($time, 0, '.', '');
                                            $time .= ' days';
                                        }else{
                                            $time .= ' minutes';
                                        }
                                    @endphp
                                    <span class="float-end text-muted text-sm">{{ $time }} ago</span>
                                    <p>{{ $followUp->text }}</p>
                                </li>  
                            @endforeach
                           
                        </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="controls">
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
                    @if (RoleTrait::hasPermission(24))
                    <button class="btn btn-danger btn-sm" onclick="cancelReservation({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
                    @endif
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
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="icon-tab-1" role="tabpanel">
                            <div class="d-flex">
                                <h4 class="flex-grow-1 tab-title">Servicios</h4>
                                @if (RoleTrait::hasPermission(12)) 
                                <!--<button class="btn btn-success float-end">
                                    <i class="align-middle" data-feather="plus"></i>
                                </button>-->
                                @endif
                            </div>
                            
                            @foreach ($reservation->items as $item)               
                            <div class="services-container">
                                <h3>{{ $item->code }}</h3>
                                <div class="items-container">                                    
                                    <div class="items">
                                        <div class="information_data">
                                            <p><strong>Tipo:</strong> {{ $item->destination_service->name }}</p>
                                            <p><strong>Pasajeros:</strong> {{ $item->passengers }}</p>
                                            <p><strong>Desde:</strong> {{ $item->from_name }}</p>
                                            <p><strong>Hacia:</strong> {{ $item->to_name }}</p>
                                            <p><strong>Pickup:</strong> {{ ((!empty($item->op_one_pickup))? date("Y-m-d H:i", strtotime($item->op_one_pickup)) : '¡CORREGIR!') }}</p>
                                            <p>
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
                                            </p>
                                        </div>
                                        <div class="actions mb-3">
                                            @if (RoleTrait::hasPermission(13))
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal" 
                                            onclick="itemInfo({{ $item }})">
                                                Editar
                                            </button>  
                                            @endif 
                                            @php
                                                //TRANSFORM NUMBER OF SECS INTO HOURS OR MINUTES
                                                $time = $item->distance_time / 60;
                                                if($time > 90){
                                                    $time /= 60;
                                                    $time = number_format($time, 0, '.', '');
                                                    $time .= ' horas';
                                                }else{
                                                    $time .= ' minutos';
                                                }
                                            @endphp                                        
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceMapModal" onclick="serviceInfo('{{ $item->from_name }}','{{ $item->to_name }}','{{ $time }}','{{ $item->distance_km }}')">
                                                Ver mapa
                                            </button>
                                            @if($item->is_primary == 1)
                                                <button class="btn btn-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#arrivalConfirmationModal" onclick="getContactPoints({{ $item->reservations_item_id }}, {{ $item->destination_id }})">
                                                    Confirmacion de llegada
                                                </button>
                                            @endif
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Confirmacion de salida
                                                </button>
                                                <div class="dropdown-menu" style="">
                                                    <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'en')">Enviar en inglés</a>
                                                    <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $item->destination_id }}, 'es')">Enviar en español</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($item->is_round_trip)
                                        <div class="flight_data">
                                            <h4>Regreso</h4>
                                        </div>
                                        <div class="items">
                                            <div class="information_data">
                                                <p><strong>Pickup:</strong> {{ ((!empty($item->op_two_pickup))? date("Y-m-d H:i", strtotime($item->op_two_pickup)) : '¡CORREGIR!') }}</p>
                                                <p>
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
                                                </p>
                                            </div>
                                        </div>              
                                    @endif                                    
                                                    
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
                    </div>
                </div>
            </div>
            
        </div>        
        
    </div>

<!-- Modals -->
<x-modals.service_map />

<x-modals.edit_reservation_service :services=$services_types />

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

<x-modals.edit_reservation_details :reservation=$reservation :sites=$sites />
<x-modals.reservations.confirmation :reservation=$reservation />

@endsection