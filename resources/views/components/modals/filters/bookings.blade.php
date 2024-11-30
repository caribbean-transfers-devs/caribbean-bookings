@props(['data','isSearch','services','vehicles','reservationstatus','servicesoperation','serviceoperationstatus','units','drivers','operationstatus','paymentstatus','currencies','methods','cancellations','zones','websites','origins','iscommissionable','ispayarrival','istoday','isbalance','isduplicated','isagency','request'])
@php
    $date = "";
    if( is_array($data) ){
        $date = $data['init']." - ".$data['end'];
    }else{
        $date = $data;
    }
@endphp
<!-- Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <form class="form" action="" method="POST" id="formSearch">
                <div class="modal-body">
                    @csrf
                    @php
                        // dump($data);
                        // dump($request);
                    @endphp
                    <div class="top">
                        <div class="item">
                            <div class="box_input transparent">
                                <svg width="24" height="24"><use xlink:href="{{ asset('/assets/img/icons/icons.svg#calendar') }}"></use></svg>
                                <div class="input">
                                    <label for="lookup_date">Fecha De Creación:</label>
                                    <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date }}">
                                </div>
                                <svg width="24" height="24"><use xlink:href="{{ asset('/assets/img/icons/icons.svg#caret-down') }}"></use></svg>
                            </div>
                        </div>
                        @if ( isset($isSearch) || ( isset($services) && !empty($services) ) )
                            <div class="item {{ !isset($isSearch) || (!isset($services) && empty($services)) ? 'one' : '' }}">
                                @if ( isset($isSearch) )
                                    <div class="box_input transparent_border">
                                        <svg width="24" height="24"><use xlink:href="{{ asset('/assets/img/icons/icons.svg#search') }}"></use></svg>
                                        <div class="input">
                                            <label for="filter_text">Buscar Por:</label>
                                            <input type="text" name="filter_text" id="filter_text" class="form-control" placeholder="#/nombre/correo/telefono/Referencia" value="{{ trim($data['filter_text']) }}">
                                        </div>
                                    </div>
                                @endif
                                @if ( isset($services) && !empty($services) )
                                    <select class="form-control selectpicker" title="Tipo De Servicio" data-live-search="true" data-selected-text-format="count > 2" name="is_round_trip[]" id="is_round_trip" data-value="{{ json_encode($data['is_round_trip']) }}" multiple data-actions-box="true">                                        
                                        @foreach ($services as $key => $service)
                                            <option value="{{ $key }}">{{ $service }}</option> 
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        @endif
                        @if ( isset($istoday) || isset($isduplicated) || isset($isagency) || !empty($currencies) )
                            <div class="item {{ !isset($istoday) && !isset($isduplicated) && !isset($isagency) ? 'one' : '' }}">
                                @if ( isset($istoday) || isset($isduplicated) || isset($isagency) )
                                    <div class="box_check">
                                        @if ( isset($istoday) )
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ isset($data['is_today']) ? $data['is_today'] : 0 }}" name="is_today" id="is_today" {{ !empty($data['is_today']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_today">Operadas Hoy</label>
                                            </div>
                                        @endif
                                        @if ( isset($isduplicated) )
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ isset($data['is_duplicated']) ? $data['is_duplicated'] : 0 }}" name="is_duplicated" id="is_duplicated" {{ !empty($data['is_duplicated']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_duplicated">Reservas Duplicadas</label>
                                            </div>
                                        @endif
                                        @if ( isset($isagency) )
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ isset($data['is_agency']) ? $data['is_agency'] : 0 }}" name="is_agency" id="is_agency" {{ !empty($data['is_agency']) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_agency">Ver Agencias</label>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if ( !empty($currencies) )
                                    <div class="box_select">
                                        <svg width="24" height="24"><use xlink:href="{{ asset('/assets/img/icons/icons.svg#currency') }}"></use></svg>
                                        <div class="input">
                                            <select class="form-control selectpicker" title="Moneda De Reservación" data-live-search="true" data-selected-text-format="count > 2" name="currency[]" id="currency" data-value="{{ json_encode($data['currency']) }}" multiple data-actions-box="true">
                                                @foreach ($currencies as $key => $currency)
                                                    <option value="{{ $key }}">{{ $currency }}</option> 
                                                @endforeach
                                            </select>                                        
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    @if ( (isset($websites) && !empty($websites)) || (isset($origins) && !empty($origins)) || (isset($reservationstatus) && !empty($reservationstatus)) || (isset($servicesoperation) && !empty($servicesoperation)) || (isset($vehicles) && !empty($vehicles)) || (isset($zones) && !empty($zones)) || (isset($serviceoperationstatus) && !empty($serviceoperationstatus)) || (isset($units) && !empty($units)) || (isset($drivers) && !empty($drivers)) || (isset($operationstatus) && !empty($operationstatus)) || (isset($paymentstatus) && !empty($paymentstatus)) || (!empty($methods)) || (isset($iscommissionable)) || (isset($ispayarrival)) || (!empty($cancellations)) || (isset($isbalance)) )
                        <div class="row g-0 bottom">

                            @if ( isset($websites) && !empty($websites) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Sitio Web" data-live-search="true" data-selected-text-format="count > 1" name="site[]" id="site" data-value="{{ json_encode($data['site']) }}" multiple data-actions-box="true">                            
                                        @foreach ($websites as $key => $value)
                                            <option value="{{ $value->id }}">{{ $value->site_name }}</option> 
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif

                            @if ( isset($origins) && !empty($origins) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Origen De Venta" data-live-search="true" data-selected-text-format="count > 2" name="origin[]" id="origin" data-value="{{ json_encode($data['origin']) }}" multiple data-actions-box="true">                            
                                        @foreach ($origins as $key => $origin)
                                            <option value="{{ $origin->id }}">{{ $origin->code }}</option> 
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif

                            @if ( isset($reservationstatus) && !empty($reservationstatus) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Estatus de reservación" data-live-search="true" data-selected-text-format="count > 2" name="reservation_status[]" id="reservation_status" data-value="{{ json_encode($data['reservation_status']) }}" multiple data-actions-box="true">
                                        @foreach ($reservationstatus as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option> 
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif

                            @if ( isset($servicesoperation) && !empty($servicesoperation) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Tipo de servicio en operación" data-live-search="true" data-selected-text-format="count > 2" name="service_operation[]" id="service_operation" data-value="{{ json_encode($data['service_operation']) }}" multiple data-actions-box="true">
                                        @foreach ($servicesoperation as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option> 
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif
                            
                            {{-- EL VEHÍCULO O UNIDAD QUE SELECCIONO EL CLIENTE AL GENERAR SU RESERVA --}}
                            @if ( isset($vehicles) && !empty($vehicles) )
                                <select class="form-control selectpicker" title="Tipo de vehículo" data-live-search="true" data-selected-text-format="count > 3" name="product_type[]" id="product_type" data-value="{{ json_encode($data['product_type']) }}" multiple data-actions-box="true">
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if ( isset($zones) && !empty($zones) )
                                <select class="form-control selectpicker" title="Zona de origen" data-live-search="true" data-selected-text-format="count > 3" name="zone_one_id[]" id="zone_one_id" data-value="{{ json_encode(( isset($data['zone_one_id']) ? $data['zone_one_id'] : array() )) }}" multiple data-actions-box="true">
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>

                                <select class="form-control selectpicker" title="Zona de destino" data-live-search="true" data-selected-text-format="count > 3" name="zone_two_id[]" id="zone_two_id" data-value="{{ json_encode(( isset($data['zone_two_id']) ? $data['zone_two_id'] : array() )) }}" multiple data-actions-box="true">
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if ( isset($serviceoperationstatus) && !empty($serviceoperationstatus) )
                                <select class="form-control selectpicker" title="Estatus de servicio" data-live-search="true" data-selected-text-format="count > 2" name="service_operation_status[]" id="service_operation_status" data-value="{{ json_encode($data['service_operation_status']) }}" multiple data-actions-box="true">
                                    @foreach ($serviceoperationstatus as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            @endif

                            @if ( isset($units) && !empty($units) )
                                <select class="form-control selectpicker" title="Unidad" data-live-search="true" data-selected-text-format="count > 3" name="unit[]" id="unit" data-value="{{ json_encode($data['unit']) }}" multiple data-actions-box="true">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option> 
                                    @endforeach
                                </select>
                            @endif
                            
                            @if ( isset($drivers) && !empty($drivers) )
                                <select class="form-control selectpicker" title="Conductor" data-live-search="true" data-selected-text-format="count > 3" name="driver[]" id="driver" data-value="{{ json_encode($data['driver']) }}" multiple data-actions-box="true">
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option> 
                                    @endforeach
                                </select>
                            @endif

                            @if ( isset($operationstatus) && !empty($operationstatus) )
                                <select class="form-control selectpicker" title="Estatus de operación" data-live-search="true" data-selected-text-format="count > 2" name="operation_status[]" id="operation_status" data-value="{{ json_encode($data['operation_status']) }}" multiple data-actions-box="true">
                                    @foreach ($operationstatus as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option> 
                                    @endforeach
                                </select>
                            @endif

                            @if ( isset($paymentstatus) && !empty($paymentstatus) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Estatus de pago" data-live-search="true" data-selected-text-format="count > 2" name="payment_status[]" id="payment_status" data-value="{{ json_encode($data['payment_status']) }}" multiple data-actions-box="true">
                                        @foreach ($paymentstatus as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option> 
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif

                            {{-- @if ( !empty($currencies) )
                                <select class="form-control selectpicker" title="Moneda de reservación" data-live-search="true" data-selected-text-format="count > 2" name="currency[]" id="currency" data-value="{{ json_encode($data['currency']) }}" multiple data-actions-box="true">
                                    @foreach ($currencies as $key => $currency)
                                        <option value="{{ $key }}">{{ $currency }}</option>
                                    @endforeach
                                </select>
                            @endif --}}

                            @if ( !empty($methods) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Metodo de pago" data-live-search="true" data-selected-text-format="count > 2" name="payment_method[]" id="payment_method" data-value="{{ json_encode($data['payment_method']) }}" multiple data-actions-box="true">
                                        @foreach ($methods as $key => $method)
                                            <option value="{{ $key }}">{{ $method }}</option> 
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif

                            @if ( isset($iscommissionable) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Reservas comisionables" name="is_commissionable" id="is_commissionable">
                                        <option value="">Selecciona una opción</option>
                                        <option {{ $data['is_commissionable'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                        <option {{ $data['is_commissionable'] == '0' ? 'selected' : '' }} value="0">No</option>
                                    </select>
                                {{-- </div> --}}
                            @endif

                            @if ( isset($ispayarrival) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Reservas pago al llegar" name="is_pay_at_arrival" id="is_pay_at_arrival">
                                        <option value="">Selecciona una opción</option>
                                        <option {{ $data['is_pay_at_arrival'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                        <option {{ $data['is_pay_at_arrival'] == '0' ? 'selected' : '' }} value="0">No</option>
                                    </select>
                                {{-- </div> --}}
                            @endif                        

                            @if ( !empty($cancellations) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Motivos de cancelación" data-live-search="true" data-selected-text-format="count > 2" name="cancellation_status[]" id="cancellation_status" data-value="{{ json_encode($data['cancellation_status']) }}" multiple data-actions-box="true">
                                        @foreach ($cancellations as $key => $cancellation)
                                            <option value="{{ $cancellation->id }}">{{ $cancellation->name_es }}</option>
                                        @endforeach
                                    </select>
                                {{-- </div> --}}
                            @endif

                            @if ( isset($isbalance) )
                                {{-- <div class="col-lg-4 col-md-6 col-12"> --}}
                                    <select class="form-control selectpicker" title="Reserva con balance" name="is_balance" id="is_today">
                                        <option value="">Selecciona una opción</option>
                                        <option {{ $data['is_balance'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                        <option {{ $data['is_balance'] == '0' ? 'selected' : '' }} value="2">No</option>
                                    </select>
                                {{-- </div> --}}
                            @endif

                            {{-- @if ( isset($istoday) )
                                <select class="form-control selectpicker" title="Operadas para hoy" name="is_today" id="is_today">
                                    <option value="">Selecciona una opción</option>
                                    <option {{ $data['is_today'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                    <option {{ $data['is_today'] == '0' ? 'selected' : '' }} value="0">No</option>
                                </select>
                            @endif --}}

                            {{-- @if ( isset($isduplicated) )
                                <select class="form-control selectpicker" title="Reservas duplicadas" name="is_duplicated" id="is_duplicated">
                                    <option value="">Selecciona una opción</option>
                                    <option {{ $data['is_duplicated'] == '1' ? 'selected' : '' }} value="1">Sí</option>
                                </select>
                            @endif --}}
                        </div>
                    @endif                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>
        </div>
    </div>
</div>