@props(['reservation','data','clips','vendors','currencyexchangedata'])
<!-- Modal -->
<div class="modal fade" id="completBookingModal" tabindex="-1" role="dialog" aria-labelledby="completBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completBookingModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form class="form" action="" method="POST" id="formComplet">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="terminal">Nombres</label>
                            <input type="text" name="client_first_name" class="form-control" value="{{ $reservation->client_first_name }}" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="vendor_id">Apellidos</label>
                            <input type="text" name="client_first_name" class="form-control" value="{{ $reservation->client_first_name }}" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="terminal">Correo</label>
                            <input type="text" name="client_email" class="form-control" value="{{ $reservation->client_email }}" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="vendor_id">Teléfono</label>
                            <input type="text" name="client_phone" class="form-control" value="{{ $reservation->client_phone }}" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="terminal">Terminal</label>
                            <select class="form-control mb-2" id="terminal" name="terminal">
                                @for ($i = 1; $i < 5; $i++)
                                    <option value="T{{ $i }}">Terminal {{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="vendor_id">Vendedor</label>
                            <select class="form-control mb-2" id="vendor_id" name="vendor_id">
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 payment-section">
                            <table class="table table-striped table-bordered" id="payments_table" style="display: none">
                                <caption align="top">Pagos agregados</caption>
                                <thead>
                                    <tr>
                                        <th>Pago</th>
                                        <th>Currency</th>
                                        <th>reference</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>

                            <div class="previous-total-container">
                                <strong>Total pagado: </strong>
                                <div class="color-total-container red">
                                    $<span id="previous_total">{{ round($data['total_payments'], 2) }}</span> <span class="total-currency">{{ $reservation->currency }}</span>    
                                </div>
                            </div>

                            <div class="total-remaining-container">
                                <strong>Falta por pagar: </strong>
                                <div class="color-total-container red">
                                    $<span class="total_remaining" id="total">{{ round($data['total_sales'], 2) }}</span> <span class="total-currency" id="sold_in_currency">{{ $reservation->currency }}</span>    
                                </div>
                            </div>
                            
                            <input type="hidden" value="{{ round($data['total_sales'], 2) }}" id="total_original" required>
                            <input type="hidden" value="{{ $reservation->currency }}" required>
                            <button type="button" class="btn btn-success btn-sm" id="openPaymentModal" data-bs-toggle="modal" data-bs-target="#addPaymentModal"><i class="align-middle" data-feather="plus"></i> Agregar pago</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>