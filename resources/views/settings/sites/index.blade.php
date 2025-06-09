@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Sitios de empresa @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/settings/sites.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/settings/sites.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/settings/sites.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Agregar un sitio',
                'className' => 'btn btn-primary ',
                'url' => route('enterprises.sites.create', [( isset($sites->id) ? $sites->id : 0 )])
            )
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
                @if ($errors->any())
                    <div class="alert alert-light-alert alert-dismissible fade show border-0 mb-4" role="alert">
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

                <table id="dataSites" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Link logo</th>
                            <th class="text-center">Dominio</th>
                            <th class="text-center">Color</th>
                            <th class="text-center">Correo</th>
                            <th class="text-center">Envio de correo</th>
                            <th class="text-center">Télefono</th>
                            <th class="text-center">Comisionable</th>
                            <th class="text-center">CxC</th>
                            <th class="text-center">CxP</th>
                            <th class="text-center">Url de pago correcto</th>
                            <th class="text-center">Url de pago cancelado</th>
                            <th class="text-center">Tipo de sitio</th>
                            <th class="text-center">Estatus</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sites->sites as $site)
                            <tr>
                                <td class="text-center">{{ $site->name }}</td>
                                <td class="text-center">
                                    <img src="{{ $site->logo }}" alt="{{ $site->name }}" width="180" height="60">
                                </td>
                                <td class="text-center">{{ $site->payment_domain }}</td>
                                <td class="text-center">
                                    <div style="border-radius:100%;width:50px;height:50px;background-color:{{ $site->color }};"></div>
                                </td>
                                <td class="text-center">{{ $site->transactional_email }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ ( $site->transactional_email_send == 1 ) ? 'success' : 'danger' }}">{{ ( $site->transactional_email_send == 1 ) ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="text-center">{{ $site->transactional_phone }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ ( $site->is_commissionable == 1 ) ? 'success' : 'danger' }}">{{ ( $site->is_commissionable == 1 ) ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ ( $site->is_cxc == 1 ) ? 'success' : 'danger' }}">{{ ( $site->is_cxc == 1 ) ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ ( $site->is_cxp == 1 ) ? 'success' : 'danger' }}">{{ ( $site->is_cxp == 1 ) ? 'Sí' : 'No' }}</span>
                                </td>
                                <td class="text-center">{{ $site->success_payment_url }}</td>
                                <td class="text-center">{{ $site->cancel_payment_url }}</td>
                                <td class="text-center">{{ $site->type_site }}</td>
                                <td class="text-center">
                                    <button class="btn btn-{{ ( $site->status == 1 ) ? 'success' : 'danger' }}" style="font-size: 13px;">{{ ( $site->status == 1 ) ? 'activo' : 'Inactivo' }}</button>
                                </td>                                
                                <td class="text-center">
                                    <div class="d-flex flex-column gap-2">
                                        <a class="btn btn-primary" href="{{ route('enterprises.sites.edit', [$site->id]) }}" style="font-size: 13px;">Editar</a>
                                        <form action="{{ route('enterprises.sites.destroy', $site) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="font-size: 13px;">Eliminar</button>
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