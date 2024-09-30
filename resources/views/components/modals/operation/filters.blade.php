@props(['data','nexDate','date','websites','units','drivers'])
<!-- Modal -->
<div class="modal fade" id="filtersOperationModal" tabindex="-1" role="dialog" aria-labelledby="filtersOperationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filtros de operaciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form class="form" action="" method="POST" id="formSearch">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="lookup_date_next" value="{{ $nexDate }}" required>
                    <div class="row">
                        <div class="col-sm-3 col-12">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date }}">
                        </div>
                        <div class="col-sm-3 col-12">
                            <label class="form-label" for="site">Agencia</label>
                            <select class="form-control selectpicker mb-3" title="Selecciona una agencia" data-live-search="true" data-selected-text-format="count > 1" name="site[]" id="site" data-value="{{ json_encode(( isset($data['site']) ? $data['site'] : array() )) }}" multiple>
                                @foreach ($websites as $value)
                                    <option value="{{ $value->id }}">{{ $value->site_name }}</option>                                                          
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-12">
                            <label class="form-label" for="unit">Unidad</label>
                            <select class="form-control selectpicker mb-3" title="Selecciona una unidad" data-live-search="true" data-selected-text-format="count > 3" name="unit[]" id="unit" data-value="{{ json_encode(( isset($data['unit']) ? $data['unit'] : array() )) }}" multiple>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>                                                          
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-12">
                            <label class="form-label" for="driver">Conductor</label>
                            <select class="form-control selectpicker mb-3" title="Selecciona un conductor" data-live-search="true" data-selected-text-format="count > 3" name="driver[]" id="driver" data-value="{{ json_encode(( isset($data['driver']) ? $data['driver'] : array() )) }}" multiple>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>                                                          
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