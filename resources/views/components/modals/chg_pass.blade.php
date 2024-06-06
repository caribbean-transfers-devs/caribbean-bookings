<!-- Modal -->
<div class="modal fade" id="chgPassModal" tabindex="-1" role="dialog" aria-labelledby="chgPassModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chgPassModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
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
                <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                <button type="button" class="btn btn-primary" id="chgPassBtn">Cambiar Contraseña</button>
            </div>
        </div>
    </div>
</div>
