@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.master')
@section('title') Roles @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/views/rolesIndex.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Roles</h1>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Roles</h5>
                        <div class="card-header d-flex justify-content-end align-items-center gap-3">   
                            @if(RoleTrait::hasPermission(7))                         
                                <a href="{{ route('roles.create') }}" class="btn btn-success">Añadir Rol</a>   
                            @endif                                                    
                        </div>                       
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table id="roles_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->role }}</td>                                            
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="actions">
                                                        @csrf
                                                        @if(RoleTrait::hasPermission(8)) 
                                                            <li><a class="dropdown-item" href="{{ route('roles.edit', $role->id) }}">Editar</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                        @endif
                                                        @if(RoleTrait::hasPermission(9))
                                                            <li><a class="dropdown-item" href="#" onclick="DelRole({{ $role->id }})">Eliminar</a></li>
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

@endsection