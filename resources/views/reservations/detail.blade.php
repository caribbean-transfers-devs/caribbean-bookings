@extends('layout.master')
@section('title') Detalle @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <link href="{{ mix('/assets/css/reservations/detail.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/reservations/detail.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&callback=initMap" async defer></script>
    <script src="{{ mix('assets/js/views/reservationsDetail.js') }}"></script>
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
                            <div class="dropdown show">
                                <a href="#" data-bs-toggle="dropdown" data-bs-display="static">
                                    <i class="align-middle" data-feather="more-horizontal"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#" type="button" data-bs-toggle="modal" data-bs-target="#serviceClientModal">Editar</a>
                                </div>
                            </div>
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
                                    <td>{{ $reservation->currency == 1 ? 'USD' : 'MXN' }}</td>
                                </tr>                                
                                <tr>
                                    <th>Estatus</th>
                                    <td>
                                        @foreach ($reservation->items as $item)
                                            @if ($item->op_one_status == 'PENDING')
                                                <span class="badge bg-info">OW Pending</span>
                                            @endif
                                            @if ($item->op_two_status == 'PENDING')
                                                <span class="badge bg-info">RT Pending</span>
                                            @endif
                                            @if ($item->op_one_status == 'CONFIRMED')
                                                <span class="badge bg-success">OW Confirmed</span>
                                            @endif
                                            @if ($item->op_two_status == 'CONFIRMED')
                                                <span class="badge bg-success">RT Confirmed</span>
                                            @endif
                                            @if ($item->op_one_status == 'CONFIRMED')
                                                <span class="badge bg-success">OW Completed</span>
                                            @endif
                                            @if ($item->op_two_status == 'CONFIRMED')
                                                <span class="badge bg-success">RT Completed</span>
                                            @endif
                                            @if ($item->op_one_status == 'CANCELLED')
                                                <span class="badge bg-danger">OW Cancelled</span>
                                            @endif
                                            @if ($item->op_two_status == 'CANCELLED')
                                                <span class="badge bg-danger">RT Cancelled</span>
                                            @endif
                                            @if ($item->op_one_status == 'NOSHOW')
                                                <span class="badge bg-danger">OW No Show</span>
                                            @endif
                                            @if ($item->op_two_status == 'NOSHOW')
                                                <span class="badge bg-danger">RT No Show</span>
                                            @endif
                                        @endforeach                                                                              
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
                            </tbody>
                        </table>

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

                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="controls">
                    @csrf
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
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Enviar Mensaje
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">SMS</a>
                            <a class="dropdown-item" href="#">Whatsapp</a>
                        </div>
                    </div>
                    <button class="btn btn-secondary btn-sm" onclick="sendInvitation('{{ $reservation->items->first()->code }}','{{ $reservation->client_email }}','en')">Invitación de pago</button>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#reservationFollowModal"><i class="align-middle" data-feather="plus"></i> Seguimiento</button>
                    <button class="btn btn-danger btn-sm" onclick="cancelReservation({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
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
                                <button class="btn btn-success float-end">
                                    <i class="align-middle" data-feather="plus"></i>
                                </button>
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
                                            <p><strong>Pickup:</strong> {{ $item->op_one_pickup }}</p>
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
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal">
                                                <i class="align-middle" data-feather="edit"></i>
                                            </button>                                           
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceMapModal" onclick="serviceInfo('{{ $item->from_name }}','{{ $item->to_name }}','{{ $item->distance_time }}','{{ $item->distance_km }}')">
                                                <i class="align-middle" data-feather="map-pin"></i>
                                            </button>
                                        </div>                                        
                                    </div>
                                    @if ($item->is_round_trip)
                                        <div class="flight_data">
                                            <h4>Round Trip</h4>
                                        </div>
                                        <div class="items">
                                            <div class="information_data">
                                                <p><strong>Desde:</strong> {{ $item->to_name }}</p>
                                                <p><strong>Hacia:</strong> {{ $item->from_name }}</p>
                                                <p><strong>Pickup:</strong> {{ $item->op_two_pickup }}</p>
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
                                            <div class="actions">
                                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal">
                                                    <i class="align-middle" data-feather="edit"></i>
                                                </button> 
                                                <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceMapModal" onclick="serviceInfo('{{ $item->to_name }}','{{ $item->from_name }}','{{ $item->distance_time }}','{{ $item->distance_km }}')">
                                                    <i class="align-middle" data-feather="map-pin"></i>
                                                </button>
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
                                <button class="btn btn-success float-end" type="button" data-bs-toggle="modal" data-bs-target="#serviceSalesModal">
                                    <i class="align-middle" data-feather="plus"></i>
                                </button>
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
                                            <td class="text-center">{{ $sale->callCenterAgent->name }}</td>
                                            <td class="text-center">
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#serviceSalesModal" onclick="getSale({{ $sale->id }})"><i class="align-middle" data-feather="edit-2"></i></a>
                                                <a href="#" onclick="deleteSale({{ $sale->id }})"><i class="align-middle" data-feather="trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="icon-tab-3" role="tabpanel">
                            <div class="d-flex">
                                <h4 class="flex-grow-1 tab-title">Pagos</h4> 
                                <button class="btn btn-success float-end" type="button" data-bs-toggle="modal" data-bs-target="#servicePaymentsModal">
                                    <i class="align-middle" data-feather="plus"></i>
                                </button>
                            </div>
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">TC</th>
                                        <th class="text-center">Estatus<sup>*</sup></th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->payments as $payment)
                                        <tr>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td class="text-end">{{ number_format($payment->total) }}</td>
                                            <td class="text-end">{{ number_format($payment->exchange_rate) }}</td>
                                            <td class="text-center">
                                                @if($payment->status == 1)
                                                    <span class="badge bg-success">Pagado</span>
                                                @else
                                                    <span class="badge bg-danger">Pendiente</span>
                                                @endif                                              
                                            </td>
                                            <td class="text-center">
                                                <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
                                                <a href="#"><i class="align-middle" data-feather="trash"></i></a>
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
</x-modals.new_payment_reservation>

<x-modals.new_follow_reservation>
    <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
</x-modals.new_follow_reservation>

<x-modals.edit_reservation_details :reservation=$reservation />
@endsection