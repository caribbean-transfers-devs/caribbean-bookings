@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Editar sitio @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/enterprise.min.js') }}"></script>
@endpush

@section('content')
    <div class="account-settings-container layout-top-spacing">
        <div class="account-content">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="info">
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
                            <form action="{{ isset($enterprise) ? route('sites.store.enterprise', $enterprise->id) : route('sites.update', $site->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( !isset($enterprise) )
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Nombre del sitio</label>
                                            <input type="text" id="name" name="name" class="form-control mb-3" placeholder="Nombre del sitio" value="{{ ( isset($site->name) ? $site->name : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="logo">Url de logo</label>
                                            <input type="url" id="logo" name="logo" class="form-control mb-3" placeholder="Url de logo" value="{{ ( isset($site->logo) ? $site->logo : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payment_domain">Dominio</label>
                                            <input type="url" id="payment_domain" name="payment_domain" class="form-control mb-3" placeholder="Dominio" value="{{ ( isset($site->payment_domain) ? $site->payment_domain : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="color">Color</label>
                                            <input type="color" id="color" name="color" class="form-control mb-3" placeholder="color" value="{{ ( isset($site->color) ? $site->color : '' ) }}">
                                        </div>
                                    </div>                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transactional_email">Correo</label>
                                            <input type="email" id="transactional_email" name="transactional_email" class="form-control mb-3" placeholder="Correo" value="{{ ( isset($site->transactional_email) ? $site->transactional_email : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transactional_email_send">Permitir envio de correo</label>
                                            <select name="transactional_email_send" id="transactional_email_send" class="form-control mb-3">
                                                <option value="1">Sí</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="transactional_phone">Teléfono</label>
                                            <input type="tel" id="transactional_phone" name="transactional_phone" class="form-control mb-3" placeholder="Teléfono" value="{{ ( isset($site->transactional_phone) ? $site->transactional_phone : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="is_commissionable">Es comisionable</label>
                                            <select name="is_commissionable" id="is_commissionable" class="form-control mb-3">
                                                <option {{ ( isset($site->is_commissionable) && $site->is_commissionable == 0 ) ? 'selected' : '' }} value="0">No</option>
                                                <option {{ ( isset($site->is_commissionable) && $site->is_commissionable == 1 ) ? 'selected' : '' }} value="1">Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="is_cxc">CxC (Cuentas por cobrar)</label>
                                            <select name="is_cxc" id="is_cxc" class="form-control mb-3">
                                                <option {{ ( isset($site->is_cxc) && $site->is_cxc == 0 ) ? 'selected' : '' }} value="0">No</option>
                                                <option {{ ( isset($site->is_cxc) && $site->is_cxc == 1 ) ? 'selected' : '' }} value="1">Sí</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="is_cxp">CxP (Cuentas por pagar)</label>
                                            <select name="is_cxp" id="is_cxp" class="form-control mb-3">
                                                <option {{ ( isset($site->is_cxp) && $site->is_cxp == 0 ) ? 'selected' : '' }} value="0">No</option>
                                                <option {{ ( isset($site->is_cxp) && $site->is_cxp == 1 ) ? 'selected' : '' }} value="1">Sí</option>
                                            </select>
                                        </div>
                                    </div>                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="success_payment_url">Url de pago satisfactorio</label>
                                            <input type="text" id="success_payment_url" name="success_payment_url" class="form-control mb-3" placeholder="Url de pago satisfactorio" value="{{ ( isset($site->success_payment_url) ? $site->success_payment_url : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="cancel_payment_url">Url de pago cancelado</label>
                                            <input type="text" id="cancel_payment_url" name="cancel_payment_url" class="form-control mb-3" placeholder="Url de pago cancelado" value="{{ ( isset($site->cancel_payment_url) ? $site->cancel_payment_url : '' ) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type_site">Tipo de sitio</label>
                                            <select id="type_site" name="type_site" class="form-control mb-3">
                                                <option {{ ( isset($site->type_site) && $site->type_site == "PLATFORM" ) ? 'selected' : '' }} value="PLATFORM">PLATFORM</option>
                                                <option {{ ( isset($site->type_site) && $site->type_site == "CALLCENTER" ) ? 'selected' : '' }} value="CALLCENTER">CALLCENTER</option>
                                                <option {{ ( isset($site->type_site) && $site->type_site == "AGENCY" ) ? 'selected' : '' }} value="AGENCY">AGENCY</option>
                                                <option {{ ( isset($site->type_site) && $site->type_site == "TICKETOFFICE" ) ? 'selected' : '' }} value="TICKETOFFICE">TICKETOFFICE</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('enterprise.sites', ( isset($enterprise) ? $enterprise->id : $site->enterprise_id )) }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($enterprise) ? 'Guardar' : 'Actualizar' ) }}</button>
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