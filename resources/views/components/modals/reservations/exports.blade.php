@props(['data'])
@php
    $date = $data;
    if( is_array($data) ){
        $date = $data['init']." - ".$data['end'];
    }else{
        $date = $data;
    }
@endphp
<!-- Modal -->
<div class="modal fade" id="filterModalExport" tabindex="-1" role="dialog" aria-labelledby="filterModalExportLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" id="formSearch" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalExportLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="lookup_date">Fecha</label>
                            <input type="text" name="date" id="lookup_date2" class="form-control" value="{{ $date }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label" for="language">Idioma</label>
                            <select name="language" id="language" class="form-control">
                                <option value="es">Español</option>
                                <option value="en">Ingles</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="generateExcel">Exportar</button>                    
                </div>
            </form>
        </div>
    </div>
</div>
