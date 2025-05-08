@php
    $users = [];
@endphp
@extends('layout.app')
@section('title') Comisiones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management/ccform.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management/ccform.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script>
        $(function() {
            const picker = new easepick.create({
                    element: "#lookup_date",        
                    css: [
                        'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                        'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                        'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                    ],
                    zIndex: 10,
                    plugins: ['RangePlugin'],
            });
            searchOne();
            searchTwo();
        });

        function searchOne(){
            $("#iframeOneContainer").empty();
            $("#btnSearch").text("Buscando....").attr("disabled", true);

            let date = $('#lookup_date').val();
            $("#placeholder_dates_one span").text(`${date} | LLEGADAS`);

            var iframe = document.createElement('iframe');
            iframe.id = 'pdfIframe';
            iframe.width = '100%';
            iframe.height = '700px';
            iframe.style.border = '1px solid #ddd';
            iframe.src = '/reports/ccform/pdf?type=arrival&date='+date;
        
            document.getElementById('iframeOneContainer').appendChild(iframe);
            
            
            $("#btnSearch").text("Buscar").attr("disabled", false);
            $('#filterModal').modal('hide');
        }

        function searchTwo(){
            $("#iframeTwoContainer").empty();
            $("#btnSearch").text("Buscando....").attr("disabled", true);

            let date = $('#lookup_date').val();
            $("#placeholder_dates_two span").text(`${date} | SALIDAS`);

            var iframe = document.createElement('iframe');
            iframe.id = 'pdfIframe';
            iframe.width = '100%';
            iframe.height = '700px';
            iframe.style.border = '1px solid #ddd';
            iframe.src = '/reports/ccform/pdf?type=departure&date='+date;
        
            document.getElementById('iframeTwoContainer').appendChild(iframe);
            
            
            $("#btnSearch").text("Buscar").attr("disabled", false);
            $('#filterModal').modal('hide');
        }
    </script>
@endpush

@section('content')
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-four">
                <div class="widget-heading">
                    <div class="d-flex gap-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</button>
                    </div>
                </div>
                <div class="widget-content">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab" aria-selected="true" id="placeholder_dates_one">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>{{ date("Y-m-d", strtotime($search['init_date'])) }} al {{ date("Y-m-d", strtotime($search['end_date'])) }} | LLEGADAS</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab" aria-selected="false" tabindex="-1" id="placeholder_dates_two">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span>{{ date("Y-m-d", strtotime($search['init_date'])) }} al {{ date("Y-m-d", strtotime($search['end_date'])) }} | SALIDAS</span>
                            </button>
                        </li>
                    </ul>                    
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="tab-1" role="tabpanel">                                                                                
                            <div id="iframeOneContainer"></div>
                        </div>
                        <div class="tab-pane" id="tab-2" role="tabpanel">                                                                                
                            <div id="iframeTwoContainer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filtro de CCForm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                    </button>
                </div>                
                <div class="modal-body">
                    <form class="form" action="" method="POST" id="formSearch">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <label class="form-label" for="lookup_date">Seleccione el rango de fechas</label>
                                <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $search['init_date']." - ".$search['end_date'] }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn btn-light-dark" data-bs-dismiss="modal"><i class="flaticon-cancel-12"></i> Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="searchOne(),searchTwo()" id="btnSearch">Buscar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
