@php
    $sites = auth()->user()->SitesTpv();
    $origins = auth()->user()->Origins();
    $users = auth()->user()->CallCenterAgent([1]);

    $allowed_emails_for_courtesy = ['development@caribbean-transfers.com', 'csanroman@caribbean-transfers.com', 'luis@caribbean-transfer.com.mx'];
@endphp
<form class="quote_container" id="formReservation">
    <div class="left_">
        @if (isset( $quotation['items'] ))
            @php
                $service_counter = 0;   
            @endphp
            @foreach ($quotation['items'] as $item)
                @php
                    $service_counter++;
                @endphp
                <div class="item">
                    <div class="one_ one">
                        <h2>{{ $item['name'] }}</h2>
                        <div class="stars">5/5</div>
                        <img src="{{ $item['image'] }}" alt="" title="" width="" height="" loading="lazy">
                        <div class="badges">
                            <span>Impuestos incluidos</span>
                            <span>Seguro de viaje</span>
                        </div>
                    </div>
                    <div class="two_ two">
                        <h3>Características</h3>
                        <ul class="inline">
                            <li><img src="/assets/img/svg/pax.svg"> Max {{ $item['passengers'] }} pasajeros</li>
                            <li><img src="/assets/img/svg/luggage.svg"> Max {{ $item['luggage'] }} maletas</li>
                        </ul>
                        <h3>¿Qué incluye mi reserva?</h3>
                        <ul>
                            {{-- <li>Cancelación gratuita</li> --}}
                            {{-- <li>Meet & greet</li> --}}
                            <li>Aire acondicionado</li>
                            <li>Encuentro y bienvenida</li>
                            {{-- <li>@lang('search.bilingual_drivers')</li> --}}
                            <li>Parada de cortesía en la tienda</li>
                        </ul>
                    </div>
                    <div class="three_ three">
                        <div class="one">
                            <p>Precio desde</p>
                            <p>${{ $item['price'] }} {{ $item['currency'] }}</p>
                            <p>Veículos ({{ $item['vehicles'] }})</p>
                        </div>
                        <div class="two">
                            <input type="radio" class="checkButton" id="serviceButton-{{$service_counter}}" name="service_token" value="{{ $item['token'] }}" data-total="{{ $item['price'] }}">
                            <label for="serviceButton-{{$service_counter}}" class="btn custom-button">
                                Seleccionar
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <div class="right_">
        <div class="client_information">
            <h3>Información personal</h3>
            <div class="two_ mb-3">
                <label class="form-label" for="formSite">Sitio</label>
                <select class="form-control selectpicker" data-live-search="true" id="formSite" name="site_id">
                    @if (isset( $sites ) && sizeof($sites) >= 1)
                        @foreach ($sites as $item)
                            <option value="{{ $item->id }}" data-type="{{ $item->type_site }}" data-phone="{{ $item->transactional_phone }}" data-email="{{ $item->transactional_email }}">{{ $item->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="one_">
                <div>
                    <label class="form-label" for="formName">Nombre</label>
                    <input class="form-control" type="text" name="first_name" id="formName" autocomplete="off">
                </div>
                <div>
                    <label class="form-label" for="formLastName">Apellidos</label>
                    <input class="form-control" type="text" name="last_name" id="formLastName" autocomplete="off">
                </div>
                <div>
                    <label class="form-label" for="formEmail">Email</label>
                    <input class="form-control" type="text" name="email_address" id="formEmail" autocomplete="off">
                </div>
                <div>
                    <label class="form-label" for="formPhone">Teléfono</label>
                    <input class="form-control" type="text" name="phone" id="formPhone" autocomplete="off">
                </div>
                @if($quotation['places']['config']['flight_required'] == 1)
                <div>
                    <label class="form-label" for="formFlightNumber">Número de vuelo</label>
                    <input class="form-control" type="text" name="flight_number" id="formFlightNumber" autocomplete="off">
                </div>
                @endif
            </div>
            <div class="two_">
                <div>
                    <label class="form-label" for="formSpecialRequest">Solicitudes especiales</label>
                    <textarea class="form-control" name="special_request" id="formSpecialRequest"></textarea>                    
                </div>
            </div>
        </div>
        
        <div class="additional">
            <h3>Información adicional</h3>
            <div class="one_">
                <div>
                    <label class="form-label" for="formPaymentMethod">Es una cotización</label>
                    <select class="form-control" id="formIsQuotation" name="is_quotation">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>                
                <div>
                    <label class="form-label" for="formPaymentMethod">Método de pago</label>
                    <select class="form-control" id="formPaymentMethod" name="payment_method">
                        <option value="CASH">Efectivo</option>
                        <option value="CARD">Tarjeta de crédito / Débito</option>
                        <option value="PAYPAL">PayPal</option>
                        @if ( in_array(auth()->user()->email, $allowed_emails_for_courtesy) )
                            <option value="CORTESIA">Cortesía</option>
                        @endif
                    </select>
                </div>                
                {{-- <div>

                </div> --}}
                <div>
                    <label class="form-label" for="formOriginSale">Origen de venta</label>
                    <select class="form-control selectpicker" data-live-search="true" id="formOriginSale" name="origin_sale_id">
                        <option value="">Selecciona un origen de venta</option>
                        @if (isset( $origins ) && sizeof($origins) >= 1)
                            @foreach ($origins as $origin)
                                <option value="{{ $origin->id }}">{{ $origin->code }}</option>
                            @endforeach
                        @endif
                    </select>                    
                </div>
                <div>
                    <label class="form-label" for="formReference">Referencia</label>
                    <input class="form-control" type="text" name="data[callcenter][reference]" id="formReference" readonly autocomplete="off">                                    
                </div>
                <div>
                    <label class="form-label" for="formAgent">Agente</label>
                    <select class="form-control selectpicker" data-live-search="true" id="formAgent" name="call_center_agent">
                        @if (isset( $users ) && $users)
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="form-label" for="formTotal">Total</label>
                    <input class="form-control" type="tel" name="data[callcenter][total]" id="formTotal" autocomplete="off" value="0" readonly>
                </div>
                <button type="submit" class="btn" id="sendReservation">Enviar</button>
            </div>
        </div>
    </div>
</form>