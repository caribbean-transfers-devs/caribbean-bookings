<div class="modal fade" id="paymentLinkAmountModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Monto personalizado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_payment_link_amount" novalidate>
                    <input type="hidden" id="pl_code">
                    <input type="hidden" id="pl_email">
                    <input type="hidden" id="pl_language">
                    <input type="hidden" id="pl_type">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 mb-3">
                            <label class="form-label" for="pl_currency">Moneda</label>
                            <select class="form-select" id="pl_currency" name="currency" required>
                                <option value="MXN">MXN</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6 mb-3">
                            <label class="form-label" for="pl_amount">Monto</label>
                            <input type="number" class="form-control" id="pl_amount" name="amount" min="0.01" step="0.01" placeholder="0.00" required>
                            <div class="invalid-feedback" id="pl_amount_feedback">Ingresa un monto válido.</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_confirm_payment_link">Generar link</button>
            </div>
        </div>
    </div>
</div>
