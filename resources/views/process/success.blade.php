@extends('layouts.empty')

@push("Css")
    <link href="{{ mix('/assets/css/sections/booking/success.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/booking/success.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container booking-information success"> 
        <div class="top">
            <h1>@lang('thank_you.title') :)</h1>
            @if ($rez)
                <p>@lang('thank_you.subtitle', ['email' => $rez['client']['email']]).</p>    
            @else
                <p>@lang('thank_you.subtitle_custom').</p>
            @endif
        </div>
        
        <div class="banner">
            <img src="/assets/img/checkout/success/banner.png">
        </div>

        <div class="resume-items">
            @if ($rez)
                <div class="one">
                    <div class="top">
                        @lang('thank_you.details')
                    </div>
                    <div class="bottom">
                        <p><strong>@lang('thank_you.name'):</strong> {{ $rez['client']['first_name'] }} {{ $rez['client']['last_name'] }}</p>
                        <p><strong>@lang('thank_you.phone'):</strong> {{ $rez['client']['phone'] }}</p>
                        <p><strong>E-mail:</strong> {{ $rez['client']['email'] }}</p>
                        <div>
                            <p>Total <span>@lang('thank_you.you_save')</span></p>
                            @php
                                $before = (( $rez['sales']['total']  * 100 ) / 70);
                                $before = $before - $rez['sales']['total'];
                            @endphp
                            <p>${{ number_format($rez['sales']['total'],2) }} {{ $rez['config']['currency'] }} <span class="blue">${{ number_format($before,2) }} {{ $rez['config']['currency'] }}</span></p>
                        </div>
                    </div>
                </div>

                @foreach ($rez['items'] as $key => $value)                                        
                    <div class="two">
                        <div class="one">
                            <p>@lang('thank_you.leaving_from')</p>
                            <p>{{ $value['from']['name'] }}</p>
                            <p>{{ $key }} | {{ $value['service_type_name'] }} | {{ $value['passengers'] }} pax | {{ (( !empty($value['flight_number'] ) )? $value['flight_number']  :'')}}</p>
                            <p>{{ date("Y-m-d", strtotime($value['pickup'])) }} @ {{ date("H:i", strtotime($value['pickup'])) }}</p>
                        </div>
                        <div class="two">
                            <img src="/assets/img/svg/loader-points.svg" width="20" height="20" alt="" title="" loading="lazy">
                        </div>
                        <div class="three">
                            <p>@lang('thank_you.going_to')</p>
                            <p>{{ $value['to']['name'] }}</p>
                            @if( !empty( $value['departure_pickup'] ) )
                                <p>{{ date("Y-m-d", strtotime($value['pickup'])) }} @ {{ date("H:i", strtotime($value['pickup'])) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="four">
                <h2>@lang('thank_you.more_information')</h2>
                <div>
                    <p><a href="mailto:{{ config('services.email') }}">{{ config('services.email') }}</a></p>
                    <p>@lang('thank_you.call_us'): 
                        <a href="tel:{{ App\Traits\GeneralTrait::clearPhone( config('services.phone.US') ) }}" class="blue">{{  config('services.phone.US') }}</a> @lang('thank_you.or')
                        <a href="tel:{{ App\Traits\GeneralTrait::clearPhone( config('services.phone.MX') ) }}" class="blue">{{  config('services.phone.MX') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("Js")
    
@endpush