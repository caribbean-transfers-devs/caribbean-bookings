@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Vehiculos @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/vehicles.min.js') }}"></script>
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
                            
                            <form action="{{ isset($vehicle) ? route('vehicles.update', $vehicle->id) : route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($vehicle) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="enterprise_id">Selecciona una empresa</label>
                                            <select id="enterprise_id" name="enterprise_id" class="form-control mb-3">
                                                @foreach ($enterprises as $enterprise)
                                                    <option {{ ( isset($vehicle->enterprise_id) && $vehicle->enterprise_id == $enterprise->id ? 'selected' : '' ) }} value="{{ $enterprise->id }}">{{ $enterprise->names }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="destination_service_id">Selecciona un servicio</label>
                                            <select id="destination_service_id" name="destination_service_id" class="form-control mb-3">
                                                @foreach ($services as $service)
                                                    <option {{ ( isset($vehicle->destination_service_id) && $vehicle->destination_service_id == $service->id ? 'selected' : '' ) }} value="{{ $service->id }}">{{ $service->name }} - {{ $service->destination->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Nombre de la unidad</label>
                                            <input type="text" id="name" name="name" class="form-control mb-3" placeholder="Nombre de la unidad" value="{{ ( isset($vehicle->name) ? $vehicle->name : '' ) }}">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="unit_code">Código de la unidad</label>
                                            <input type="text" id="unit_code" name="unit_code" class="form-control mb-3" placeholder="Código de la unidad" value="{{ ( isset($vehicle->unit_code) ? $vehicle->unit_code : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="plate_number">Número de placa</label>
                                            <input type="text" id="plate_number" name="plate_number" class="form-control mb-3" placeholder="Número de placa" value="{{ ( isset($vehicle->plate_number) ? $vehicle->plate_number : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Selecciona el estatus</label>
                                            <select id="status" name="status" class="form-control mb-3">
                                                <option {{ isset($vehicle->status) && $vehicle->status == 1 ? 'selected' : '' }} value="1">Activo</option>
                                                <option {{ isset($vehicle->status) && $vehicle->status == 0 ? 'selected' : '' }} value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>                                    
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('vehicles.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($vehicle) ? 'Actualizar' : 'Guardar' ) }}</button>
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