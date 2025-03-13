<div class="modal fade" id="addPaymentsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPaymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentsModalLabel">Agregar pago</h5>
                <button type="button" class="btn-close __close_modal" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <div class="modal-body">
                <form id="frm_new_payment">
                    <div class="loading_container text-center d-none" id="loading_container"></div>
                    <div class="form_container row d-none" id="form_container">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsTypeModal">Tipo</label>
                            <select class="form-select mb-2" id="servicePaymentsTypeModal" name="payment_method">
                                <option value="CASH" selected>Efectivo</option>
                                <option value="PAYPAL">PayPal</option>
                                <option value="STRIPE">Stripe</option>
                                <option value="TRANSFER">Transferencia</option>
                                <option value="MIFEL">MIFEL</option>
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
                                <option value="USD">USD</option>
                                <option value="MXN">MXN</option>
                            </select>
                        </div>
                        {{-- readonly --}}
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsExchangeModal">Tipo de cambio</label>
                            <input type="number" class="form-control mb-2" id="servicePaymentsExchangeModal" name="exchange_rate" value="1.00">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsCategory">Tipo de pago</label>
                            <select class="form-select mb-2" id="servicePaymentsCategory" name="category">
                                <option value="PAYOUT">PAGO</option>
                                <option value="REFUND">REEMBOLSO</option>
                            </select>                            
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="servicePaymentsLinkRefund">Link del reembolso</label>
                            <input type="text" class="form-control mb-2" id="servicePaymentsLinkRefund" name="link_refund">
                        </div>                        
                        <div class="col-sm-12 col-md-6 d-none">
                            <label class="form-label" for="servicePaymentsConciliationModal">Conciliado</label>
                            <select class="form-select mb-2" id="servicePaymentsConciliationModal" name="is_conciliated">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12 box_comment d-none">
                            <label class="form-label" for="servicePaymentsMessageConciliationModal">Mensaje de conciliación</label>
                            <textarea class="form-control" id="servicePaymentsMessageConciliationModal" name="conciliation_comment" cols="5" rows="5"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="reservation_id" value="" id="reservation_id">
                    <input type="hidden" name="reservation_refund_id" value="" id=reservation_refund_id>
                    <input type="hidden" name="operation" value="multiplication" id="operation_pay">
                </form>
                <input type="hidden" id="type_form_pay" value="1">
                <input type="hidden" id="payment_id">                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark __close_modal" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_new_payment">Guardar</button>
            </div>
        </div>
    </div>
</div>