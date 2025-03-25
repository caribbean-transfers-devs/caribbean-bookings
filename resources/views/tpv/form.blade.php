@php
    $users = auth()->user()->CallCenterAgent([1]);    
@endphp
<form class="col-xl-12 quote_container" id="formReservation">
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
                    <div class="one_">
                        <img src="{{ $item['image'] }}">
                    </div>
                    <div class="two_">
                        <h2>{{ $item['name'] }}</h2>
                        <ul>
                            <li>Cancelación gratuita</li>
                            <li>Hasta {{ $item['passengers'] }} Pax</li>
                            <li>Meet & greet</li>
                            <li>{{ $item['luggage'] }} Maletas</li>
                        </ul>
                    </div>
                    <div class="three_">
                        <div>
                            <p>${{ $item['price'] }} {{ $item['currency'] }}</p>
                            <p>Veículos ({{ $item['vehicles'] }})</p>
                        </div>
                        <div>
                            <input type="radio" class="checkButton" id="serviceButton-{{$service_counter}}" name="service_token" value="{{ $item['token'] }}" onclick="setTotal('{{ $item['price'] }}')">
                            <label for="serviceButton-{{$service_counter}}" class="btn custom-button">
                                Reservar
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
                <div>
                    <label class="form-label" for="formPaymentMethod">Método de pago</label>
                    <select class="form-control" id="formPaymentMethod" name="payment_method">
                        <option value="CARD">Tarjeta de crédito / Débito</option>
                        <option value="PAYPAL">PayPal</option>
                    </select>
                </div>
                <input type="hidden" id="formQuotation" name="is_quotation" value="1">         
            </div>
        </div>
        
        <div class="additional">
            <h3>Información adicional</h3>
            <div class="one_">
                <div>
                    <label class="form-label" for="formSite">Sitio</label>
                    <select class="form-control" id="formSite" name="site_id">
                        @if (isset( $sites ) && sizeof($sites) >= 1)
                            @foreach ($sites as $item)
                                <option value="{{ $item->id }}" data-type="{{ $item->type_site }}">{{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="form-label" for="formOriginSale">Origen de venta</label>
                    <select class="form-control" id="formOriginSale" name="origin_sale_id">
                        <option value="">Selecciona un origen de venta</option>
                        @if (isset( $origin_sales ) && sizeof($origin_sales) >= 1)
                            @foreach ($origin_sales as $origin_sale)
                                <option value="{{ $origin_sale->id }}">{{ $origin_sale->code }}</option>
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
                    <select class="form-control" id="formAgent" name="call_center_agent">
                        @if (isset( $users ) && $users)
                            @foreach ($users as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="form-label" for="formTotal">Total</label>
                    <input class="form-control" type="number" name="data[callcenter][total]" id="formTotal" autocomplete="off" value="0" readonly>
                </div>
                <button type="button" class="btn btn-success" onclick="makeReservationButton(event)" id="btn_make_reservation">Enviar</button>
            </div>
        </div>

    </div>
</form>