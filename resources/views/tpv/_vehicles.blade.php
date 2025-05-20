@if(!isset($data['error']))
    @foreach($data['items'] as $key => $val)
        @php
            $before = (( $val['price']  * 100 ) / 70);
            $percentage = (( $val['price']  / 100 ) * 43);
        @endphp
        <div class="item">
            <div class="one">
                <h2>{{ $val['name'] }}</h2>
                <div class="stars">5/5</div>
                <img src="{{ $val['image'] }}" alt="" title="" width="" height="" loading="lazy">
                <div class="badges">
                    <span>@lang('search.taxes_included')</span>
                    <span>@lang('search.travel_insurance')</span>
                </div>
            </div>
            <div class="two">
                <h3>@lang('search.features')</h3>
                <ul class="inline">
                    <li><img src="/assets/img/svg/pax.svg"> Max {{ $val['passengers'] }} @lang('search.passengers')</li>
                    <li><img src="/assets/img/svg/luggage.svg"> Max {{ $val['luggage'] }} @lang('search.suitcase')</li>                    
                </ul>
                <h3>@lang('search.what_include')</h3>
                <ul class="included">
                    <li>@lang('search.air_conditioner')</li>
                    <li>@lang('search.meet_and_greet')</li>
                    <li>@lang('search.bilingual_drivers')</li>
                    <li>@lang('search.courtesy_stop')</li>
                </ul>
            </div>
            <div class="three">
                <div class="one">
                    <p>@lang('search.price_from')</p>
                    <p><span>${{ number_format( $before , 2 ) }} {{ $val['currency'] }}</span> <span>30% OFF</span></p>
                    <p class="three">${{ $val['price'] }} {{ $val['currency'] }}</p>
                    <p>${{ $val['vehicles'] }} @lang('search.vehicle')</p>
                </div>
                <div class="two">
                    <button class="btn btn-item" name="id" data-id="{{ $val['id'] }}">@lang('search.book_now')</button>
                </div>                
            </div>
        </div>
    @endforeach
@endif

@if(isset($data['error']))
    <div class="error">
        <p>@lang('search.error_pre') :(</p>
        <h2>@lang('search.error_title')</h2>
        <div>
            <a href="tel:+19299991258" class="btn" title="+1 929-999-1258">
                <svg width="20" height="20"><use xlink:href="/assets/img/svg/icons.svg#phone-solid"></use></svg>                
                USA & Canada
            </a>
            <a href="tel:+529983870435" class="btn" title="+52 998 387 0435">
                <svg width="20" height="20"><use xlink:href="/assets/img/svg/icons.svg#phone-solid"></use></svg>
                @lang('search.error_call_mexico')
            </a>
            @if(false)
            <a href="#" class="btn">
                <svg width="20" height="20"><use xlink:href="/assets/img/svg/icons.svg#whatsapp"></use></svg>
                WhatsApp
            </a>
            @endif
        </div>
    </div>
@endif