@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Tipo de cancelación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')    
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

                            <form action="{{ isset($sale) ? route('types.sales.update', $sale->id) : route('types.sales.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($sale) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Nombre del tipo de venta</label>
                                            <input type="text" id="name" name="name" class="form-control mb-3" placeholder="Nombre" value="{{ ( isset($sale->name) ? $sale->name : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Tarifa a reportar</label>
                                            <input type="number" id="rate_report" name="rate_report" class="form-control mb-3" placeholder="Tarifa a reportar" value="{{ ( isset($sale->rate_report) ? $sale->rate_report : '' ) }}" value="1.00">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="name">Tarifa publica</label>
                                            <input type="number" id="public_rate" name="public_rate" class="form-control mb-3" placeholder="Tarifa publica" value="{{ ( isset($sale->public_rate) ? $sale->public_rate : '' ) }}" value="1.00">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status">Selecciona tipo</label>
                                            <select id="status" name="status" class="form-control mb-3">
                                                <option {{ ( isset($sale->status) && $sale->status == 'public' ) ? 'selected' : '' }} value="public">Publico</option>
                                                <option {{ ( isset($sale->status) && $sale->status == 0 ) ? 'private' : '' }} value="private">Privado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('types.sales.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($sale) ? 'Actualizar' : 'Guardar' ) }}</button>
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