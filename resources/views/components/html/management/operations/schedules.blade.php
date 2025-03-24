@php
    use App\Traits\RoleTrait;
    use App\Traits\FiltersTrait;
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $units = FiltersTrait::Units('active'); //LAS UNIDADES DADAS DE ALTA
    $drivers = FiltersTrait::Drivers('active');
@endphp
<div class="table-responsive">
    <table class="table custom-table">
        <thead>
            <tr>
                <th class="text-center">Hora entrada</th>
                <th class="text-center">Hora salida</th>
                <th class="text-center">Hora salida/final</th>
                <th class="text-center">Horas extras</th>
                <th class="text-center">Unidad</th>
                <th class="text-center">Driver</th>
                <th class="text-center">Estatus</th>
                <th class="text-center">Observaciónes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($schedules as $schedule)
                <tr>
                    <td class="text-center">{{ Carbon::parse($schedule->check_in_time)->format('H:i A') }}</td>
                    <td class="text-center"><span class="badge badge-success w-100">{{ Carbon::parse($schedule->check_out_time)->format('H:i A') }}</span></td>
                    <td class="text-center">
                        @php
                            $time = Carbon::parse($schedule->end_check_out_time)->format('H:i A');
                        @endphp
                        @if ( $schedule->end_check_out_time != NULL )
                            <?=( $schedule->end_check_out_time != NULL ? '<span class="badge badge-'.( $schedule->extra_hours != NULL && $schedule->check_out_time != $schedule->end_check_out_time ? 'danger' : 'success' ).' w-100">'.$time.'</span>' : 'NO DEFINIDO' )?>
                        @else
                            <div class="form-group">
                                <input type="text" id="end_check_out_time" name="end_check_out_time" class="form-control change_schedule end_check_out_time" data-code="{{ $schedule->id }}" data-type="end_check_out_time" placeholder="Hora de salida final" value="{{ isset($schedule->end_check_out_time) ? $schedule->end_check_out_time : '' }}">
                            </div>                            
                        @endif                        
                    </td>
                    <td class="text-center">
                        @php
                            $time = Carbon::parse($schedule->extra_hours)->format('H:i');
                        @endphp
                        <?=( $schedule->extra_hours != NULL && $schedule->extra_hours != "00:00:00" ? '<span class="badge badge-success w-100">'.$time.'</span>' : 'NO DEFINIDO' )?>
                    </td>
                    <td class="text-center">
                        @if ( $schedule->vehicle_id != NULL )
                            <button class="btn btn-dark w-100">{{ isset($schedule->vehicle->name) ? $schedule->vehicle->name : 'NO DEFINIDO' }} - {{ isset($schedule->vehicle->destination_service->name) ? $schedule->vehicle->destination_service->name : 'NO DEFINIDO' }} - {{ isset($schedule->vehicle->enterprise->names) ? $schedule->vehicle->enterprise->names : 'NO DEFINIDO' }}</button>
                        @else
                            <div class="form-group">
                                <select class="form-control selectpicker change_schedule" data-code="{{ $schedule->id }}" data-type="vehicle" data-live-search="true" id="vehicle_id" name="vehicle_id">
                                    <option value="0">Selecciona una unidad</option>
                                    @if ( isset($units) && count($units) >= 1 )
                                        @foreach ($units as $unit)
                                            <option {{ isset($schedule->vehicle_id) && $schedule->vehicle_id == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->destination_service->name }} - {{ $unit->enterprise->names }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @endif                        
                    </td>
                    <td class="text-center">
                        @if ( $schedule->driver_id != NULL )
                            {{ isset($schedule->driver->names) ? $schedule->driver->names : 'NO DEFINIDO' }} {{ isset($schedule->driver->surnames) ? $schedule->driver->surnames : 'NO DEFINIDO' }}
                        @else
                            <div class="form-group">
                                <select class="form-control selectpicker change_schedule" data-code="{{ $schedule->id }}" data-type="driver" data-live-search="true" id="driver_id" name="driver_id">
                                    <option value="0">Selecciona un conductor</option>
                                    @if ( isset($drivers) && count($drivers) >= 1 )
                                        @foreach ($drivers as $driver)
                                            <option {{ isset($schedule->driver_id) && $schedule->driver_id == $driver->id ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        @endif                        
                    </td>
                    <td class="text-center">
                        @if ( $schedule->status != NULL )
                            <button class="btn btn-{{ $schedule->status == "DT" ? 'info' : ( $schedule->status == "F" ? 'danger' : 'success' ) }} w-100">{{ $schedule->status }}</button>
                        @else
                            <div class="form-group">
                                <select class="form-control selectpicker change_schedule" data-code="{{ $schedule->id }}" data-type="status" data-live-search="true" id="status" name="status">
                                    <option value="0">Selecciona una opción</option>
                                    <option {{ isset($schedule->status) && $schedule->status == "A" ? 'selected' : '' }} value="A">A</option>
                                    <option {{ isset($schedule->status) && $schedule->status == "F" ? 'selected' : '' }} value="F">F</option>
                                    <option {{ isset($schedule->status) && $schedule->status == "DT" ? 'selected' : '' }} value="DT">DT</option>
                                </select>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ( $schedule->observations != NULL )
                            {{ $schedule->observations }}
                        @else
                            <div class="form-group">
                                <input type="text" class="form-control change_schedule" data-code="{{ $schedule->id }}" data-type="observations" name="observations" id="observations" value="{{ isset($schedule->observations) ? $schedule->observations : '' }}">
                            </div>                            
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>    