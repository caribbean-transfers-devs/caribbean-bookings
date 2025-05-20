<!-- Modal -->
@php
    use App\Traits\RoleTrait;
@endphp
<div class="modal fade" id="messageModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel"></h5>
                <button type="button" class="btn-close __close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="tab">
                    <ul class="nav nav-tabs mb-2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" style="display:flex; align-items:center; gap:5px;" href="#icon-tab-1" data-bs-toggle="tab" role="tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                Agregar o editar comentario
                            </a>
                        </li>
                        @if (auth()->user()->hasPermission(64))
                            <li class="nav-item">
                                <a class="nav-link" style="display:flex; align-items:center; gap:5px;" href="#icon-tab-2" data-bs-toggle="tab" role="tab">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    Agregar multimedia
                                </a>
                            </li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="icon-tab-1" role="tabpanel">
                            <form id="formComment" enctype="multipart/form-data">
                                @csrf
                                    <input type="hidden" name="id" id="id_item" required>
                                    <input type="hidden" name="code" id="code_item" required>
                                    <input type="hidden" name="operation" id="operation_item" required>
                                    <input type="hidden" name="type" id="type_item" required>
                                    <div class="col-12 col-sm-12">
                                        <label class="form-label" for="comment_item">Ingresa el comentario</label>
                                        <textarea name="comment" id="comment_item" class="form-control" cols="30" rows="10"></textarea>
                                    </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn btn-light-dark __close" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                                    <button type="submit" class="btn btn-primary">guardar</button>                    
                                </div>
                            </form>                        
                        </div>
                        @if (auth()->user()->hasPermission(64))
                            <div class="tab-pane" id="icon-tab-2" role="tabpanel">
                                @if (auth()->user()->hasPermission(64))
                                    <form id="upload-form" class="dropzone" action="/reservations/upload">
                                        @csrf
                                        <input type="hidden" name="type_action" id="type_action" value="upload">
                                        <input type="hidden" name="id" id="id" required>
                                        <input type="hidden" name="folder" id="reservation_id" value="">
                                        <input type="hidden" name="reservation_item" id="reservation_item" required>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
