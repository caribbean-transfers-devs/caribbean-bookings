@php
    $users = [];
@endphp
@extends('layout.master')
@section('title') Comisiones @endsection

@push('up-stack')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        table thead th{ font-size: 8pt; }
        table tbody td{ font-size: 8pt; }
        .button_{ display: flex; justify-content: space-between; }
    </style>
@endpush

@push('bootom-stack')
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
            $("#placeholder_dates_one").text(`${date} | LLEGADAS`);

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
            $("#placeholder_dates_two").text(`${date} | SALIDAS`);

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
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3 button_">
            Descarga de CCForm
            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>
        </h1>

        <div class="row">
            <div class="col-12 col-sm-12">

                <div class="tab">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" href="#tab-1" data-bs-toggle="tab" role="tab" id="placeholder_dates_one">{{ date("Y-m-d", strtotime($search['init_date'])) }} al {{ date("Y-m-d", strtotime($search['end_date'])) }} | LLEGADAS</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-2" data-bs-toggle="tab" role="tab" id="placeholder_dates_two">{{ date("Y-m-d", strtotime($search['init_date'])) }} al {{ date("Y-m-d", strtotime($search['end_date'])) }} | SALIDAS</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-1" role="tabpanel">                                                                                
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
@endsection

<div class="modal" tabindex="-1" id="filterModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtro de CCForm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row" action="" method="POST" id="formSearch">                    
                    @csrf
                    <div class="col-12 col-sm-12">
                        <label class="form-label" for="lookup_date">Seleccione el rango de fechas</label>
                        <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $search['init_date']." - ".$search['end_date'] }}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="searchOne(),searchTwo()" id="btnSearch">Buscar</button>
            </div>
        </div>
    </div>
</div>
