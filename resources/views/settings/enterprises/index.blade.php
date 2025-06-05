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
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="alert alert-icon-left alert-light-primary alert-dismissible fade show mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <strong>Información</strong> Solo las empresas que sean de tipo <strong>"CLIENTE"</strong>, tienen permitido tener sitios, ya que son los que se muestran en el listado del <strong>TPV</strong>, a excepción de las empresas <strong>"Caribbean Transfers, Caribbean Taxi"</strong>, que están como "PROVEEDORES".
            </div>

            <div class="alert alert-icon-left alert-light-primary alert-dismissible fade show mb-4" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <strong>Información</strong> Se agregaron identificadores en los botones de sitios, zonas y tarifas, para que sea mas facil identificar si ya se le cargo la información, sobre este identificador no aplica para las empresas <strong>"Caribbean Transfers, Caribbean Taxi"</strong>, ya que esta ya cuenta con esta información.
            </div>

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
                                    <button class="btn btn-{{ ( $enterprise->is_invoice_iva == 1 ) ? 'success' : 'danger' }}" style="font-size: 13px;">{{ ( $enterprise->is_invoice_iva == 1 ) ? 'Con I.V.A' : 'Sin I.V.A' }}</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->is_rates_iva == 1 ) ? 'success' : 'danger' }}" style="font-size: 13px;">{{ ( $enterprise->is_rates_iva == 1 ) ? 'Con I.V.A' : 'Sin I.V.A' }}</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->is_foreign == 1 ) ? 'success' : 'danger' }}" style="font-size: 13px;">{{ ( $enterprise->is_foreign == 1 ) ? 'Sí' : 'No' }}</button>
                                </td>
                                <td class="text-center">
                                    {{ $enterprise->currency }}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->status == 1 ) ? 'success' : 'danger' }}" style="font-size: 13px;">{{ ( $enterprise->status == 1 ) ? 'activo' : 'Inactivo' }}</button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $enterprise->type_enterprise == "PROVIDER" ) ? 'success' : 'primary' }}" style="font-size: 13px;">{{ ( $enterprise->type_enterprise == "PROVIDER" ) ? 'Proveedor' : 'Cliente' }}</button>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-2">
                                        @if ( ( ($enterprise->type_enterprise == "CUSTOMER") || ( ($enterprise->type_enterprise == "CUSTOMER" || $enterprise->type_enterprise == "PROVIDER") && $enterprise->is_external == 0 ) ) && auth()->user()->hasPermission(102) )
                                            <a class="btn btn-primary w-100 position-relative overflow-visible" href="{{ route('enterprises.sites.index', [$enterprise->id]) }}" style="font-size: 13px;">                                                
                                                <span class="btn-text-inner">Sitios</span>
                                                @if ($enterprise->is_external == 1)
                                                    <span class="badge badge-danger counter">{{ $enterprise->sites_count }}</span>
                                                @endif                                                
                                            </a>
                                        @endif

                                        {{-- ZONAS DE PAGINA WEB --}}
                                        @if ( $enterprise->is_external == 0 && auth()->user()->hasPermission(28) )
                                            <a class="btn btn-secondary w-100" href="{{ route('enterprises.zones.web.index', [$enterprise->id]) }}" style="font-size: 13px;">Zonas Web</a>                                            
                                        @endif
                                        {{-- ZONAS DE AGENCIA --}}                              
                                        @if ( $enterprise->is_external == 1 )
                                            <a class="btn btn-secondary w-100 position-relative overflow-visible" href="{{ route('enterprises.zones.index',     [$enterprise->id]) }}" style="font-size: 13px;">                                                
                                                <span class="btn-text-inner">Zonas</span>
                                                <span class="badge badge-danger counter">{{ $enterprise->zones_enterprises_count }}</span>
                                            </a>
                                        @endif

                                        {{-- TARIFA DE PAGINA WEB --}}
                                        @if ( $enterprise->is_external == 0 && auth()->user()->hasPermission(32) )
                                            <a class="btn btn-success w-100" href="{{ route('enterprises.rates.web.index',    [$enterprise->id]) }}" style="font-size: 13px;">Tarifas Web</a>
                                        @endif
                                        {{-- TARIFA DE AGENCIA --}}
                                        @if ( $enterprise->is_external == 1 && auth()->user()->hasPermission(104) )
                                            <a class="btn btn-success w-100 position-relative overflow-visible" href="{{ route('enterprises.rates.index',        [$enterprise->id]) }}" style="font-size: 13px;">                                            
                                                <span class="btn-text-inner">Tarifas</span>
                                                <span class="badge badge-danger counter">{{ $enterprise->rates_enterprises_count }}</span>
                                            </a>
                                        @endif
                                        
                                        {{-- SOLO PERMITE EDITAR A EMPRESA EXTERNAS --}}
                                        @if ( $enterprise->is_external == 1 )
                                            <a class="btn btn-primary w-100" href="{{ route('enterprises.edit', [$enterprise->id]) }}" style="font-size: 13px;">Editar</a>
                                            <form action="{{ route('enterprises.destroy', $enterprise->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100" style="font-size: 13px;">Eliminar</button>
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