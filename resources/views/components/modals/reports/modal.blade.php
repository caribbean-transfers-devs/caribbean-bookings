@props(['data','services','zones','websites'])
<!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $data['init'] }} - {{ $data['end'] }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="filter_text">Nombre/#/Teléfono/Referencia</label>
                            <input type="text" name="filter_text" id="filter_text" class="form-control mb-3" value="{{ trim($data['filter_text']) }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="product_type">Tipo de producto</label>
                            <select class="form-control mb-3" name="product_type" id="product_type">
                                <option value='0'>Seleccionar</option>
                                @foreach ($services as $key => $value)
                                    <optgroup label="{{ $key }}">
                                        @foreach ($value as $service)                                    
                                            <option value="{{ $service->id }}" {{ (($data['product_type'] == $service->id)?'selected':'') }}>{{ $service->service_name }}</option>                                                          
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="zone">Zona</label>
                                <select class="form-control mb-3" name="zone" id="zone">
                                    <option value='0'>Seleccionar</option>
                                    @foreach ($zones as $key => $value)
                                    <optgroup label="{{ $key }}">
                                        @foreach ($value as $zone)                                    
                                            <option value="{{ $zone->id }}" {{ (($data['zone'] == $zone->id)?'selected':'') }}>{{ $zone->zone_name }}</option>                                                          
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-12">
                            <label class="form-label" for="site">Sitio</label>
                            <select class="form-select mb-3" placeholder="Selecciona un sitio" name="site[]" id="site" multiple>                            
                                @foreach ($websites as $key => $value)
                                    <option value="{{ $value->id }}" {{ (($data['site'] == $value->id)?'selected':'') }}>{{ $value->site_name }}</option> 
                                @endforeach
                            </select>
                        </div>
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