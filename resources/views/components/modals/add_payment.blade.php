@php
    use App\Traits\RoleTrait;
@endphp
@props(['clips'])

<div class="modal" tabindex="-1" id="addPaymentModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" for="reference">Referencia de pago</label>
                        <input class="form-control" type="text" name="reference" id="reference" required min="3">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label" for="payment_method">Forma de pago</label>
                        <select class="form-control mb-2" id="payment_method" name="payment_method">
                            <option value="CASH">Efectivo</option>
                            <option value="CARD">Tarjeta</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3" id="clip_container" style="display: none">
                    <div class="col-12">
                        <label class="form-label" for="clip_id">Selecciona la terminal</label>
                        <select class="form-control mb-2" id="clip_id" name="clip_id">
                            @foreach($clips as $clip)
                                <option value="{{ $clip->id }}">{{ $clip->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="password" class="">El cliente pagó con:</label>
                        <select class="form-control mb-2" id="paid_in_currency" name="paid_in_currency">
                            <option value="USD">USD</option>
                            <option value="MXN">MXN</option>
                            <option value="CAD">CAD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>
                @if(RoleTrait::hasPermission(58))
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="tipo_cambio_select" class="">Tipo de cambio personalizado?</label>
                            <select class="form-control mb-2" id="tipo_cambio_select" name="tipo_cambio_select">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="col-6" id="tipo_cambio_container" style="display: none">
                            <label for="tipo_cambio" class="">Tipo de cambio</label>
                            <input type="number" step=".01" class="form-control" id="tipo_cambio">
                        </div>
                    </div>
                @endif
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="paid" class="">Cantidad pagada:</label>
                        <input type="number" step=".01" class="form-control" id="payment" required>
                    </div>
                </div>
                <div class="alert alert-danger" role="alert" style="padding: .95rem; display:none">
                    Escribe una cantidad correcta
                </div>
                <div style="mt-3">
                    Faltan: $<span class="total_remaining">0</span> <span class="total-currency">USD</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="addPayment">Agregar</button>
            </div>
        </div>
    </div>
</div>
