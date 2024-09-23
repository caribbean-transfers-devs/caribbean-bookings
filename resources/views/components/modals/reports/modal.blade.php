@props(['data','services','zones','websites','origins','istoday'])
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
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $data['init'] }} - {{ $data['end'] }}">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="filter_text">/#/Nombre/Correo/Teléfono/Referencia</label>
                            <input type="text" name="filter_text" id="filter_text" class="form-control mb-3" value="{{ trim($data['filter_text']) }}">
                        </div>

                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="is_round_trip">Tipo de servicio</label>
                            <select class="form-control selectpicker mb-3" title="Selecciona una opción" data-live-search="true" name="is_round_trip[]" id="is_round_trip" data-value="{{ json_encode($data['is_round_trip']) }}" multiple>                                                            
                                <option value="0">One Way</option>
                                <option value="1">Round Trip</option>
                            </select>
                        </div>                        

                        @if ( !empty($websites) )
                            <div class="col-12 col-sm-4">
                                <label class="form-label" for="site">Sitio</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un sitio" data-live-search="true" data-selected-text-format="count > 1" name="site[]" id="site" data-value="{{ json_encode($data['site']) }}" multiple>                            
                                    @foreach ($websites as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->site_name }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if ( !empty($origins) )
                            <div class="col-12 col-sm-4">
                                <label class="form-label" for="origin">Origen de venta</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona un origen" data-live-search="true" data-selected-text-format="count > 2" name="origin[]" id="origin" data-value="{{ json_encode($data['origin']) }}" multiple>                            
                                    @foreach ($origins as $key => $origin)
                                        <option value="{{ $origin->id }}">{{ $origin->code }}</option> 
                                    @endforeach
                                </select>
                            </div>
                        @endif                        

                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="product_type">Tipo de vehículo</label>
                            <select class="form-control selectpicker mb-3" title="Selecciona un vehículo" data-live-search="true" data-selected-text-format="count > 3" name="product_type[]" id="product_type" data-value="{{ json_encode($data['product_type']) }}" multiple>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->service_name }}</option>                                                          
                                @endforeach
                            </select>                            
                        </div>

                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="zone">Zona de origen</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona una zona" data-live-search="true" data-selected-text-format="count > 3" name="zone_one_id[]" id="zone_one_id" data-value="{{ json_encode($data['zone_one_id']) }}" multiple>
                                    @foreach ($zones as $key => $value)
                                    <optgroup label="{{ $key }}">
                                        @foreach ($value as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->zone_name }}</option>                                                          
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-4">
                            <label class="form-label" for="zone">Zona de destino</label>
                                <select class="form-control selectpicker mb-3" title="Selecciona una zona" data-live-search="true" data-selected-text-format="count > 3" name="zone_two_id[]" id="zone_two_id" data-value="{{ json_encode($data['zone_two_id']) }}" multiple>
                                    @foreach ($zones as $key => $value)
                                    <optgroup label="{{ $key }}">
                                        @foreach ($value as $zone)
                                            <option value="{{ $zone->id }}">{{ $zone->zone_name }}</option>                                                          
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>                        

                        @if ( isset($istoday) )
                            <div class="col-12 col-sm-4">
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