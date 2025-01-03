@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings_vehicles.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings_vehicles.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/vehicles.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar un vehículo',
                'className' => 'btn btn-primary __btn_create',
                'url' => route('vehicles.create')
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
                
                <table id="dataVehicles" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Empresa</th>
                            <th class="text-center">Servicio</th>
                            <th class="text-center">Destino</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Código de la unidad</th>
                            <th class="text-center">Número de placa</th>
                            <th class="text-center">Estatus</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vehicles as $vehicle)
                            <tr>
                                <td class="text-center">{{ $vehicle->enterprise->names }}</td>
                                <td class="text-center">{{ $vehicle->destination_service->name }}</td>
                                <td class="text-center">{{ $vehicle->destination->name }}</td>
                                <td class="text-center">{{ $vehicle->name }}</td>
                                <td class="text-center">{{ $vehicle->unit_code }}</td>
                                <td class="text-center">{{ $vehicle->plate_number }}</td>
                                <td class="text-center"><button class="btn btn-{{ $vehicle->status == 1 ? 'success' : 'warning' }}">{{ $vehicle->status == 1 ? 'Activo' : 'inactivo' }}</button></td>
                                <td class="text-center">
                                    <div class="d-flex gap-3">
                                        <a class="btn btn-primary" href="{{ route('vehicles.edit', [$vehicle->id]) }}">Editar</a>
                                        <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>                                        
                        @endforeach
                    </tbody>
                </table>
            </div>   
        </div>
    </div>
@endsection