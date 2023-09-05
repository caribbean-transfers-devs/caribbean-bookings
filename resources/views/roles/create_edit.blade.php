@extends('layout.master')
@section('title') @if($v_type == 1) Crear @else Editar @endif Roles @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
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
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">@if($v_type == 1) Crear @else Editar @endif Roles</h1>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        @php
                            if(count($role->permits) > 0) {
                                $permits = $role->permits->pluck('submodule_id')->toArray();
                            } else {
                                $permits = [];
                            }
                        @endphp                     
                    </div>
                    <div class="card-body">
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
@endsection