<div class="modal fade" id="refundNotApplicableModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="refundNotApplicableModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundNotApplicableModalLabel">Indica porque no procede el reembolso solicitado</h5>
                <button type="button" class="btn-close __close_modal" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRefundNotApplicable">
                    <div class="col-sm-12 col-md-12">
                        <label class="form-label" for="response_message">Agrega un mensaje</label>
                        <textarea class="form-control" id="response_message" name="response_message" cols="5" rows="5"></textarea>
                    </div>
                    <input type="hidden" name="reservation_id" value="" id="reservation_id">
                    <input type="hidden" name="reservation_refund_id" value="" id=reservation_refund_id>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark __close_modal" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="refundNotApplicable">Guardar</button>
            </div>
        </div>
    </div>
</div>