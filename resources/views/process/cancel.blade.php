@extends('layouts.empty')

@push("Css")
    <link href="{{ mix('/assets/css/sections/booking/cancel.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/booking/cancel.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container booking-information cancel">
        <div class="top">
            <h1>@lang('cancel.title') :(</h1>
            <p>@lang('cancel.subtitle').</p>
        </div>
        
        <div class="banner">
            <img src="/assets/img/checkout/cancel/banner.png">
        </div>

        <div class="resume-items">
            <div class="one">
                <h2>@lang('cancel.more_information')</h2>
                <div>
                    <p><a href="mailto:{{ config('services.email') }}">{{ config('services.email') }}</a></p>
                    <p>@lang('cancel.call_us'): 
                        <a href="tel:{{ App\Traits\GeneralTrait::clearPhone( config('services.phone.US') ) }}" class="blue">{{  config('services.phone.US') }}</a> @lang('cancel.or')
                        <a href="tel:{{ App\Traits\GeneralTrait::clearPhone( config('services.phone.MX') ) }}" class="blue">{{  config('services.phone.MX') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("Js")
    
@endpush