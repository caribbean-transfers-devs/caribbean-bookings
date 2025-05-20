@extends('layout.app')
@section('title') Usuarios @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/users.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/users.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/users.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-four">
                <div class="widget-heading">
                    <div class="d-flex gap-3">
                        @if(auth()->user()->hasPermission(2))
                            <a href="{{ route('users.create') }}" class="btn btn-success">Añadir Usuario</a>
                        @endif
                        @if(auth()->user()->hasPermission(5))
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
                            <table id="active_users" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Correo</th>
                                        <th class="text-center">Restringido</th>
                                        <th class="text-center">Como comisiona</th>
                                        <th class="text-center">Metas o porcentage</th>
                                        <th class="text-center">Es externo</th>
                                        <th class="text-center">Roles</th>                                        
                                        <th class="text-center">Acciones</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($active_users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $user->restricted == 0 ? 'success' : 'danger' }}">{{ $user->restricted == 0 ? 'No' : 'Sí' }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if ( $user->is_commission == 1 )
                                                    <span class="badge bg-primary">{{ $user->type_commission == "target" ? "Metas" : "Porcentaje" }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $user->is_commission == 1 )
                                                    @if ( $user->type_commission == "target" )
                                                        @if ( optional($user->target)->object )
                                                            @foreach ($user->target->object as $item)
                                                                <p>$ {{ number_format($item['amount'], 2) }} => {{ $item['percentage'] }}%</p>
                                                            @endforeach
                                                        @endif                                                    
                                                    @else
                                                        {{ $user->percentage }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ( $user->is_commission == 1 )
                                                    <span class="badge bg-{{ $user->is_external == 0 ? 'danger' : 'success' }}">{{ $user->is_external == 0 ? 'No' : 'Sí' }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column gap-2">
                                                    @foreach ($user->roles as $role)
                                                        <span class="badge bg-primary w-100">{{ $role->role->role }}</span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column gap-2">
                                                    @if(auth()->user()->hasPermission(3))
                                                        <a type="button" class="btn btn-primary w-100" href="{{ route('users.edit', $user->id) }}">Editar</a>
                                                        <button type="button" class="btn btn-info w-100" onclick="ChangePass({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#chgPassModal">Actualizar contraseña</button>
                                                    @endif

                                                    @if(auth()->user()->hasPermission(4))
                                                        <button type="button" class="btn btn-danger w-100" onclick="chgStatus({{ $user->id }},0)" >Desactivar usuario</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Links de paginación -->
                            <div>
                                {{-- {{ $active_users->links() }} --}}
                            </div>
                        </div>                       
                        <div class="tab-pane fade" id="tab-2" role="tabpanel">
                            <table id="inactive_users" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
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
                                                <span class="badge bg-{{ $user->restricted == 0 ? 'success' : 'danger' }}">{{ $user->restricted == 0 ? 'No' : 'Sí' }}</span>
                                            </td>
                                            <td>
                                                @foreach ($user->roles as $role)
                                                    <span class="badge bg-primary">{{ $role->role->role }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-primary">Acciones</button>
                                                    <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                        <span class="visually-hidden ">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="actions">
                                                        @if(auth()->user()->hasPermission(3)) 
                                                            <li><a class="dropdown-item" href="{{ route('users.edit', $user->id) }}">Editar</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                        @endif
                                                        @if(auth()->user()->hasPermission(4)) 
                                                            <li><a class="dropdown-item" href="#" onclick="chgStatus({{ $user->id }},1)">Activar</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>                                        
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- Links de paginación -->
                            <div>
                                {{-- {{ $inactive_users->links() }} --}}
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