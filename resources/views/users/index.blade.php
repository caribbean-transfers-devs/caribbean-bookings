@extends('layout.master')
@section('title') Usuarios @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
    <script>
        $(function() {
            $('#active_users,#inactive_users,#tbl_whitelist').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                }
            });
        });

        function ChangePass(id){
            $("#password").val('');
            $("#confirm_pass").val('');
            $("#pass_id").val(id);
        }

        $("#chgPassBtn").on('click', () => {
            $("#chgPassBtn").prop('disabled', true);
            $("#chgPassBtn").html('<i class="fas fa-spinner fa-pulse"></i>');
            let frm_data = $("#frm_chg_pass").serialize();
            let id = $("#pass_id").val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                }
            });       
            $.ajax({
                url: '/ChangePass/'+id,
                type: 'PUT',
                data: frm_data,
                success: function(resp) {
                    if(resp.success == 1){
                        let timerInterval
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: 'success',
                            html: 'Contraseña cambiada con éxito. Será redirigido en <b></b>',
                            timer: 2500,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                    const b = Swal.getHtmlContainer().querySelector('b')
                                    timerInterval = setInterval(() => {
                                    b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
                                    }, 100)
                                },
                                willClose: () => {
                                    clearInterval(timerInterval)
                                }
                        }).then((result) => {
                            window.location.href = '/users'                        
                        })
                    }else{
                        console.log(resp);
                    }
                }
            }).fail(function(xhr, status, error) {
                Swal.fire(
                    '¡ERROR!',
                    xhr.responseJSON.message,
                    'error'
                )
                $("#chgPassBtn").html('Cambiar Contraseña');
                $("#chgPassBtn").prop('disabled', false);
            });        
        })

        function chgStatus(id,status){
            let msg = status == 1 ? 'activa' : 'desactiva';
            Swal.fire({
                title: '¿Está seguro?',
                text: "¿Desea "+msg+" este usuario?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '¡Si, '+msg+'r!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                        }
                    });       
                    $.ajax({
                        url: '/ChangeStatus/'+id,
                        type: 'PUT',
                        data: {status:status},
                        success: function(resp) {
                            if(resp.success == 1){
                                let timerInterval
                                Swal.fire({
                                    title: '¡Éxito!',
                                    icon: 'success',
                                    html: 'Usuario '+msg+'do con éxito. Será redirigido en <b></b>',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                            const b = Swal.getHtmlContainer().querySelector('b')
                                            timerInterval = setInterval(() => {
                                            b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
                                            }, 100)
                                        },
                                        willClose: () => {
                                            clearInterval(timerInterval)
                                        }
                                }).then((result) => {
                                    location.reload();                        
                                })
                            }else{
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
                }
            })
        }

        function StoreIP(){
            let ip = $("#valid_ip").val();
            if(ip == ''){
                Swal.fire(
                    '¡ERROR!',
                    'Debe ingresar una IP válida',
                    'error'
                )
            }else{
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                    }
                });       
                $.ajax({
                    url: '/StoreIP',
                    type: 'POST',
                    data: {ip:ip},
                    success: function(resp) {
                        if(resp.success == 1){
                            let timerInterval
                            Swal.fire({
                                title: '¡Éxito!',
                                icon: 'success',
                                html: 'IP agregada con éxito. Será redirigido en <b></b>',
                                timer: 2500,
                                timerProgressBar: true,
                                didOpen: () => {
                                    Swal.showLoading()
                                        const b = Swal.getHtmlContainer().querySelector('b')
                                        timerInterval = setInterval(() => {
                                        b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
                                        }, 100)
                                    },
                                    willClose: () => {
                                        clearInterval(timerInterval)
                                    }
                            }).then((result) => {
                                location.reload();                        
                            })
                        }else{
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
            }
        }

        function DelIP(id){
            Swal.fire({
                title: '¿Está seguro?',
                text: "¿Desea eliminar esta IP de la lista blanca?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '¡Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                        }
                    });       
                    $.ajax({
                        url: '/DeleteIPs/'+id,
                        type: 'DELETE',
                        success: function(resp) {
                            if(resp.success == 1){
                                let timerInterval
                                Swal.fire({
                                    title: '¡Éxito!',
                                    icon: 'success',
                                    html: 'IP eliminada con éxito. Será redirigido en <b></b>',
                                    timer: 2500,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                            const b = Swal.getHtmlContainer().querySelector('b')
                                            timerInterval = setInterval(() => {
                                            b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
                                            }, 100)
                                        },
                                        willClose: () => {
                                            clearInterval(timerInterval)
                                        }
                                }).then((result) => {
                                    location.reload();                        
                                })
                            }else{
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
                }
            })
        }
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Usuarios</h1>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Usuarios</h5>
                        <div class="card-header d-flex justify-content-end align-items-center gap-3">                            
                            <a href="{{ route('users.create') }}" class="btn btn-success">Añadir Usuario</a>   
                            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#whiteIPsModal">Ver IPs</a>                         
                        </div>                       
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab" aria-selected="true">Usuarios Activos</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab" aria-selected="false" tabindex="-1">Usuarios Inactivos</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-1" role="tabpanel">
                                <div class="table-responsive mt-3">
                                    <table id="active_users" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Correo</th>
                                                <th>Restringido</th>
                                                <th>Roles</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($active_users as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if ($user->restricted == 0)
                                                            <span class="badge bg-secondary">No</span>
                                                        @else
                                                            <span class="badge bg-danger">Si</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @foreach ($user->roles as $role)
                                                            <span class="badge bg-primary">{{ $role->role->role }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Acciones
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="actions">
                                                                <li><a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">Editar</a></li>
                                                                <li><a class="dropdown-item" href="#" onclick="ChangePass({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#chgPassModal">Cambiar Contraseña</a></li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="#" onclick="chgStatus({{ $user->id }},0)">Desactivar</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>                                        
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-2" role="tabpanel">                                
                                <div class="table-responsive mt-3">
                                    <table id="inactive_users" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Correo</th>
                                                <th>Restringido</th>
                                                <th>Roles</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inactive_users as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if ($user->restricted == 0)
                                                            <span class="badge bg-secondary">No</span>
                                                        @else
                                                            <span class="badge bg-danger">Si</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @foreach ($user->roles as $role)
                                                            <span class="badge bg-primary">{{ $role->role->role }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Acciones
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="actions">
                                                                <li><a class="dropdown-item" href="#" onclick="chgStatus({{ $user->id }},1)">Activar</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>                                        
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        
                    </div>
                </div>
            </div>
        </div>

    </div>

    <x-modals.chg_pass />

    <x-modals.whitelist_ip :valid_ips="$valid_ips" />
@endsection