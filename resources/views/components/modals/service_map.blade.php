<div class="modal fade" id="serviceMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información de servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>Desde:</strong> <span id="origin_location"></span></p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>Hacia:</strong> <span id="destination_location"></span></p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>Tiempo:</strong> <span id="destination_time"></span></p>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <p><strong>KM:</strong> <span id="destination_kms"></span></p>
                    </div>
                    <div class="col-12">
                        <div class="content" id="services_map"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>