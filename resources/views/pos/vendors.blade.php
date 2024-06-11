@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Vendedores @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/pos/sellers.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
        );
        if(RoleTrait::hasPermission(57)):
            array_push($buttons,
                array(  
                    'text' => 'Añadir vendedor',
                    'className' => 'btn btn-primary __btn_create',
                    'attr' => array(
                        'data-title' =>  "Crear vendedor",
                        'data-ction' =>  "create",
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#vendorModal'
                    )
                ),        
            );
        endif;
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
                @if ($errors->any())
                    <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
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
                                <td>{{ $vendor->email ?? 'No se registró' }}</td>
                                <td>{{ $vendor->phone ?? 'No se registró' }}</td>
                                <td>
                                    @if ($vendor->status)
                                        <span class="badge badge-light-success mb-2 me-4">Activo</span>
                                    @else
                                        <span class="badge badge-light-danger mb-2 me-4">Inactivo</span>
                                    @endif
                                </td>
                                @if( RoleTrait::hasPermission(55) || RoleTrait::hasPermission(56) )
                                    <td>
                                        <div class="btn-group mb-2 me-4">
                                            <button type="button" class="btn btn-primary">Acciones</button>
                                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                <span class="visually-hidden ">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="actions">
                                                @if(RoleTrait::hasPermission(55)) 
                                                    <li><a class="dropdown-item __btn_update" data-bs-toggle="modal" data-bs-target="#vendorModal" data-title="Editar vendedor" data-id="'{{$vendor->id}}'" data-name="'{{$vendor->name}}'"
                                                        data-email="'{{$vendor->email}}'"
                                                        data-phone="'{{$vendor->phone}}'"
                                                        data-status="'{{$vendor->status}}'"
                                                    href="#">Editar</a></li>
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

    <x-modals.create_vendor />
@endsection