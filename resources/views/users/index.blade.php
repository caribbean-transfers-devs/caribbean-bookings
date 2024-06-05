@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Usuarios @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/users.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/users.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/users.min.js') }}"></script>
@endpush

@section('content')
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-four">
                <div class="widget-heading">
                    <div class="d-flex gap-3">
                        @if(RoleTrait::hasPermission(2))
                            <a href="{{ route('users.create') }}" class="btn btn-success">Añadir Usuario</a>   
                        @endif
                        @if(RoleTrait::hasPermission(5))
                            <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#whiteIPsModal">Ver IPs</button>
                        @endif                      
                    </div>                    
                </div>
                <div class="widget-content">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab" aria-selected="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                Usuarios Activos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab" aria-selected="false" tabindex="-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                Usuarios Inactivos
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="tab-1" role="tabpanel">
                            <table id="active_users" class="table table-rendering dt-table-hover">
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
                                                <div class="btn-group mb-2 me-4">
                                                    <button type="button" class="btn btn-primary">Acciones</button>
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                        <span class="visually-hidden ">Toggle Dropdown</span>
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
                        <div class="tab-pane fade" id="tab-2" role="tabpanel">
                            <table id="inactive_users" class="table table-rendering dt-table-hover">
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
                                                <div class="btn-group mb-2 me-4">
                                                    <button type="button" class="btn btn-primary">Acciones</button>
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                        <span class="visually-hidden ">Toggle Dropdown</span>
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

    <x-modals.chg_pass />
    <x-modals.whitelist_ip :valid_ips="$valid_ips" />
@endsection