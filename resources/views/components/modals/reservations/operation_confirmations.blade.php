<!-- Modal -->
@php
    use App\Traits\RoleTrait;
@endphp
<div class="modal fade" id="confirmationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="confirmationModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel"></h5>
                <button type="button" class="btn-close __close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="tab">
                    <ul class="nav nav-tabs mb-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#terminal-tab-1" data-bs-toggle="tab" role="tab">Terminal 1</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#terminal-tab-2" data-bs-toggle="tab" role="tab">Terminal 2</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#terminal-tab-3" data-bs-toggle="tab" role="tab">Terminal 3</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" data-code="terminal1" id="terminal-tab-1" role="tabpanel" style="padding: .75rem 1rem;">
                            <div class="wrapper_confirmation" id="terminal1"></div>
                        </div>
                        <div class="tab-pane" data-code="terminal2" id="terminal-tab-2" role="tabpanel" style="padding: .75rem 1rem;">
                            <div class="wrapper_confirmation" id="terminal2"></div>
                        </div>
                        <div class="tab-pane" data-code="terminal3" id="terminal-tab-3" role="tabpanel" style="padding: .75rem 1rem;">
                            <div class="wrapper_confirmation" id="terminal3"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary copy_confirmation"> Copiar</button>
                <button type="button" class="btn btn-primary send_confirmation_whatsapp"> Enviar whatsapp</button>
                <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
            </div>            
        </div>
    </div>
</div>
