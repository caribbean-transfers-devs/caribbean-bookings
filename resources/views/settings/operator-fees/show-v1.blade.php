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
    <h1>Historial de Cambios: {{ $operatorFee->name }}</h1>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Datos Actuales</h5>
            <p><strong>Importe Base:</strong> ${{ number_format($operatorFee->base_amount, 2) }}</p>
            <p><strong>% Comisión:</strong> {{ $operatorFee->commission_percentage }}%</p>
            <p><strong>Comisión Calculada:</strong> ${{ number_format($operatorFee->calculateCommission(), 2) }}</p>
            <p><strong>Zonas:</strong> {{ implode(', ', $operatorFee->zone_ids ?? []) }}</p>
        </div>
    </div>
    
    <h3>Historial de Modificaciones</h3>
    
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                    <td>
                        @if($log->action == 'create')
                            <span class="badge bg-success">Creación</span>
                        @elseif($log->action == 'update')
                            <span class="badge bg-primary">Actualización</span>
                        @else
                            <span class="badge bg-danger">Eliminación</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}" aria-expanded="false"> 
                            Ver detalles
                        </button>
                        
                        <div class="collapse mt-2" id="logDetails{{ $log->id }}">
                            <div class="card card-body">
                                <h6>Notas:</h6>
                                <p>{{ $log->notes }}</p>
                                
                                @if($log->old_data)
                                <h6 class="mt-3">Datos anteriores:</h6>
                                <pre>{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                                
                                @if($log->new_data)
                                <h6 class="mt-3">Datos nuevos:</h6>
                                <pre>{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <a href="{{ route('operator-fees.index') }}" class="btn btn-secondary">Volver</a>    
@endsection