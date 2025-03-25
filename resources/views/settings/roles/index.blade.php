@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Administración De Roles @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/roles.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/roles.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/roles.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
        if(auth()->user()->hasPermission(7)):
            array_push($buttons,array(
                'text' => 'Añadir Rol',
                'className' => 'btn btn-primary ',
                'url' => route('roles.create')
            ));
        endif;
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
                                    <div class="d-flex flex-column gap-2">
                                        @if(auth()->user()->hasPermission(8)) 
                                            <a type="button" class="btn btn-primary w-100" href="{{ route('roles.edit', $role->id) }}">Editar</a>
                                        @endif

                                        @if(auth()->user()->hasPermission(9)) 
                                            <button type="button" class="btn btn-danger w-100" onclick="DelRole({{ $role->id }})">Eliminar</button>
                                        @endif
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