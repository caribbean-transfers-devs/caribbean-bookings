@extends('layout.app')
@section('title') Zonas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/zones.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/sections/settings/zones.min.css') }}" rel="stylesheet">
@endpush

@push('Js')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.gmaps.key') }}&libraries=drawing" async defer></script>
    <script src="{{ mix('/assets/js/sections/settings/zones.min.js') }}"></script>
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
                            Filtro de zonas <div class="icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                        </div>
                    </section>
                </div>
                <div id="defaultAccordionOne" class="collapse show" aria-labelledby="headingOne1" data-bs-parent="#filters">
                    <div class="card-body">
                        <form action="" class="row" method="GET" id="zoneForm">
                            <div class="col-12 col-sm-5 mb-3 mb-lg-0">
                                <label class="form-label" for="lookup_date">Selecciona una zona</label>
                                <select name="destinationID" class="form-control" id="destinationID">
                                    <option value="1" {{ request('id') == 1 ? 'selected' : '' }}>Cancún</option>
                                    <option value="2" {{ request('id') == 2 ? 'selected' : '' }}>Los Cabos</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-3 align-self-end">
                                <button type="button" class="btn btn-primary btn-lg btn-filter w-100" id="btnSendZone">Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div>

        @if(isset($zones))
            <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
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
                    <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
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
                                            <button class="btn btn-primary" onclick="getPoints(event, {{ $value->destination_id }}, {{ $value->id }} )"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <x-zones.map/>
@endsection