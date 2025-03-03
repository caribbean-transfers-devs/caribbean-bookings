<div class="bookingbox">
    <form id="aff-form">
        <div class="options">
            <div class="one">
                <button class="aff-toggle-type active" type="button" data-type="OW">@lang('bookingbox.one_way')</button>
                <button class="aff-toggle-type" type="button" data-type="RT">@lang('bookingbox.round_trip')</button>
            </div>
            <div class="two">
                <button type="button" class="aff-toggle-currency active" data-currency="USD">USD</button>
                <button type="button" class="aff-toggle-currency" data-currency="MXN">MXN</button>
            </div>
        </div>
        <div class="elements">
            <div class="one">                    
                <div>
                    <label for="aff-input-from">@lang('bookingbox.leaving_from')</label>
                    <input type="text" class="form-control" id="aff-input-from" placeholder="@lang('bookingbox.enter_pickup')" autocomplete="off">
                    <div id="aff-input-from-elements"></div>
                </div>
                <div>
                    <label for="aff-input-to">@lang('bookingbox.going_to')</label>
                    <input type="text" class="form-control" id="aff-input-to" placeholder="@lang('bookingbox.enter_destination')" autocomplete="off">
                    <div id="aff-input-to-elements"></div>
                </div>
                <div>
                    <label for="aff-input-pickup-date">@lang('bookingbox.pickup_date')</label>
                    <input type="date" class="form-control" id="aff-input-pickup-date">
                </div>
                <div>
                    <label for="aff-input-pickup-time">@lang('bookingbox.pickup_time')</label>                    
                    <input type="time" class="form-control" id="aff-input-pickup-time" value="00:00">
                </div>
                <div>
                    <label for="aff-input-passengers">@lang('bookingbox.passengers')</label>
                    <select class="form-control" id="aff-input-passengers">
                        <?php for($i=1; $i<=25; $i++): ?>
                            <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="two hidden" id="aff-round-trip-element">
                <div>
                    <label for="aff-input-to-from">@lang('bookingbox.leaving_from')</label>
                    <input type="text" class="form-control" id="aff-input-to-from" placeholder="@lang('bookingbox.enter_pickup')" disabled>
                </div>
                <div>
                    <label for="aff-input-to-to">@lang('bookingbox.going_to')</label>
                    <input type="text" class="form-control" id="aff-input-to-to" placeholder="@lang('bookingbox.enter_destination')" disabled>
                </div>
                <div>
                    <label for="aff-input-to-pickup-date">@lang('bookingbox.pickup_date')</label>
                    <input type="date" class="form-control" id="aff-input-to-pickup-date">
                </div>
                <div>
                    <label for="aff-input-to-pickup-time">@lang('bookingbox.pickup_time')</label>
                    <input type="time" class="form-control" id="aff-input-to-pickup-time" value="00:00">
                </div>
                <div>
                    <label for="aff-input-to-passengers">@lang('bookingbox.passengers')</label>
                    <input type="text" class="form-control" id="aff-input-to-passengers" disabled value="1">
                </div>
            </div>
        </div>
        <div class="button">
            @csrf            
        </div>
    </form>
    <div id="aff-error-list"></div>
    <p>            
        <?php if(app()->getLocale() == "en"): ?>
            Enjoy the ride!
        <?php endif; ?>
        <?php if(app()->getLocale() == "es"): ?>
            ¡Disfruta del viaje!
        <?php endif; ?>
    </p>
    {{-- @php
        dump(Session('token'));
        dump(Session('token_data'));
    @endphp --}}
</div>