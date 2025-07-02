@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Listado De Costo Operativo @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/drivers.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/drivers.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/drivers.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar costo operativo',
                'className' => 'btn btn-primary __btn_create',
                'url' => route('operator-fees.create')
            )
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-8">
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
                
                <table id="dataDrivers" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Zona</th>
                            <th class="text-center">Importe</th>
                            <th class="text-center">% Comisión</th>
                            <th class="text-center">Comisión</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fees as $fee)
                            <tr>
                                <td class="text-center">{{ $fee->name }}</td>
                                <td class="text-center">${{ number_format($fee->base_amount, 2) }}</td>
                                <td class="text-center">{{ $fee->commission_percentage }}%</td>
                                <td class="text-center">${{ number_format($fee->commission, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('operator-fees.show', $fee->id) }}" class="btn btn-sm btn-primary">Logs</a>
                                    <a href="{{ route('operator-fees.edit', $fee->id) }}" class="btn btn-sm btn-warning">Editar</a>
                                    <form action="{{ route('operator-fees.destroy', $fee->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>                                        
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection