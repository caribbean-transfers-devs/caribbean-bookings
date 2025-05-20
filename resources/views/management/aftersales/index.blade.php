@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;

    $today = new DateTime();
    $dates = [];

    for ($i = 0; $i < 30; $i++) {
        $dates[] = $today->format('Y-m-d');
        $today->modify('-1 day');
    }

@endphp
@extends('layout.app')
@section('title') Gestión De POST Venta @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management/aftersales.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/aftersales.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/management/aftersales.min.js') }}"></script>
@endpush

@section('content')
    <div class="row layout-top-spacing callcenter-container">
        <div class="col-12 col-sm-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pendientes de pago</h5>
                </div>
                <div class="card-body card-data p-3">
                    <div id="pending-general-container">
                        <div class="loaderItem"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6">
            <div class="card">
                <div class="card-header spam-date-container">
                    <h5 class="card-title mb-0">Gestión de SPAM</h5>
                    <select class="form-select" id="spam-selec-date" onchange="getSpamByDate(event)">
                        @foreach($dates as $key => $value) 
                            <option value="{{ $value }}">{{ date("Y/m/d", strtotime($value)) }}</option>
                        @endforeach                            
                    </select>                        
                </div>                
                <div class="card-body card-data p-3">
                    <div class="row" id="spam-general-container">
                        <div class="loaderItem"></div>
                    </div>
                </div>                
            </div>
        </div>
    </div>

    <x-modals.management.aftersales />
@endsection