@extends('layout.master')
@section('title') Zonas @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/zones/index.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/zones/index.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=drawing" async defer></script>
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="{{ mix('/assets/js/views/zones/index.min.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Reservaciones</h1>
        <div class="card">                    
            <form class="card-body search-container" action="" method="GET" id="zoneForm">
                <div>
                    <select class="form-control" id="destinationID">
                        <option value="1">Cancún</option>
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary btn-sm" type="button" id="btnSendZone">Buscar</button>
                </div>
            </form>
        </div>
        @if(isset($zones))
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="card">                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="zones_table" class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Nombre de zona</th>
                                        <th class="text-center">Primario</th>
                                        <th class="text-center">Estatus</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($zones) >= 1)
                                        @foreach($zones as $key => $value)
                                            <tr>
                                                <td>{{ $value->name }}</td>
                                                <td class="text-center">{{ $value->is_primary }}</td>
                                                <td class="text-center">{{ $value->status }}</td>
                                                <td class="text-center">
                                                    <!--<button class="btn btn-secondary btn-sm" disabled>Editar</button>-->
                                                    <button class="btn btn-primary btn-sm" onclick="getPoints(event, {{ $value->destination_id }}, {{ $value->id }} )"><i class="fas fa-fw fa-map-marker"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

<x-zones.map/>
@endsection