<!-- Modal -->
<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form class="form" action="" method="POST" id="vendorForm">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="name">Nombre</label>
                            <input class="form-control" name="name" id="name" autocomplete="off" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" autocomplete="off" name="email" id="email">
                        </div>
                        <div class="col-6">
                            <label for="phone">Teléfono</label>
                            <input type="tel" class="form-control" autocomplete="off" name="phone" id="phone">
                        </div>                    
                        <div class="col-6">
                            <label for="status" class="">Status</label>
                            <select class="form-control mb-2" id="status" name="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="submitVendorBtn" onclick="fetchVendor()">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>