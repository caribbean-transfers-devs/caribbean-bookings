@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;

    $today = new DateTime();
    $dates = [];

    for ($i = 0; $i < 30; $i++) {
        $dates[] = $today->format('Y-m-d');
        $today->modify('-1 day');
    }

@endphp
@extends('layout.app')
@section('title') POST Venta @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/new_spam.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/new_spam.min.css') }}" rel="stylesheet" >
    <style>
        .table-wrapper {
            max-height: 700px; /* Altura máxima del contenedor */
            overflow-y: auto;  /* Habilitar desplazamiento vertical */
        }        
    </style>
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/operations/spam-v2.min.js') }}"></script>
    <script src="{{ mix('/assets/js/sections/operations/pending.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
    @endphp

    @if ($errors->any())
        <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-3" style="margin-top:25px;">
        <div class="col-12 col-sm-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pendientes de pago</h5>
                    <div class="row" id="pending-general-container">
                        <div class="loaderItem"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="spam-date-container">
                        <h5 class="card-title">Gestión de SPAM</h5>
                        <select class="form-select" id="spam-selec-date" onchange="getSpamByDate(event)">
                            @foreach($dates as $key => $value) 
                                <option value="{{ $value }}">{{ date("Y/m/d", strtotime($value)) }}</option>
                            @endforeach                            
                        </select>
                    </div>
                    <p class="mb-0"></p>
                    
                    <div class="row" id="spam-general-container">
                        <div class="loaderItem"></div>
                    </div>

                </div>                
            </div>
        </div>
    </div>


    <div class="modal" tabindex="-1" id="viewSpamDetailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles para envío de SPAM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                            <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="spamResumeInformationContainer-tab" tabindex="0" id="spamResumeInformationContainer">
                                <div class="loaderItem"></div>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                <form id="formSpamAddComment">
                                    <input type="hidden" name="id" id="spam_rez_id">
                                    <input type="hidden" name="id_item" id="spam_item_id">
                                    <div class="form-group mb-3">
                                        <label for="forSpamNewStatus">Seleccione el nuevo estatus</label>
                                        <select class="form-select" id="forSpamNewStatus" name="spam_status">                                    
                                            <option value="PENDING">Pendiente</option>
                                            <option value="SENT">Enviado</option>
                                            <option value="LATER">Después</option>
                                            <option value="CONFIRMED">Confirmado</option>
                                            <option value="REJECTED">Rechazado</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-4">
                                        <label for="forSpamAddComment">Deja un comentario del seguimiento...</label>
                                        <textarea class="form-control" id="forSpamAddComment" rows="8" name="spam_comment"></textarea>
                                    </div>
                                    <div class="form-group mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="spamRememberCheck" name="spam_remember" value="1">
                                            <label class="form-check-label" for="spamRememberCheck">¿Desea agregar un recordatorio?</label>                                        
                                        </div>
                                        <div class="form-group follow_up_hide" id="spamRememberDisplay">
                                            <input type="date" class="form-control" value="{{ date("Y-m-d") }}" name="spam_remember_date" style="padding: 15px; font-size: 10pt;">
                                        </div>
                                    </div>
                                    <button class="btn btn-primary mb-2 me-4 btn-lg _effect--ripple waves-effect waves-light" id="btnSaveSpamComment" onclick="saveSpamComment(event)">Guardar</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="spamHistory" role="tabpanel" aria-labelledby="spamHistory-tab" tabindex="0"></div>
                        </div>
                    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection