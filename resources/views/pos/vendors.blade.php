@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.master')
@section('title') Vendedores @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/pos/vendors.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/pos/vendors.min.css') }}" rel="stylesheet"> 
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="{{ mix('assets/js/views/pos/vendors.min.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="top_items">
            <h1 class="h3">Vendedores</h1>
            <div>   
                @if(RoleTrait::hasPermission(57))                         
                    <button onclick="openVendorModal('create')" class="btn btn-success">Añadir Vendedor</button>   
                @endif                    
            </div>   
        </div>        

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">                               
                        <table id="vendors" class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                    <th>Status</th>
                                    @if( RoleTrait::hasPermission(55) || RoleTrait::hasPermission(56) )
                                        <th>Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vendors as $vendor)
                                    <tr>
                                        <td>{{ $vendor->name }}</td>
                                        <td>{{ $vendor->email }}</td>
                                        <td>{{ $vendor->phone }}</td>
                                        <td>
                                            @if ($vendor->status)
                                                <span class="badge bg-primary">Activo</span>
                                            @else
                                                <span class="badge bg-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        @if( RoleTrait::hasPermission(55) || RoleTrait::hasPermission(56) )
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="actions">
                                                        @if(RoleTrait::hasPermission(55)) 
                                                            <li><a class="dropdown-item" onclick="openVendorModal('edit', {
                                                                id: '{{$vendor->id}}',
                                                                name: '{{$vendor->name}}',
                                                                email: '{{$vendor->email}}',
                                                                phone: '{{$vendor->phone}}',
                                                                status: '{{$vendor->status}}',
                                                            })" href="#">Editar</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                        @endif
                                                        @if(RoleTrait::hasPermission(55)) 
                                                            <li><a class="dropdown-item toogle-status" href="#">
                                                                <form>
                                                                    @csrf
                                                                    <input type="hidden" name="id" value="{{ $vendor->id }}">
                                                                    <input type="hidden" name="name" value="{{ $vendor->name }}">
                                                                    <input type="hidden" name="email" value="{{ $vendor->email }}">
                                                                    <input type="hidden" name="phone" value="{{ $vendor->phone }}">
                                                                    <input type="hidden" name="status" value="{{ $vendor->status ? '0' : '1' }}">
                                                                </form>
                                                                <span>{{ $vendor->status ? 'Desactivar' : 'Activar' }}</span>
                                                            </a></li>
                                                        @endif
                                                        @if(RoleTrait::hasPermission(56)) 
                                                        <li><a class="dropdown-item delete-vendor" href="#">
                                                            <form>
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ $vendor->id }}">
                                                            </form>
                                                            <span>Eliminar</span>
                                                        </a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>                                        
                                @endforeach
                            </tbody>
                        </table>  
                    </div>
                </div>
            </div>
        </div>

        <x-modals.create_vendor />

    </div>
@endsection