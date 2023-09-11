<div class="modal fade" id="serviceSalesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_new_sale">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceSalesTypeForm">Tipo</label>
                            <select class="form-select mb-2" id="serviceSalesTypeForm" name="sale_type_id">
                                <option value="1" selected>Transportación</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceSalesDescriptionForm">Descripción</label>
                            <input type="text" class="form-control mb-2" id="serviceSalesDescriptionForm" name="description">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceSalesQuantityForm">Cantidad</label>
                            <input type="number" class="form-control mb-2" id="serviceSalesQuantityForm" name="quantity">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceSalesTotalForm">Total</label>
                            <input type="number" class="form-control mb-2" id="serviceSalesTotalForm" name="total">
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="serviceSalesAgentForm">Agente</label>
                            <select class="form-select mb-2" id="serviceSalesAgentForm" name="call_center_agent_id"> 
                                <option value="1" selected>Juan Perez</option>
                                <option value="1">Esteban Vega</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="reservation_id" value="{{ $reservation_id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_new_sale">Guardar</button>
            </div>
        </div>
    </div>
</div>