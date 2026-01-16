<!-- Modal -->
@php
    use App\Traits\RoleTrait;
@endphp
<div class="modal fade" id="item_comment_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="item_comment_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="item_comment_modalLabel">Editar comentario</h5>
                <button type="button" class="btn-close __close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body p-3">
                <form id="item_comment_form">
                    @csrf
                    <input type="hidden" name="item_id">
                    <input type="hidden" name="type">
                    <textarea name="comment" style="width: 100%;" rows="10"></textarea>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cancelar</button>
                <button type="button" class="btn btn-primary confirm-edit-comment">Confirmar cambios</button>
            </div>
        </div>
    </div>
</div>
