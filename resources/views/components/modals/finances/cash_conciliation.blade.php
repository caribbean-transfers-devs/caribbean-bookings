<div class="modal fade" id="addCashConciliationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addCashConciliationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCashConciliationModalLabel">Conciliación de pago en efectivo</h5>
                <button type="button" class="btn-close __close_modal" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form id="formCashConciliation">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="actionType" name="action_type" value="" required>
                        <input type="hidden" name="codes" value="" id="paymentsID">                
                        <div class="col-12 mb-2">
                            <label class="form-label" for="status_conciliation">Selecciona opción</label>
                            <select class="form-control" id="status_conciliation" name="status_conciliation">
                                <option value="1">Conciliado</option>
                                <option value="2">CxC</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label class="form-label" for="date_conciliation">Fecha de conciliación</label>
                            <input type="text" class="form-control" name="date_conciliation" id="date_conciliation" value="{{ date('Y-m-d') }}" readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label class="form-label" for="receives_money_conciliation">Quien recibe dinero</label>
                            <select class="form-control" id="receives_money_conciliation" name="receives_money_conciliation">
                                <option value="carlos">Carlos</option>
                                <option value="margarita">Margarita</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-12">
                            <label class="form-label" for="response_message">Agrega un mensaje</label>
                            <textarea class="form-control" id="response_message" name="response_message" cols="5" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-dark __close_modal" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="CashConciliation">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>