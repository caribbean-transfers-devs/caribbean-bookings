<div class="modal fade" id="servicePaymentsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_new_payment">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsTypeModal">Tipo</label>
                            <select class="form-select mb-2" id="servicePaymentsTypeModal" name="payment_method">
                                <option value="CASH" selected>Efectivo</option>
                                <option value="PAYPAL">PayPal</option>
                                <option value="CARD">Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsDescriptionModal">Descripción / referencia</label>
                            <input type="text" class="form-control mb-2" id="servicePaymentsDescriptionModal" name="reference">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsTotalModal">Total</label>
                            <input type="number" class="form-control mb-2" id="servicePaymentsTotalModal" name="total">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsCurrencyModal">Moneda</label>
                            <select class="form-select mb-2" id="servicePaymentsCurrencyModal" name="currency">
                                <option value="USD" selected>USD</option>
                                <option value="MXN">MXN</option>                            
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsExchangeModal">Tipo de cambio</label>
                            <input type="number" class="form-control mb-2" id="servicePaymentsExchangeModal" name="exchange_rate" value="1.00">
                        </div>
                    </div>
                    <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
                </form>
                <input type="hidden" id="type_form_pay" value="1">
                <input type="hidden" id="payment_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_new_payment">Guardar</button>
            </div>
        </div>
    </div>
</div>