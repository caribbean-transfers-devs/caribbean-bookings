@if(sizeof($rates) >= 1)
    <form id="editPriceForm">
        <button type="button" class="btn btn-sm btn-success btnUpdateRates">Actualizar Tarifas</button>
        @foreach($rates as $key => $value)
            @php
                // echo "<pre>";
                // print_r($value);
                // die();
            @endphp
            <div class="item">
                <input type="hidden" name="price[{{ $value->id }}][id]" value="{{ $value->id }}">
                <div class="top_">
                    <p><strong>Desde:</strong> {{ $value->from_name }}</p>
                    <p><strong>Hacia:</strong> {{ $value->to_name }}</p>
                    <p><strong>Servicio:</strong> {{ $value->service_name }}</p>                               
                </div>
                @if($value->price_type == "vehicle" || $value->price_type == "shared")
                    <div class="bottom_">
                        <div class="single_">
                            <div>
                                <p>One way</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][one_way]" value="{{ $value->one_way }}">
                            </div>
                            <div>                                    
                                <p>Round Trip</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][round_trip]" value="{{ $value->round_trip }}">
                            </div>
                        </div>
                        <button class="btn btn-sm btn-danger" type="button" onclick="deleteItem({{ $value->id }})" data-id="{{ $value->id }}">Eliminar</button>
                    </div>
                @endif

                @if($value->price_type == "passenger")
                    <div class="bottom_">
                        <div class="multiple_">
                            <div>
                                <p>One Way (1-2)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][ow_12]" value="{{ $value->ow_12 }}">
                            </div>
                            <div>
                                <p>Round Trip (1-2)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][rt_12]" value="{{ $value->rt_12 }}">
                            </div>
                            <div>
                                <p>One Way (3-7)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][ow_37]" value="{{ $value->ow_37 }}">
                            </div>
                            <div>
                                <p>Round Trip (3-7)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][rt_37]" value="{{ $value->rt_37 }}">
                            </div>
                            <div>
                                <p>Up OW (> 8)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][up_8_ow]" value="{{ $value->up_8_ow }}">
                            </div>
                            <div>
                                <p>Up RT (>8)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][up_8_rt]" value="{{ $value->up_8_rt }}">
                            </div>
                        </div>
                        <button class="btn btn-sm btn-danger" type="button" onclick="deleteItem({{ $value->id }})" data-id="{{ $value->id }}">Eliminar</button>
                    </div>
                @endif
                
            </div>
        @endforeach
        <button type="button" class="btn btn-sm btn-success btnUpdateRates">Actualizar Tarifas</button>
    </form>
@else

    <form class="item" id="newPriceForm">
        <input type="hidden" name="rate_group_id" value="{{ $data['rate_group_data']['id'] }}"/>
        <input type="hidden" name="destination_service_id" value="{{ $data['service_data']['id'] }}"/>
        <input type="hidden" name="destination_service_type" value="{{ $data['service_data']['price_type'] }}"/>
        <input type="hidden" name="destination_id" value="{{ $data['destination_data'] }}"/>
        <input type="hidden" name="zone_one" value="{{ $data['from_data']['id'] }}"/>
        <input type="hidden" name="zone_two" value="{{ $data['to_data']['id'] }}"/>        

        <div class="top_">
            <p><strong>Desde:</strong> {{ $data['from_data']['name'] }}</p>
            <p><strong>Hacia:</strong> {{ $data['to_data']['name'] }}</p>
            <p><strong>Servicio:</strong> {{ $data['service_data']['name'] }}</p>                    
            <p><strong>Grupo de tarifa:</strong> ({{ $data['rate_group_data']['code'] }}) {{ $data['rate_group_data']['name'] }}</p>
        </div>
        @if($data['service_data']['price_type'] == "vehicle" || $data['service_data']['price_type'] == "shared")
            <div class="bottom_">
                <div class="single_">
                    <div>
                        <p>One way</p>
                        <input type="text" class="form-control" value="0.00" name="one_way">
                    </div>
                    <div>                                    
                        <p>Round Trip</p>
                        <input type="text" class="form-control" value="0.00" name="round_trip">
                    </div>
                </div>
                <button class="btn btn-sm btn-success" type="button" id="btn_add_rate">Agregar Tarifa</button>
            </div>
        @endif

        @if($data['service_data']['price_type'] == "passenger")
            <div class="bottom_">
                <div class="multiple_">
                    <div>
                        <p>One Way (1-2)</p>
                        <input type="text" class="form-control" value="0.00" name="ow_12">
                    </div>
                    <div>
                        <p>Round Trip (1-2)</p>
                        <input type="text" class="form-control" value="0.00" name="rt_12">
                    </div>
                    <div>
                        <p>One Way (3-7)</p>
                        <input type="text" class="form-control" value="0.00" name="ow_37">
                    </div>
                    <div>
                        <p>Round Trip (3-7)</p>
                        <input type="text" class="form-control" value="0.00" name="rt_37">
                    </div>
                    <div>
                        <p>One Way (> 8)</p>
                        <input type="text" class="form-control" value="0.00" name="up_8_ow">
                    </div>
                    <div>
                        <p>Round Trip (>8)</p>
                        <input type="text" class="form-control" value="0.00" name="up_8_rt">
                    </div>
                </div>
                <button class="btn btn-sm btn-success" type="button" id="btn_add_rate">Agregar Tarifa</button>
            </div>
        @endif
    </form>
@endif