@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Vehiculos @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/driver.min.js') }}"></script>
@endpush

@section('content')
    <div class="account-settings-container layout-top-spacing">
        <div class="account-content">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="info">
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

                            @if (session('success'))
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">a     
                                    {{ session('success') }}
                                </div>
                            @endif
                            <form action="{{ isset($driver) ? route('drivers.update', $driver->id) : route('drivers.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($driver) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="enterprise_id">Selecciona una empresa</label>
                                            <select id="enterprise_id" name="enterprise_id" class="form-control mb-3">
                                                @foreach ($enterprises as $enterprise)
                                                    <option {{ ( isset($driver->enterprise_id) && $driver->enterprise_id == $enterprise->id ? 'selected' : '' ) }} value="{{ $enterprise->id }}">{{ $enterprise->names }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="names">Nombres</label>
                                            <input type="text" id="names" name="names" class="form-control mb-3" placeholder="Nombres" value="{{ ( isset($driver->names) ? $driver->names : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="surnames">Apellidos</label>
                                            <input type="text" id="surnames" name="surnames" class="form-control mb-3" placeholder="Apellidos" value="{{ ( isset($driver->surnames) ? $driver->surnames : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="phone">Teléfono</label>
                                            <input type="text" id="phone" name="phone" class="form-control mb-3" placeholder="Teléfono" value="{{ ( isset($driver->phone) ? $driver->phone : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('drivers.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($driver) ? 'Actualizar' : 'Guardar' ) }}</button>
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