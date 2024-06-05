<div class="modal" tabindex="-1" id="chgPassModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_chg_pass" action="#">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="password" class="">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="confirm_pass" class="">Confirmar Contraseña</label>
                            <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="pass_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="chgPassBtn">Cambiar Contraseña</button>
            </div>
        </div>
    </div>
</div>
