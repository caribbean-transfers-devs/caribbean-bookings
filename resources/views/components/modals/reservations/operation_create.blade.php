<!-- Modal -->
<div class="modal fade" id="operationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="operationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form class="pos-form" id="posForm" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="from_lat">
                <input type="hidden" name="from_lng">
                <input type="hidden" name="to_lat">
                <input type="hidden" name="to_lng">
                <div class="modal-header">
                    <h5 class="modal-title" id="operationModalLabel">Agregar nuevo servicio</h5>
                    <button type="button" class="btn-close __close" data-bs-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body px-3">
                    <div class="wrapper">
                        <div class="box-option">
                            <h2 class="fs-6">Información general</h2>
                            <div class="row">
                                {{-- <div class="col-12 col-lg-3">
                                    <label class="form-label" for="type_service">Tipo de servicio</label>
                                    <select name="type_service" class="form-control" id="type_service">
                                        <option value="PRIVATE">Privado</option>
                                        <option value="SHARED">Compartido</option>
                                    </select>
                                </div> --}}
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="formReference">Referencia</label>
                                    <input class="form-control" type="text" name="reference" id="formReference">
                                </div>                                
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="formSite">Agencia</label>
                                    <select class="form-control mb-2" id="formSite" name="site_id">
                                        @foreach($websites as $website)
                                            <option value="{{ $website->id }}" data-type="{{ $website->type_site }}" data-phone="{{ $website->transactional_phone }}" data-email="{{ $website->transactional_email }}">{{ $website->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="formOriginSale">Origen de venta</label>
                                    <select class="form-control selectpicker" data-live-search="true" id="formOriginSale" name="origin_sale_id">
                                        <option value="">Selecciona un origen de venta</option>
                                        @if (isset( $origins ) && sizeof($origins) >= 1)
                                            @foreach ($origins as $origin)
                                                <option value="{{ $origin->id }}">{{ $origin->code }}</option>
                                            @endforeach
                                        @endif
                                    </select>                    
                                </div>                                
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="client_last_name">Idioma</label>
                                    <select class="form-control mb-2" id="language" name="language">
                                        <option value="es">Español</option>
                                        <option value="en">Ingles</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-12 checkbox_box d-flex align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="is_open" id="is_open" value="0">
                                        <label class="form-check-label" for="is_open">Es un servicio abierto</label>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 d-none checkbox_time">
                                    <label class="form-label" for="open_service_time">Ingresa el tiempo</label>
                                    <input type="number" step=".01" class="form-control" id="open_service_time" >
                                </div>                                
                            </div>
                        </div>

                        <div class="box-option">
                            <h2 class="fs-6">Datos del cliente</h2>
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="client_first_name">Nombre</label>
                                    <input class="form-control" type="text" name="client_first_name" id="formName">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="client_last_name">Apellidos</label>
                                    <input class="form-control" type="text" name="client_last_name" id="formLastName">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="client_phone">Teléfono (opcional)</label>
                                    <input class="form-control" type="text" name="client_phone" id="formPhone">
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="client_email">E-mail (opcional)</label>
                                    <input class="form-control" type="text" name="client_email" id="formEmail">
                                </div>
                            </div>
                        </div>

                        <div class="box-option">
                            <h2 class="fs-6">Destinos</h2>
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="from_zone_id">Zona origen</label>
                                    <select class="form-control mb-2" id="from_zone_id" name="from_zone_id">
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ $zone->id == 1 ? 'selected' : '' }}>{{ $zone->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="position-relative w-100">
                                        <label class="form-label" for="from_name">Lugar de origen</label>
                                        <input class="form-control" type="text" name="from_name" id="from_name" value="Cancun Airport">
                                        <div class="autocomplete-results" id="from_name_elements"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="to_zone_id">Zona destino</label>
                                    <select class="form-control mb-2" id="to_zone_id" name="to_zone_id">
                                        @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                        @endforeach                        
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="position-relative w-100">
                                        <label class="form-label" for="to_name">Lugar de destino (Hotel, Airb&b, Zona)</label>
                                        <input class="form-control" type="text" name="to_name" id="to_name">
                                        <div class="autocomplete-results" id="to_name_elements"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-option">
                            <h2 class="fs-6">Datos del viaje</h2>
                            <div class="row">
                                <div class="col-12 col-lg-4">
                                    <label class="form-label" for="passengers">Pasajeros</label>
                                    <select class="form-control mb-2" id="passengers" name="passengers">
                                        @for ($i = 1; $i < 35; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>                             
                                        @endfor                            
                                    </select>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label class="form-label" for="departure_date">Fecha de servicio</label>
                                    <input class="form-control" id="departure_date" name="departure_date" data-default-mode="single" value="{{ date("Y-m-d H:i") }}">
                                </div>
                                <div class="col-12 col-lg-4">
                                    <label class="form-label" for="destination_service_id">Vehículo</label>
                                    <select class="form-control mb-2" id="destination_service_id" name="destination_service_id">
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ $vehicle->id == 4 ? 'selected' : '' }}>{{ $vehicle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-lg-12">
                                    <label class="form-label" for="comments">Observaciones</label>
                                    <textarea class="form-control" name="comments" row="4"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="box-option">
                            <h2 class="fs-6">Datos de la venta ($)</h2>
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="sold_in_currency">Se cotizó la venta en:</label>
                                    <select class="form-control mb-2" id="sold_in_currency" name="sold_in_currency">
                                        <option value="USD">USD</option>
                                        <option value="MXN">MXN</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label class="form-label" for="total">Precio cotizado <span id="currency_span">(USD)</span></label>
                                    <input type="number" step=".01" class="form-control" name="total" id="total">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-light-dark __close" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">guardar</button>                    
                </div>
            </form>
        </div>
    </div>
</div>
