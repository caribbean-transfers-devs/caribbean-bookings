@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Restricciones de Horarios @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/schedule_restrictions.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/schedule_restrictions.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/schedule_restrictions.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
        if( auth()->user()->hasPermission(136) ):
            $buttons[] = array(
                'text'      => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar restricción',
                'className' => 'btn btn-primary',
                'url'       => route('config.schedule-restrictions.create')
            );
        endif;
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
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

                <table id="dataScheduleRestrictions" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">NOMBRE</th>
                            <th class="text-center">INICIO</th>
                            <th class="text-center">FIN</th>
                            <th class="text-center">ACTIVA</th>
                            <th class="text-center" style="width:60px;">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($restrictions as $restriction)
                            @php $now = now(); @endphp
                            <tr style="{{ $restriction->is_active && $now->between($restriction->start_at, $restriction->end_at) ? 'background-color:#00ab5533;' : '' }}">
                                <td class="text-center">{{ $restriction->name }}</td>
                                <td class="text-center">{{ $restriction->start_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center">{{ $restriction->end_at->format('Y-m-d H:i') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-{{ $restriction->is_active ? 'success' : 'danger' }} mb-2">{{ $restriction->is_active ? 'Activa' : 'Inactiva' }}</button>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @if ( auth()->user()->hasPermission(137) )
                                            <a class="btn btn-primary btn-sm" href="{{ route('config.schedule-restrictions.edit', $restriction->id) }}" title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </a>
                                        @endif

                                        @if ( auth()->user()->hasPermission(138) )
                                            <form class="form-delete-restriction" action="{{ route('config.schedule-restrictions.destroy', $restriction->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm btn-confirm-delete" title="Eliminar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4h6v2"></path></svg>
                                                </button>
                                            </form>
                                        @endif
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
