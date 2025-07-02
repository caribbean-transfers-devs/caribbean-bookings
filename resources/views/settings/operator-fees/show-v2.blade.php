@extends('layout.app')
@section('title') Vehiculos @endsection

@push('Css')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="{{ mix('/assets/css/sections/settings/operator-fee.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/operator-fee.min.css') }}" rel="stylesheet" >
    <style>
.timeline {
        position: relative;
        padding-left: 2rem;
        list-style: none;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 2rem;
    }
    
    .timeline-item-first {
        padding-top: 0.5rem;
    }
    
    .timeline-badge {
        position: absolute;
        left: -2.5rem;
        top: 0;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        z-index: 1;
    }
    
    .timeline-card {
        position: relative;
        border-left: 3px solid #e9ecef;
        border-radius: 0.35rem;
    }
    
    .timeline-card:before {
        content: '';
        position: absolute;
        left: -3px;
        top: 1.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #4e73df;
    }
    
    pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.35rem;
        font-size: 0.85rem;
    }        
    </style>
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/drivers.min.js') }}"></script>    
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Historial de Cambios: {{ $operatorFee->name }}</h5>
                            <a href="{{ route('operator-fees.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-uppercase text-muted mb-3">Datos Actuales</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Nombre</small></p>
                                                <p class="fw-semibold">{{ $operatorFee->name }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Importe Base</small></p>
                                                <p class="fw-semibold">${{ number_format($operatorFee->base_amount, 2) }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">% Comisión</small></p>
                                                <p class="fw-semibold">{{ $operatorFee->commission_percentage }}%</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Comisión</small></p>
                                                <p class="fw-semibold">${{ number_format($operatorFee->calculateCommission(), 2) }}</p>
                                            </div>
                                            <div class="col-12">
                                                <p class="mb-2"><small class="text-muted">Zonas Asignadas</small></p>
                                                <div class="tag-container">
                                                    @foreach($operatorFee->zone_ids as $zoneId)
                                                        @php
                                                            $zone = $allZones->firstWhere('id', $zoneId);
                                                        @endphp
                                                        @if($zone)
                                                            <span class="tag tag-{{ $zone['type'] }}">
                                                                {{ $zone['name'] }}
                                                                <span class="tag-enterprise">{{ $zone['enterprise'] }}</span>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-uppercase text-muted mb-3">Estadísticas</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Total Cambios</small></p>
                                                <p class="fw-semibold">{{ $logs->count() }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Último Cambio</small></p>
                                                <p class="fw-semibold">{{ $logs->first()->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Creado por</small></p>
                                                <p class="fw-semibold">{{ $logs->last()->user->name ?? 'Sistema' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-2"><small class="text-muted">Fecha Creación</small></p>
                                                <p class="fw-semibold">{{ $operatorFee->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="mb-4">Registro de Cambios</h5>
                        
                        <div class="timeline">
                            @foreach($logs as $log)
                            <div class="timeline-item {{ $loop->first ? 'timeline-item-first' : '' }}">
                                <div class="timeline-badge bg-{{ $log->action == 'create' ? 'success' : ($log->action == 'update' ? 'primary' : 'danger') }}">
                                    <i class="fas fa-{{ $log->action == 'create' ? 'plus' : ($log->action == 'update' ? 'pencil-alt' : 'trash') }}"></i>
                                </div>
                                <div class="timeline-card card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h6 class="mb-0">
                                                @if($log->action == 'create')
                                                    Creación de Registro
                                                @elseif($log->action == 'update')
                                                    Actualización de Datos
                                                @else
                                                    Eliminación de Registro
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-2"><small class="text-muted">Realizado por:</small> {{ $log->user->name ?? 'Sistema' }}</p>
                                        
                                        <p class="mb-3">{{ $log->notes }}</p>
                                        
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#logDetails{{ $log->id }}" aria-expanded="false">
                                            <i class="fas fa-chevron-down me-1"></i> Detalles Técnicos
                                        </button>
                                        
                                        <div class="collapse mt-3" id="logDetails{{ $log->id }}">
                                            <div class="row">
                                                @if($log->old_data)
                                                <div class="col-md-6">
                                                    <div class="card bg-light">
                                                        <div class="card-body">
                                                            <h6 class="card-title text-muted mb-2">Datos Anteriores</h6>
                                                            <pre class="mb-0"><code>{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                @if($log->new_data)
                                                <div class="col-md-6">
                                                    <div class="card bg-light">
                                                        <div class="card-body">
                                                            <h6 class="card-title text-muted mb-2">Datos Nuevos</h6>
                                                            <pre class="mb-0"><code>{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection