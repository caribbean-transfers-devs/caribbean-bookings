@props([])
<div class="modal" tabindex="-1" id="arrivalConfirmationModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmación de llegada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row" action="" method="POST" id="formArrivalConfirmation">
                    @csrf
                    <input type="hidden" name="item_id" id="arrival_confirmation_item_id" value="">
                    <div class="col-12 col-sm-6">
                        <label class="form-label" for="terminal_id">Seleccione la terminal de llegada</label>
                        <select class="form-control mb-3" name="terminal_id" id="terminal_id">
                            <option value='0'>Cargando...</option>                           
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label class="form-label" for="formConfirmationLanguage">Idioma</label>
                        <select class="form-control mb-3" name="lang" id="formConfirmationLanguage">
                            <option value='en'>Inglés</option>
                            <option value='es'>Español</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="sendArrivalConfirmation()" id="btnSendArrivalConfirmation">Enviar</button>
            </div>
        </div>
    </div>
</div>
