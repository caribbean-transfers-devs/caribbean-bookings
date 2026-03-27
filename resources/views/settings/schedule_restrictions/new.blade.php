@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Restricción de Horario @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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

                            <form action="{{ isset($restriction) ? route('config.schedule-restrictions.update', $restriction->id) : route('config.schedule-restrictions.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($restriction) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nombre de la restricción</label>
                                            <input type="text" id="name" name="name" class="form-control mb-3" placeholder="Ej. Nochebuena 2026" value="{{ isset($restriction->name) ? $restriction->name : old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="is_active">Estatus</label>
                                            <select id="is_active" name="is_active" class="form-control mb-3">
                                                <option {{ ( isset($restriction->is_active) && $restriction->is_active == 1 ) ? 'selected' : '' }} value="1">Activa</option>
                                                <option {{ ( isset($restriction->is_active) && $restriction->is_active == 0 ) ? 'selected' : '' }} value="0">Inactiva</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_at">Inicio de la restricción</label>
                                            <input type="text" id="start_at" name="start_at" class="form-control mb-3" placeholder="YYYY-MM-DD HH:MM" value="{{ isset($restriction->start_at) ? $restriction->start_at->format('Y-m-d H:i') : old('start_at') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_at">Fin de la restricción</label>
                                            <input type="text" id="end_at" name="end_at" class="form-control mb-3" placeholder="YYYY-MM-DD HH:MM" value="{{ isset($restriction->end_at) ? $restriction->end_at->format('Y-m-d H:i') : old('end_at') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('config.schedule-restrictions.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ isset($restriction) ? 'Actualizar' : 'Guardar' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#start_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
            });
            flatpickr("#end_at", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
            });
        });
    </script>
@endsection
