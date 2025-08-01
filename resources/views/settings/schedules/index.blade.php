@php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $units = auth()->user()->UnitsSchedules(); //LAS UNIDADES DADAS DE ALTA
    $drivers = auth()->user()->DriversSchedules();
    // dump($units);
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/types_cancellations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/types_cancellations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/schedules.min.js') }}"></script>
@endpush

@section('content')
    @php
        // 'url' => route('schedules.create')
        $buttons = [];
        // $buttons = array(
        //     array(  
        //         'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
        //         'className' => 'btn btn-primary __btn_create',
        //         'attr' => array(
        //             'data-title' =>  "Filtros de pagos horarios",
        //             'data-bs-toggle' => 'modal',
        //             'data-bs-target' => '#filterModal'
        //         )
        //     ),            
        //     array(  
        //         'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Generar nuevos horarios',
        //         'className' => 'btn btn-primary reloadSchedules',                
        //     )
        // );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-8">
                @if ($errors->any())
                    <div class="alert alert-light-primary alert-dismissible fade show border-0 mb-4" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('danger'))
                    <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                        {{ session('danger') }}
                    </div>
                @endif

                <div class="d-flex gap-3">
                    <button class="btn btn-primary" data-title="Filtros de pagos horarios" data-bs-toggle="modal" data-bs-target="#filterModal"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar</button>
                    <button class="btn btn-primary updateDriver"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agrega operadores pendientes</button>
                    <button class="btn btn-primary reloadSchedules"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Generar nuevos horarios</button>

                    <button class="btn btn-primary creatingSchedules">Generar preasignación</button>
                </div>

                <table id="dataSchedules" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Hora entrada</th>
                            <th class="text-center">Hora salida</th>
                            {{-- <th class="text-center">Hora salida/final</th> --}}
                            <th class="text-center">Horas extras</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Estatus unidad</th>
                            <th class="text-center">Conductor</th>                            
                            <th class="text-center">Estatus conductor</th>
                            <th class="text-center">Observaciónes</th>
                            <th class="text-center">Estado</th>
                            {{-- <th></th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedulesMain as $schedulemain)
                            <tr>
                                <td class="text-center">{{ Carbon::parse($schedulemain->date)->translatedFormat('d F Y') }}</td>
                                <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        @php
                                            $check_in_time = Carbon::parse($schedulemain->check_in_time)->format('H:i A');
                                        @endphp
                                        {{ $schedulemain->check_in_time != NULL ? $schedulemain->check_in_time : 'SIN HORE DE ENTRADA' }}
                                    @else
                                        <input type="text" name="check_in_time" class="form-control check_in_time" placeholder="Hora de entrada" value="{{ isset($schedulemain->check_in_time) ? $schedulemain->check_in_time : '' }}" data-code="{{ $schedulemain->id }}">                                        
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $check_out_time = Carbon::parse($schedulemain->check_out_time)->format('H:i A');
                                    @endphp                                    
                                    <?=( $schedulemain->check_out_time != NULL ? '<span class="badge badge-success w-100">'.$check_out_time.'</span>' : 'SIN HORA DE SALIDA'  )?>
                                </td>

                                {{-- <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        @php
                                            $time = Carbon::parse($schedulemain->end_check_out_time)->format('H:i A');
                                        @endphp
                                        <?=( $schedulemain->end_check_out_time != NULL ? '<span class="badge badge-'.( $schedulemain->extra_hours != NULL && $schedulemain->check_out_time != $schedulemain->end_check_out_time ? 'danger' : 'success' ).' w-100">'.$time.'</span>' : 'SIN HORARIO DE SALIDA' )?>
                                    @else
                                        <input type="text" name="end_check_out_time" class="form-control end_check_out_time" placeholder="Hora de salida final" value="{{ isset($schedulemain->end_check_out_time) ? $schedulemain->end_check_out_time : '' }}" data-code="{{ $schedulemain->id }}">
                                    @endif
                                </td> --}}

                                <td class="text-center">
                                    @php
                                        $time = Carbon::parse($schedulemain->extra_hours)->format('H:i');
                                    @endphp
                                    <?=( $schedulemain->extra_hours != NULL && $schedulemain->extra_hours != "00:00:00" ? '<span class="badge badge-success w-100">'.$time.'</span>' : 'SIN HORAS EXTRAS' )?>
                                </td>

                                {{-- <td class="text-center"><button class="btn btn-dark w-100">{{ isset($schedulemain->vehicle->name) ? $schedulemain->vehicle->name : 'SIN UNIDAD' }} - {{ isset($schedulemain->vehicle->destination_service->name) ? $schedulemain->vehicle->destination_service->name : 'SIN NOMBRE DE VEHÍCULO' }} - {{ isset($schedulemain->vehicle->enterprise->names) ? $schedulemain->vehicle->enterprise->names : 'SIN NOMBRE DE EMPRESA' }}</button></td> --}}
                                <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        {{ isset($schedulemain->vehicle->name) ? $schedulemain->vehicle->name : 'SIN UNIDAD' }} - {{ isset($schedulemain->vehicle->destination_service->name) ? $schedulemain->vehicle->destination_service->name : 'SIN NOMBRE DE VEHÍCULO' }} - {{ isset($schedulemain->vehicle->enterprise->names) ? $schedulemain->vehicle->enterprise->names : 'SIN NOMBRE DE EMPRESA' }}
                                    @else
                                        <select class="form-control schedule_unit" name="unit_id" data-code="{{ $schedulemain->id }}">
                                            <option value="0">Selecciona un unidad</option>
                                            @if ( isset($units) && count($units) >= 1 )
                                                @foreach ($units as $unit)
                                                    <option {{ isset($schedulemain->vehicle_id) && $schedulemain->vehicle_id == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>                                        
                                    @endif                                    
                                </td>                                
                                <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        <?=auth()->user()->renderStatusSchedulesUnit($schedulemain->status_unit)?>
                                    @else
                                        <select class="form-control schedule_status_unit" name="status_unit" data-code="{{ $schedulemain->id }}">
                                            <option value="0">Selecciona una opción</option>
                                            <option {{ isset($schedulemain->status_unit) && $schedulemain->status_unit == "OP" ? 'selected' : '' }} value="OP">OPERACIÓN</option>
                                            <option {{ isset($schedulemain->status_unit) && $schedulemain->status_unit == "S" ? 'selected' : '' }} value="S">SINIESTRO</option>
                                            <option {{ isset($schedulemain->status_unit) && $schedulemain->status_unit == "OPB" ? 'selected' : '' }} value="OPB">OPERACIÓN BAJA</option>
                                            <option {{ isset($schedulemain->status_unit) && $schedulemain->status_unit == "FO" ? 'selected' : '' }} value="FO">FALTA DE OPERADOR</option>
                                            <option {{ isset($schedulemain->status_unit) && $schedulemain->status_unit == "T" ? 'selected' : '' }} value="T">TALLER</option>
                                        </select>
                                    @endif
                                </td>

                                {{-- <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        {{ isset($schedulemain->driver->names) ? $schedulemain->driver->names : 'SIN NOMBRE DE CONDUCTOR' }} {{ isset($schedulemain->driver->surnames) ? $schedulemain->driver->surnames : 'SIN APELLIDO DE CONDUCTOR' }}
                                    @else
                                        <select class="form-control schedule_driver" name="driver_id" data-code="{{ $schedulemain->id }}">
                                            <option value="0">Selecciona un conductor</option>
                                            @if ( isset($drivers) && count($drivers) >= 1 )
                                                @foreach ($drivers as $driver)
                                                    <option {{ isset($schedulemain->driver_id) && $schedulemain->driver_id == $driver->id ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }} - {{ $driver->enterprise->names }}</option>
                                                @endforeach
                                            @endif
                                        </select>                                        
                                    @endif                                    
                                </td> --}}
                                <td class="text-center"><button class="btn btn-dark w-100">{{ isset($schedulemain->driver->names) ? $schedulemain->driver->names : 'SIN NOMBRE' }} - {{ isset($schedulemain->driver->surnames) ? $schedulemain->driver->surnames : 'SIN APELLIDO' }}</button></td>
                                <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        <?=auth()->user()->renderStatusSchedulesDriver($schedulemain->status)?>
                                    @else
                                        <select class="form-control schedule_status_driver" name="status_driver" data-code="{{ $schedulemain->id }}">
                                            <option value="0">Selecciona una opción</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "A" ? 'selected' : '' }} value="A">ASISTENCIA</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "F" ? 'selected' : '' }} value="F">FALTA</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "DT" ? 'selected' : '' }} value="DT">DESCANSO TRABAJADO</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "PSG" ? 'selected' : '' }} value="PSG">PERMISO SIN GOZE</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "INC" ? 'selected' : '' }} value="INC">INCAPACIDAD</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "D" ? 'selected' : '' }} value="D">DESCANSO</option>
                                            <option {{ isset($schedulemain->status) && $schedulemain->status == "V" ? 'selected' : '' }} value="V">VACACIONES</option>
                                        </select>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        {{ $schedulemain->observations }}
                                    @else
                                        <input type="text" class="form-control schedule_comments" placeholder="Comentario opcional" value="{{ isset($schedulemain->observations) ? $schedulemain->observations : '' }}" data-code="{{ $schedulemain->id }}">
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ( $schedulemain->is_open == 0 )
                                        <button class="btn btn-{{ auth()->user()->classStatus($schedulemain->is_open) }} w-100" style="font-size: 13px;">{{ auth()->user()->classStatusText($schedulemain->is_open) }}</button>
                                    @else
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-{{ auth()->user()->classStatus($schedulemain->is_open) }} w-100 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white; font-size:13px;">
                                                {{ auth()->user()->classStatusText($schedulemain->is_open) }}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a href="javascript:void(0);" class="dropdown-item statusSchedule" data-code="{{ $schedulemain->id }}" data-status="1" >ABIERTO</a>
                                                {{-- <a href="javascript:void(0);" class="dropdown-item statusSchedule" data-code="{{ $schedulemain->id }}" data-status="2" >OTRO HORARIO</a> --}}
                                                <a href="javascript:void(0);" class="dropdown-item statusSchedule" data-code="{{ $schedulemain->id }}" data-status="0" >CERRADO</a>
                                            </div>
                                        </div>
                                    @endif                                    
                                </td>
                                {{-- <td class="text-center">
                                    <div class="d-flex flex-column gap-3">
                                        @if ( $schedulemain->is_open == 1 )
                                            <a class="btn btn-primary" href="{{ route('schedules.edit', [$schedulemain->id]) }}" style="font-size: 13px;">Editar</a>    
                                        @endif

                                        @if ( $schedulemain->is_open == 1 )
                                            <form action="{{ route('schedules.destroy', $schedulemain->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" style="font-size: 13px;">Eliminar</button>
                                            </form>                                            
                                        @endif
                                    </div>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <table id="dataSchedules" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Hora entrada</th>
                            <th class="text-center">Hora salida</th>
                            {{-- <th class="text-center">Hora salida/final</th> --}}
                            <th class="text-center">Horas extras</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Estatus unidad</th>
                            <th class="text-center">Conductor</th>                            
                            <th class="text-center">Estatus conductor</th>
                            <th class="text-center">Observaciónes</th>
                            <th class="text-center">Estado</th>
                            {{-- <th></th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedulesSecondary as $schedule)
                            <tr>
                                <td class="text-center">{{ Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                                <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        @php
                                            $check_in_time = Carbon::parse($schedule->check_in_time)->format('H:i A');
                                        @endphp
                                        {{ $schedule->check_in_time != NULL ? $schedule->check_in_time : 'SIN HORE DE ENTRADA' }}
                                    @else
                                        <input type="text" name="check_in_time" class="form-control check_in_time" placeholder="Hora de entrada" value="{{ isset($schedule->check_in_time) ? $schedule->check_in_time : '' }}" data-code="{{ $schedule->id }}">                                        
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $check_out_time = Carbon::parse($schedule->check_out_time)->format('H:i A');
                                    @endphp                                    
                                    <?=( $schedule->check_out_time != NULL ? '<span class="badge badge-success w-100">'.$check_out_time.'</span>' : 'SIN HORA DE SALIDA'  )?>
                                </td>

                                {{-- <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        @php
                                            $time = Carbon::parse($schedule->end_check_out_time)->format('H:i A');
                                        @endphp
                                        <?=( $schedule->end_check_out_time != NULL ? '<span class="badge badge-'.( $schedule->extra_hours != NULL && $schedule->check_out_time != $schedule->end_check_out_time ? 'danger' : 'success' ).' w-100">'.$time.'</span>' : 'SIN HORARIO DE SALIDA' )?>
                                    @else
                                        <input type="text" name="end_check_out_time" class="form-control end_check_out_time" placeholder="Hora de salida final" value="{{ isset($schedule->end_check_out_time) ? $schedule->end_check_out_time : '' }}" data-code="{{ $schedule->id }}">
                                    @endif
                                </td> --}}

                                <td class="text-center">
                                    @php
                                        $time = Carbon::parse($schedule->extra_hours)->format('H:i');
                                    @endphp
                                    <?=( $schedule->extra_hours != NULL && $schedule->extra_hours != "00:00:00" ? '<span class="badge badge-success w-100">'.$time.'</span>' : 'SIN HORAS EXTRAS' )?>
                                </td>

                                {{-- <td class="text-center"><button class="btn btn-dark w-100">{{ isset($schedule->vehicle->name) ? $schedule->vehicle->name : 'SIN UNIDAD' }} - {{ isset($schedule->vehicle->destination_service->name) ? $schedule->vehicle->destination_service->name : 'SIN NOMBRE DE VEHÍCULO' }} - {{ isset($schedule->vehicle->enterprise->names) ? $schedule->vehicle->enterprise->names : 'SIN NOMBRE DE EMPRESA' }}</button></td> --}}
                                <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        {{ isset($schedule->vehicle->name) ? $schedule->vehicle->name : 'SIN UNIDAD' }} - {{ isset($schedule->vehicle->destination_service->name) ? $schedule->vehicle->destination_service->name : 'SIN NOMBRE DE VEHÍCULO' }} - {{ isset($schedule->vehicle->enterprise->names) ? $schedule->vehicle->enterprise->names : 'SIN NOMBRE DE EMPRESA' }}
                                    @else
                                        <select class="form-control schedule_unit" name="unit_id" data-code="{{ $schedule->id }}">
                                            <option value="0">Selecciona un unidad</option>
                                            @if ( isset($units) && count($units) >= 1 )
                                                @foreach ($units as $unit)
                                                    <option {{ isset($schedule->vehicle_id) && $schedule->vehicle_id == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>                                        
                                    @endif                                    
                                </td>                                
                                <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        <?=auth()->user()->renderStatusSchedulesUnit($schedule->status_unit)?>
                                    @else
                                        <select class="form-control schedule_status_unit" name="status_unit" data-code="{{ $schedule->id }}">
                                            <option value="0">Selecciona una opción</option>
                                            <option {{ isset($schedule->status_unit) && $schedule->status_unit == "OP" ? 'selected' : '' }} value="OP">OPERACIÓN</option>
                                            <option {{ isset($schedule->status_unit) && $schedule->status_unit == "S" ? 'selected' : '' }} value="S">SINIESTRO</option>
                                            <option {{ isset($schedule->status_unit) && $schedule->status_unit == "OPB" ? 'selected' : '' }} value="OPB">OPERACIÓN BAJA</option>
                                            <option {{ isset($schedule->status_unit) && $schedule->status_unit == "FO" ? 'selected' : '' }} value="FO">FALTA DE OPERADOR</option>
                                            <option {{ isset($schedule->status_unit) && $schedule->status_unit == "T" ? 'selected' : '' }} value="T">TALLER</option>
                                        </select>
                                    @endif
                                </td> 

                                {{-- <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        {{ isset($schedule->driver->names) ? $schedule->driver->names : 'SIN NOMBRE DE CONDUCTOR' }} {{ isset($schedule->driver->surnames) ? $schedule->driver->surnames : 'SIN APELLIDO DE CONDUCTOR' }}
                                    @else
                                        <select class="form-control schedule_driver" name="driver_id" data-code="{{ $schedule->id }}">
                                            <option value="0">Selecciona un conductor</option>
                                            @if ( isset($drivers) && count($drivers) >= 1 )
                                                @foreach ($drivers as $driver)
                                                    <option {{ isset($schedule->driver_id) && $schedule->driver_id == $driver->id ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }} - {{ $driver->enterprise->names }}</option>
                                                @endforeach
                                            @endif
                                        </select>                                        
                                    @endif                                    
                                </td> --}}
                                <td class="text-center"><button class="btn btn-dark w-100">{{ isset($schedule->driver->names) ? $schedule->driver->names : 'SIN NOMBRE' }} - {{ isset($schedule->driver->surnames) ? $schedule->driver->surnames : 'SIN APELLIDO' }}</button></td>
                                <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        <?=auth()->user()->renderStatusSchedulesDriver($schedule->status)?>
                                    @else
                                        <select class="form-control schedule_status_driver" name="status_driver" data-code="{{ $schedule->id }}">
                                            <option value="0">Selecciona una opción</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "A" ? 'selected' : '' }} value="A">ASISTENCIA</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "F" ? 'selected' : '' }} value="F">FALTA</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "DT" ? 'selected' : '' }} value="DT">DESCANSO TRABAJADO</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "PSG" ? 'selected' : '' }} value="PSG">PERMISO SIN GOZE</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "INC" ? 'selected' : '' }} value="INC">INCAPACIDAD</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "D" ? 'selected' : '' }} value="D">DESCANSO</option>
                                            <option {{ isset($schedule->status) && $schedule->status == "V" ? 'selected' : '' }} value="V">VACACIONES</option>
                                        </select>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        {{ $schedule->observations }}
                                    @else
                                        <input type="text" class="form-control schedule_comments" placeholder="Comentario opcional" value="{{ isset($schedule->observations) ? $schedule->observations : '' }}" data-code="{{ $schedule->id }}">
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ( $schedule->is_open == 0 )
                                        <button class="btn btn-{{ auth()->user()->classStatus($schedule->is_open) }} w-100" style="font-size: 13px;">{{ auth()->user()->classStatusText($schedule->is_open) }}</button>
                                    @else
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-{{ auth()->user()->classStatus($schedule->is_open) }} w-100 dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color:white; font-size:13px;">
                                                {{ auth()->user()->classStatusText($schedule->is_open) }}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                            </button>
                                            <div class="dropdown-menu" style="">
                                                <a href="javascript:void(0);" class="dropdown-item statusSchedule" data-code="{{ $schedule->id }}" data-status="1" >ABIERTO</a>
                                                {{-- <a href="javascript:void(0);" class="dropdown-item statusSchedule" data-code="{{ $schedule->id }}" data-status="2" >OTRO HORARIO</a> --}}
                                                <a href="javascript:void(0);" class="dropdown-item statusSchedule" data-code="{{ $schedule->id }}" data-status="0" >CERRADO</a>
                                            </div>
                                        </div>
                                    @endif                                    
                                </td>
                                {{-- <td class="text-center">
                                    <div class="d-flex flex-column gap-3">
                                        @if ( $schedule->is_open == 1 )
                                            <a class="btn btn-primary" href="{{ route('schedules.edit', [$schedule->id]) }}" style="font-size: 13px;">Editar</a>    
                                        @endif

                                        @if ( $schedule->is_open == 1 )
                                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" style="font-size: 13px;">Eliminar</button>
                                            </form>                                            
                                        @endif
                                    </div>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>                
            </div>   
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" />    
@endsection