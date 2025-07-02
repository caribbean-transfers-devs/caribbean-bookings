@extends('layout.app')
@section('title') Vehiculos @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="stylesheet" > 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/drivers.min.js') }}"></script>    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Selecciona las zonas",
                allowClear: true
            });
        });        
    </script>
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
                                                        
                            <form action="{{ isset($operatorFee) ? route('operator-fees.update', $operatorFee->id) : route('operator-fees.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($operatorFee) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="name">Nombre de la Zona/Grupo:</label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ isset($operatorFee->name) ? $operatorFee->name : '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="zone_ids">Zonas (IDs):</label>
                                            <select name="zone_ids[]" id="zone_ids" class="form-control select2" multiple required>
                                                @foreach($allZones as $zone)
                                                    <option {{ in_array($zone['id'], $operatorFee->zone_ids ?? []) ? 'selected' : '' }} value="{{ $zone['id'] }}" data-type="{{ $zone['type'] }}">
                                                        {{ $zone['name'] }} - {{ $zone['enterprise'] }} - ({{ $zone['type'] == 'internal' ? 'Interno' : 'Cliente' }})
                                                    </option>                                                
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Selecciona las zonas que tendrán este mismo costo operativo.</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="base_amount">Importe Base:</label>
                                            <input type="number" name="base_amount" id="base_amount" class="form-control" step="0.01" min="0" value="{{ isset($operatorFee->base_amount) ? $operatorFee->base_amount : '' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="form-group">
                                            <label for="commission_percentage">% de Comisión:</label>
                                            <input type="number" name="commission_percentage" id="commission_percentage" class="form-control" step="0.01" min="0" max="100" value="{{ isset($operatorFee->commission_percentage) ? $operatorFee->commission_percentage : '' }}" required>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between gap-3">
                                        <a class="btn btn-danger w-50" href="{{ route('operator-fees.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary w-50">{{ ( isset($operatorFee) ? 'Actualizar' : 'Guardar' ) }}</button>
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