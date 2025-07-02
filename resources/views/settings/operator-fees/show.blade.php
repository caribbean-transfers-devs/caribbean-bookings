@extends('layout.app')
@section('title') Historial De Cambios @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/operator-fee-history.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/operator-fee-history.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script>
        // resources/js/sections/settings/operator-fee-history.js
        document.addEventListener('DOMContentLoaded', function() {
            // Mejorar la experiencia de los detalles
            const detailButtons = document.querySelectorAll('.btn-details');
            
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const target = this.getAttribute('data-bs-target');
                    const collapse = document.querySelector(target);
                    
                    // Scroll suave al contenido expandido
                    if (!collapse.classList.contains('show')) {
                        setTimeout(() => {
                            collapse.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 300);
                    }
                });
            });
            
            // Resaltar filas al pasar el mouse
            const historyRows = document.querySelectorAll('.history-row');
            
            historyRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(52, 152, 219, 0.03)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>    
@endpush

@section('content')
    <div class="operator-fee-history layout-top-spacing">
        <div class="history-header">
            <h4 class="history-title mb-0">
                <i class="fas fa-history"></i> Historial de Cambios: {{ $operatorFee->name }}
                <a href="{{ route('operator-fees.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Volver al listado
                </a>
            </h4>
        </div>

        <div class="dashboard-grid">
            <!-- Panel de Datos Actuales -->
            <div class="dashboard-card summary-card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Datos Actuales</h2>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <span class="summary-label">Nombre:</span>
                        <span class="summary-value">{{ $operatorFee->name }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Importe Base:</span>
                        <span class="summary-value">${{ number_format($operatorFee->base_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">% Comisión:</span>
                        <span class="summary-value">{{ $operatorFee->commission_percentage }}%</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Comisión:</span>
                        <span class="summary-value">${{ number_format($operatorFee->calculateCommission(), 2) }}</span>
                    </div>
                    <div class="summary-item zones-container">
                        <span class="summary-label">Zonas Asignadas:</span>
                        <div class="zones-list">
                            @foreach($operatorFee->zone_ids as $zoneId)
                                @php
                                    $zone = $allZones->firstWhere('id', $zoneId);
                                @endphp
                                @if($zone)
                                    <span class="zone-tag {{ $zone['type'] }}">
                                        {{ $zone['name'] }}
                                        <span class="zone-enterprise">{{ $zone['enterprise'] }}</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Estadísticas -->
            <div class="dashboard-card stats-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Estadísticas</h2>
                </div>
                <div class="card-body">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value">{{ $logs->count() }}</div>
                            <div class="stat-label">Total Cambios</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $logs->first()->created_at->format('d/m/Y H:i') }}</div>
                            <div class="stat-label">Último Cambio</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $logs->last()->user->name ?? 'Sistema' }}</div>
                            <div class="stat-label">Creado por</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $operatorFee->created_at->format('d/m/Y Himi') }}</div>
                            <div class="stat-label">Fecha Creación</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel de Historial -->
            <div class="dashboard-card history-table-card">
                <div class="card-header">
                    <h2><i class="fas fa-list-alt"></i> Registro de Cambios</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-container">
                        <table class="history-table">
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
                                <tr class="history-row">
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'Sistema' }}</td>
                                    <td>
                                        <span class="action-badge {{ $log->action }}">
                                            @if($log->action == 'create')
                                                Creación
                                            @elseif($log->action == 'update')
                                                Actualización
                                            @else
                                                Eliminación
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-details" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#logDetails{{ $log->id }}" aria-expanded="false">
                                            <i class="fas fa-chevron-down"></i> Detalles
                                        </button>
                                    </td>
                                </tr>
                                <tr class="details-row">
                                    <td colspan="4">
                                        <div class="collapse details-collapse" id="logDetails{{ $log->id }}">
                                            <div class="details-content">
                                                <div class="details-notes">
                                                    <h4>Notas del Cambio</h4>
                                                    <p>{{ $log->notes }}</p>
                                                </div>
                                                
                                                @if($log->old_data || $log->new_data)
                                                <div class="details-data">
                                                    @if($log->old_data)
                                                    <div class="data-section">
                                                        <h4><i class="fas fa-arrow-left"></i> Datos Anteriores</h4>
                                                        <pre>{{ json_encode($log->old_data, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                    @endif
                                                    
                                                    @if($log->new_data)
                                                    <div class="data-section">
                                                        <h4><i class="fas fa-arrow-right"></i> Datos Nuevos</h4>
                                                        <pre>{{ json_encode($log->new_data, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection