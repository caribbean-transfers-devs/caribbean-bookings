<!-- Modal -->
<div class="modal fade" id="reservationPaymentsModal" tabindex="-1" role="dialog" aria-labelledby="freservationPaymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="freservationPaymentsModalLabel">Pagos de la reservación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Total original</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Moneda</th>
                            <th class="text-center">Tipo de cambio</th>
                            <th class="text-center">Método de pago</th>
                            <th class="text-center">Referencia</th>
                        </tr>
                    </thead>
                    <tbody id="containerReservationPayments">

                    </tbody>
                    <tfoot id="footerReservationPayments">

                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>
