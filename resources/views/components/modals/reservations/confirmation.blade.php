@php
    use App\Traits\FiltersTrait;
    $terminals = FiltersTrait::ContactPoints($reservation->destination_id);    
@endphp
<div class="modal" tabindex="-1" id="arrivalConfirmationModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModal">Confirmación de llegada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalHeader">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <div class="modal-body">
                <div id="formConfirmation" class="d-none">
                    <form class="row" action="" method="POST" id="formArrivalConfirmation">
                        @csrf
                        <input type="hidden" name="item_id" id="arrival_confirmation_item_id" value="">
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="terminal_id">Seleccione la terminal de llegada</label>
                            <select class="form-control mb-3" name="terminal_id" id="terminal_id">
                                @if ( !empty($terminals) )
                                    @foreach ($terminals as $terminal)
                                        <option value="{{ $terminal->id }}">{{ $terminal->name }}</option>
                                    @endforeach
                                @endif                   
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
                <div class="d-flex flex-column w-100" id="messageConfirmation"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" id="closeModalFooter" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary d-none" onclick="sendArrivalConfirmation()" id="btnSendArrivalConfirmation">Enviar</button>              
            </div>
        </div>
    </div>
</div>
