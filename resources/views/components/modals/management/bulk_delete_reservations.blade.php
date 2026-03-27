<div class="modal fade" id="bulkDeleteReservationsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="bulkDeleteReservationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkDeleteReservationsModalLabel">Confirmar eliminación de reservas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light-danger border-0 mb-3">
                    <strong>⚠ Atención:</strong> Estás a punto de eliminar <strong id="bulk-delete-count">0</strong> reserva(s). Esta acción <strong>no se puede deshacer</strong>.
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Estatus</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="bulk-delete-table-body">
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="mb-0">
                    <label for="confirm-delete-input" class="form-label">Para confirmar, escribe <strong>eliminar</strong> en el campo de abajo:</label>
                    <input type="text" class="form-control" id="confirm-delete-input" placeholder='Escribe "eliminar"' autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-delete-btn" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4h6v2"></path></svg>
                    Confirmar eliminación
                </button>
            </div>
        </div>
    </div>
</div>
