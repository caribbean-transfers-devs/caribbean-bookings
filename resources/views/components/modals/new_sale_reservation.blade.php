@php
    $typeSales = auth()->user()->TypeSales();
@endphp
<div class="modal fade" id="serviceSalesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_new_sale">
                    <div class="row">
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="new_sale_type_id">Tipo</label>
                            <select class="form-select mb-2" id="new_sale_type_id" name="sale_type_id">
                                @foreach($typeSales as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="new_sale_description">Descripción</label>
                            <input type="text" class="form-control mb-2" id="new_sale_description" name="description">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="new_sale_quantity">Cantidad</label>
                            <input type="number" class="form-control mb-2" id="new_sale_quantity" name="quantity">
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <label class="form-label" for="new_sale_total">Total</label>
                            <input type="number" class="form-control mb-2" id="new_sale_total" name="total">
                        </div>
                    </div>
                    <input type="hidden" name="reservation_id" value="{{ $reservation_id }}">
                </form>
                <input type="hidden" id="type_form" value="1">
                <input type="hidden" id="sale_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn_new_sale">Guardar</button>
            </div>
        </div>
    </div>
</div>