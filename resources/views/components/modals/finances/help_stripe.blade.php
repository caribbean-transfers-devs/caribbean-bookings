<div class="modal fade" id="helpStripeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="helpStripeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="helpStripeModalLabel">Datos de cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <div class="modal-body">
                <table class="table table-hover table-striped table-bordered table-details-booking mb-0">
                    <thead>
                        <tr>
                            <th>Prefijo</th>
                            <th>Objeto que representa</th>
                        </tr>
                    </thead>
                    <tbody>        
                        <tr>
                            <th class="text-left">pi_</th>
                            <td>Payment Intent (Intento de pago)</td>
                        </tr>
                        <tr>
                            <th class="text-left">ch_</th>
                            <td>Charge (Cargo/Pago realizado)</td>
                        </tr>
                        <tr>
                            <th class="text-left">py_</th>
                            <td>Payout (Retiro de fondos)</td>
                        </tr>
                        <tr>
                            <th class="text-left">txn_</th>
                            <td>Balance Transaction (Transacción en el saldo de Stripe)</td>
                        </tr>
                        <tr>
                            <th class="text-left">pm_</th>
                            <td>Payment Method (Método de pago)</td>
                        </tr>
                        <tr>
                            <th class="text-left">cus_</th>
                            <td>Customer (Cliente en Stripe)</td>
                        </tr>
                        <tr>
                            <th class="text-left">inv_</th>
                            <td>Invoice (Factura)</td>
                        </tr>
                        <tr>
                            <th class="text-left">sub_</th>
                            <td>Subscription (Suscripción)</td>
                        </tr>
                    </tbody>
                </table>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>