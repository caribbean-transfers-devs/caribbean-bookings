<!-- Modal -->
<div class="modal fade" id="callcenterQRModal" tabindex="-1" role="dialog" aria-labelledby="callcenterQRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="callcenterQRModalLabel">QR</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 col-sm-6 col-sm-6">
                        <div class="card">
                            <div class="card-body text-center pt-3">
                                <h5 class="card-title mb-3">Ingles</h5>
                                <img src="{{ route('qr.createQr', ["tpv", auth()->user()->id, 'en']) }}" class="qr">
                            </div>
                            <div class="card-footer text-center px-4 pt-0 border-0">
                                <p class="text-center">Reserva con la mejor empresa de transporte ❤️</p>
                            </div>
                        </div>                                
                    </div>
                    <div class="col-12 col-sm-6 col-sm-6">
                        <div class="card">
                            <div class="card-body text-center pt-3">
                                <h5 class="card-title mb-3">Español</h5>
                                <img src="{{ route('qr.createQr', ["tpv", auth()->user()->id, 'es']) }}" class="qr">
                            </div>
                            <div class="card-footer text-center px-4 pt-0 border-0">
                                <p class="text-center">Reserva con la mejor empresa de transporte ❤️</p>
                            </div>
                        </div>                                
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>