<div class="modal fade" id="serviceClientModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos del cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_edit_details">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientFirstNameModal">Nombres</label>
                            <input type="text" class="form-control mb-2" id="serviceClientFirstNameModal" name="client_first_name" value="{{ $reservation->client_first_name }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientLastNameModal">Apellidos</label>
                            <input type="text" class="form-control mb-2" id="serviceClientLastNameModal" name="client_last_name" value="{{ $reservation->client_last_name }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientEmailModal">E-mail</label>
                            <input type="email" class="form-control mb-2" id="serviceClientEmailModal" name="client_email" value="{{ $reservation->client_email }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientPhoneModal">Teléfono</label>
                            <input type="text" class="form-control mb-2" id="serviceClientPhoneModal" name="client_phone" value="{{ $reservation->client_phone }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsCurrencyModal">Moneda</label>
                            <select class="form-select mb-2" id="servicePaymentsCurrencyModal" name="currency" readonly>
                                <option value="1" {{ $reservation->currency == 1 ? 'selected' : '' }}>USD</option>
                                <option value="2" {{ $reservation->currency == 2 ? 'selected' : '' }}>MXN</option>                            
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="reservation_id" value="{{ $reservation->id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_edit_res_details">Guardar</button>
            </div>
        </div>
    </div>
</div>