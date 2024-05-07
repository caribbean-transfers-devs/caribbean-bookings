@props(['data'])
<div class="modal" tabindex="-1" id="filterModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtro de reportes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="formSearch">
                @csrf
                <div class="modal-body">                                    
                    <div class="col-12 col-sm-12">
                        <label class="form-label" for="lookup_date">Fecha de creación</label>
                        <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $data['init'] }} - {{ $data['end'] }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Buscar</button>
                </div>
            </form>
        </div>
    </div>
</div>
