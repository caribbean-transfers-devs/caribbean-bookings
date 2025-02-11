@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/enterprises.min.js') }}"></script>
@endpush

@section('content')
    <div class="account-settings-container layout-top-spacing">
        <div class="account-content">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="info">
                            @if ($errors->any())
                                <div class="alert alert-light-primary alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert"> 
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('danger'))
                                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                                    {{ session('danger') }}
                                </div>
                            @endif

                            <form action="{{ isset($enterprise) ? route('enterprises.update', $enterprise->id) : route('enterprises.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($enterprise) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="names">Nombre de la empresa</label>
                                            <input type="text" id="names" name="names" class="form-control mb-3" placeholder="Nombre de la empresa" value="{{ ( isset($enterprise->names) ? $enterprise->names : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="is_external">Selecciona si es interno o externo</label>
                                            <select id="is_external" name="is_external" class="form-control mb-3">
                                                <option {{ ( isset($enterprise->is_external) && $enterprise->is_external == 0 ) ? 'selected' : '' }} value="0">Interno</option>
                                                <option {{ ( isset($enterprise->is_external) && $enterprise->is_external == 1 ) ? 'selected' : '' }} value="1">Externo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="is_external">Selecciona estatus</label>
                                            <select id="is_external" name="status" class="form-control mb-3">
                                                <option {{ ( isset($enterprise->status) && $enterprise->status == 1 ) ? 'selected' : '' }} value="1">Activo</option>
                                                <option {{ ( isset($enterprise->status) && $enterprise->status == 0 ) ? 'selected' : '' }} value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="is_external">Selecciona tipo de empresa</label>
                                            <select id="is_external" name="type_enterprise" class="form-control mb-3">
                                                <option {{ ( isset($enterprise->type_enterprise) && $enterprise->type_enterprise == "PROVIDER" ) ? 'selected' : '' }} value="PROVIDER">Proveedor</option>
                                                <option {{ ( isset($enterprise->type_enterprise) && $enterprise->type_enterprise == "CUSTOMER" ) ? 'selected' : '' }} value="CUSTOMER">Cliente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('enterprises.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($enterprise) ? 'Actualizar' : 'Guardar' ) }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection