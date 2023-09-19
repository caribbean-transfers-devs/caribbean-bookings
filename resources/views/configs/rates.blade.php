@extends('layout.master')
@section('title') Tarifas @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/rates/index.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/rates/index.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')    
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
    <script src="{{ mix('/assets/js/views/rates/index.min.js') }}"></script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Tarifas</h1>

        <div class="card">                    
            <form class="card-body search-container" action="" method="GET" id="zoneForm">
                <div>
                    <select class="form-control" id="destinationID">
                        <option value="0">Selecciona el destino</option>
                        <option value="1">Cancún</option>
                    </select>
                </div>
                <div class="two_">
                    <div>
                        <select class="form-control" id="rateZoneOneId">
                            <option value="0">Zona de origen</option>
                        </select>
                    </div>
                    <div class="label_">a</div>
                    <div>
                        <select class="form-control" id="rateZoneTwoId">
                            <option value="0">Zona de destino</option>
                        </select>
                    </div>
                </div>
                <div>
                    <select class="form-control" id="rateServicesID">
                        <option value="0">Selecciona el servicio</option>
                    </select>
                </div>                
                <div>
                    <select class="form-control" id="rateGroupID">
                        @if(sizeof($rate_groups) >= 1)
                            @foreach ($rate_groups as $value)
                                <option value="{{ $value->id }}">({{ $value->code }}) {{ $value->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <button class="btn btn-primary btn-sm" type="button" id="btnGetRates">Buscar</button>
                </div>
            </form>
        </div>
        
        <div class="row">
            <div class="col-12 col-sm-12">
            
                <div class="card">
                    <div class="card-body" id="rates-container"></div>
                </div>

            </div>
        </div>

    </div>
@endsection