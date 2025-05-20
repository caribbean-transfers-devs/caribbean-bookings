<div class="modal fade" id="serviceMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información de servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 w-100 d-flex flex-wrap justify-content-between">
                    <p><strong style="color: #000;">Desde:</strong> <span id="origin_location"></span></p>
                    <p><strong style="color: #000;">Hacia:</strong> <span id="destination_location"></span></p>
                    <p><strong style="color: #000;">Tiempo:</strong> <span id="destination_time"></span></p>                    
                    <p><strong style="color: #000;">KM:</strong> <span id="destination_kms"></span></p>
                </div>
                <div class="content" id="services_map"></div>
            </div>
        </div>
    </div>
</div>