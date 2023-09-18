@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.master')
@section('title') Usuarios @endsection

@push('up-stack')    
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="{{ mix('assets/js/views/userIndex.js') }}"></script>
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
                            @if(RoleTrait::hasPermission(2))                         
                                <a href="{{ route('users.create') }}" class="btn btn-success">Añadir Usuario</a>   
                            @endif
                            @if(RoleTrait::hasPermission(5)) 
                                <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#whiteIPsModal">Ver IPs</a>   
                            @endif                      
                        </div>                       
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab" aria-selected="true">Usuarios Activos</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab" aria-selected="false" tabindex="-1">Usuarios Inactivos</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show mt-3" id="tab-1" role="tabpanel">                                
                                <table id="active_users" class="table table-striped table-sm">
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
                                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                            Acciones
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="actions">
                                                            @if(RoleTrait::hasPermission(3)) 
                                                                <li><a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">Editar</a></li>
                                                                <li><a class="dropdown-item" href="#" onclick="ChangePass({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#chgPassModal">Contraseña</a></li>
                                                                <li><hr class="dropdown-divider"></li>
                                                            @endif
                                                            @if(RoleTrait::hasPermission(4)) 
                                                                <li><a class="dropdown-item" href="#" onclick="chgStatus({{ $user->id }},0)">Desactivar</a></li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>                                        
                                        @endforeach
                                    </tbody>
                                </table>                                
                            </div>
                            <div class="tab-pane mt-3" id="tab-2" role="tabpanel">                                
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
                                                        <button class="btn btn-secondary dropdown-toggle button-sm" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                            Acciones
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="actions">
                                                            @if(RoleTrait::hasPermission(3)) 
                                                                <li><a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">Editar</a></li>
                                                                <li><hr class="dropdown-divider"></li>
                                                            @endif
                                                            @if(RoleTrait::hasPermission(4)) 
                                                                <li><a class="dropdown-item" href="#" onclick="chgStatus({{ $user->id }},1)">Activar</a></li>
                                                            @endif
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

    <x-modals.chg_pass />

    <x-modals.whitelist_ip :valid_ips="$valid_ips" />
@endsection