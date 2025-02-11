@php
    use App\Traits\RoleTrait;
@endphp

@if(sizeof($rates) >= 1)
    <form id="editPriceForm">
        @if (RoleTrait::hasPermission(106))
            <button type="button" class="btn btn-success btnUpdateRates">Actualizar Tarifas</button>
        @endif
        @foreach($rates as $key => $value)
            {{-- @dump($value) --}}
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
                            {{-- <div>                                    
                                <p>Round Trip</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][round_trip]" value="{{ $value->round_trip }}">
                            </div> --}}
                            <div>
                                <p>Costo operativo</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][operating_cost]" value="{{ $value->operating_cost }}">
                            </div>
                        </div>
                        @if (RoleTrait::hasPermission(107))
                            <button class="btn btn-sm btn-danger" type="button" onclick="deleteItem({{ $value->id }})" data-id="{{ $value->id }}">Eliminar</button>
                        @endif
                    </div>
                @endif

                @if($value->price_type == "passenger")
                    <div class="bottom_">
                        <div class="multiple_">
                            <div>
                                <p>One Way (1-2)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][ow_12]" value="{{ $value->ow_12 }}">
                            </div>
                            {{-- <div>
                                <p>Round Trip (1-2)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][rt_12]" value="{{ $value->rt_12 }}">
                            </div> --}}
                            <div>
                                <p>One Way (3-7)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][ow_37]" value="{{ $value->ow_37 }}">
                            </div>
                            {{-- <div>
                                <p>Round Trip (3-7)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][rt_37]" value="{{ $value->rt_37 }}">
                            </div> --}}
                            <div>
                                <p>Up OW (> 8)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][up_8_ow]" value="{{ $value->up_8_ow }}">
                            </div>
                            {{-- <div>
                                <p>Up RT (>8)</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][up_8_rt]" value="{{ $value->up_8_rt }}">
                            </div> --}}
                            <div>                                    
                                <p>Costo operativo</p>
                                <input type="text" class="form-control" name="price[{{ $value->id }}][operating_cost]" value="{{ $value->operating_cost }}">
                            </div>
                        </div>
                        @if (RoleTrait::hasPermission(107))
                            <button class="btn btn-danger" type="button" onclick="deleteItem({{ $value->id }})" data-id="{{ $value->id }}">Eliminar</button>
                        @endif
                    </div>
                @endif
                
            </div>
        @endforeach
        @if (RoleTrait::hasPermission(106))
            <button type="button" class="btn btn-success btnUpdateRates">Actualizar Tarifas</button>
        @endif
    </form>
@else
    @if ( isset( $data['from_data'] ) && !empty( $data['from_data'] ) )
    <form class="item" id="newPriceForm">
        <input type="hidden" name="enterprise_id" value="{{ $data['enterprise_data']['id'] }}"/>
        <input type="hidden" name="destination_service_id" value="{{ $data['service_data']['id'] }}"/>
        <input type="hidden" name="destination_service_type" value="{{ $data['service_data']['price_type'] }}"/>
        <input type="hidden" name="destination_id" value="{{ $data['destination_data'] }}"/>
        <input type="hidden" name="zone_one" value="{{ $data['from_data']['id'] }}"/>
        <input type="hidden" name="zone_two" value="{{ $data['to_data']['id'] }}"/>        

        <div class="top_">
            <p><strong>Desde:</strong> {{ $data['from_data']['name'] }}</p>
            <p><strong>Hacia:</strong> {{ $data['to_data']['name'] }}</p>
            <p><strong>Servicio:</strong> {{ $data['service_data']['name'] }}</p>                    
            <p><strong>Empresa:</strong> ({{ $data['enterprise_data']['id'] }}) {{ $data['enterprise_data']['names'] }}</p>
        </div>
        @if($data['service_data']['price_type'] == "vehicle" || $data['service_data']['price_type'] == "shared")
            <div class="bottom_">
                <div class="single_">
                    <div>
                        <p>One way</p>
                        <input type="text" class="form-control" value="0.00" name="one_way">
                    </div>
                    {{-- <div>                                    
                        <p>Round Trip</p>
                        <input type="text" class="form-control" value="0.00" name="round_trip">
                    </div> --}}
                    <div>
                        <p>Costo operativo</p>
                        <input type="text" class="form-control" value="0.00" name="operating_cost">
                    </div>
                </div>
                @if (RoleTrait::hasPermission(105))
                    <button class="btn btn-sm btn-success" type="button" id="btn_add_rate">Agregar Tarifa</button>
                @endif
            </div>
        @endif

        @if($data['service_data']['price_type'] == "passenger")
            <div class="bottom_">
                <div class="multiple_">
                    <div>
                        <p>One Way (1-2)</p>
                        <input type="text" class="form-control" value="0.00" name="ow_12">
                    </div>
                    {{-- <div>
                        <p>Round Trip (1-2)</p>
                        <input type="text" class="form-control" value="0.00" name="rt_12">
                    </div> --}}
                    <div>
                        <p>One Way (3-7)</p>
                        <input type="text" class="form-control" value="0.00" name="ow_37">
                    </div>
                    {{-- <div>
                        <p>Round Trip (3-7)</p>
                        <input type="text" class="form-control" value="0.00" name="rt_37">
                    </div> --}}
                    <div>
                        <p>One Way (> 8)</p>
                        <input type="text" class="form-control" value="0.00" name="up_8_ow">
                    </div>
                    {{-- <div>
                        <p>Round Trip (>8)</p>
                        <input type="text" class="form-control" value="0.00" name="up_8_rt">
                    </div> --}}
                    <div>                                    
                        <p>Costo operativo</p>
                        <input type="text" class="form-control" value="0.00" name="operating_cost">
                    </div>
                </div>
                @if (RoleTrait::hasPermission(105))
                    <button class="btn btn-sm btn-success" type="button" id="btn_add_rate">Agregar Tarifa</button>
                @endif
            </div>
        @endif
    </form>
    @else
        <div class="alert alert-primary alert-dismissible" role="alert" style="margin:0px;">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <div class="alert-message">
                <strong>¡Lo sentimos!</strong> no hay tarifas que editar...
            </div>
        </div>
    @endif
@endif