@extends('layout.app')
@section('title') @if($v_type == 1) Crear @else Editar @endif Roles @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/enterprise_forms.min.css') }}" rel="stylesheet" > 
@endpush

@push('Js')
    <script>
        $("#save").click(() => {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                }
            });
            let frm_data = $("#frm_role").serializeArray();
            let type_req = '{{ $v_type == 1 ? 'POST' : 'PUT' }}';
            let url_req = '{{ $v_type == 1 ? '/roles' : '/roles/' . $role->id }}';
            $.ajax({
                url: url_req,
                type: type_req,
                data: frm_data,
                success: function(resp) {
                    if (resp.success == 1) {
                        window.onbeforeunload = null;
                        let timerInterval
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: 'success',
                            html: 'Datos guardados con éxito. Será redirigido en <b></b>',
                            timer: 2500,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                    b.textContent = (Swal.getTimerLeft() / 1000)
                                        .toFixed(0)
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                        }).then((result) => {
                            if (window.location.href.includes('create')) {
                                window.location.href = '/roles'
                            } else {
                                location.reload();
                            }
                        })
                    } else {
                        console.log(resp);
                    }
                }
            }).fail(function(xhr, status, error) {
                Swal.fire(
                    '¡ERROR!',
                    xhr.responseJSON.message,
                    'error'
                )
            });

        })
    </script>
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
                            @php
                                if(count($role->permits) > 0) {
                                    $permits = $role->permits->pluck('submodule_id')->toArray();
                                } else {
                                    $permits = [];
                                }
                            @endphp                            

                            <form id="frm_role">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="role" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="role" name="role"
                                                value="{{ $role->role }}" required>
                                    </div>                                
                                </div>  
                                <div class="row mb-3">
                                    <ul class="nav nav-tabs" role="tablist">
                                        @foreach ($modules as $module)
                                            <li class="nav-item" role="presentation"><a class="nav-link @if ($loop->first)
                                                active
                                            @endif" href="#tab-{{ $module->id }}" data-bs-toggle="tab" role="tab" aria-selected="true">{{ $module->module }}</a></li>
                                        @endforeach                                    
                                    </ul>
                                    <div class="tab-content">
                                        @foreach ($modules as $module)
                                            @php
                                                // dump($module);
                                            @endphp
                                            <div class="tab-pane @if ($loop->first) active show @endif" id="tab-{{ $module->id }}" role="tabpanel">
                                                <div class="row mt-2">
                                                    @foreach ($module->submodules as $submodule)
                                                        <div class="col-4 my-2">
                                                            <label class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="{{ $submodule->id }}" name="permits[]" @if (in_array($submodule->id, $permits) )
                                                                    checked                                                                
                                                                @endif>
                                                                <span class="form-check-label">
                                                                    {{ $submodule->submodule }}
                                                                </span>
                                                            </label>
                                                        </div>                                                        
                                                    @endforeach
                                                </div>                                            
                                            </div>
                                        @endforeach
                                        
                                    </div>
                                </div>
                            </form>                           
                            <button class="btn btn-success" id="save">@if ($v_type == 1)
                                Crear
                            @else
                                Editar
                            @endif Rol</button>                             
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection