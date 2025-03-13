<div class="modal fade" id="viewProofsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="viewProofsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProofsModalLabel">Evidencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>                
            </div>
            <div class="modal-body">
                <div class="simple-pill">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="spamResumeInformationContainer-tab" data-bs-toggle="pill" data-bs-target="#spamResumeInformationContainer" type="button" role="tab" aria-controls="spamResumeInformationContainer" aria-selected="true">General</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Seguimiento</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="spamHistory-tab" data-bs-toggle="pill" data-bs-target="#spamHistory" type="button" role="tab" aria-controls="spamHistory" aria-selected="false">Historial</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="spamResumeInformationContainer-tab" tabindex="0" id="spamResumeInformationContainer"></div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0"></div>
                        <div class="tab-pane fade" id="spamHistory" role="tabpanel" aria-labelledby="spamHistory-tab" tabindex="0"></div>
                    </div>                
                </div>                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-dark" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>