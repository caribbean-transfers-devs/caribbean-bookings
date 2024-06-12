@extends('layout.app')
@section('title') Tarifas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/rates.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/sections/rates.min.css') }}" rel="stylesheet">
@endpush

@push('Js')    
    {{-- <script src="{{ mix('/assets/js/views/rates/index.min.js') }}"></script> --}}
    <script src="{{ mix('/assets/js/sections/rates.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
        );
        // dump($buttons);
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
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
                            <div>
                                <select name="destinationID" class="form-control" id="destinationID">
                                    <option value="0">Selecciona el destino</option>
                                    <option value="1">Cancún</option>
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
                                <select name="rateGroupID" class="form-control" id="rateGroupID">
                                    @if(sizeof($rate_groups) >= 1)
                                        @foreach ($rate_groups as $value)
                                            <option value="{{ $value->id }}">({{ $value->code }}) {{ $value->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>                            
                            <div>
                                <button type="button" class="btn btn-primary btn-lg btn-filter w-100" id="btnGetRates">Buscar</button>
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