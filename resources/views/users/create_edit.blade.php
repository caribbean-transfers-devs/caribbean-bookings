@extends('layout.master')
@section('title') @if($v_type == 1) Crear @else Editar @endif Usuarios @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
    <script>
        const choices = new Choices(document.getElementById('roles'),{
            removeItemButton: true,
            loadingText: 'Cargando...',
            noResultsText: 'No se encontraron resultados',
            noChoicesText: 'No hay opciones para elegir',
            itemSelectText: 'Clic para elegir',
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">@if($v_type == 1) Crear @else Editar @endif Usuarios</h1>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                                               
                    </div>
                    <div class="card-body">
                        <form id="frm_user">
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
                                        <label for="password_confirmation " class="form-label">Confirmar Contraseña</label>
                                        <input type="password" class="form-control" id="password_confirmation " name="password_confirmation " required>
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
                                    <label for="email" class="form-label">Roles</label>
                                    <select class="form-select" id="roles" name="roles[]" multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @if (in_array($role->id, $user->roles->toArray())) selected @endif>{{ $role->role }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 

                        </form>   
                        
                        <button class="btn btn-success">@if ($v_type == 1)
                            Crear
                        @else
                            Editar
                        @endif Usuario</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection