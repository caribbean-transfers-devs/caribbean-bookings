@extends('layout.app')
@section('title') Tarifas De Agencia @endsection

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

            <div class="alert alert-icon-left alert-light-info alert-dismissible fade show mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <strong>Información:</strong> Tomar en cuenta que las tarifas se deben cargar conforme a la moneda seleccionada al dar de alta la empresa, <strong>esta empresa tiene seleccionado la moneda: {{ $enterprise->currency }}</strong>
            </div>            
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
                        <form action="" class="search-container" method="POST" id="zoneForm">
                            @csrf
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
                                <select name="destination_id" class="form-control" id="destinationID">
                                    <option value="">Selecciona el destino</option>
                                    @if (sizeof($destinations) >= 1)
                                        @foreach ($destinations as $destination)
                                            <option {{ isset($_REQUEST['destination_id']) && $_REQUEST['destination_id'] == $destination->id ? 'selected' : '' }} value="{{ $destination->id }}">{{ $destination->name }}</option>
                                        @endforeach                                        
                                    @endif
                                </select>
                            </div>
                            <div class="two_">
                                <div>
                                    <select name="zone_one" class="form-control" id="rateZoneOneId" data-code="{{ isset($_REQUEST['zone_one']) ? $_REQUEST['zone_one'] : '' }}">
                                        <option value="">Zona de origen</option>
                                    </select>
                                </div>
                                <div class="label_">a</div>
                                <div>
                                    <select name="zone_two" class="form-control" id="rateZoneTwoId" data-code="{{ isset($_REQUEST['zone_two']) ? $_REQUEST['zone_two'] : '' }}">
                                        <option value="">Zona de destino</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <select name="destination_service_id" class="form-control" id="rateServicesID" data-code="{{ isset($_REQUEST['destination_service_id']) ? $_REQUEST['destination_service_id'] : '' }}">
                                    <option value="">Selecciona el servicio</option>
                                </select>
                            </div>
                            <div>
                                {{-- id="btnGetRates" --}}
                                <button type="submit" class="btn btn-primary" style="padding: 12px 20px;">Buscar</button>
                                @if (auth()->user()->hasPermission(105))
                                    <a href="{{ route('enterprises.rates.create', [( isset($enterprise->id) ? $enterprise->id : 0 )]) }}" class="btn btn-success" style="padding: 12px 20px;">Agregar tarifa</a>                                    
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>
    
        <div class="col-12 col-sm-12">
            <div class="card">
                <div class="card-body" id="rates-container">
                    @foreach ($enterprise->rates_enterprises as $key => $value)
                        <div class="item">                            
                            @if ($value->id == 129)
                                {{-- @dump($value->toArray()) --}}
                            @endif
                            <div class="top_">
                                <p><strong>Desde:</strong> {{ isset($value->zoneOne->name) ? $value->zoneOne->name." (".$value->zoneOne->id.") " : 'Zona no encontrada' }}</p>
                                <p><strong>Hacia:</strong> {{ isset($value->zoneTwo->name) ? $value->zoneTwo->name." (".$value->zoneTwo->id.") " : 'Zona no encontrada' }}</p>
                                <p><strong>Servicio:</strong> {{ isset($value->destination_service->name) ? $value->destination_service->name : 'Tipo de unidad no encontrada' }}</p>
                                <p><strong>Empresa:</strong> {{ isset($value->enterprise->names) ? $value->enterprise->names." (".$value->enterprise->id.") " : 'Empresa no encontrada' }}</p>
                            </div>

                            @if($value->destination_service->price_type == "vehicle" || $value->destination_service->price_type == "shared")
                                <div class="bottom_">
                                    <div class="single_2">
                                        <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">One way:</strong> $ {{ number_format($value->one_way,2) }}</p>
                                        </div>
                                        {{-- <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Round Trip:</strong> $ {{ number_format($value->round_trip,2) }}</p>
                                        </div> --}}
                                        <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Costo operativo:</strong> $ {{ number_format($value->operating_cost,2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($value->destination_service->price_type == "passenger")
                                <div class="bottom_">
                                    <div class="multiple_2">
                                        <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">One Way (1-2):</strong> $ {{ number_format($value->ow_12,2) }}</p>
                                        </div>
                                        {{-- <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Round Trip (1-2):</strong> $ {{ number_format($value->rt_12,2) }}</p>
                                        </div> --}}
                                        <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">One Way (3-7):</strong> $ {{ number_format($value->ow_37,2) }}</p>
                                        </div>
                                        {{-- <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Round Trip (3-7):</strong> $ {{ number_format($value->rt_37,2) }}</p>
                                        </div> --}}
                                        <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Up OW (> 8):</strong> $ {{ number_format($value->up_8_ow,2) }}</p>
                                        </div>
                                        {{-- <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Up RT (>8):</strong> $ {{ number_format($value->up_8_rt,2) }}</p>
                                        </div> --}}
                                        <div>
                                            <p style="font-size: 14px;"><strong style="font-size: 14px;">Costo operativo:</strong> $ {{ number_format($value->operating_cost,2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- justify-content-between --}}
                            <div class="d-flex  gap-3">
                                @if (auth()->user()->hasPermission(106))
                                    <a class="btn btn-success" href="{{ route('enterprises.rates.edit', [$value->id]) }}">Editar tarifa</a>
                                @endif                            
                                @if (auth()->user()->hasPermission(107))
                                    <button class="btn btn-danger" type="button" onclick="deleteItem({{ $value->id }})" data-id="{{ $value->id }}">Eliminar tarifa</button>
                                @endif
                            </div>                            
                        </div>                        
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection