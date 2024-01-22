@props(['data','services','zones','websites'])
<div class="modal" tabindex="-1" id="filterModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtro de reservaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row" action="" method="POST" id="formSearch">                    
                    @csrf
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
                        <select class="form-select mb-3" name="site[]" id="site" multiple>                            
                            @foreach ($websites as $key => $value)
                                <option value="{{ $value->id }}" {{ (($data['site'] == $value->id)?'selected':'') }}>{{ $value->site_name }}</option> 
                            @endforeach
                        </select>                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="Search()" id="btnSearch">Buscar</button>
            </div>
        </div>
    </div>
</div>
