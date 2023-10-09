<div class="modal fade" id="reservationFollowModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Seguimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_new_followup">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceSalesTypeForm">Tipo</label>
                            <select class="form-select mb-2" id="serviceSalesTypeForm" name="type">
                                <option value="CLIENT" selected>Cliente</option>
                                <option value="INTERN" >Interno</option>
                                <option value="OPERATION" >Operación</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="follow_up_name">Asunto</label>
                            <input type="text" class="form-control mb-2" id="follow_up_name" name="name">
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="follow_up_text">Texto</label>
                            <textarea name="text" id="" cols="30" rows="10" class="form-control"></textarea> 
                        </div>   
                    </div>
                    <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="saveFollowUp()" id="btn_new_followup">Guardar</button>
            </div>
        </div>
    </div>
</div>