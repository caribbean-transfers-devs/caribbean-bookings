<!-- Modal -->
<div class="modal fade" id="messageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="formComment" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Agregar comentario</h5>
                    <button type="button" class="btn-close __close" data-bs-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>            
                <div class="modal-body">
                    <input type="hidden" name="id" id="id_item" required>
                    <input type="hidden" name="code" id="code_item" required>
                    <input type="hidden" name="type" id="type_item" required>
                    <div class="col-12 col-sm-12">
                        <label class="form-label" for="comment_item">Ingresa el comentario</label>
                        <textarea name="comment" id="comment_item" class="form-control" cols="30" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-light-dark __close" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary">guardar</button>                    
                </div>
            </form>
        </div>
    </div>
</div>
