@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Listado De Costo Operativo @endsection

@push('Css')
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link href="{{ mix('/assets/css/sections/settings/operator-fee-list.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/operator-fee-list.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
@endpush

@section('content')
    <div class="operator-fee-list layout-top-spacin">
        <div class="list-header">
            <h1 class="list-title">
                <i class="fas fa-money-bill-wave"></i> Listado de Grupos de Costo Operativo
            </h1>
            <a href="{{ route('operator-fees.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Nuevo Grupo
            </a>
        </div>

        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
            <button type="button" class="alert-close" data-dismiss="alert" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <div class="list-container">
            <div class="list-toolbar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar grupo...">
                </div>
                <div class="list-stats">
                    Mostrando <strong>{{ $fees->firstItem() }} - {{ $fees->lastItem() }}</strong> de <strong>{{ $fees->total() }}</strong> registros
                </div>
            </div>

            <div class="list-table">
                <table>
                    <thead>
                        <tr>
                            <th class="col-name">Zona/Grupo</th>
                            <th class="col-zones">Zonas Asignadas</th>
                            <th class="col-amount">Importe</th>
                            <th class="col-percent">% Comisión</th>
                            <th class="col-commission">Comisión</th>
                            <th class="col-actions">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fees as $fee)
                        <tr>
                            <td class="col-name">
                                <div class="name-wrapper">
                                    <span class="name">{{ $fee->name }}</span>
                                    <a href="{{ route('operator-fees.show', $fee->id) }}" class="history-link">
                                        <i class="fas fa-history"></i> Historial
                                    </a>
                                </div>
                            </td>
                            <td class="col-zones">
                                @if($fee->zone_ids)
                                    <div class="zones-tags">
                                        @foreach($fee->zone_ids as $zoneId)
                                            @php
                                                $zone = $allZones->firstWhere('id', $zoneId);
                                            @endphp
                                            @if($zone)
                                                <span class="zone-tag {{ $zone['type'] }}">
                                                    {{ $zone['name'] }}
                                                    <span class="enterprise">{{ $zone['enterprise'] }}</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="no-zones">Sin zonas asignadas</span>
                                @endif
                            </td>
                            <td class="col-amount">${{ number_format($fee->base_amount, 2) }}</td>
                            <td class="col-percent">{{ $fee->commission_percentage }}%</td>
                            <td class="col-commission">${{ number_format($fee->commission, 2) }}</td>
                            <td class="col-actions">
                                <div class="actions-wrapper">
                                    <a href="{{ route('operator-fees.edit', $fee->id) }}" class="btn-edit" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('operator-fees.destroy', $fee->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este grupo?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($fees->hasPages())
            <div class="list-pagination">
                {{ $fees->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection