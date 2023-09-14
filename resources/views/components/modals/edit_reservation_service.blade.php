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
                            <select class="form-control mb-2" id="destination_serv" name="destination_service_id">
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach                            
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaxForm">Pasajeros</label>
                            <input type="text" class="form-control mb-2" id="servicePaxForm" name="passengers">
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="serviceFlightForm">Numéro de Vuelo</label>
                            <input type="text" class="form-control mb-2" id="serviceFlightForm" name="flight_number">
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="serviceFromForm">Desde</label>
                            <input type="text" class="form-control mb-2" id="serviceFromForm" name="from_name">
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="serviceToForm">Hacia</label>
                            <input type="text" class="form-control mb-2" id="serviceToForm" name="to_name">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceDateForm">Hora de recogida</label>
                            <input type="datetime-local" class="form-control mb-2" id="serviceDateForm" name="op_one_pickup">
                        </div>
                        <div class="col-sm-12 col-md-6 d-none" id="info_return">
                            <label class="form-label" for="serviceDateRoundForm">Hora de regreso</label>
                            <input type="datetime-local" class="form-control mb-2" id="serviceDateRoundForm" name="op_two_pickup">
                        </div>
                        <input type="hidden" name="item_id_edit" id="item_id_edit">
                        <input type="hidden" name="from_lat" id="from_lat_edit">
                        <input type="hidden" name="from_lng" id="from_lng_edit">
                        <input type="hidden" name="to_lat" id="to_lat_edit">
                        <input type="hidden" name="to_lng" id="to_lng_edit">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_edit_item">Guardar</button>
            </div>
        </div>
    </div>
</div>