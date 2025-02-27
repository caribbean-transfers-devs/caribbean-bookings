@extends('layout.app')
@section('title') @if($v_type == 1) Crear @else Editar @endif Usuarios @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')    
    <script src="{{ mix('assets/js/sections/user_edit.min.js') }}"></script>
    <script>
        function isCommissionF(__this){
            const box = document.querySelector('.callcenteagent');
            const box2 = document.querySelector('.callcenteagent2');
            if( __this.value == 1 ){
                box.classList.remove('d-none');
                box2.classList.remove('d-none');
            }else{
                box.classList.add('d-none');
                box2.classList.add('d-none');
            }
        }
        function typeCommission(__this){
            const percentage = document.querySelector('.percentage');
            if( __this.value == "percentage" ){
                percentage.classList.remove('d-none');
            }else{
                percentage.classList.add('d-none')
            }
        }
        const isCommission = document.getElementById("is_commission");
        const type_commission = document.getElementById('type_commission');

        const choices = new Choices(document.getElementById('roles'),{
            removeItemButton: true,
            loadingText: 'Cargando...',
            noResultsText: 'No se encontraron resultados',
            noChoicesText: 'No hay opciones para elegir',
            itemSelectText: 'Clic para elegir',
        });

        if( isCommission ){
            isCommissionF(isCommission);
            isCommission.addEventListener('change', function(){
                isCommissionF(this);
            });
        }
        if (type_commission) {
            typeCommission(type_commission);
            type_commission.addEventListener('change', function(){
                typeCommission(this);
            });
        }

        $("#save").click(() => {
            let frm_data = $("#frm_user").serializeArray();
            let type_req = '{{ $v_type == 1 ? 'POST' : 'PUT' }}';
            let url_req = '{{ $v_type == 1 ? '/users' : '/users/' . $user->id }}';
            $.ajax({
                url: url_req,
                type: type_req,
                data: frm_data,
                success: function(resp) {
                    if (resp.success == 1) {
                        window.onbeforeunload = null;
                        let timerInterval
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: 'success',
                            html: 'Datos guardados con éxito. Será redirigido en <b></b>',
                            timer: 2500,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = (Swal.getTimerLeft() / 1000)
                                        .toFixed(0)
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            if (window.location.href.includes('create')) {
                                window.location.href = '/users'
                            } else {
                                location.reload();
                            }
                        })
                    } else {
                        console.log(resp);
                    }
                }
            }).fail(function(xhr, status, error) {
                Swal.fire(
                    '¡ERROR!',
                    xhr.responseJSON.message,
                    'error'
                )
            });
        })
    </script>
@endpush

@section('content')
    <div class="account-settings-container layout-top-spacing">
        <div class="account-content">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="info">
                            @if ($errors->any())
                                <div class="alert alert-light-primary alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert"> 
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('danger'))
                                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                                    {{ session('danger') }}
                                </div>
                            @endif

                            <form id="frm_user">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label for="name" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $user->name }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="email" class="form-label">Correo</label>
                                        <input type="text" class="form-control" id="email" name="email"
                                                value="{{ $user->email }}" required>
                                    </div>
                                </div>  
                                @if ($v_type == 1)
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <label for="password" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                        <div class="col-6">
                                            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                @endif                                  
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label for="restricted" class="form-label">Restringido</label>
                                        <select class="form-select" id="restricted" name="restricted">
                                            <option value="0" @if ($user->restricted == 0) selected @endif>No</option>
                                            <option value="1" @if ($user->restricted == 1) selected @endif>Si</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="is_commission" class="form-label">Comisiona</label>
                                        <select class="form-select" id="is_commission" name="is_commission">
                                            <option value="0" @if ($user->is_commission == 0) selected @endif>No</option>
                                            <option value="1" @if ($user->is_commission == 1) selected @endif>Si</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3 d-none callcenteagent">
                                    <div class="col-6">
                                        <label for="type_commission" class="form-label">Tipo de comisión</label>
                                        <select class="form-select" id="type_commission" name="type_commission">
                                            <option value="">Selecciona una opción</option>
                                            <option value="target" @if ($user->type_commission == "target") selected @endif>Por metas</option>
                                            <option value="percentage" @if ($user->type_commission == "percentage") selected @endif>Porcentaje</option>
                                        </select>
                                    </div>
                                    <div class="col-6 d-none percentage">
                                        <label for="percentage" class="form-label">Porcentage</label>
                                        <input type="number" step=".01" class="form-control" id="percentage" name="percentage"
                                                value="{{ $user->percentage }}" required>
                                    </div>                                    
                                </div>
                                <div class="row mb-3 d-none callcenteagent2">
                                    <div class="col-6">
                                        <label for="daily_goal" class="form-label">Indica la meta de venta diaria en pesos</label>
                                        <input type="number" step=".01" class="form-control" id="daily_goal" name="daily_goal"
                                                value="{{ $user->daily_goal }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="is_external" class="form-label">Es externo</label>
                                        <select class="form-select" id="is_external" name="is_external">
                                            <option value="0" @if ($user->is_external == 0) selected @endif>No</option>
                                            <option value="1" @if ($user->is_external == 1) selected @endif>Sí</option>
                                        </select>
                                    </div>                                        
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        @php
                                            $active_roles = [];
                                            if($user->roles){
                                                foreach($user->roles as $role){
                                                    $active_roles[] = $role->role_id;
                                                }
                                            }
                                        @endphp
                                        <label for="email" class="form-label">Roles</label>
                                        <select class="form-select" id="roles" name="roles[]" multiple>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" @if (in_array($role->id, $active_roles)) selected @endif>{{ $role->role }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>                            
                            <button class="btn btn-success" id="save">@if ($v_type == 1)
                                Crear
                            @else
                                Editar
                            @endif Usuario</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection