@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/enterprises.min.js') }}"></script>
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

                            <form action="{{ isset($enterprise) ? route('enterprises.update', $enterprise->id) : route('enterprises.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if ( isset($enterprise) )
                                    @method('PUT')
                                @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-2 text-uppercase fw-bold">Datos generales</h6>
                                        <div class="row">
                                            <div class="col-md-3 mb-3 d-none">
                                                <div class="form-group">
                                                    <label for="isExternal">Selecciona si es interno o externo</label>
                                                    <select name="is_external" id="isExternal" class="form-control">
                                                        <option {{ ( isset($enterprise->is_external) && $enterprise->is_external == 0 ) ? 'selected' : '' }} value="0">Interno</option>
                                                        <option {{ ( isset($enterprise->is_external) && $enterprise->is_external == 1 ) ? 'selected' : '' }} value="1">Externo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="companyName">Nombre de la empresa*</label>
                                                    <input type="text" name="names" id="companyName" class="form-control" placeholder="Nombre de la empresa" value="{{ old('names', $enterprise->names ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="companyAddress">Dirección de la empresa*</label>
                                                    <input type="text" name="address" id="companyAddress" class="form-control" placeholder="Dirección de la empresa" value="{{ old('address', $enterprise->address ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="companyPhone">Teléfono de la empresa*</label>
                                                    <input type="number" name="phone" id="companyPhone" class="form-control" placeholder="Teléfono de la empresa" value="{{ old('phone', $enterprise->phone ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="companyEmail">Correo de la empresa*</label>
                                                    <input type="email" name="email" id="companyEmail" class="form-control" placeholder="Correo de la empresa" value="{{ old('email', $enterprise->email ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="companyContactName">Nombre del contacto</label>
                                                    <input type="text" name="company_contact" id="companyContactName" class="form-control" placeholder="Nombre del contacto" value="{{ old('company_contact', $enterprise->company_contact ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group mb-3">
                                                    <label for="companyContactName">Días de crédito*</label>
                                                    <input type="number" name="credit_days" id="creditDays" class="form-control" min="0" max="10000" placeholder="Días de crédito" value="{{ old('credit_days', $enterprise->credit_days ?? 0) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h6 class="mb-2 text-uppercase fw-bold">Datos fiscales</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label for="companyNameInvoice">Razón social</label>
                                                    <input type="text" name="company_name_invoice" id="companyNameInvoice" class="form-control" placeholder="Razon social" value="{{ old('company_name_invoice', $enterprise->company_name_invoice ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label for="companyRfcInvoice">RFC</label>
                                                    <input type="text" name="company_rfc_invoice" id="companyRfcInvoice" class="form-control" placeholder="RFC" value="{{ old('company_rfc_invoice', $enterprise->company_rfc_invoice ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label for="companyAddressInvoice">Dirección fiscal</label>
                                                    <input type="text" name="company_address_invoice" id="companyAddressInvoice" class="form-control" placeholder="Dirección" value="{{ old('company_address_invoice', $enterprise->company_address_invoice ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label for="companyEmailInvoice">Correo fiscal</label>
                                                    <input type="email" name="company_email_invoice" id="companyEmailInvoice" class="form-control" placeholder="Correo" value="{{ old('company_email_invoice', $enterprise->company_email_invoice ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- -------------------------------------------------------------- --}}

                                    <div class="col-md-12">
                                        <h6 class="mb-2 text-uppercase fw-bold">Configuración</h6>
                                        <div class="row">
                                            <div class="col-md-4 d-flex flex-column">
                                                <label for="invoiceIva">Facturación</label>
                                                <div class="switch form-switch-custom switch-inline form-switch-success form-switch-custom inner-label-toggle mb-3 {{ old('is_invoice_iva', $enterprise->is_invoice_iva ?? 0) ? 'show' : '' }}">
                                                    <div class="input-checkbox">
                                                        <span class="switch-chk-label label-left">Sin IVA</span>
                                                        <input class="switch-input" type="checkbox" role="switch" value="{{ old('is_invoice_iva', $enterprise->is_invoice_iva ?? 0) }}" id="invoiceIva" name="is_invoice_iva" onchange="this.checked ? (this.closest('.inner-label-toggle').classList.add('show'), this.value = 1) : (this.closest('.inner-label-toggle').classList.remove('show'), this.value = 0)" {{ old('is_invoice_iva', $enterprise->is_invoice_iva ?? 0) ? 'checked' : '' }}>
                                                        <span class="switch-chk-label label-right">Con IVA</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex flex-column">
                                                <label for="ratesIva">Tarifas</label>
                                                <div class="switch form-switch-custom switch-inline form-switch-success form-switch-custom inner-label-toggle mb-3 {{ old('is_rates_iva', $enterprise->is_rates_iva ?? 0) ? 'show' : '' }}">
                                                    <div class="input-checkbox">
                                                        <span class="switch-chk-label label-left">Sin IVA</span>
                                                        <input class="switch-input" type="checkbox" role="switch" value="{{ old('is_rates_iva', $enterprise->is_rates_iva ?? 0) }}" id="ratesIva" name="is_rates_iva" onchange="this.checked ? (this.closest('.inner-label-toggle').classList.add('show'), this.value = 1) : (this.closest('.inner-label-toggle').classList.remove('show'), this.value = 0)" {{ old('is_rates_iva', $enterprise->is_rates_iva ?? 0) ? 'checked' : '' }}>
                                                        <span class="switch-chk-label label-right">Con IVA</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 d-flex flex-column">
                                                <label for="isForeign">Extranjero</label>
                                                <div class="switch form-switch-custom switch-inline form-switch-success form-switch-custom inner-label-toggle mb-3 {{ old('is_foreign', $enterprise->is_foreign ?? 0) ? 'show' : '' }}">
                                                    <div class="input-checkbox">
                                                        <span class="switch-chk-label label-left">No</span>
                                                        <input class="switch-input" type="checkbox" role="switch" value="{{ old('is_foreign', $enterprise->is_foreign ?? 0) }}" id="isForeign" name="is_foreign" onchange="this.checked ? (this.closest('.inner-label-toggle').classList.add('show'), this.value = 1) : (this.closest('.inner-label-toggle').classList.remove('show'), this.value = 0)" {{ old('is_foreign', $enterprise->is_foreign ?? 0) ? 'checked' : '' }}>
                                                        <span class="switch-chk-label label-right">Sí</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label for="is_external">Moneda*</label>
                                                    <select id="is_external" name="currency" class="form-control">
                                                        <option {{ old('currency', $enterprise->currency ?? 'MXN') == 'MXN' ? 'selected' : '' }} value="MXN">MXN</option>
                                                        <option {{ old('currency', $enterprise->currency ?? 'MXN') == 'USD' ? 'selected' : '' }} value="USD">USD</option>
                                                    </select>
                                                </div>
                                            </div>                                            
                                            <div class="col-md-3 mb-3">
                                                <div class="form-group">
                                                    <label for="is_external">Estatus*</label>
                                                    <select id="is_external" name="status" class="form-control">
                                                        <option {{ old('status', $enterprise->status ?? 1) == 1 ? 'selected' : '' }} value="1">Activo</option>
                                                        <option {{ old('status', $enterprise->status ?? 1) == 0 ? 'selected' : '' }} value="0">Inactivo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="is_external">Tipo de empresa</label>
                                                    <select id="is_external" name="type_enterprise" class="form-control mb-3">
                                                        <option {{ old('type_enterprise', $enterprise->type_enterprise ?? '') == 'PROVIDER' ? 'selected' : '' }} value="PROVIDER">Proveedor</option>
                                                        <option {{ old('type_enterprise', $enterprise->type_enterprise ?? '') == 'CUSTOMER' ? 'selected' : '' }} value="CUSTOMER">Cliente</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-between">
                                        <a class="btn btn-danger" href="{{ route('enterprises.index') }}">Cancelar</a>
                                        <button type="submit" class="btn btn-primary">{{ ( isset($enterprise) ? 'Actualizar' : 'Guardar' ) }}</button>
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