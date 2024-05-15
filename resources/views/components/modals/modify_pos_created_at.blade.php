<div class="modal" tabindex="-1" id="modify_pos_created_at">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar fecha de creación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" for="created_at">Nueva fecha de creación</label>
                        <input class="form-control" id="created_at" name="created_at" data-default-mode="single" value="2023-09-15 12:00">
                    </div>
                </div>
                <div class="alert alert-danger" id="alert_created_at" role="alert" style="padding: .95rem; display:none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="modifyCreatedAt">Modificar</button>
            </div>
        </div>
    </div>
</div>
