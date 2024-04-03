@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.master')
@section('title') Punto de venta @endsection


@push('up-stack')
    <link href="{{ mix('/assets/css/pos/capture.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/pos/capture.min.css') }}" rel="stylesheet" >
@endpush

@push('bootom-stack')
    <script src="{{ mix('/assets/js/views/pos/capture.min.js') }}"></script>
@endpush

<script>
    var currency_exchange_data = <?= $currency_exchange_data ?>;
</script>

@section('content')
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">Punto de venta</h1>
        </div>

        <div class="row">

            <div class="col-xs-12">
                <form class="pos-form" id="posForm" method="post">
                    @csrf

                    <div class="top-grid-container">
                        <div>
                            <label class="form-label" for="folio">Folio</label>
                            <input class="form-control" type="text" name="folio" id="folio">
                        </div>
                        <div class="pending">
                            <label class="form-label" for="terminal">Terminal</label>
                            <select class="form-control mb-2" id="terminal" name="terminal">
                                @for ($i = 1; $i < 5; $i++)
                                    <option value="T{{ $i }}">Terminal {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="sellers-container">
                            <div>
                                <label class="form-label" for="vendor_id">Vendedor</label>
                                <select class="form-control mb-2" id="vendor_id" name="vendor_id">
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(RoleTrait::hasPermission(57))
                                <button class="btn btn-success" id="createVendor"><i class="align-middle" data-feather="plus"></i></button>
                            @endif
                        </div>
                    </div>

                    <div class="cards-container">
                        <div class="capture-card">
                            <h2>Datos del cliente</h2>
                            
                            <div class="capture-inputs-container">
                                <div>
                                    <label class="form-label" for="client_first_name">Nombre</label>
                                    <input class="form-control" type="text" name="client_first_name" id="client_first_name">
                                </div>
                                <div>
                                    <label class="form-label" for="client_last_name">Apellidos</label>
                                    <input class="form-control" type="text" name="client_last_name" id="client_last_name">
                                </div>
                                <div>
                                    <label class="form-label" for="client_phone">Teléfono (opcional)</label>
                                    <input class="form-control" type="text" name="client_phone" id="client_phone">
                                </div>
                                <div>
                                    <label class="form-label" for="client_email">E-mail (opcional)</label>
                                    <input class="form-control" type="text" name="client_email" id="client_email">
                                </div>
                            </div>
                        </div>
    
                        <div class="capture-card">
                            <h2>Destinos</h2>
                            
                            <div class="capture-inputs-container">
                                <div>
                                    <label class="form-label" for="from_zone_id">Zona origen</label>
                                    <select class="form-control mb-2" id="from_zone_id" name="from_zone_id">
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" for="from_name">Desde (Hotel)</label>
                                    <input class="form-control" type="text" name="from_name" id="from_name">
                                </div>
                                <div>
                                    <label class="form-label" for="to_zone_id">Zona destino</label>
                                    <select class="form-control mb-2" id="to_zone_id" name="to_zone_id">
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach                        
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" for="to_name">Hasta (Hotel)</label>
                                    <input class="form-control" type="text" name="to_name" id="to_name">
                                </div>
                            </div>
                        </div>

                        <div class="capture-card sail-data">
                            <h2>Datos de la venta ($)</h2>

                            <div class="capture-inputs-container">
                                <div class="double-col">
                                    <label class="form-label" for="reference">Referencia de pago</label>
                                    <input class="form-control" type="text" name="reference" id="reference">
                                </div>
                                <div>
                                    <label class="form-label" for="sold_in_currency">Se cotizó la venta en:</label>
                                    <select class="form-control mb-2" id="sold_in_currency" name="sold_in_currency">
                                        <option value="USD">USD</option>
                                        <option value="MXN">MXN</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" for="total">Precio cotizado <span id="currency_span">(USD)</span></label>
                                    <input type="number" step=".01" class="form-control" name="total" id="total">
                                </div>
                                
                            </div>

                            <div class="payment-section" style="display: none">
                                <table class="table table-striped table-bordered" id="payments_table" style="display: none">
                                    <caption align="top">Pagos agregados</caption>
                                    <thead>
                                        <tr>
                                            <th>Pago</th>
                                            <th>Currency</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>

                                <div class="previous-total-container">
                                    <strong>Total pagado: </strong>
                                    <div class="color-total-container red">
                                        $<span id="previous_total">0</span> <span class="total-currency">USD</span>    
                                    </div>
                                </div>

                                <div class="total-remaining-container">
                                    <strong>Falta por pagar: </strong>
                                    <div class="color-total-container red">
                                        $<span id="total_remaining">0</span> <span class="total-currency">USD</span>    
                                    </div>
                                </div>

                                <button class="btn btn-success btn-sm" id="openPaymentModal" data-bs-toggle="modal" data-bs-target="#addPaymentModal"><i class="align-middle" data-feather="plus"></i> Agregar pago</button>

                            </div>

                        </div>

                        <div class="capture-card">
                            <h2>Datos del viaje</h2>

                            <div class="capture-inputs-container">
                                <div>
                                    <label class="form-label" for="is_round_trip">Tipo de servicio</label>
                                    <select class="form-control mb-2" id="is_round_trip" name="is_round_trip">
                                        <option value="0">Sencillo</option>
                                        <option value="1">Redondo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" for="passengers">Pasajeros</label>
                                    <select class="form-control mb-2" id="passengers" name="passengers">
                                        @for ($i = 1; $i < 35; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>                             
                                        @endfor                            
                                    </select>
                                </div>
                                <div class="double-col">
                                    <label class="form-label" for="destination_service_id">Vehículo</label>
                                    <select class="form-control mb-2" id="destination_service_id" name="destination_service_id">
                                        @foreach($destination_services as $destination_service)
                                            <option value="{{ $destination_service->id }}">{{ $destination_service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
    
                    </div>

                    <div class="button-container">
                        <button class="btn btn-primary" id="submitBtn">Generar venta</button>
                    </div>
                </form>
            </div>

            <x-modals.add_payment :clips="$clips" />
            <x-modals.create_vendor />

        </div>
    </div>
@endsection