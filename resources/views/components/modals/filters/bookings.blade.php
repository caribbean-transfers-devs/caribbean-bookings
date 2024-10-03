@props(['data','services','vehicles','reservationstatus','currencies','methods','cancellations','zones','websites','origins','istoday','isbalance'])
<!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form class="form" action="" method="POST" id="formSearch">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control mb-3" value="{{ $data['init'] }} - {{ $data['end'] }}">
                        </div>
                        <div class="col-lg-4 col-12">
                            <label class="form-label" for="filter_text">/#/Nombre/Correo/Teléfono/Referencia</label>
                            <input type="text" name="filter_text" id="filter_text" class="form-control mb-3" value="{{ trim($data['filter_text']) }}">
                        </div>

                        @if ( isset($services) && !empty($services) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="is_round_trip">Tipo de servicio</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un opción" data-live-search="true" data-selected-text-format="count > 2" name="is_round_trip[]" id="is_round_trip" data-value="{{ json_encode($data['is_round_trip']) }}" multiple>
                                    @foreach ($services as $key => $service)
                                        <option value="{{ $key }}">{{ $service }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( isset($websites) && !empty($websites) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="site">Sitio web</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un sitio" data-live-search="true" data-selected-text-format="count > 1" name="site[]" id="site" data-value="{{ json_encode($data['site']) }}" multiple>                            
                                    @foreach ($websites as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->site_name }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( isset($origins) && !empty($origins) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="origin">Origen de venta</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un origen" data-live-search="true" data-selected-text-format="count > 2" name="origin[]" id="origin" data-value="{{ json_encode($data['origin']) }}" multiple>                            
                                    @foreach ($origins as $key => $origin)
                                        <option value="{{ $origin->id }}">{{ $origin->code }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( isset($reservationstatus) && !empty($reservationstatus) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="reservation_status">Estatus de reservación</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un estatus" data-live-search="true" data-selected-text-format="count > 2" name="reservation_status[]" id="reservation_status" data-value="{{ json_encode($data['reservation_status']) }}" multiple>
                                    @foreach ($reservationstatus as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- EL VEHÍCULO O UNIDAD QUE SELECCIONO EL CLIENTE AL GENERAR SU RESERVA --}}
                        @if ( isset($vehicles) && !empty($vehicles) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="product_type">Tipo de vehículo</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un vehículo" data-live-search="true" data-selected-text-format="count > 3" name="product_type[]" id="product_type" data-value="{{ json_encode($data['product_type']) }}" multiple>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>                                                          
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( isset($zones) && !empty($zones) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="zone">Zona de origen</label>
                                    <select class="form-control selectpicker mb-3" title="Selecciona una zona" data-live-search="true" data-selected-text-format="count > 3" name="zone_one_id[]" id="zone_one_id" data-value="{{ json_encode(( isset($data['zone_one_id']) ? $data['zone_one_id'] : array() )) }}" multiple>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>                                                          
                                        @endforeach
                                    </select>
                            </div>

                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="zone">Zona de destino</label>
                                    <select class="form-control selectpicker mb-3" title="Selecciona una zona" data-live-search="true" data-selected-text-format="count > 3" name="zone_two_id[]" id="zone_two_id" data-value="{{ json_encode(( isset($data['zone_two_id']) ? $data['zone_two_id'] : array() )) }}" multiple>
                                        @foreach ($zones as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->name }}</option>                                                          
                                        @endforeach
                                    </select>
                            </div>
                        @endif

                        @if ( !empty($currencies) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="currency">Moneda de reservación</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona una moneda" data-live-search="true" data-selected-text-format="count > 2" name="currency[]" id="currency" data-value="{{ json_encode($data['currency']) }}" multiple>
                                    @foreach ($currencies as $key => $currency)
                                        <option value="{{ $key }}">{{ $currency }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( !empty($methods) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="payment_method">Metodo de pago de reservación</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un metodo de pago" data-live-search="true" data-selected-text-format="count > 2" name="payment_method[]" id="payment_method" data-value="{{ json_encode($data['payment_method']) }}" multiple>
                                    @foreach ($methods as $key => $method)
                                        <option value="{{ $key }}">{{ $method }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( !empty($cancellations) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="cancellation_status">Motivos de cancelación</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un motivo de cancelación" data-live-search="true" data-selected-text-format="count > 2" name="cancellation_status[]" id="cancellation_status" data-value="{{ json_encode($data['cancellation_status']) }}" multiple>
                                    @foreach ($cancellations as $key => $cancellation)
                                        <option value="{{ $cancellation->id }}">{{ $cancellation->name_es }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( isset($isbalance) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="is_balance">Reserva con balance</label>
                                <select class="form-control mb-3" name="is_balance" id="is_today">
                                    <option {{ $data['is_balance'] == '0' ? 'selected' : '' }} value="0">Selecciona una opción</option>
                                    <option {{ $data['is_balance'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                    <option {{ $data['is_balance'] == '2' ? 'selected' : '' }} value="2">No</option>
                                </select>
                            </div>
                        @endif                        

                        @if ( isset($istoday) )
                            <div class="col-lg-4 col-12">
                                <label class="form-label" for="site">Operadas para hoy</label>
                                <select class="form-control mb-3" name="is_today" id="is_today">
                                    <option {{ $data['is_today'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                    <option {{ $data['is_today'] == '0' ? 'selected' : '' }} value="0">No</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
</div>