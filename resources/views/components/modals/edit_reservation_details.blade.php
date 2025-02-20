@php
    use App\Traits\FiltersTrait;
    $sites = FiltersTrait::Sites();
    $origins = FiltersTrait::Origins();
@endphp
<div class="modal fade" id="serviceClientModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos del cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_edit_details">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientFirstNameModal">Nombres</label>
                            <input type="text" class="form-control mb-2" id="serviceClientFirstNameModal" name="client_first_name" value="{{ $reservation->client_first_name }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientLastNameModal">Apellidos</label>
                            <input type="text" class="form-control mb-2" id="serviceClientLastNameModal" name="client_last_name" value="{{ $reservation->client_last_name }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientEmailModal">E-mail</label>
                            <input type="email" class="form-control mb-2" id="serviceClientEmailModal" name="client_email" value="{{ $reservation->client_email }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientPhoneModal">Teléfono</label>
                            <input type="text" class="form-control mb-2" id="serviceClientPhoneModal" name="client_phone" value="{{ $reservation->client_phone }}">
                        </div>
                        <div class="col-sm-12 col-md-12"><hr></div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceSiteReference">Sitio</label>
                            <select class="form-select mb-2" id="serviceSiteReference" name="site_id" readonly>
                                @foreach($sites as $key => $value)
                                    <option value="{{ $value->id }}" {{ $reservation->site_id == $value->id ? 'selected' : '' }}>{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="serviceClientReference">Referencia</label>
                            <input type="text" class="form-control mb-2" id="serviceClientReference" name="reference" readonly value="{{ $reservation->reference }}">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="originSale">Origen de venta</label>
                            <select class="form-select mb-2" id="originSale" name="origin_sale_id">
                                <option value="0">Selecciona una opción</option>
                                @foreach($origins as $key => $origin)
                                    <option value="{{ $origin->id }}" {{ $reservation->origin_sale_id == $origin->id ? 'selected' : '' }}>{{ $origin->code }}</option>
                                @endforeach
                            </select>
                        </div>                        
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="servicePaymentsCurrencyModal">Moneda</label>
                            <select class="form-select mb-2" id="servicePaymentsCurrencyModal" name="currency" readonly>
                                <option value="USD" {{ $reservation->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="MXN" {{ $reservation->currency == 'MXN' ? 'selected' : '' }}>MXN</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label" for="bookingComment">Solicitudes especiales</label>
                            <textarea class="form-control" name="special_request" id="formSpecialRequest"></textarea>
                        </div>                        
                    </div>
                    <input type="hidden" id="reservation_id" value="{{ $reservation->id }}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_edit_res_details">Guardar</button>
            </div>
        </div>
    </div>
</div>