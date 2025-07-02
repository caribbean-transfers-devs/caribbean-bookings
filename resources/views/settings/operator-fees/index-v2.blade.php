@extends('layout.app')
@section('title') Listado De Costo Operativo @endsection

@push('Css')
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="{{ mix('/assets/css/sections/settings/operator-fee.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/operator-fee.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/drivers.min.js') }}"></script>
@endpush

@section('content')
    <div class="layout-top-spacing">
        <div class="card">
            <div class="card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listado de grupos</h5>
                    <a href="{{ route('operator-fees.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i> Nuevo grupo
                    </a>
                </div>
            </div>
            
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Zona/Grupo</th>
                                <th>Zonas Asignadas</th>
                                <th>Importe</th>
                                <th>% Comisión</th>
                                <th>Comisión</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees as $fee)
                            <tr>
                                <td class="ps-4 align-middle">
                                    <strong>{{ $fee->name }}</strong>
                                </td>
                                <td class="align-middle">
                                    @if($fee->zone_ids)
                                        <div class="zones-list d-flex flex-wrap gap-2">
                                            @foreach($fee->zone_ids as $zoneId)
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
                                    @else
                                        <span class="text-muted">Sin zonas asignadas</span>
                                    @endif
                                </td>
                                <td class="align-middle">${{ number_format($fee->base_amount, 2) }}</td>
                                <td class="align-middle">{{ $fee->commission_percentage }}%</td>
                                <td class="align-middle">${{ number_format($fee->commission, 2) }}</td>
                                <td class="text-end pe-4 align-middle">
                                    <div class="btn-group">
                                        @if ( auth()->user()->hasPermission(131) )
                                            <a href="{{ route('operator-fees.edit', $fee->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>                                            
                                        @endif

                                        @if ( auth()->user()->hasPermission(133) )
                                            <a href="{{ route('operator-fees.show', $fee->id) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-history"></i>
                                            </a>                                        
                                        @endif

                                        @if ( auth()->user()->hasPermission(132) )
                                            <form action="{{ route('operator-fees.destroy', $fee->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($fees->hasPages())
                <div class="card-footer px-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $fees->firstItem() }} a {{ $fees->lastItem() }} de {{ $fees->total() }} registros
                        </div>
                        <div>
                            {{ $fees->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection