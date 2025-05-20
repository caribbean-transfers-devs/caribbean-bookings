
@extends('layout.empty')
@php
    $url = (( app()->getLocale() == "en" ) ? '/tpv2/book/'.$request->id.'/make':'/es/tpv2/book/'.$request->id.'/make')
@endphp
@push('Css')
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700&display=swap" rel="stylesheet">
    <link href="{{ mix('/assets/css/sections/tpv2.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/tpv2.min.css') }}" rel="stylesheet" >
@endpush

@section('content')
    <div id="one" class="active">
        <div class="top">
            <div>
                <img src="{{ asset('assets/img/logos/logo.svg') }}" width="150" height="50" loading="lazy" alt="Logo | Caribbean Transfers" title="Logo | Caribbean Transfers">                    
            </div>
            <h1>@lang('bookingbox.bookingbox')</h1>
            <span id="clearBooking">@lang('bookingbox.clean_bookingbox')</span>
        </div>
        <div class="middle">
            <div class="top">
                <x-bookingbox/>
            </div>
            <div class="bottom">
                <button id="aff-button-send">@lang('bookingbox.next')</button>
            </div>
        </div>
    </div>

    <div id="two">
        <div class="top">
            <a href="#" class="go" data-id="one"><svg width="20" height="20"><use xlink:href="/assets/img/svg/icons.svg#arrow-left"></use></svg></a>
            <h1>@lang('bookingbox.vehicle')</h1>
            <div>
                <img src="{{ asset('assets/img/logos/logo.svg') }}" width="150" height="50" loading="lazy" alt="Logo | Caribbean Transfers" title="Logo | Caribbean Transfers">                    
            </div>
        </div>
        <div class="middle" id="two-elements">
        </div>
    </div>

    <div id="three">
        <div class="top">
            <a href="#" class="go" data-id="two"><svg width="20" height="20"><use xlink:href="/assets/img/svg/icons.svg#arrow-left"></use></svg></a>
            <h1>@lang('checkout.passenger_information')</h1>
            <div>
                <img src="{{ asset('assets/img/logos/logo.svg') }}" width="150" height="50" loading="lazy" alt="Logo | Caribbean Transfers" title="Logo | Caribbean Transfers">                    
            </div>
        </div>
        <form action="{{ $url }}" method="POST" id="checkoutForm">
            <input type="hidden" name="id" id="fill-affiliate-id" value="{{ $request->id }}">
            <input type="hidden" name="service_token" id="fill-vehicle-token">
            <input type="hidden" name="phone" id="fill-phone">
            <div class="left">
                @if(isset($_GET['code']))
                    <div class="badge-error">
                        <p><strong>{{ $_GET['code'] }}</strong>: {{ $_GET['message'] }}</p>
                    </div>
                @endif
                <div class="item-information">
                    <div class="one">
                        <img src="" alt="" title="" width="" height="" loading="lazy" id="fill-vehicle-image">
                    </div>
                    <div class="two">
                        <h2 id="fill-vehicle-name"></h2>
                        <div class="stars">5/5</div>
                        <div class="badges">
                            <span>@lang('checkout.taxes_included')</span>
                            <span>@lang('checkout.travel_insurance')</span>
                        </div>
                        <ul class="inline">
                            <li><img src="/assets/img/svg/pax.svg"> Max <span id="fill-passengers"></span> @lang('checkout.passengers')</li>
                            <li><img src="/assets/img/svg/luggage.svg"> Max <span id="fill-suitcase"></span> @lang('checkout.suitcase')</li>
                        </ul>
                    </div>
                    <div class="three">
                        <h3>@lang('checkout.whats_include')</h3>
                        <ul>
                            <li>@lang('checkout.air_conditioner')</li>
                            <li>@lang('checkout.meet_and_greet')</li>
                            <li>@lang('checkout.bilingual_drivers')</li>
                            <li>@lang('checkout.courtesy_stop')</li>
                        </ul>
                    </div>
                </div>
    
                <div class="details-information">
                    <div class="top">
                        <h2>@lang('checkout.add_contact_details')</h2>
                        <p>@lang('checkout.add_contact_details_details')</p>
                    </div>
                    <div class="bottom">
                        <div>
                            <label>@lang('checkout.client_first_name')</label>
                            <input type="text" placeholder="@lang('checkout.client_first_name_placeholder')" name="first_name">
                        </div>
                        <div>
                            <label>@lang('checkout.client_last_name')</label>
                            <input type="text" placeholder="@lang('checkout.client_last_name_placeholder')" name="last_name">
                        </div>
                        <div>
                            <label>@lang('checkout.client_email')</label>
                            <input type="text" placeholder="@lang('checkout.client_email_placeholder')..." name="email">
                        </div>
                        <div class="phone">                            
                            <input type="text" placeholder="000 000-0000" name="phone_input" id="phone">
                        </div>
                    </div>
                </div>
    
                <div class="additional-information">
                    <div class="top">
                        <h2>@lang('checkout.add_comment_title')</h2>
                        <p>@lang('checkout.add_comment_subtitle')</p>
                    </div>
                    <div class="bottom">
                        <div>
                            <label>@lang('checkout.add_comment_write')</label>
                            <textarea row="3" name="special_request"></textarea>
                        </div>
                    </div>
                </div>
    
                <div class="flight-information" id="fill-flight-information">
                    <div class="top">
                        <h2>@lang('checkout.add_flight_title')</h2>
                        <p>@lang('checkout.add_flight_subtitle').</p>
                    </div>
                    <div class="bottom">
                        <div class="one">
                            <label>@lang('checkout.add_flight_number')</label>
                            <input type="text" placeholder="@lang('checkout.add_flight_number_placeholder')..." name="flight_number">
                        </div>
                        <div>
                            <p>@lang('checkout.add_flight_number_additional').</p>
                        </div>
                    </div>
                </div>
    
                <div class="payment-information">
                    <button type="button" class="btn" onclick="handler()" id="btn_make_one">@lang('checkout.book_now')</button>
                </div>
    
            </div>
            <div class="right">
                <div class="box-one">
                    <p>@lang('checkout.total_price')</p>
                    <h2 id="fill-vehicle-price"></h2>
                </div>
                <button type="button" class="btn" onclick="handler()" id="btn_make_two">@lang('checkout.book_now')</button>
                <p>@lang('checkout.by_clicking').</p>
                <div class="journey">
                    <h3>@lang('checkout.your_journey')</h3>
                    <div class="top">
                        <button type="button" class="active" id="arrivalBtn">@lang('checkout.arrival')</button>
                        <button type="button" id="returnBtn">@lang('checkout.return')</button>
                    </div>
                    <div class="bottom">
    
                        <div class="one">
                            <div>
                                <img src="/assets/img/svg/location.svg" alt="" title="" width="" height="" loading="lazy">
                                <div>
                                    <p>@lang('checkout.leaving_from')</p>
                                    <p id="fill-from-name"></p>
                                </div>
                            </div>
                            <div>
                                <img src="/assets/img/svg/location-tick.svg" alt="" title="" width="" height="" loading="lazy">
                                <div>
                                    <p>@lang('checkout.going_to')</p>
                                    <p id="fill-to-name"></p>
                                </div>
                            </div>
                            <div>
                                <img src="/assets/img/svg/calendar.svg" alt="" title="" width="" height="" loading="lazy">
                                <div>
                                    <p>@lang('checkout.pickup_date')</p>
                                    <p id="fill-from-date"></p>
                                </div>
                            </div>
                            <div>
                                <img src="/assets/img/svg/clock.svg" alt="" title="" width="" height="" loading="lazy">
                                <div>
                                    <p>@lang('checkout.pickup_time')</p>
                                    <p id="fill-from-time"></p>
                                </div>
                            </div>
                        </div>
    
                        
                            <div class="two hidden">
                                <div>
                                    <img src="/assets/img/svg/location.svg" alt="" title="" width="" height="" loading="lazy">
                                    <div>
                                        <p>@lang('checkout.leaving_from')</p>
                                        <p id="fill-return-from-name"></p>
                                    </div>
                                </div>
                                <div>
                                    <img src="/assets/img/svg/location-tick.svg" alt="" title="" width="" height="" loading="lazy">
                                    <div>
                                        <p>@lang('checkout.going_to')</p>
                                        <p id="fill-return-to-name"></p>
                                    </div>
                                </div>
                                <div>
                                    <img src="/assets/img/svg/calendar.svg" alt="" title="" width="" height="" loading="lazy">
                                    <div>
                                        <p>@lang('checkout.pickup_date')</p>
                                        <p id="fill-return-to-date"></p>
                                    </div>
                                </div>
                                <div>
                                    <img src="/assets/img/svg/clock.svg" alt="" title="" width="" height="" loading="lazy">
                                    <div>
                                        <p>@lang('checkout.pickup_time')</p>
                                        <p id="fill-return-to-time"></p>
                                    </div>
                                </div>
                            </div>
    
                    </div>
                </div>
                @if(app()->getLocale() == "en")
                    <p>Are the trip details not correct? You can modify them <a href="#" class="go" data-id="one">here</a></p>
                @else
                    <p>¿No son correctos los detalles del viaje? Puedes modificarlos <a href="#" class="go" data-id="one">aquí</a></p>
                @endif                
            </div>
            @csrf            
        </form>
    </div>

    <div id="resultado"></div>
@endsection

@push('Js')
    <script defer src="{{ mix('/assets/js/views/tpv/index2.min.js') }}"></script>
@endpush