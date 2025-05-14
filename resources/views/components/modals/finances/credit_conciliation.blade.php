massiveReconciliationReservesCredit
<div class="modal fade" id="ConciliationReservesCreditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ConciliationReservesCreditModallabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ConciliationReservesCreditModallabel">Conciliación de pago</h5>
                <button type="button" class="btn-close __close_modal" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <form id="processSelected" accept-charset="utf-8" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="actionType" name="action_type" value="" required>
                        <input type="hidden" id="reservationID" name="reservation_id" value="" required>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="optionConciliation">Seleccione opción</label>
                            <select name="option" id="optionConciliation" class="form-select">
                                <option value="">Seleccione opción</option>
                                <option value="2">Pre-conciliar</option>
                                <option value="1">Conciliar</option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-6 d-none" id="boxMethodPayment">
                            <label class="form-label" for="methodPayment">Metodo de pafo</label>
                            <select class="form-select mb-2" id="methodPayment" name="payment_method">
                                <option value="CASH" selected>Efectivo</option>
                                <option value="PAYPAL">PayPal</option>
                                <option value="STRIPE">Stripe</option>
                                <option value="TRANSFER">Transferencia</option>
                                <option value="MIFEL">MIFEL</option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-6" id="boxReferenceInvoiceAgency">
                            <label class="form-label" for="referenceInvoiceAgency">referencia / agencia</label>
                            <input type="text" class="form-control mb-2" id="referenceInvoiceAgency" name="reference_invoice">
                        </div>
                        {{-- <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsTotalModal">Total</label>
                            <input type="number" class="form-control mb-2" id="servicePaymentsTotalModal" name="total">
                        </div> --}}
                        <div class="col-sm-12 col-md-6" id="boxPaymentCurrency">
                            <label class="form-label" for="paymentCurrency">Moneda</label>
                            <select class="form-select mb-2" id="paymentCurrency" name="currency">
                                <option value="USD">USD</option>
                                <option value="MXN">MXN</option>
                            </select>
                        </div>
                        {{-- <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="servicePaymentsExchangeModal">Tipo de cambio</label>
                            <input type="number" class="form-control mb-2" id="servicePaymentsExchangeModal" name="exchange_rate" value="1.00">
                        </div> --}}

                        <div class="col-12 col-md-6" id="boxdateConciliation">
                            <label class="form-label" for="dateConciliation">Fecha de conciliación</label>
                            <input type="text" class="form-control" name="date_conciliation" id="dateConciliation" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                        <div class="col-12 col-md-6 d-none" id="boxDepositDate">
                            <label class="form-label" for="depositDate">Fecha de deposito</label>
                            <input type="text" class="form-control" name="deposit_date" id="depositDate" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                        <div class="col-sm-12 col-md-6 d-none" id="boxReferencePayment">
                            <label class="form-label" for="referencePayment">referencia / pago</label>
                            <input type="text" class="form-control mb-2" id="referencePayment" name="reference_payment">
                        </div>                        

                        <div class="col-sm-12 col-md-12 d-none" id="boxCommentConciliation">
                            <label class="form-label" for="messageConciliation">Mensaje de conciliación</label>
                            <textarea class="form-control" id="messageConciliation" name="conciliation_comment" cols="5" rows="5"></textarea>
                        </div>
                    </div>                        
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark __close_modal" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>