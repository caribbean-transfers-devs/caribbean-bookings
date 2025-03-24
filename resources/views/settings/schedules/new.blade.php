@php
    use App\Traits\FiltersTrait;
    $units = FiltersTrait::Units('active'); //LAS UNIDADES DADAS DE ALTA
    $drivers = FiltersTrait::Drivers('active');
@endphp
@extends('layout.app')
@section('title') Vehiculos @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/driver_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/schedules.min.js') }}"></script>
@endpush

@section('content')
    <div class="account-settings-container layout-top-spacing">
        <div class="account-content">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="info">
                            @if ($errors->any())
                                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
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
                                                        
                            <form action="{{ isset($schedule) ? route('schedules.update', $schedule->id) : route('schedules.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($schedule) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-{{ isset($schedule) ? '3' : '2' }}">
                                        <div class="form-group mb-3">
                                            <label for="date_schedule">Selecciona una fecha</label>
                                            <input type="text" id="date_schedule" name="date" class="form-control" placeholder="Selecciona una fecha" value="{{ isset($schedule->date) ? $schedule->date : date('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-{{ isset($schedule) ? '3' : '2' }}">
                                        <div class="form-group mb-3">
                                            <label for="check_in_time">Hora de entrada</label>
                                            <input type="text" id="check_in_time" name="check_in_time" class="form-control" placeholder="Hora de entrada" value="{{ isset($schedule->check_in_time) ? $schedule->check_in_time : '00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-{{ isset($schedule) ? '3' : '2' }}">
                                        <div class="form-group mb-3">
                                            <label for="check_out_time">Hora de salida</label>
                                            <input type="text" id="check_out_time" name="check_out_time" class="form-control" placeholder="Hora de salida" value="{{ isset($schedule->check_out_time) ? $schedule->check_out_time : '00' }}">
                                        </div>
                                    </div>
                                    @if ( isset($schedule) )
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label for="end_check_out_time">Hora de salida final</label>
                                                <input type="text" id="end_check_out_time" name="end_check_out_time" class="form-control" placeholder="Hora de salida final" value="{{ isset($schedule->end_check_out_time) ? $schedule->end_check_out_time : '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="status">Selecciona un estatus</label>
                                                <select class="form-control selectpicker" data-live-search="true" id="status" name="status">
                                                    <option value="0">Selecciona una opción</option>
                                                    <option {{ isset($schedule->status) && $schedule->status == "A" ? 'selected' : '' }} value="A">A</option>
                                                    <option {{ isset($schedule->status) && $schedule->status == "F" ? 'selected' : '' }} value="F">F</option>
                                                    <option {{ isset($schedule->status) && $schedule->status == "DT" ? 'selected' : '' }} value="DT">DT</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-{{ isset($schedule) ? '4' : '3' }}">
                                        <div class="form-group mb-3">
                                            <label for="vehicle_id">Selecciona una unidad</label>
                                            <select class="form-control selectpicker" data-live-search="true" id="vehicle_id" name="vehicle_id">
                                                <option value="0">Selecciona una unidad</option>
                                                @if ( isset($units) && count($units) >= 1 )
                                                    @foreach ($units as $unit)
                                                        <option {{ isset($schedule->vehicle_id) && $schedule->vehicle_id == $unit->id ? 'selected' : '' }} value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->destination_service->name }} - {{ $unit->enterprise->names }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-{{ isset($schedule) ? '4' : '3' }}">
                                        <div class="form-group mb-3">
                                            <label for="driver_id">Selecciona un conductor</label>
                                            <select class="form-control selectpicker" data-live-search="true" id="driver_id" name="driver_id">
                                                <option value="0">Selecciona un conductor</option>
                                                @if ( isset($drivers) && count($drivers) >= 1 )
                                                    @foreach ($drivers as $driver)
                                                        <option {{ isset($schedule->driver_id) && $schedule->driver_id == $driver->id ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                    @if ( isset($schedule) )
                                        <div class="col-md-12">
                                            <div class="form-group mb-3">
                                                <label for="observations">Observaciones</label>
                                                <textarea class="form-control" name="observations" id="observations" cols="30" rows="10">{{ isset($schedule->observations) ? $schedule->observations : '' }}</textarea>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('schedules.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($schedule) ? 'Actualizar' : 'Guardar' ) }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection