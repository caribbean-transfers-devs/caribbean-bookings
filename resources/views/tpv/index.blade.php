@extends('layout.master')
@section('title') TPV @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/tpv/index.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/tpv/index.min.css') }}" rel="stylesheet" >        
@endpush

@push('bootom-stack')    
    <script src="{{ mix('/assets/js/views/tpv/index.min.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">TPV</h1>            
        </div>

        <div class="row">

            <div class="col-xs-12">
                <form class="bookingbox" method="post" action="/tpv/new/00653ec6-7fde-4bf6-a4c2-937fc0376496">
                    @csrf
                    <div class="type">                        
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="round_trip" value="1">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Viaje redondo</label>
                        </div>
                        <div class="reset">
                            <a href="/tpv/handler" class="btn btn-secondary btn-sm">Limpiar</a>
                        </div>
                    </div>
                    <div class="from">
                        <label class="form-label" for="bookingFromForm">Desde</label>
                        <input class="form-control" type="text" name="from_name" id="bookingFromForm">
                        <div id="autocomplete-results" class="autocomplete-results"></div>
                    </div>
                    <div class="to">
                        <label class="form-label" for="bookingToForm">Hacia</label>
                        <input type="text" class="form-control" id="bookingToForm" name="to_name">
                    </div>
                    <div class="language">
                        <label class="form-label" for="bookingLanguageForm">Idioma</label>
                        <select class="form-control mb-2" id="bookingLanguageForm" name="language">
                            <option value="en">EN</option>
                            <option value="es">ES</option>
                        </select>
                    </div>
                    <div class="passengers">
                        <label class="form-label" for="bookingPassengersForm">Pasajeros</label>
                        <select class="form-control mb-2" id="bookingPassengersForm" name="passengers">
                            @for ($i = 1; $i < 25; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>                                
                            @endfor                            
                        </select>
                    </div>
                    <div class="rate_group">
                        <label class="form-label" for="bookingRategroupForm">Grupo de tarifa</label>
                        <select class="form-control mb-2" id="bookingRategroupForm" name="rate_group">
                            <option value="xLjDl18">Default</option>                                    
                        </select>
                    </div>
                    <div class="dates">
                        <div>
                            <label class="form-label" for="bookingPickupForm">Pickup</label>
                            <input type="text" class="form-control" id="bookingPickupForm" name="pickup">
                        </div>
                        <div>
                            <label class="form-label" for="bookingDeparturePickupForm">Departure pickup</label>
                            <input type="text" class="form-control" id="bookingDeparturePickupForm" name="departure_pickup">
                        </div>
                    </div>
                    <div class="button">
                        <button class="btn btn-primary">Cotizar</button>
                    </div>
                </form>
            </div>

            <div class="col-xl-4">
            </div>
            <div class="col-xl-8">
            </div>

        </div>
    </div>
@endsection