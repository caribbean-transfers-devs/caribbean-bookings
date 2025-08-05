@extends('layout.app')
@section('title') Vehiculos @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/drivers.min.js') }}"></script>
@endpush

@section('content')
    <div class="account-settings-container layout-top-spacing">
        <div class="account-content">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="alert alert-icon-left alert-light-primary alert-dismissible fade show mb-4" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <strong>Información</strong> Solo las empresas que sean de tipo <strong>"PROVEEDOR"</strong>, tienen permitido agregarle un conductor.
                    </div>

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
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('danger'))
                                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                                    {{ session('danger') }}
                                </div>
                            @endif
                                                        
                            <form action="{{ isset($driver) ? route('drivers.update', $driver->id) : route('drivers.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($driver) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-4">
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
                                    {{-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vehicle_id">Selecciona un vehículo</label>
                                            <select id="vehicle_id" name="vehicle_id" class="form-control mb-3">
                                                <option value="">Elige una opción</option>
                                                @foreach ($units as $unit)
                                                    <option {{ isset($driver->vehicle_id) && $driver->vehicle_id == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Selecciona el estatus</label>
                                            <select id="status" name="status" class="form-control mb-3">
                                                <option {{ isset($driver->status) && $driver->status == 1 ? 'selected' : '' }} value="1">Activo</option>
                                                <option {{ isset($driver->status) && $driver->status == 0 ? 'selected' : '' }} value="0">Inactivo</option>
                                            </select>
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