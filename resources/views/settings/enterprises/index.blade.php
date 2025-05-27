@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/enterprises.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/enterprises.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/enterprises.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar una empresa',
                'className' => 'btn btn-primary ',
                'url' => route('enterprises.create')
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

                <table id="dataEnterprises" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-left">DATOS</th>
                            <th class="text-center">DATOS FISCALES</th>
                            <th class="text-center">DÍAS DE CREDITO</th>
                            <th class="text-center">FACTURACIÓN</th>
                            <th class="text-center">TARIFAS</th>
                            <th class="text-center">EXTRANJERO</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">ESTATUS</th>
                            <th class="text-center">TIPO</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enterprises as $enterprise)
                            <tr>
                                <td class="text-left">
                                    <p class="mb-1"><strong>Nombre:</strong> {{ $enterprise->names }}</p>
                                    <p class="mb-1"><strong>Dirección:</strong> {{ $enterprise->address ? $enterprise->address : 'SIN DIRECCIÓN' }}</p>
                                    <p class="mb-1"><strong>Teléfono:</strong> {{ $enterprise->phone ? $enterprise->phone : 'SIN TELÉFONO' }}</p>
                                    <p class="mb-3"><strong>Correo:</strong> {{ $enterprise->email ? $enterprise->email : 'SIN CORREO' }}</p>
                                    <p class="mb-1"><strong>Contacto de agencia:</strong> {{ $enterprise->company_contact ? $enterprise->company_contact : 'SIN CONTACTO' }}</p>
                                </td>
                                <td class="text-left">
                                    <p class="mb-1"><strong>Razon social:</strong> {{ $enterprise->company_name_invoice ? $enterprise->company_name_invoice : 'SIN RAZON SOCIAL' }}</p>
                                    <p class="mb-1"><strong>RFC:</strong> {{ $enterprise->company_rfc_invoice ? $enterprise->company_rfc_invoice : 'SIN RFC' }}</p>
                                    <p class="mb-1"><strong>Dirección:</strong> {{ $enterprise->company_address_invoice ? $enterprise->company_address_invoice : 'SIN DIRECCIÓN' }}</p>
                                    <p class="mb-3"><strong>Correo:</strong> {{ $enterprise->company_email_invoice ? $enterprise->company_email_invoice : 'SIN CORREO' }}</p>
                                </td>
                                <td class="text-center">
                                    {{ $enterprise->credit_days }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->is_invoice_iva == 1 ) ? 'success' : 'danger' }} mb-2">{{ ( $enterprise->is_invoice_iva == 1 ) ? 'CON IVA' : 'SIN IVA' }}</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->is_rates_iva == 1 ) ? 'success' : 'danger' }} mb-2">{{ ( $enterprise->is_rates_iva == 1 ) ? 'CON IVA' : 'SIN IVA' }}</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->is_foreign == 1 ) ? 'success' : 'danger' }} mb-2">{{ ( $enterprise->is_foreign == 1 ) ? 'SÍ' : 'NO' }}</button>
                                </td>
                                <td class="text-center">
                                    {{ $enterprise->currency }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->status == 1 ) ? 'success' : 'danger' }} mb-2">{{ ( $enterprise->status == 1 ) ? 'ACTIVO' : 'INACTIVO' }}</button>
                                </td>

                                {{-- <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->is_external == 0 ) ? 'success' : 'danger' }} mb-2">{{ ( $enterprise->is_external == 0 ) ? 'Interno' : 'Externo' }}</button>
                                </td> --}}
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->type_enterprise == "PROVIDER" ) ? 'success' : 'primary' }} mb-2">{{ ( $enterprise->type_enterprise == "PROVIDER" ) ? 'Proveedor' : 'Cliente' }}</button>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-3">
                                        <a class="btn btn-primary" href="{{ route('enterprises.edit', [$enterprise->id]) }}">Editar</a>
                                        {{-- <form action="{{ route('enterprises.destroy', $enterprise->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                        </form> --}}
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