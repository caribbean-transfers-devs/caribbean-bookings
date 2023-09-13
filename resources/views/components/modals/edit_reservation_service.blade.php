<div class="modal fade" id="serviceEditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit_reservation_service">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceTypeForm">Tipo</label>
                            <select class="form-control mb-2">
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach                            
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" >Guardar</button>
            </div>
        </div>
    </div>
</div>