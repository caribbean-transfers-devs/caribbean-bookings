@extends('layout.master')
@section('title') Usuarios @endsection

@push('up-stack')
@endpush

@push('bootom-stack')
    <script>
        $(function() {
            $('#active_users','#inactive_users').DataTable({
                language: {
                    url: '/assets/vendor/datatables/es.json'
                }
            });
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Usuarios</h1>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Usuarios</h5>
                            <a href="{{ route('users.create') }}" class="btn btn-success">Añadir Usuario</a>
                        </div>                        
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab" aria-selected="true">Usuarios Activos</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab" aria-selected="false" tabindex="-1">Usuarios Inactivos</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-1" role="tabpanel">
                                <div class="table-responsive">
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
                                                    <td></td>
                                                    <td></td>
                                                </tr>                                        
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-2" role="tabpanel">                                
                                <div class="table-responsive">
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
                                                    <td></td>
                                                    <td></td>
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
@endsection