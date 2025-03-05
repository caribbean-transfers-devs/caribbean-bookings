@extends('layout.app')
@section('title') @if($v_type == 1) Crear @else Editar @endif Usuarios @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
    <style>
        .responsive-account-container * {
            box-sizing: border-box;
        }
        .responsive-account-container {
            color: #333;
            display: block;
            font-size: 1em;
            min-height: 400px;
            min-width: 300px;
            position: relative;
            width: 90%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            margin: 0 auto;
            overflow-wrap: anywhere;
        }
        .manage-access-container--with-soad-button {
            display: flex;
            flex-direction: column;
        }
        .manage-access-container .manage-access-header {
            margin-bottom: 25px;
            text-align: center;
        }
        .manage-access-container .manage-access-header h1 {
            font-size: 32px;
            font-weight: 500;
        }
        .manage-access-container .manage-access-header p {
            font-size: 20px;
            line-height: 30px;
            margin: auto;
            width: 75%;
        }
        .manage-access-container .device-list {
            margin: 0;
            padding: 0;
            display: flex;
        }
        .manage-access-container .device-list-item {
            background-color: #ffffff;
            border: 1px solid hsla(0, 0%, 100%, .2);
            border-radius: 4px;
            box-shadow: 0 0 2px rgba(0, 0, 0, .1), 0 4px 8px rgba(0, 0, 0, .1);
            display: inline-block;
            list-style: none;
            margin: 12px;
            padding: 5px 20px 10px;
            width: calc(50% - 24px);
        }
        .manage-access-container .device-list-item-header {
            align-items: center;
            border-bottom: 1px solid #e2e2e2;
            display: flex;
            height: 65px;
            justify-content: space-between;
        }
        svg:not(:root) {
            overflow: hidden;
        }
        .manage-access-container .device-list-item-header .device-icon {
            flex: 0 0 auto;
            height: 24px;
            margin-right: 12px;
            width: 24px;
        }
        .manage-access-container .device-list-item-header h2 {
            display: inline-block;
            display: -webkit-box;
            flex-grow: 1;
            font-size: 18px;
            font-weight: 500;
            line-height: 25px;
            margin-top: 5px;
            max-height: 48px;
            overflow: hidden;
            padding-right: 16px;
            text-overflow: ellipsis;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .manage-access-container .device-list-item-header .current-device-badge {
            background-color: #cce6ff;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 500;
            line-height: 10px;
            padding: 3px 8px;
            text-align: center;
            white-space: nowrap;
        }
        .manage-access-container .device-list-item-details {
            color: #4d4d4d;
            font-size: 16px;
            margin-top: 16px;
        }
        .manage-access-container .device-list-item-details .paragraph {
            margin-bottom: 13px;
            margin-top: 13px;
            display: flex;
            align-items: center;
        }
        .manage-access-container .device-list-item-details .profile-icon {
            border-radius: 2px;
            margin-right: 10px;
            vertical-align: sub;
            width: 16px;
        }
        .manage-access-container .device-list-item-details .last-watched, .manage-access-container .device-list-item-details .profile-container {
            display: inline-block;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
            white-space: pre;
            width: calc(100% - 26px);
        }
        .manage-access-container .device-list-item-details .icon {
            margin-left: 1px;
            margin-right: 10px;
            vertical-align: middle;
        }
        .pressable_styles__a6ynkg0 {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: none;
            border-radius: 0;
            border: 0;
            box-sizing: content-box;
            color: inherit;
            cursor: default;
            display: inline;
            font: inherit;
            letter-spacing: inherit;
            line-height: inherit;
            margin: 0;
            opacity: 1;
            padding: 0;
            text-decoration: none;
        }
        .button_styles {
            border: 0;
            cursor: pointer;
            fill: currentColor;
            position: relative;
            transition-duration: 250ms;
            transition-property: background-color, border-color;
            transition-timing-function: cubic-bezier(0.4,0,0.68,0.06);
            vertical-align: text-top;
            width: auto;
            font-size: 1rem;
            font-weight: 500;
            min-height: 2.5rem;
            padding: 0.375rem 1rem;
            border-radius: 0.25rem;
            background: rgba(128, 128, 128, 0.0);
            color: rgb(0, 0, 0);
        }
        .button_styles {
            align-items: center;
            background: gainsboro;
            border-radius: 2px;
            border: 1px solid dimgray;
            box-sizing: border-box;
            color: black;
            cursor: default;
            display: inline-flex;
            font-size: 13px;
            font-weight: 400;
            justify-content: center;
            letter-spacing: normal;
            line-height: 1;
            padding: 2px 7px;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }
        .manage-access-container .device-list-item-header button {
            font-size: 14px;
            line-height: 21px;
            min-height: 36px;
            min-width: 89px;
            white-space: nowrap;
        }
        .manage-access-container .incomplete-data-disclaimer {
            border-top: 1px solid hsla(0, 0%, 50%, .4);
            color: rgba(0, 0, 0, .7);
            line-height: 24px;
            margin-top: 50px;
            padding-top: 65px;
            text-align: center;
        }
        .manage-access-container .incomplete-data-disclaimer--no-border {
            border-top: none;
            margin-top: 0;
            padding-top: 0;
        }

        .pressable_styles__a6ynkg0 {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: none;
            border-radius: 0;
            border: 0;
            box-sizing: content-box;
            color: inherit;
            cursor: default;
            display: inline;
            font: inherit;
            letter-spacing: inherit;
            line-height: inherit;
            margin: 0;
            opacity: 1;
            padding: 0;
            text-decoration: none;
        }
        .button_styles__1kwr4ym0 {
            align-items: center;
            background: gainsboro;
            border-radius: 2px;
            border: 1px solid dimgray;
            box-sizing: border-box;
            color: black;
            cursor: default;
            display: inline-flex;
            font-size: 13px;
            font-weight: 400;
            justify-content: center;
            letter-spacing: normal;
            line-height: 1;
            padding: 2px 7px;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }        
        .default-ltr-cache-oqubc1-StyledBaseButton {
            cursor: pointer;
            fill: currentColor;
            position: relative;
            transition-duration: 250ms;
            transition-property: background-color, border-color;
            transition-timing-function: cubic-bezier(0.4,0,0.68,0.06);
            vertical-align: text-top;
            width: 100%;
            font-size: 1.125rem;
            font-weight: 400;
            min-height: 3rem;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            background: rgba(0, 0, 0, 0);
            color: rgb(193, 17, 25);
        }
        .manage-access-container .soad-button {
            margin: 24px auto 0;
        }
        .manage-access-container .see-more-button, .manage-access-container .soad-button {
            max-width: 272px;
        }        
    </style>
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

                @if ( optional($user->sessions) )
                    <div class="responsive-account-container">                
                        <div class="manage-access-container manage-access-container--with-soad-button">
                            <header class="manage-access-header">
                                <h1>Administrar acceso y dispositivos</h1>
                                <p>Estos dispositivos con sesión iniciada han estado activos recientemente en esta cuenta. Para mayor seguridad, puedes cerrar la sesión de cualquier dispositivo desconocido o </p>
                            </header>
                            <ul class="device-list">
                            @foreach($user->sessions as $session)                                
                                <li class="device-list-item">
                                    <header class="device-list-item-header">
                                        <svg width="16" height="12" viewBox="0 0 16 12" fill="none" class="device-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.5001 6.5V1.5H12.5001V6.5H3.5001ZM2.0001 1C2.0001 0.44772 2.44782 0 3.0001 0H13.0001C13.5524 0 14.0001 0.44772 14.0001 1V7C14.0001 7.55228 13.5524 8 13.0001 8H3.0001C2.44782 8 2.0001 7.55228 2.0001 7V1ZM0.75606 11.8398C3.06492 11.564 5.49346 11.4167 8.00043 11.4167C10.5074 11.4167 12.9359 11.564 15.2448 11.8398L15.4227 10.3504C13.0537 10.0674 10.5657 9.9167 8.00043 9.9167C5.43511 9.9167 2.94713 10.0674 0.578125 10.3504L0.75606 11.8398Z" fill="black"></path></svg>
                                        <h2>{{ $session->device_name }}</h2>
                                        @if($currentSession && $currentSession->id == $session->id)
                                            <div class="current-device-badge">DISPOSITIVO ACTUAL</div>
                                        @else
                                            <form action="{{ route('logout.other', $session->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="button_styles">Cerrar sesión</button>
                                            </form>
                                        @endif
                                    </header>
                                    <div class="device-list-item-details">
                                        <div class="paragraph">
                                            <svg width="12" height="13" viewBox="0 0 12 13" fill="none" class="profile-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.00007 3.33333C4.00007 2.22877 4.89553 1.33333 6.00007 1.33333C7.10467 1.33333 8.00007 2.22877 8.00007 3.33333C8.00007 4.4379 7.10467 5.33333 6.00007 5.33333C4.89553 5.33333 4.00007 4.4379 4.00007 3.33333ZM6.00007 0C4.15913 0 2.66674 1.49239 2.66674 3.33333C2.66674 5.17427 4.15913 6.66667 6.00007 6.66667C7.841 6.66667 9.3334 5.17427 9.3334 3.33333C9.3334 1.49239 7.841 0 6.00007 0ZM1.98713 12.1307C2.31245 10.5042 3.74063 9.33333 5.3994 9.33333H6.60073C8.25953 9.33333 9.68774 10.5042 10.013 12.1307L11.3205 11.8693C10.8705 9.61947 8.89507 8 6.60073 8H5.3994C3.10506 8 1.12965 9.61947 0.679687 11.8693L1.98713 12.1307Z" fill="black"></path></svg>
                                            <span class="profile-container"><span class="profile-name">{{ $session->ip_address }}</span></span>
                                        </div>
                                        <div class="paragraph">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M7 1.5C3.96243 1.5 1.5 3.96243 1.5 7C1.5 10.0376 3.96243 12.5 7 12.5C10.0376 12.5 12.5 10.0376 12.5 7C12.5 3.96243 10.0376 1.5 7 1.5ZM14 7C14 10.866 10.866 14 7 14C3.13401 14 0 10.866 0 7C0 3.13401 3.13401 0 7 0C10.866 0 14 3.13401 14 7ZM7.75 3V6.68934L9.5303 8.46967L8.46967 9.5303L6.46967 7.53033L6.25 7.31066V7V3H7.75Z" fill="black" fill-opacity="0.7"></path></svg>
                                            <span class="last-watched">{{ $session->last_activity }}</span>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
                            <div class="incomplete-data-disclaimer incomplete-data-disclaimer--no-border"><p class="_text">Es posible que esta lista no esté completa. Es posible que no aparezcan algunos dispositivos o que no se muestre toda su actividad.</p></div>
                                <form action="{{ route('logout.all') }}" method="POST" class="text-center">
                                    @csrf
                                    <button type="submit" class="pressable_styles__a6ynkg0 button_styles__1kwr4ym0 soad-button default-ltr-cache-oqubc1-StyledBaseButton e1ax5wel2" dir="ltr" role="button" href="/ManageDevices">Cierra sesión en todos los dispositivos</button>
                                </form>
                        </div>
                    </div>                    
                @endif
            </div>
        </div>
    </div>
@endsection