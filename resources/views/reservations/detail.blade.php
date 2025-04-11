@php
    use Carbon\Carbon;
    // dump($reservation->toArray());
@endphp

@extends('layout.app')
@section('title') Detalle @endsection

@push('Css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link href="{{ mix('/assets/css/sections/reservation_details.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reservation_details.min.css') }}" rel="stylesheet" >
    <style>
        .countdown-container {
            margin-bottom: 16px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 16px;            
        }

        .countdown {
            display: flex;
            gap: 16px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .countdown div {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 15px;
            border-radius: 10px;
            min-width: 70px;
            transition: all 0.3s ease;
        }

        .countdown div span {
            display: block;
            font-size: 16px;
            font-weight: normal;
            opacity: 0.8;
        }

        /* Animación sutil */
        .countdown div:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .expired {
            font-size: 18px;
            font-weight: bold;
            color: #ff5757;
        }  
    </style>
@endpush

@push('Js')
    <script>
        const rez_id = {{ isset($reservation->id) ? $reservation->id : 0 }};
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=places"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.10/dayjs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.10/plugin/duration.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.11.10/plugin/relativeTime.min.js"></script>    
    <script src="{{ mix('assets/js/sections/reservations/details.min.js') }}"></script>
    @if ( $data['status'] == "QUOTATION" )
        <script>
            dayjs.extend(dayjs_plugin_duration);
            dayjs.extend(dayjs_plugin_relativeTime);

            function iniciarContador(fechaObjetivo) {
                function actualizarContador() {
                    const ahora = dayjs();
                    const vencimiento = dayjs(fechaObjetivo);
                    const diferencia = vencimiento.diff(ahora);

                    if (diferencia <= 0) {
                        document.getElementById("countdown").innerHTML = '<span class="expired">¡Tiempo vencido!</span>';
                        clearInterval(intervalo);
                        return;
                    }

                    const duracion = dayjs.duration(diferencia);
                    document.getElementById("countdown").innerHTML = `
                        <div><span>DÍAS</span>${duracion.days()}</div>
                        <div><span>HORAS</span>${duracion.hours()}</div>
                        <div><span>MINUTOS</span>${duracion.minutes()}</div>
                        <div><span>SEGUNDOS</span>${duracion.seconds()}</div>
                    `;
                }

                actualizarContador();
                const intervalo = setInterval(actualizarContador, 1000);
            }

            // Fecha de vencimiento (Cambia esto por la fecha que necesites)
            const fechaVencimiento = `{{ $reservation->expires_at }}`;
            iniciarContador(fechaVencimiento);
        </script>
    @endif
@endpush

@section('content')
    {{-- @dump($reservation->toArray()); --}}
    <div class="row layout-top-spacing">
        <div class="col-xxl-3 col-xl-4 col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-actions float-end">
                        @if (auth()->user()->hasPermission(11))
                        <div class="dropdown show">
                            <a href="#" data-bs-toggle="dropdown" data-bs-display="static">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal align-middle"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#" type="button" data-bs-toggle="modal" data-bs-target="#serviceClientModal">Editar</a>
                            </div>
                        </div>
                        @endif
                    </div>
                    <h5 class="card-title mb-0">{{ $reservation->site->name }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-container table-details-booking">
                        <table class="table table-hover table-striped table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th>Estatus</th>
                                    <td><button type="button" class="btn btn-{{ auth()->user()->classStatusBooking($data['status']) }}">{{ auth()->user()->statusBooking($data['status']) }}</button></td>
                                </tr>
                                @if ( $data['status'] == "QUOTATION" )
                                    <tr>
                                        <th>Fecha limite de pago</th>
                                        <td>{{ $reservation->expires_at }}</td>
                                    </tr>                                    
                                @endif
                                <tr>
                                    <th>Pago al llegar</th>
                                    <td><span class="badge bg-{{ $reservation->pay_at_arrival == 1 ? 'success' : 'danger' }}">{{ $reservation->pay_at_arrival == 1 ? 'Sí' : 'No' }}</span></td>
                                </tr>
                                @if ( $reservation->is_quotation == 0 && $reservation->was_is_quotation == 1 )
                                    <tr>
                                        <th>Fue cotización</th>
                                        <td><span class="badge bg-{{ $reservation->was_is_quotation == 1 ? 'success' : 'danger' }}">{{ $reservation->was_is_quotation == 1 ? 'Sí' : 'No' }}</span></td>
                                    </tr>
                                @endif
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
                                    <th>Destino</th>
                                    <td>{{ $reservation->destination->name ?? '' }}</td>
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
                                @if ( $reservation->callCenterAgent != null )
                                    <tr>
                                        <th>Agente de Call Center</th>
                                        <td>{{ $reservation->callCenterAgent->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estatus de comisión</th>
                                        <td><span class="badge btn-{{ $reservation->is_commissionable == 1 ? "success ".( auth()->user()->hasPermission(95) ? 'deleteCommission' : '' ) : "danger" }}" data-code="{{ $reservation->id }}" style="cursor: pointer;">{{ $reservation->is_commissionable == 1 ? "Comsionable" : "No comisionable" }}</span></td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Total a pagar:</th>
                                    <td>$ {{ round( $data['total_sales'], 2) }} {{ $reservation->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Total pagado:</th>
                                    <td>$ {{ round( $data['total_payments'], 2) }} {{ $reservation->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Total pendiente de pago:</th>
                                    <td>$ {{ round( $data['total_sales'], 2) - round( $data['total_payments'], 2) }} {{ $reservation->currency }}</td>
                                </tr>
                                @if( isset( $reservation->cancellationType->name_es ) )
                                    <tr>
                                        <th>Motivo de cancelación</th>
                                        <td>{{ $reservation->cancellationType->name_es }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Creación</th>
                                    <td>{{ $reservation->created_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- <hr style="width:95%; margin-left: auto; margin-right: auto;"> --}}
                    @if (auth()->user()->hasPermission(25))
                        <div class="NewTimeLine">
                            <h6 class="my-3">Actividad</h6>
                            <ul>
                                @foreach($reservation->followUps as $key => $followUp)
                                    <li>
                                        @php
                                            $fecha = Carbon::parse($followUp->created_at);
                                        @endphp
                                        <div style="display: flex;justify-content: space-between;align-items: center;">
                                            <strong class="text-black">[{{ $followUp->type }}]</strong>
                                            <span>{{ date("Y/m/d H:i", strtotime($followUp->created_at)) }}</span> 
                                            <span>{{ $fecha->diffForHumans() }}</span>
                                        </div>
                                        <div class="content">
                                            <h3>{{ $followUp->name }}</h3>
                                            <p>{{ $followUp->text }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>                        
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xxl-9 col-xl-8 col-12">
            <div class="controls">
                @csrf
                <input type="hidden" value='{{ json_encode($types_cancellations) }}' id="types_cancellations">

                {{-- NOS PERMITE REENVIO DE CORREO DE LA RESERVACIÓN AL CLIENTE, CUANDO TENEMOS EL PERMISO Y ES PENDIENTE, CONFIRMADA O A CREDITO --}}
                @if ( ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" || $data['status'] == "QUOTATION" ) && auth()->user()->hasPermission(20) )
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btndefault" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            RE-ENVIO DE CORREO
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btndefault">
                            <a class="dropdown-item" href="#" onclick="sendMail('{{ $reservation->items->first()->code }}','{{ $reservation->client_email }}','es')">Español</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="sendMail('{{ $reservation->items->first()->code }}','{{ $reservation->client_email }}','en')">Inglés</a>
                        </div>
                    </div>
                @endif

                {{-- NOS PERMITE AGREGAR SEGUIMIENTOS DE LA RESERVA, SOLO CUANDO ESTA COMO PENDIENTE, CONFIRMADA O A CREDITO --}}
                {{-- ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" || $data['status'] == "QUOTATION" ) &&  --}}
                @if ( auth()->user()->hasPermission(23) )
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#reservationFollowModal"><i class="align-middle" data-feather="plus"></i> AGREGAR SEGUIMIENTO</button>
                @endif

                {{-- NOS PERMITE ENVIAR UN MENSAJE --}}
                {{-- @if ( ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" ) && auth()->user()->hasPermission(21) )
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            ENVIAR MENSAJE
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">SMS</a>
                            <a class="dropdown-item" href="#">Whatsapp</a>
                        </div>
                    </div>
                @endif --}}

                {{-- NOS PERMITE ENVIAR UNA INVITACIÓN DE PAGO AL CLIENTE CUANDO LA RESERVA SEA DIFERENTE DE CANCELADO O DUPLICADO --}}
                @if ( ( $data['status'] != "CANCELLED" && $data['status'] != "DUPLICATED" ) && auth()->user()->hasPermission(22))
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btndefault" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            INVITACIÓN DE PAGO
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btndefault">
                            <a class="dropdown-item" href="#" onclick="sendInvitation(event, '{{ $reservation->id }}','en')">Enviar en inglés</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="sendInvitation(event, '{{ $reservation->id }}','es')">Enviar en español</a>
                        </div>
                    </div>
                @endif

                {{-- NOS PERMITE COPIAR EL LINK DE PAGO PARA ENVIARSELO AL CLIENTE LA RESERVA SEA DIFERENTE DE CANCELADO O DUPLICADO --}}
                @if ( $data['status'] != "CANCELLED" && $data['status'] != "DUPLICATED" )
                    <div class="btn-group btn-group-sm" role="group">
                        <button id="btndefault" type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            COPIAR LINK DE PAGO
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btndefault">
                            <a class="dropdown-item" href="#" onclick="copyPaymentLink(event, '{{ $reservation->items[0]['code'] }}', '{{ trim($reservation->client_email) }}', 'en')">Inglés</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="copyPaymentLink(event, '{{ $reservation->items[0]['code'] }}', '{{ trim($reservation->client_email) }}', 'es')">Español</a>
                        </div>
                    </div>
                @endif

                {{-- NOS PERMITE INDICAR QUE CLIENTE PAGARA A LA LLEGADA, SOLO SE MOSTRARA CUANDO SEA COTIZACIÓN O PENDIENTE --}}
                @if ( $reservation->pay_at_arrival == 0 && ( $data['status'] == "QUOTATION" || $data['status'] == "PENDING" ) )
                    <button class="btn btn-warning btn-sm enablePayArrival" id="enablePayArrival" data-code="{{ $reservation->id }}"><i class="align-middle" data-feather="plus"></i> ACTIVAR PAGO A LA LLEGADA</button>
                @endif

                {{-- MOSTRARA EL BOTON DE ACTIVACION DE SERVICIO PLUS, SIEMPRE QUE LA RESERVA NO ESTA CANCELADA NI DUPLICADA --}}
                @if (auth()->user()->hasPermission(94) && $reservation->is_quotation == 0 && $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 && $reservation->is_advanced == 0 )
                    <button class="btn btn-success btn-sm enablePlusService" id="enablePlusService" data-code="{{ $reservation->id }}"><i class="align-middle" data-feather="delete"></i> ACTIVAR SERVICIO PLUS</button>
                @endif

                {{-- NOS PERMITE PONER COMO CREDITO ABIERTO CUANDO LA RESERVA ESTA CONFIRMADA Y EL CLIENTE QUIERE CANCELAR --}}
                {{-- NECESITO APLICAR RECLAS MUCHO MAS ESPECIFICAS --}}
                @if ( $data['status'] == "CONFIRMED" && auth()->user()->hasPermission(72) )
                    <button class="btn btn-warning btn-sm markReservationOpenCredit" id="markReservationOpenCredit" data-code="{{ $reservation->id }}" data-status="{{ $data['status'] }}" onclick="openCredit({{ $reservation->id }})"><i class="align-middle" data-feather="delete"></i> CRÉDITO ABIERTO</button>
                @endif
                    
                {{-- NOS PERMITE PODER ACTIVAR LA RESERVA CUANDO ESTA COMO CREDITO ABIERTO --}}
                @if ( ( $data['status'] == "OPENCREDIT" || $data['status'] == "DUPLICATED" || $data['status'] == "CANCELLED" || ( $data['status'] == "CANCELLED" && $reservation->was_is_quotation == 1 ) ) && auth()->user()->hasPermission(67) )
                    <button class="btn btn-success btn-sm reactivateReservation" id="reactivateReservation" data-code="{{ $reservation->id }}" data-status="{{ $data['status'] }}" data-pay_at_arrival="{{ $reservation->pay_at_arrival }}"><i class="align-middle" data-feather="alert-circle"></i> REACTIVAR RESERVA</button>
                @endif

                {{-- NOS PERMITE INDICAR QUE CLIENTE ESTA SOLICITANDO UN REEMBOLSO --}}
                @if ( ( $data['status'] == "CONFIRMED" || $data['status'] == "CANCELLED" ) && $data['total_payments'] > 0 )
                    <button class="btn btn-warning btn-sm refundRequest" id="refundRequest" data-code="{{ $reservation->id }}"><i class="align-middle" data-feather="delete"></i> SOLICITUD DE REEMBOLSO A CONTABILIDAD</button>
                @endif

                {{-- NOS PERMITE MARCAR COMO DUPLICADA LA RESERVA --}}
                @if (auth()->user()->hasPermission(24) && $reservation->is_quotation == 0 && $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 )
                    <button class="btn btn-danger btn-sm markReservationDuplicate" id="markReservationDuplicate" data-code="{{ $reservation->id }}" data-status="{{ $data['status'] }}"><i class="align-middle" data-feather="delete"></i> MARCAR COMO DUPLICADO</button>
                @endif                
            </div>

            @if ( $data['status'] == "QUOTATION" )
                <div class="countdown-container">
                    <h1>Tiempo restante para realizar el pago o la reserva se cancelara automaticamente</h1>
                    <div id="countdown" class="countdown">
                        <div><span>Días</span>00</div>
                        <div><span>Horas</span>00</div>
                        <div><span>Minutos</span>00</div>
                        <div><span>Segundos</span>00</div>
                    </div>
                </div>
            @endif

            <div class="tab">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#icon-tab-1" data-bs-toggle="tab" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin align-middle"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            Detalles
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#icon-tab-2" data-bs-toggle="tab" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-shopping-bag align-middle"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#icon-tab-3" data-bs-toggle="tab" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card align-middle"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            Pagos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#icon-tab-4" data-bs-toggle="tab" role="tab">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card align-middle"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            Reembolsos
                        </a>
                    </li>
                    @if (auth()->user()->hasPermission(65))
                        <li class="nav-item">
                            <a class="nav-link" href="#icon-tab-5" data-bs-toggle="tab" role="tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-camera align-middle"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                Imagenes
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane services active" id="icon-tab-1" role="tabpanel">
                        @foreach ($reservation->items as $item)
                            <div class="services-container">
                                <h3>{{ $item->code }}</h3>

                                {{-- NOS INDICA QUE TIENE ACTIVO EL SERVICIO AVANZADO --}}
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
                                            <p><strong># de Vuelo:</strong> {{ $item->flight_number ?? 'N/A' }}</p>
                                        </div>
                                        <div class="actions mb-3">
                                            @if ( ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" || $data['status'] == "QUOTATION" ) && auth()->user()->hasPermission(13))
                                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceEditModal" onclick="itemInfo({{ $item }})">EDITAR SERVICIO</button>
                                            @endif

                                            {{-- NOS PERMITE REALIZAR ESTAS ACCIONES SOLO CUANDO LA RESERVA ESTA PENDIENTE CONFIRMADA O A CREDITO --}}
                                            @if ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" || $data['status'] == "QUOTATION" )                                            
                                                <button class="btn btn-secondary btn-sm arrivalConfirmation" type="button" data-id="{{ $item->reservations_item_id }}" data-bs-toggle="modal" data-bs-target="#arrivalConfirmationModal">CONFIRMACIÓN DE LLEGADA</button>                            
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        CONFIRMACIÓN DE SALIDA
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $reservation->destination_id }}, 'en', 'departure')">Enviar en inglés</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $reservation->destination_id }}, 'es', 'departure')">Enviar en español</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        TRANSFER RECOGIDA
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $reservation->destination_id }}, 'en', 'transfer-pickup')">Enviar en inglés</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $reservation->destination_id }}, 'es', 'transfer-pickup')">Enviar en español</a>
                                                    </div>
                                                </div>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        TRANSFER REGRESO
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                    </button>
                                                    <div class="dropdown-menu" style="">
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $reservation->destination_id }}, 'en', 'transfer-return')">Enviar en inglés</a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item" href="#" onclick="sendDepartureConfirmation(event, {{ $item->reservations_item_id }}, {{ $reservation->destination_id }}, 'es', 'transfer-return')">Enviar en español</a>
                                                    </div>
                                                </div>

                                                @if ( $reservation->reserve_rating != NULL )
                                                    <div class="btn-group" role="group" aria-label="likes">                                                    
                                                        <button type="button" class="btn btn-{{ $reservation->reserve_rating == 1 ? 'success' : 'danger' }} bs-tooltip" title="Esta es la calificación final de la reserva."><?=( $reservation->reserve_rating == 1 ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-down"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path></svg>' )?></button>
                                                    </div>
                                                @else
                                                    <div class="btn-group" role="group" aria-label="likes">                                                    
                                                        <button type="button" class="btn btn-success bs-tooltip enabledLike" title="click para calificar como positiva la reserva." data-reservation="{{ $reservation->id }}" data-status="1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg></button>
                                                        <button type="button" class="btn btn-danger bs-tooltip enabledLike" title="click para calificar como negativa la reserva." data-reservation="{{ $reservation->id }}" data-status="0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-down"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"></path></svg></button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="item-data">
                                        <div class="table-container">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Tipo de servicio</th>
                                                        <th>Desde</th>
                                                        <th>Hacia</th>
                                                        <th>Pickup</th>
                                                        <th>Estatus de servicio</th>
                                                        <th>Comentario</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            @php
                                                                $service = (object) array( 
                                                                    'final_service_type' => $item->final_service_type_one, 
                                                                    'op_type' => 'TYPE_ONE', 
                                                                    'op_one_preassignment' => $item->op_one_preassignment 
                                                                );
                                                            @endphp
                                                            <?=auth()->user()->renderServicePreassignment($service)?>
                                                        </td>
                                                        <td>{{ auth()->user()->typeService($item->final_service_type_one) }}</td>
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
                                                                $tooltip = ( $item->op_one_operation_close == 1 ? 'data-bs-toggle="tooltip" data-bs-placement="top" title="Este es el estatus final asignado por operaciones"' : '' );
                                                            @endphp
                                                            {{-- PERMITIRA LA MODIFICACION DEL SERVICIO, DE ACUERDO A LAS SIGUIENTES REGLAS --}}
                                                            {{-- SOLO CUANDO SE TENGA EL PERMISO --}}
                                                            {{-- NO ESTE CERRADA LA OPERACION --}}
                                                            {{-- CUANDO EL ESTATUS DE LA RESERVA SEA PENDIENTE, CONFIMADO O CREDTIO --}}
                                                            @if ( auth()->user()->hasPermission(68) && $item->op_one_operation_close == 0 && ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" ) )
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button" class="btn {{ $btn_op_one_type }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">{{ auth()->user()->statusBooking($item->op_one_status) }}</button>
                                                                    <div class="dropdown-menu" style="">
                                                                        <a class="dropdown-item" href="#" onclick="setStatus(event, 'arrival', 'PENDING', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Pendiente</a>
                                                                        <a class="dropdown-item" href="#" onclick="setStatus(event,  'arrival', 'COMPLETED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Completado</a>
                                                                        <a class="dropdown-item" href="#" onclick="setStatus(event, 'arrival', 'NOSHOW', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">No show</a>
                                                                        <hr>
                                                                        <a class="dropdown-item" href="#" onclick="setStatus(event, 'arrival', 'CANCELLED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Cancelado</a>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <button <?=$tooltip?> type="button" class="btn {{ $btn_op_one_type }} btn-sm bs-tooltip">{{ auth()->user()->statusBooking($item->op_one_status) }}</button>                                
                                                            @endif
                                                        </td>
                                                        <td>{{ isset($item->op_one_comments) ? $item->op_one_comments : 'NO DEFINIDO' }}</td>
                                                        <td>
                                                            {{-- NOS PERMITE ACTUALIZAR Y ENVIAR LA CONFIRMACION DEL SERVICIO AL CLIENTE POR CORREO --}}
                                                            {{-- VER SI EL SERVICIO ESTA EN UNA OPERACION ABIERTA O CERRADA --}}
                                                            {{-- SOLO CUANDO LA RESERVA ESTA PENDIENTE, CONFIRMADA O A CREDITO --}}
                                                            @if ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" )
                                                                <div class="d-flex gap-2">                                                                
                                                                    @php
                                                                        $message_operation_one = ( $item->op_one_operation_close == 1 ? "El servicio se encuentra en una operación cerrada".( auth()->user()->hasPermission(92) ? ", da click si desea desbloquear el servicio del cierre de operación" : "" ) : "El servicio se encuentra en una operacón abierta" );
                                                                    @endphp
                                                                    @if ( auth()->user()->hasPermission(69))
                                                                        <button class="btn {{ $item->op_one_confirmation == 1 ? 'btn-success' : 'btn-warning' }} confirmService" type="button" data-item="{{ $item->reservations_item_id }}" data-service="{{ $item->final_service_type_one }}" data-status="{{ $item->op_one_confirmation == 1 ? 0 : 1 }}" data-type="TYPE_ONE">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle align-middle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                                        </button>
                                                                    @endif
                                                                    <button data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $message_operation_one }}" class="btn btn-{{ $item->op_one_operation_close == 1 ? "danger" : "success" }} {{  auth()->user()->hasPermission(92) && $item->op_one_operation_close == 1 ? "updateServiceUnlock" : "" }} bs-tooltip" type="button" data-item="{{ $item->reservations_item_id }}" data-service="{{ $item->final_service_type_one }}" data-type="TYPE_ONE">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-{{ $item->op_one_operation_close == 1 ? "unlock" : "lock" }} align-middle"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    @if($item->is_round_trip == 1)
                                                        <tr>
                                                            <td>
                                                                @php
                                                                    $service = (object) array( 
                                                                        'final_service_type' => $item->final_service_type_two, 
                                                                        'op_type' => 'TYPE_TWO', 
                                                                        'op_two_preassignment' => $item->op_two_preassignment 
                                                                    );
                                                                @endphp
                                                                <?=auth()->user()->renderServicePreassignment($service)?>
                                                            </td>
                                                            <td>{{ auth()->user()->typeService($item->final_service_type_two) }}</td>
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
                                                                    $tooltip = ( $item->op_two_operation_close == 1 ? 'data-bs-toggle="tooltip" data-bs-placement="top" title="Este es el estatus final asignado por operaciones"' : '' );
                                                                @endphp
                                                                {{-- PERMITIRA LA MODIFICACION DEL SERVICIO, DE ACUERDO A LAS SIGUIENTES REGLAS --}}
                                                                {{-- SOLO CUANDO SE TENGA EL PERMISO --}}
                                                                {{-- NO ESTE CERRADA LA OPERACION --}}
                                                                {{-- CUANDO EL ESTATUS DE LA RESERVA SEA PENDIENTE, CONFIMADO O CREDTIO --}}
                                                                @if ( auth()->user()->hasPermission(68) && $item->op_two_operation_close == 0 && ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" ) )
                                                                    <div class="btn-group btn-group-sm">
                                                                        <button type="button" class="btn {{ $btn_op_two_type }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white;">{{ auth()->user()->statusBooking($item->op_two_status) }}</button>
                                                                        <div class="dropdown-menu" style="">
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event, 'departure', 'PENDING', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Pendiente</a>
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event,  'departure', 'COMPLETED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Completado</a>
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event, 'departure', 'NOSHOW', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">No show</a>
                                                                            <hr>
                                                                            <a class="dropdown-item" href="#" onclick="setStatus(event, 'departure', 'CANCELLED', {{ $item->reservations_item_id }}, {{ $item->reservation_id }})">Cancelado</a>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <button <?=$tooltip?> type="button" class="btn {{ $btn_op_two_type }} btn-sm bs-tooltip">{{ auth()->user()->statusBooking($item->op_two_status) }}</button> 
                                                                @endif
                                                            </td>
                                                            <td>{{ isset($item->op_two_comments) ? $item->op_two_comments : 'NO DEFINIDO' }}</td>
                                                            <td>
                                                                {{-- NOS PERMITE ACTUALIZAR Y ENVIAR LA CONFIRMACION DEL SERVICIO AL CLIENTE POR CORREO --}}
                                                                {{-- VER SI EL SERVICIO ESTA EN UNA OPERACION ABIERTA O CERRADA --}}
                                                                {{-- SOLO CUANDO LA RESERVA ESTA PENDIENTE, CONFIRMADA O A CREDITO --}}
                                                                @if ( $data['status'] == "PENDING" || $data['status'] == "PAY_AT_ARRIVAL" || $data['status'] == "CONFIRMED" || $data['status'] == "CREDIT" )
                                                                    @php
                                                                        $message_operation_two = ( $item->op_two_operation_close == 1 ? "El servicio se encuentra en una operación cerrada".( auth()->user()->hasPermission(92) ? ", da click si desea desbloquear el servicio del cierre de operación" : "" ) : "El servicio se encuentra en una operacón abierta" );
                                                                    @endphp
                                                                    <div class="d-flex gap-2">
                                                                        @if (auth()->user()->hasPermission(69))
                                                                            <button class="btn {{ $item->op_two_confirmation == 1 ? 'btn-success' : 'btn-warning' }} confirmService" type="button" data-item="{{ $item->reservations_item_id }}" data-service="{{ $item->final_service_type_two }}" data-status="{{ $item->op_two_confirmation == 1 ? 0 : 1 }}" data-type="TYPE_TWO">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle align-middle"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                                                            </button>
                                                                        @endif
                                                                        <button data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $message_operation_two }}" class="btn btn-{{ $item->op_two_operation_close == 1 ? "danger" : "success" }} {{  auth()->user()->hasPermission(92) && $item->op_two_operation_close == 1 ? "updateServiceUnlock" : "" }} bs-tooltip" type="button" data-item="{{ $item->reservations_item_id }}" data-service="{{ $item->final_service_type_two }}" data-type="TYPE_TWO">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-{{ $item->op_two_operation_close == 1 ? "unlock" : "lock" }} align-middle"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
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
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            @if (auth()->user()->hasPermission(17))
                                <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#serviceSalesModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus align-middle"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    NUEVA VENTA
                                </button>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th class="text-left">Descripción</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-center">Total</th>
                                        {{-- <th class="text-center">Vendedor</th> --}}
                                        <th class="text-center">Fecha de venta</th>
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
                                            {{-- <td class="text-center">{{ $sale->callCenterAgent->name ?? 'System' }}</td> --}}
                                            <td class="text-center">{{ $sale->created_at }}</td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermission(15))
                                                    <a href="#" class="action-btn btn-delete" data-bs-toggle="modal" data-bs-target="#serviceSalesModal" onclick="getSale({{ $sale->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                                    </a>
                                                @endif
                                                @if (auth()->user()->hasPermission(16))
                                                    <a href="#" class="action-btn btn-delete" onclick="deleteSale({{ $sale->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="icon-tab-3" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            {{-- @if ( $data['status'] != "CREDIT" ) --}}
                                @if (auth()->user()->hasPermission(14) )
                                    <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#servicePaymentsModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus align-middle"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                        NUEVO PAGO
                                    </button>
                                @endif
                            {{-- @endif --}}
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Método</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Moneda</th>
                                        <th class="text-center">TC</th>
                                        <th class="text-start">Ref.</th>
                                        <th class="text-start">Categoria.</th>
                                        <th class="text-center">Fecha de pago</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->payments as $payment)
                                        <tr style="{{ $payment->category == "REFUND" ? 'background-color: #fbeced;' : '' }}">
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->description }}</td>
                                            <td class="text-end">{{ number_format($payment->total) }}</td>
                                            <td class="text-center">{{ $payment->currency }}</td>
                                            <td class="text-end">{{ number_format($payment->exchange_rate) }}</td>
                                            <td class="text-start">{{ $payment->reference }}</td>
                                            <td class="text-start">{{ $payment->category }}</td>
                                            <td class="text-center">{{ $payment->created_at }}</td>
                                            <td class="text-center">
                                                @if (auth()->user()->hasPermission(15))
                                                    <a href="#" data-bs-toggle="modal" data-bs-target="#servicePaymentsModal" onclick="getPayment({{ $payment->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 align-middle"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
                                                    </a>
                                                @endif
                                                @if (auth()->user()->hasPermission(16))
                                                    <a href="#" onclick="deletePayment({{ $payment->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash align-middle"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="icon-tab-4" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>SOLITADO</th>
                                        <th>Estatus</th>
                                        <th>Descripción</th>
                                        <th>Respuesta</th>
                                        <th class="text-center">Fecha de solicitud</th>
                                        <th class="text-center">Fecha de aplicación</th>
                                        <th class="text-center">comprobante de reembolso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservation->refunds as $refund)
                                        <tr>
                                            <td>{{ isset($refund->user->name) ? $refund->user->name : 'NO DEFINIDO' }}</td>
                                            <td>
                                                <button class="btn btn-{{ auth()->user()->classStatusRefund($refund->status) }} btn-sm">{{ auth()->user()->statusRefund($refund->status) }}</button>
                                            </td>
                                            <td>{{ $refund->message_refund }}</td>
                                            <td>{{ $refund->response_message != NULL ? $refund->response_message : 'NO DEFINIDA' }}</td>
                                            <td class="text-center">{{ date("Y-m-d", strtotime($refund->created_at)) }}</td>
                                            <td class="text-center">
                                                @if ( $refund->status == "REFUND_NOT_APPLICABLE" )
                                                    {{ 'NO APLICA' }}
                                                @else
                                                    @if ( $refund->end_at != null )
                                                        {{ date("Y-m-d", strtotime($refund->end_at)) }}
                                                    @else
                                                        {{ 'NO DEFINIDO' }}
                                                    @endif                                                    
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $refund->status == "REFUND_NOT_APPLICABLE" )
                                                    {{ 'NO APLICA' }}
                                                @else
                                                    @if ( $refund->link_refund != null )
                                                        <a href="{{ $refund->link_refund }}" target="_black">click para ver</a>
                                                    @else
                                                        {{ 'NO DEFINIDO' }}
                                                    @endif                                                    
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if (auth()->user()->hasPermission(65))
                        <div class="tab-pane" id="icon-tab-5" role="tabpanel">
                            @if (auth()->user()->hasPermission(64))
                                <form id="upload-form" class="dropzone" action="/reservations/upload">
                                    @csrf
                                    <input type="hidden" name="folder" value="{{ $reservation->id }}">
                                </form>
                            @endif
                            @if (auth()->user()->hasPermission(65))
                                <div class="image-listing" id="media-listing"></div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-modals.service_map />
    {{-- MODAL PARA EDITAR LOS DATOS PRINCIPALES, DE LA RESERVA --}}
    <x-modals.edit_reservation_details :reservation=$reservation />

    {{-- MODAL PARA EDITAR UN SERVICIO, DE LA RESERVA --}}
    <x-modals.edit_reservation_service :reservation=$reservation />

    {{-- MODAL PARA AGREGAR UNA NUEVA VENTA, A LA RESERVA --}}
    <x-modals.new_sale_reservation>
        <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
    </x-modals.new_sale_reservation>

    {{-- MODAL PARA AGREGAR UN PAGO A LA RESERVA --}}
    <x-modals.new_payment_reservation>
        <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
        <x-slot name="currency">{{ $reservation->currency }}</x-slot>
        <x-slot name="type_site">{{ isset($request['bookingtracking']) ? $request['bookingtracking'] : $reservation->site->type_site }}</x-slot>
        <x-slot name="platform">{{ isset($request['trackingType']) ? $request['trackingType'] : ( $data['status'] == "PENDING" ? 'Bookign' : 'GENERAL' ) }}</x-slot>
    </x-modals.new_payment_reservation>

    <x-modals.new_follow_reservation>
        <x-slot name="reservation_id">{{ $reservation->id }}</x-slot>
    </x-modals.new_follow_reservation>
    <x-modals.reservations.confirmation :reservation=$reservation />
@endsection