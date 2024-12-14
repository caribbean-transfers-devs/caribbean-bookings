@php
    use App\Traits\RoleTrait;
@endphp
@extends('layout.app')
@section('title') Empresas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('assets/js/sections/enterprise.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => 'Agregar una empresa',
                'className' => 'btn btn-primary ',
                'url' => route('enterprises.create')
            )
        );
        // dump($buttons);
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
                <table id="dataEnterprises" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">Nombres</th>
                            <th class="text-center">External</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($enterprises as $enterprise)
                            <tr>
                                <td class="text-center">{{ $enterprise->names }}</td>
                                <td class="text-center">
                                    <span class="badge badge-light-{{ ( $enterprise->is_external == 0 ) ? 'success' : 'danger' }} mb-2 me-4">{{ ( $enterprise->is_external == 0 ) ? 'Interno' : 'Externo' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-3">
                                        <a class="btn btn-primary" href="{{ route('enterprises.edit', [$enterprise->id]) }}">Editar</a>
                                        <a class="btn btn-primary" href="{{ route('enterprise.sites', [$enterprise]) }}">Sitios</a>
                                        <form action="{{ route('enterprises.destroy', $enterprise->id) }}" method="POST">
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