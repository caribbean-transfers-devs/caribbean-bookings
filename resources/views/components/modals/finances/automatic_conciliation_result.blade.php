<div class="modal fade" id="generateStripeAutomaticConciliationDataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="generateStripeAutomaticConciliationDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateStripeAutomaticConciliationDataModalLabel">Datos de cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <div class="modal-body">
                <table class="table table-hover table-striped table-bordered table-details-booking mb-0">
                    <thead>
                        <tr>
                            <th>Código de stripe por conciliar</th>
                            <th>Banco</th>
                            <th>Monto total</th>
                            <th>Fecha de depósito al banco</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="generateStripeAutomaticConciliationDataModal_tbody">        
                        
                    </tbody>
                </table>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirm_conciliation">Confirmar conciliación</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="paymentsFromStripeAutomaticConciliationDataModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="paymentsFromStripeAutomaticConciliationDataModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentsFromStripeAutomaticConciliationDataModal">Datos de cargo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <div class="modal-body">
                <div style="overflow: auto; width: 100%">
                    <table class="table table-hover table-striped table-bordered table-details-booking mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Sitio</th>
                                <th>Código</th>
                                <th>Estatus</th>
                                <th>Cliente</th>
                                <th>Servicio</th>
                                <th>PAX</th>
                                <th>Destino</th>
                                <th>Importe de Venta</th>
                                <th>Importe Cobrado</th>
                                <th>Moneda</th>
                                <th>Método de Pago</th>
                                <th>Importe Pesos</th>
                                <th>ID Stripe / Referencia</th>
                                <th>Fecha de Cobro Stripe</th>
                                <th>Estatus de Cobro Stripe</th>
                                <th>Total Cobrado en Stripe</th>
                                <th>Comisión de Stripe</th>
                                <th>Total a Depositar por Stripe</th>
                                <th>Fecha Depositada al Banco</th>
                                <th>Estatus del Deposito Banco</th>
                                <th>Total Depositado al Banco</th>
                                <th>Referencia del Deposito al Banco</th>
                                <th>Banco</th>
                                <th>Tiene Reembolso</th>
                                <th>Tiene Disputa</th>
                            </tr>
                        </thead>
                        <tbody id="paymentsFromStripeAutomaticConciliationDataModal_tbody"></tbody>
                    </table>  
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>