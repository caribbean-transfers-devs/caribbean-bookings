
@extends('layout.app')
@section('title') TPV @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/tpv.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/tpv.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script>
        const code = '{!! $config['code'] !!}';
    </script>
    <script src="{{ mix('/assets/js/sections/tpv/index.min.js') }}"></script>
    <script>
        if ( document.getElementById('lookup_date') != null ) {
            const picker = new easepick.create({
                element: "#lookup_date",
                css: [
                    'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                ],
                zIndex: 10,
            });
        }
    </script>
@endpush

@section('content')
    <div class="row layout-top-spacing">
        <div class="widget-content widget-content-area br-8">
            <form class="bookingbox" id="bookingboxForm" method="post">
                @csrf
                <input type="hidden" name="code" value="{{ $config['code'] }}">
                <input type="hidden" name="from_lat">
                <input type="hidden" name="from_lng">
                <input type="hidden" name="to_lat">
                <input type="hidden" name="to_lng">
                <div class="type">                        
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="is_round_trip" value="1">
                        <label class="form-check-label" for="flexSwitchCheckDefault">Viaje redondo</label>
                    </div>
                    <div class="reset">
                        <a href="/tpv/handler" class="btn btn-secondary btn-sm">Limpiar</a>
                    </div>
                </div>
                <div class="from">
                    <label class="form-label" for="aff-input-from">Desde</label>
                    <input class="form-control" type="text" name="from_name" id="aff-input-from">
                    <div class="autocomplete-results" id="aff-input-from-elements"></div>
                </div>
                <div class="to">
                    <label class="form-label" for="bookingToForm">Hacia</label>
                    <input type="text" class="form-control" name="to_name" id="aff-input-to">
                    <div class="autocomplete-results" id="aff-input-to-elements"></div>
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
                        @for ($i = 1; $i < 50; $i++)
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
                <div class="currency">
                    <label class="form-label" for="bookingCurrencyForm">Moneda</label>
                    <select class="form-control mb-2" id="bookingCurrencyForm" name="currency">
                        <option value="USD">USD</option>
                        <option value="MXN">MXN</option>
                    </select>
                </div>
                <div class="dates">
                    <div>
                        <label class="form-label" for="bookingPickupForm">Fecha de recogida</label>
                        <input type="text" class="form-control" id="bookingPickupForm" name="pickup" data-default-mode="single" value="{{ date("Y-m-d H:i") }}">
                    </div>
                    <div id="departureContainer">
                        <label class="form-label" for="bookingDepartureForm">Fecha de regreso</label>
                        <input type="text" class="form-control" id="bookingDepartureForm" name="pickup_departure" data-default-mode="single" value="{{ date("Y-m-d H:i") }}">
                    </div>
                </div>
                <div class="button">
                    <button class="btn btn-primary" onclick="saveQuote(event)" id="btn_quote">Cotizar</button>
                </div>
            </form>
        </div>
        <div class="p-0" id="loadContent"></div>
    </div>
@endsection