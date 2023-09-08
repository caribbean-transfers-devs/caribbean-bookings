@extends('layout.master')
@section('title') TPV @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/tpv/index.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/tpv/index.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">TPV</h1>            
        </div>

        <div class="row">

            <div class="col-xs-12">
                <div class="bookingbox">
                    <div class="type">
                        <label class="form-label" for="serviceTypeForm">Tipo de viaje</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault">Viaje redondo</label>
                        </div>
                    </div>                            
                    <div class="from">
                        <label class="form-label" for="serviceTypeForm">Desde</label>
                        <input type="text" class="form-control" id="serviceFromForm">
                    </div>
                    <div class="to">
                        <label class="form-label" for="serviceTypeForm">Hacia</label>
                        <input type="text" class="form-control" id="serviceFromForm">
                    </div>
                    <div class="language">
                        <label class="form-label" for="serviceTypeForm">Idioma</label>
                        <select class="form-control mb-2">
                            <option value="USD">EN</option>
                            <option value="MXN">ES</option>
                        </select>
                    </div>
                    <div class="passengers">
                        <label class="form-label" for="serviceTypeForm">Pasajeros</label>
                        <select class="form-control mb-2">
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                    <div class="rate_group">
                        <label class="form-label" for="serviceTypeForm">Grupo de tarifa</label>
                        <select class="form-control mb-2">
                            <option value="xLjDl18">Default</option>                                    
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
            </div>
            <div class="col-xl-8">
            </div>

        </div>
    </div>
@endsection