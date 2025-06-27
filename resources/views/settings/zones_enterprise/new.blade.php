@extends('layout.app')
@section('title') Editar zona @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    
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
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert"> 
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('danger'))
                                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                                    {{ session('danger') }}
                                </div>
                            @endif

                            <form action="{{ !isset($zone) ? route('enterprises.zones.store', [( isset($enterprise->id) ? $enterprise->id : 0 )]) : route('enterprises.zones.update', [( isset($zone->id) ? $zone->id : 0 )]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($zone) )
                                    @method('PUT')
                                @endif

                                <div class="row">                                            
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="destinationID">Selecciona destino</label>
                                            <select name="destination_id" class="form-control" id="destinationID">
                                                @if (sizeof($destinations) >= 1)
                                                    @foreach ($destinations as $destination)
                                                        <option {{ old('destination_id', $zone->destination_id ?? '') == $destination->id ? 'selected' : '' }} value="{{ $destination->id }}">{{ $destination->name }}</option>
                                                    @endforeach                                        
                                                @endif                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Nombre de zona</label>
                                            <input type="text" id="name" name="name" class="form-control mb-3" placeholder="Nombre" value="{{ old('name', $zone->name ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="is_primary">Es primario (aplica cuando es aereopuerto)</label>
                                            <select name="is_primary" id="is_primary" class="form-control mb-3">
                                                <option {{ old('is_primary', $zone->is_primary ?? '') == '1' ? 'selected' : '' }} value="1">Sí</option>
                                                <option {{ old('is_primary', $zone->is_primary ?? '') == '0' ? 'selected' : '' }} value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="iata_code">IATA (aplica cuando es aereopuerto)</label>
                                            <input type="text" id="iata_code" name="iata_code" class="form-control mb-3" placeholder="IATA" value="{{ old('iata_code', $zone->iata_code ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cut_off">Cut off</label>
                                            <input type="number" id="cut_off" name="cut_off" step="0.01" min="0" max="10000" class="form-control mb-3" placeholder="Cut off" value="{{ old('cut_off', $zone->cut_off ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="distance">Distancia</label>
                                            <input type="text" id="distance" name="distance" class="form-control mb-3" placeholder="Distancia" value="{{ old('distance', $zone->distance ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="time">Tiempo</label>
                                            <input type="text" id="time" name="time" class="form-control mb-3" placeholder="Tiempo" value="{{ old('time', $zone->time ?? '') }}">
                                        </div>
                                    </div>                                    
                                    

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Estatus</label>
                                            <select name="status" id="status" class="form-control mb-3">
                                                <option {{ old('status', $zone->status ?? '') == '1' ? 'selected' : '' }} value="1">Sí</option>
                                                <option {{ old('status', $zone->status ?? '') == '0' ? 'selected' : '' }} value="0">No</option>                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('enterprises.zones.index', [( isset($enterprise->id) ? $enterprise->id : $zone->enterprise_id )]) }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( !isset($zone) ? 'Guardar' : 'Actualizar' ) }}</button>
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