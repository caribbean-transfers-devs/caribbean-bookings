<div class="modal" tabindex="-1" id="vendorModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="vendorForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="name">Nombre</label>
                            <input class="form-control" name="name" id="name" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" autocomplete="off" name="email" id="email">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="phone">Teléfono</label>
                            <input type="tel" class="form-control" autocomplete="off" name="phone" id="phone">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="status" class="">Status</label>
                            <select class="form-control mb-2" id="status" name="status">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id">
                </form>

                <div class="alert alert-danger" role="alert" style="padding: .95rem; display:none">
                    Escribe una cantidad correcta
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="submitVendorBtn" onclick="fetchVendor()"></button>
            </div>
        </div>
    </div>
</div>
