@extends('layout.app')
@section('title') Tarifas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/rates_enterprise.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/sections/settings/rates_enterprise.min.css') }}" rel="stylesheet">
@endpush

@push('Js')    
    <script src="{{ mix('/assets/js/sections/settings/rates_enterprise.min.js') }}"></script>
@endpush

@section('content')
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <p class="text-danger">{{ $enterprise->is_rates_iva == 1 ? "La tarifas incluyen I.V.A" : "Las tarifas no incluyen I.V.A" }}</p>
            <p class="text-danger">{{ $enterprise->currency == "MXN" ? "Cargar tarifa y costo operativo en MXN" : "Cargar tarifa y costo operativo en USD" }}</p>
            <div id="filters" class="accordion">
                <div class="card">
                <div class="card-header" id="headingOne1">
                    <section class="mb-0 mt-0">
                        <div role="menu" class="" data-bs-toggle="collapse" data-bs-target="#defaultAccordionOne" aria-expanded="true" aria-controls="defaultAccordionOne">
                            Filtro de tarifas <div class="icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                        </div>
                    </section>
                </div>
                <div id="defaultAccordionOne" class="collapse show" aria-labelledby="headingOne1" data-bs-parent="#filters">
                    <div class="card-body">
                        <form action="" class="search-container" method="GET" id="zoneForm">
                            <div class="d-none">
                                <select name="enterpriseID" class="form-control" id="enterpriseID">
                                    <option value="0">Seleccione una empresa empresa</option>
                                    @if(sizeof($enterprises) >= 1)
                                        @foreach ($enterprises as $location)
                                            <option {{ isset($enterprise->id) && $enterprise->id == $location->id ? 'selected' : '' }} value="{{ $location->id }}">{{ $location->names }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <select name="destinationID" class="form-control" id="destinationID">
                                    <option value="0">Selecciona el destino</option>
                                    @if (sizeof($destinations) >= 1)
                                        @foreach ($destinations as $destination)
                                            <option {{ isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] == $destination->id ? 'selected' : '' }} value="{{ $destination->id }}">{{ $destination->name }}</option>
                                        @endforeach                                        
                                    @endif
                                </select>
                            </div>
                            <div class="two_">
                                <div>
                                    <select name="rateZoneOneId" class="form-control" id="rateZoneOneId">
                                        <option value="0">Zona de origen</option>
                                    </select>
                                </div>
                                <div class="label_">a</div>
                                <div>
                                    <select name="rateZoneTwoId" class="form-control" id="rateZoneTwoId">
                                        <option value="0">Zona de destino</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <select name="rateServicesID" class="form-control" id="rateServicesID">
                                    <option value="0">Selecciona el servicio</option>
                                </select>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary btn-lg w-100" id="btnGetRates">Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-body" id="rates-container"></div>
            </div>
        </div>
    </div>
@endsection