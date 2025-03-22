@php
    use App\Traits\RoleTrait;
    use Carbon\Carbon;
    Carbon::setLocale('es');
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/types_cancellations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/types_cancellations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/types_cancellations.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar Horario de conductor',
                'className' => 'btn btn-primary ',
                'url' => route('schedules.create')
            )
        );
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

                <table id="dataSchedules" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Fecha</th>
                            <th class="text-center">Hora entrada</th>
                            <th class="text-center">Hora salida</th>
                            <th class="text-center">Hora salida/final</th>
                            <th class="text-center">Horas extras</th>
                            <th class="text-center">Unidad</th>
                            <th class="text-center">Driver</th>
                            <th class="text-center">Estatus</th>
                            <th class="text-center">Observaciónes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $schedule)
                            <tr>
                                <td class="text-center">{{ Carbon::parse($schedule->date)->translatedFormat('d F Y') }}</td>
                                <td class="text-center">{{ Carbon::parse($schedule->check_in_time)->format('H:i A') }}</td>
                                <td class="text-center"><span class="badge badge-success w-100">{{ Carbon::parse($schedule->check_out_time)->format('H:i A') }}</span></td>
                                <td class="text-center">
                                    @php
                                        $time = Carbon::parse($schedule->end_check_out_time)->format('H:i A');
                                    @endphp
                                    <?=( $schedule->end_check_out_time != NULL ? '<span class="badge badge-'.( $schedule->extra_hours != NULL ? 'danger' : 'success' ).' w-100">'.$time.'</span>' : 'NO DEFINIDO' )?>
                                </td>
                                <td class="text-center">
                                    @php
                                        $time = Carbon::parse($schedule->extra_hours)->format('H:i');
                                    @endphp                                    
                                    <?=( $schedule->extra_hours != NULL ? '<span class="badge badge-success w-100">'.$time.'</span>' : 'NO DEFINIDO' )?>
                                </td>
                                <td class="text-center"><button class="btn btn-dark w-100">{{ isset($schedule->vehicle->name) ? $schedule->vehicle->name : 'NO DEFINIDO' }} - {{ isset($schedule->vehicle->destination_service->name) ? $schedule->vehicle->destination_service->name : 'NO DEFINIDO' }} - {{ isset($schedule->vehicle->enterprise->names) ? $schedule->vehicle->enterprise->names : 'NO DEFINIDO' }}</button></td>
                                <td class="text-center">{{ isset($schedule->driver->names) ? $schedule->driver->names : 'NO DEFINIDO' }} {{ isset($schedule->driver->surnames) ? $schedule->driver->surnames : 'NO DEFINIDO' }}</td>
                                <td class="text-center">
                                    @if ( $schedule->status != NULL )
                                        <button class="btn btn-{{ $schedule->status == "DT" ? 'info' : ( $schedule->status == "F" ? 'danger' : 'success' ) }} w-100">{{ $schedule->status }}</button>
                                    @else
                                        {{ "NO DEFINIDO" }}                                        
                                    @endif
                                </td>
                                <td class="text-center">{{ $schedule->observations }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-3">
                                        <a class="btn btn-primary" href="{{ route('schedules.edit', [$schedule->id]) }}">Editar</a>
                                        <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>   
        </div>
    </div>
@endsection