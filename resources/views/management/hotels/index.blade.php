@extends('layout.app')
@section('title') Gestión De Hoteles @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management/hotels.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/hotels.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=places"></script>
    <script src="{{ mix('assets/js/sections/management/hotels.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div id="filters" class="accordion">
                <div class="card">
                    <div class="card-header" id="headingOne1">
                        <section class="mb-0 mt-0">
                            <div role="menu" class="" data-bs-toggle="collapse" data-bs-target="#defaultAccordionOne" aria-expanded="true" aria-controls="defaultAccordionOne">
                                Formulario para agregar nuevo hotel <div class="icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                            </div>
                        </section>
                    </div>
                    <div id="defaultAccordionOne" class="collapse show" aria-labelledby="headingOne1" data-bs-parent="#filters">
                        <div class="card-body">
                            <form class="row" method="GET" id="hotelAdd">
                                @csrf
                                <div class="col-12 col-sm-3 mb-2 mb-lg-0">
                                    <label class="form-label" for="destinationID">Selecciona destino</label>
                                    <select name="destinationID" class="form-control" id="destinationID">
                                        <option value="0">Selecciona el destino</option>
                                        @if ( !empty($destinations) )
                                            @foreach ($destinations as $destination)
                                                <option value="{{ $destination->id }}">{{ $destination->name }}</option>  
                                            @endforeach
                                        @endif                                    
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 mb-2 mb-lg-0">
                                    <label class="form-label" for="zoneId">Selecciona una zona</label>
                                    <select name="zoneId" class="form-control" id="zoneId">
                                        <option value="0">Zona</option>
                                    </select>
                                </div>
                                <div class="col-12 col-sm-3 mb-3 mb-lg-0">
                                    <label class="form-label" for="serviceFromForm">Ingrese nombre de hotel</label>
                                    <input type="text" name="from_name" class="form-control" id="serviceFromForm" required>
                                </div>
                                @if ( auth()->user()->hasPermission(124) )
                                    <div class="col-12 col-sm-3 align-self-end">
                                        <button type="button" class="btn btn-primary btn-lg w-100" id="btnAdd">Agregar Hotel</button>
                                    </div>
                                @endif
                                <input type="hidden" name="from_address" id="from_address">
                                <input type="hidden" name="from_lat" id="from_lat">
                                <input type="hidden" name="from_lng" id="from_lng">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
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

                <table id="dataHotels" class="table table-bookings dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">DESTINO</th>
                            <th class="text-center">ZONA</th>
                            <th class="text-center">NOMBRE</th>
                            <th class="text-center">DIRECCION</th>
                            <th class="text-center">LATITUD</th>
                            <th class="text-center">LONGITUD</th>
                            <th class="text-center">VER UBICACION EN MAPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($hotels) >= 1)
                            @foreach ($hotels as $hotel)
                                <tr>
                                    <td class="text-center">{{ $hotel->id }}</td>
                                    <td class="text-center">{{ $hotel->zone->destination->name }}</td>
                                    <td class="text-center">{{ $hotel->zone->name }}</td>
                                    <td class="text-center">{{ $hotel->name }}</td>
                                    <td class="text-center">{{ $hotel->address }}</td>
                                    <td class="text-center">{{ $hotel->latitude }}</td>
                                    <td class="text-center">{{ $hotel->longitude }}</td>
                                    <td class="text-center">
                                        @if ( auth()->user()->hasPermission(125) )
                                            <button class="btn btn-primary viewMap" data-lat="{{ $hotel->latitude }}" data-lng="{{ $hotel->longitude }}">Ver mapa</button>   
                                        @endif                                        
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serviceMapModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Información de servicio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="content" id="services_map"></div>
                </div>
            </div>
        </div>
    </div>    
@endsection