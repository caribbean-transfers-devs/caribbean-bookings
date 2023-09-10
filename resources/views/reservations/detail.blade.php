@extends('layout.master')
@section('title') Detalle @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <link href="{{ mix('/assets/css/reservations/detail.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/reservations/detail.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
    
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
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Re-envio de correo
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">Español</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Inglés</a>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            TEXTO
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">SMS</a>
                            <a class="dropdown-item" href="#">Whatsapp</a>
                        </div>
                    </div>
                    <button class="btn btn-secondary btn-sm">Invitación de pago</button>
                    <button class="btn btn-success btn-sm"><i class="align-middle" data-feather="plus"></i> Seguimiento</button>
                    <button class="btn btn-danger btn-sm"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
                    <button class="btn btn-danger btn-sm"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
                    <button class="btn btn-danger btn-sm"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
                    <button class="btn btn-danger btn-sm"><i class="align-middle" data-feather="delete"></i> Cancelar reservación</button>
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
                            
                            @for ($i = 1; $i <=2; $i++)                                                    
                            <div class="services-container">
                                <h3>xFs81LF</h3>
                                <div class="items-container">                                    
                                    <div class="items">
                                        <div class="information_data">
                                            <p><strong>Tipo:</strong> Taxi</p>
                                            <p><strong>Pasajeros:</strong> 4</p>
                                            <p><strong>Desde:</strong> Aeropuerto de Cancún</p>
                                            <p><strong>Hacia:</strong> Hotel Alux</p>
                                            <p><strong>Pickup:</strong> 2023-10-15 @ 12:30</p>
                                            <p>
                                                <!-- 'PENDING', 'COMPLETED', 'NOSHOW', 'CANCELLED' -->
                                                <span class="badge bg-secondary">PENDING</span>
                                                <span class="badge bg-success">COMPLETED</span>
                                                <span class="badge bg-warning">NOSHOW</span>
                                                <span class="badge bg-danger">CANCELLED</span>
                                            </p>
                                        </div>
                                        <div class="actions">
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal">
                                                <i class="align-middle" data-feather="edit"></i>
                                            </button>                                           
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceMapModal">
                                                <i class="align-middle" data-feather="map-pin"></i>
                                            </button>
                                        </div>
                                        <div class="flight_data">
                                            <h4>Datos de vuelo</h4>
                                        </div>
                                    </div>
                                    <div class="items">
                                        <div class="information_data">
                                            <p><strong>Tipo:</strong> Taxi</p>
                                            <p><strong>Pasajeros:</strong> 4</p>
                                            <p><strong>Desde:</strong> Aeropuerto de Cancún</p>
                                            <p><strong>Hacia:</strong> Hotel Alux</p>
                                            <p><strong>Pickup:</strong> 2023-10-17 @ 14:00</p>
                                            <p>
                                                <!-- 'PENDING', 'COMPLETED', 'NOSHOW', 'CANCELLED' -->
                                                <span class="badge bg-secondary">PENDING</span>
                                                <span class="badge bg-success">COMPLETED</span>
                                                <span class="badge bg-warning">NOSHOW</span>
                                                <span class="badge bg-danger">CANCELLED</span>
                                            </p>
                                        </div>
                                        <div class="actions">
                                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal">
                                                <i class="align-middle" data-feather="edit"></i>
                                            </button> 
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceMapModal">
                                                <i class="align-middle" data-feather="map-pin"></i>
                                            </button>
                                        </div>
                                    </div>                              
                                </div>                                
                            </div>
                            @endfor
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
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Vendedor</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Transportación</td>
                                        <td class="text-left">Taxi | Viaje Sencillo</td>
                                        <td class="text-end">40</td>
                                        <td class="text-center">Juan Pérez</td>
                                        <td class="text-center">
                                            <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
                                            <a href="#"><i class="align-middle" data-feather="trash"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Extra</td>
                                        <td class="text-left">Vino </td>
                                        <td class="text-end">40</td>
                                        <td class="text-center">Juan Pérez</td>
                                        <td class="text-center">
                                            <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
                                            <a href="#"><i class="align-middle" data-feather="trash"></i></a>
                                        </td>
                                    </tr> 
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
                                    <tr>
                                        <td>Efectivo</td>
                                        <td class="text-end">20.00</td>
                                        <td class="text-end">18.00</td>
                                        <td class="text-center">
                                            <span class="badge bg-success">Pagado</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
                                            <a href="#"><i class="align-middle" data-feather="trash"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Efectivo</td>
                                        <td class="text-end">5.00</td>
                                        <td class="text-end">18.00</td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">Pendiente</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="#"><i class="align-middle" data-feather="edit-2"></i></a>
                                            <a href="#"><i class="align-middle" data-feather="trash"></i></a>
                                        </td>
                                    </tr>  
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>        
        
    </div>

<!-- Modals -->
<div class="modal fade" id="serviceMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información de servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>Desde:</strong> Aeropuerto de Cancún</p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>Hacia:</strong> Hotel Alux Cancun</p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>Tiempo:</strong> 25 min.</p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>KM:</strong> 8 KM</p>
                    </div>
                    <div class="col-12">
                        <div class="content" id="services_map">Div para visualizar el mapa</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="serviceEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="serviceTypeForm">Tipo</label>
                        <select class="form-control mb-2">
                            <option value="1">Taxi</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaxForm">Pasajeros</label>
						<input type="text" class="form-control mb-2" id="servicePaxForm">
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <label class="form-label" for="serviceFromForm">Desde</label>
						<input type="text" class="form-control mb-2" id="serviceFromForm">
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <label class="form-label" for="serviceToForm">Hacia</label>
						<input type="text" class="form-control mb-2" id="serviceToForm">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="serviceDateForm">Hora de recogida</label>
						<input type="datetime-local" class="form-control mb-2" id="serviceDateForm">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="serviceSalesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="serviceSalesTypeForm">Tipo</label>
                        <select class="form-control mb-2" id="serviceSalesTypeForm">
                            <option value="1" selected>Transportación</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="serviceSalesDescriptionForm">Descripción</label>
						<input type="text" class="form-control mb-2" id="serviceSalesDescriptionForm">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="serviceSalesQuantityForm">Cantidad</label>
						<input type="number" class="form-control mb-2" id="serviceSalesQuantityForm">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="serviceSalesTotalForm">Total</label>
						<input type="number" class="form-control mb-2" id="serviceSalesTotalForm">
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <label class="form-label" for="serviceSalesAgentForm">Agente</label>
						<select class="form-control mb-2" id="serviceSalesAgentForm">
                            <option value="1" selected>Juan Perez</option>
                            <option value="1">Esteban Vega</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="servicePaymentsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaymentsTypeModal">Tipo</label>
                        <select class="form-control mb-2" id="servicePaymentsTypeModal">
                            <option value="1" selected>Efectivo</option>
                            <option value="2">Tarjeta</option>
                            <option value="3">PayPal</option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaymentsDescriptionModal">Descripción / referencia</label>
						<input type="text" class="form-control mb-2" id="servicePaymentsDescriptionModal">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaymentsTotalModal">Total</label>
						<input type="number" class="form-control mb-2" id="servicePaymentsTotalModal">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaymentsCurrencyModal">Moneda</label>
						<select class="form-control mb-2" id="servicePaymentsCurrencyModal">
                            <option value="1" selected>USD</option>
                            <option value="2">MXN</option>                            
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaymentsExchangeModal">Tipo de cambio</label>
						<input type="number" class="form-control mb-2" id="servicePaymentsExchangeModal">
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label class="form-label" for="servicePaymentsRequestModal">Solicitar pago</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="servicePaymentsRequestModal">
                            <label class="form-check-label" for="servicePaymentsRequestModal">Solicitar al cliente al abordar</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<x-modals.edit_reservation_details :reservation=$reservation />
@endsection