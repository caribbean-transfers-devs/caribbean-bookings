@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Roles @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/vehicle.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/vehicle.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/roles.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
        if(RoleTrait::hasPermission(7)):
            array_push($buttons,array(
                'text' => 'Añadir Rol',
                'className' => 'btn btn-primary __btn_create',
                'url' => route('roles.create')            
            ));
        endif;
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-8">
                <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
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
                                    <div class="btn-group mb-2 me-4">
                                        <button type="button" class="btn btn-primary">Acciones</button>
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                            <span class="visually-hidden ">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu">
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
@endsection