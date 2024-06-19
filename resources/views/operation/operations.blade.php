@php
    use App\Traits\RoleTrait;
    $resume = [
        'status' => [
            'PENDING' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CONFIRMED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
            'CANCELLED' => [ 'USD' => 0, 'MXN' => 0, 'count' => 0 ],
        ]
    ];
    $sites = [];
    $destinations = [];
@endphp
@extends('layout.custom')
@section('title') Operación @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/operations.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/operations.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>    
    <script src="{{ mix('assets/js/sections/operations/operations.min.js') }}"></script>    
    <script src="https://cdn.socket.io/4.4.1/socket.io.min.js"></script>
    <script>
        let managment = {
            /**
             * ===== Render Table Settings ===== *
             * @param {*} table //tabla a renderizar
            */
            actionTable: function(table, param = ""){
                let buttons = [];
                const _settings = {},
                    _buttons = table.data('button');

                if( _buttons != undefined && _buttons.length > 0 ){
                    _buttons.forEach(_btn => {
                        if( _btn.hasOwnProperty('url') ){
                            _btn.action = function(e, dt, node, config){
                                window.location.href = _btn.url;
                            }
                        };
                        buttons.push(_btn);
                    });
                }

                _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                                <'table-responsive'tr>
                                <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;                        
                // _settings.destroy = true;
                // _settings.ajax = {
                //     type: 'GET',
                //     dataType: 'json',
                //     url: '/operation/dataOperations',
                //     "data": function ( d ) {
                //         if( typeof param != 'undefined' && param != '' ){ d.data = param; }else{}
                //         // console.log(d);
                //         return JSON.stringify( d );
                //         // return ( d );
                //     },
                //     contentType: 'application/json; charset=utf-8',
                // };                                   
                _settings.deferRender = true;
                _settings.responsive = true;
                _settings.buttons =  _buttons;
                _settings.paging = false;
                _settings.oLanguage = {
                    "sProcessing": "Procesando...",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",                    
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": components.getTranslation("table.search") + "...",
                    "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
                    "oPaginate": { 
                        "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                        "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
                    },
                };

                table.DataTable( _settings );
            },

            bsPopover: function() {
                var bsPopover = document.querySelectorAll('.bs-popover');
                for (let index = 0; index < bsPopover.length; index++) {
                    var popover = new bootstrap.Popover(bsPopover[index])
                }
            },

            setStatus: function(_status){
                let alert_type = 'btn-secondary';
                switch (_status) {
                    case 'PENDING':
                        alert_type = 'btn-secondary';
                        break;
                    case 'COMPLETED':
                    case 'OK':
                        alert_type = 'btn-success';
                        break;
                    case 'NOSHOW':
                    case 'C':
                        alert_type = 'btn-warning';
                        break;
                    case 'CANCELLED':
                        alert_type = 'btn-danger';
                        break;
                    case 'E':
                        alert_type = 'btn-info';
                        break;                        
                    default:
                        alert_type = 'btn-secondary';
                        break;
                }
                return alert_type;
            }            
        };

        if ( document.getElementById('lookup_date') != null ) {
            const picker = new easepick.create({
                element: "#lookup_date",
                css: [
                    'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                ],
                zIndex: 10,
            });   
        }

        if( document.querySelector('.table-rendering') != null ){
            managment.actionTable($('.table-rendering'));
            // managment.actionTable( $(".table-rendering"), components.serialize(document.getElementById('formSearch'),'object') );
        }
        managment.bsPopover();
        components.formReset();
        
        //DECLARACION DE VARIABLES
        const __vehicles = document.querySelectorAll('.vehicles'); //* ===== SELECT VEHICLES ===== */
        const __drivers = document.querySelectorAll('.drivers'); //* ===== SELECT DRIVERS ===== */
        const __open_modal_comments = document.querySelectorAll('.__open_modal_comment');
        const __title_modal = document.getElementById('filterModalLabel');
        const __button_form = document.getElementById('formComment'); //* ===== BUTTON FORM ===== */

        //DEFINIMOS EL SERVIDOR SOCKET QUE ESCUCHARA LAS PETICIONES
        const socket = io( (_LOCAL_URL == 'http://127.0.0.1:8000' ) ? 'http://localhost:3000': 'https://socket-production-bed1.up.railway.app' );
        socket.on('connection');

        if (__vehicles.length > 0) {
            __vehicles.forEach(__vehicle => {
                __vehicle.addEventListener('change', function(event) {
                    event.preventDefault();                    
                    const { id, item, code } = this.dataset;
                    $.ajax({
                        url: `/operation/vehicle/set`,
                        type: 'PUT',
                        data: { item : item, vehicle_id : __vehicle.value, reservation_item_id : code },
                        success: function(resp) {
                            console.log(resp);
                            if( resp.success ){
                                Swal.fire({
                                    icon: "success",
                                    text: resp.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                socket.emit("setVehicleReservationServer", resp.data);
                            }                            
                        }
                    }).fail(function(xhr, status, error) {
                        console.log(xhr);
                        Swal.fire(
                            '¡ERROR!',
                            xhr.responseJSON.message,
                            'error'
                        );
                    });
                });
            });
        }

        if (__drivers.length > 0) {
            __drivers.forEach(__driver => {
                __driver.addEventListener('change', function() {
                    const { id, item, code } = this.dataset;
                    $.ajax({
                        url: `/operation/driver/set`,
                        type: 'PUT',
                        data: { item : item, driver_id : __driver.value, reservation_item_id : code },
                        success: function(resp) {
                            console.log(resp);
                            if( resp.success ){
                                Swal.fire({
                                    icon: "success",
                                    text: resp.message,
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                socket.emit("setDriverReservationServer", resp.data);
                            }
                        }
                    }).fail(function(xhr, status, error) {
                            console.log(xhr, status, error);
                            Swal.fire(
                                '¡ERROR!',
                                xhr.responseJSON.message,
                                'error'
                            );
                    });
                });
            });
        }

        function updateStatusOperation(event, type, status, item_id, rez_id, id){
            event.preventDefault();

            swal.fire({
                title: '¿Está seguro de actualizar el estatus?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed == true){
                    $.ajax({
                        url: `/operation/status/operation`,
                        type: 'PUT',
                        data: { item: id, rez_id: rez_id, item_id: item_id, type: type, status: status },
                        beforeSend: function() {
                        },
                        success: function(resp) {
                            Swal.fire({
                                title: '¡Éxito!',
                                icon: 'success',
                                html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
                                timer: 1500,
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
                                // statusCell.innerHTML = `<span class="badge badge-light-${alert_type} mb-2 me-4">${status}</span>`;
                                socket.emit("updateStatusOperationServer", resp.data);
                            })
                        }
                    }).fail(function(xhr, status, error) {
                        console.log(xhr);
                        Swal.fire(
                            '¡ERROR!',
                            xhr.responseJSON.message,
                            'error'
                        );
                    });
                }
            });        
        }

        function updateStatusBooking(event, type, status, item_id, rez_id, id){
            event.preventDefault();

            swal.fire({
                title: '¿Está seguro de actualizar el estatus?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed == true){      
                    $.ajax({
                        url: `/operation/status/booking`,
                        type: 'PUT',
                        data: { item: id, rez_id: rez_id, item_id: item_id, type: type, status: status },
                        beforeSend: function() {                            
                        },
                        success: function(resp) {
                            Swal.fire({
                                title: '¡Éxito!',
                                icon: 'success',
                                html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
                                timer: 1500,
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
                                // statusCell.innerHTML = `<span class="badge badge-light-${alert_type} mb-2 me-4">${status}</span>`;
                                socket.emit("updateStatusBookingServer", resp.data);
                            })
                        }
                    }).fail(function(xhr, status, error) {
                        console.log(xhr);
                        Swal.fire(
                            '¡ERROR!',
                            xhr.responseJSON.message,
                            'error'
                        );
                    });

                }
            });        
        }        

        //ACCION PARA ABRIR MODAL PARA AÑADIR UN COMENTARIO
        if( __open_modal_comments.length > 0 ){
            __open_modal_comments.forEach(__open_modal_comment => {
                __open_modal_comment.addEventListener('click', function(){
                    console.log(this.dataset.code, this.dataset.type);
                    document.getElementById('id_item').value = this.dataset.id;
                    document.getElementById('code_item').value = this.dataset.code;
                    document.getElementById('type_item').value = this.dataset.type;
                });
            });
        }

        //ACCION DE FORMULARIO
        __button_form.addEventListener('submit', function (event) {
            event.preventDefault();
            let _params = components.serialize(this,'object');
            if( _params != null ){
                $.ajax({
                    type: "POST", // Método HTTP de la solicitud
                    url: _LOCAL_URL + "/operation/comment/add", // Ruta del archivo PHP que manejará la solicitud
                    data: JSON.stringify(_params), // Datos a enviar al servidor
                    dataType: "json", // Tipo de datos que se espera en la respuesta del servidor
                    contentType: 'application/json; charset=utf-8',
                    beforeSend: function(){
                        components.loadScreen();
                    },
                    success: function(response) {
                        // Manejar la respuesta exitosa del servidor
                        $("#messageModal").modal('hide');
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            willClose: () => {
                                socket.emit("addCommentServer", response.data);
                            }
                        })
                    }
                });
            }else{
                event.stopPropagation();
                components.sweetAlert({"status": "error", "message": "No se definieron parametros"});
            }
        });
    
        // $(document).delegate(".drivers",'change', function (e) {
        //     e.preventDefault();
        //     let _params = new Object({});
        //     let _code = ( typeof $(this).data("code") != 'undefined' ? $(this).data("code") : "" );
            
        //     _params.driver_id = $(this).val();
        //     _params.reservation_item_id = _code;
        //     console.log(_params);

        //     $.ajax({
        //         type : 'PUT',
        //         url : '/operation/driver/set',
        //         data : _params,
        //         beforeSend: function(){
        //         },
        //         success : function(resp) {
        //             console.log(resp);
        //             if( resp.success ){
        //                 Swal.fire({
        //                     icon: "success",
        //                     text: resp.message,
        //                     showConfirmButton: false,
        //                     timer: 1000
        //                 });
        //                 socket.emit("setDriverReservationServer", resp.data);
        //             }
        //         }
        //     }).fail(function(xhr, status, error) {
        //         console.log(xhr, status, error);
        //         Swal.fire(
        //             '¡ERROR!',
        //             xhr.responseJSON.message,
        //             'error'
        //         );
        //     });
        // });
        
        //EVENTOS SOCKET IO, ESCUCHAN DE LADO DEL CLIENTE
        socket.on("setVehicleReservationClient", function(data){
            console.log("nueva asignación de unidad");
            console.log(data);
            //DECLARACION DE VARIABLES
            const __select_vehicle = document.getElementById('vehicle_id_' + data.item);
            if( __select_vehicle != null ){
                const __Row = ( __select_vehicle != null ? components.closest(__select_vehicle, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(10)') : null );
                console.log(__select_vehicle, __Row, __Cell);                
                __select_vehicle.value = data.value;
            }
            
            Snackbar.show({ 
                text: data.message, 
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });

        socket.on("setDriverReservationClient", function(data){
            console.log("nueva asignación de conductor");
            console.log(data);
            //DECLARACION DE VARIABLES
            const __select_driver = document.getElementById('driver_id_' + data.item);
            if( __select_driver != null ){
                const __Row = ( __select_driver != null ? components.closest(__select_driver, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(11)') : "" );
                console.log(__select_vehicle, __Row, __Cell);                
                __select_driver.value = data.value;
            }
                        
            Snackbar.show({ 
                text: data.message, 
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });

        socket.on("updateStatusOperationClient", function(data){
            console.log("operación");
            console.log(data);
            //DECLARACION DE VARIABLES
            const __status_operation = document.getElementById('optionsOperation' + data.item);
            if( __status_operation != null ){
                const __Row = ( __status_operation != null ? components.closest(__status_operation, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(12)') : "" );
                console.log(__status_operation, __Row, __Cell);                
                __status_operation.classList.remove('btn-secondary', 'btn-success', 'btn-warning', 'btn-danger');
                __status_operation.classList.add(managment.setStatus(data.value));
                __status_operation.querySelector('span').innerText = data.value;
            }
                        
            Snackbar.show({ 
                text: data.message,
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });

        socket.on("updateStatusBookingClient", function(data){
            console.log("reservación");
            console.log(data);
            //DECLARACION DE VARIABLES
            const __status_booking = document.getElementById('optionsBooking' + data.item);
            if( __status_booking != null ){
                const __Row = ( __status_booking != null ? components.closest(__status_booking, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(13)') : "" );
                console.log(__status_booking, __Row, __Cell);                
                __status_booking.classList.remove('btn-secondary', 'btn-success', 'btn-warning', 'btn-danger');
                __status_booking.classList.add(managment.setStatus(data.value));
                __status_booking.querySelector('span').innerText = data.value;
            }
                        
            Snackbar.show({
                text: data.message,
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });

        socket.on("addCommentClient", function(data){
            console.log("comentario");
            console.log(data);
            //DECLARACION DE VARIABLES
            const __btn_comment = document.getElementById('btn_add_modal_' + data.item);
            if( __btn_comment != null ){
                const __Row = ( __btn_comment != null ? components.closest(__btn_comment, 'tr') : null );
                const __indicators = ( __Row != null ? __Row.querySelector('td:nth-child(2)') : "" );
                const __btn_open_modal_comment = ( __Row != null ? __Row.querySelector('td:nth-child(19)') : "" );
                console.log(__btn_comment, __Row, __indicators, __btn_open_modal_comment);
                __btn_comment.dataset.status = data.status;
                __indicators.innerText = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square bs-popover" data-bs-container="body" data-bs-trigger="hover" data-bs-content="'+ data.value +'"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
            }
                        
            Snackbar.show({
                text: data.message,
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });
    </script>
@endpush

@section('content')
    @php
        // dump(count($items));
        $buttons = array();
        // dump($buttons);
    @endphp
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
        <div id="filters" class="accordion">
            <div class="card">
            <div class="card-header" id="headingOne1">
                <section class="mb-0 mt-0">
                    <div role="menu" class="" data-bs-toggle="collapse" data-bs-target="#defaultAccordionOne" aria-expanded="true" aria-controls="defaultAccordionOne">
                        Filtros <div class="icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                    </div>
                </section>
            </div>
            <div id="defaultAccordionOne" class="collapse show" aria-labelledby="headingOne1" data-bs-parent="#filters">
                <div class="card-body">
                    <form action="" class="row" method="POST" id="formSearch">
                        <div class="col-12 col-sm-5 mb-3 mb-lg-0">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date }}">
                        </div>
                        <div class="col-12 col-sm-3 align-self-end">
                            <button type="submit" class="btn btn-primary btn-lg btn-filter w-100">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-8">
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
            <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>' data-action="dataOperations">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>INDICADORES</th>
                        <th>HORA</th>
                        <th>CLIENTE</th>
                        <th class="text-center">TIPO DE SERVICIO</th>
                        <th>PAX</th>
                        <th>ORIGEN</th>
                        <th>DESTINO</th>
                        <th>AGENCIA</th>
                        <th>UNIDAD</th>
                        <th>CONDUCTOR</th>
                        <th class="text-center">ESTATUS OPERACIÓN</th>
                        <th class="text-center">ESTATUS RESERVACIÓN</th>
                        <th>CÓDIGO</th>
                        <th>VEHÍCULO</th>
                        <th>PAGO</th>
                        <th>TOTAL</th>
                        <th>MONEDA</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($items)>=1)
                        @foreach($items as $key => $value)                                
                            @php
                                $payment = ( $value->total_sales - $value->total_payments );
                                if($payment < 0) $payment = 0;

                                $operation_status = (($value->operation_type == 'arrival')? $value->op_one_status_operation : $value->op_two_status_operation );
                                $operation_booking = (($value->operation_type == 'arrival')? $value->op_one_status : $value->op_two_status );
                                $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );

                                $flag_comment = ( ($value->operation_type == 'arrival') && $value->op_one_comments != "" ? true : ( ($value->operation_type == 'departure') && $value->op_two_comments != "" ? true : false ) );
                                $comment = (($value->operation_type == 'arrival')? $value->op_one_comments : $value->op_two_comments );

                                switch ($operation_status) {
                                    case 'PENDING':
                                        $label = 'secondary';
                                        break;
                                    case 'E':
                                        $label = 'info';
                                        break;
                                    case 'C':
                                        $label = 'warning';
                                        break;
                                    case 'OK':
                                        $label = 'success';
                                        break;
                                    default:
                                        $label = 'secondary';
                                        break;
                                }

                                switch ($operation_booking) {
                                    case 'PENDING':
                                        $label2 = 'secondary';
                                        break;
                                    case 'COMPLETED':
                                        $label2 = 'success';
                                        break;
                                    case 'NOSHOW':
                                        $label2 = 'warning';
                                        break;
                                    case 'CANCELLED':
                                        $label2 = 'danger';
                                        break;
                                    default:
                                        $label2 = 'secondary';
                                        break;
                                }
                            @endphp
                            <tr class="item-{{ $key.$value->id }}" id="item-{{ $key.$value->id }}">
                                <td></td>
                                <td>
                                    @if ( $flag_comment )
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square bs-popover" data-bs-container="body" data-bs-trigger="hover" data-bs-content="{{ $comment }}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    @endif
                                </td>
                                <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>                                    
                                <td>
                                    {{ $value->client_first_name }} {{ $value->client_last_name }}
                                    @if(!empty($value->reference))
                                        [{{ $value->reference }}]
                                    @endif
                                </td>
                                <td>{{ $value->final_service_type }}</td>
                                <td class="text-center">{{ $value->passengers }}</td>
                                <td>{{ $operation_from }}</td>
                                <td>{{ $operation_to }}</td>
                                <td>{{ $value->site_name }}</td>
                                <td>
                                    <select class="form-control vehicles " data-live-search="true" name="vehicle_id" id="vehicle_id_{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" data-code="{{ $value->id }}">
                                        <option value="0">Selecciona un vehículo</option>
                                        @if ( isset($vehicles) && count($vehicles) >= 1 )
                                            @foreach ($vehicles as $vehicle)
                                                <option {{ ( isset($value->vehicle_id) && $value->vehicle_id == $vehicle->id ) ? 'selected' : '' }} value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control drivers " data-live-search="true" name="driver_id" id="driver_id_{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" data-code="{{ $value->id }}">
                                        <option value="0">Selecciona un conductor</option>
                                        @if ( isset($drivers) && count($drivers) >= 1 )
                                            @foreach ($drivers as $driver)
                                                <option {{ ( isset($value->driver_id) && $value->driver_id == $driver->id ) ? 'selected' : '' }} value="{{ $driver->id }}">{{ $driver->names }} {{ $driver->surnames }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button id="optionsOperation{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" type="button" class="btn btn-{{ $label }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span>{{ $operation_status }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="optionsOperation{{ $key.$value->id }}">
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusOperation(event, '{{ $value->operation_type }}', 'PENDING',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusOperation(event, '{{ $value->operation_type }}', 'E',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusOperation(event, '{{ $value->operation_type }}', 'C',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusOperation(event, '{{ $value->operation_type }}', 'OK',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button id="optionsBooking{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span>{{ $operation_booking }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="optionsBooking{{ $key.$value->id }}">
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusBooking(event, '{{ $value->operation_type }}', 'PENDING',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusBooking(event, '{{ $value->operation_type }}', 'COMPLETED',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusBooking(event, '{{ $value->operation_type }}', 'NOSHOW',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatusBooking(event, '{{ $value->operation_type }}', 'CANCELLED',{{ $value->id }}, {{ $value->reservation_id }}, {{ $key.$value->id }})"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>
                                        </div>
                                    </div>                                     
                                </td>
                                <td>
                                    @if (RoleTrait::hasPermission(38))
                                        <a href="/reservations/detail/{{ $value->reservation_id }}">{{ $value->code }}</a>
                                    @else
                                        {{ $value->code }}
                                    @endif
                                </td>
                                <td>{{ $value->service_name }}</td>                                    
                                <td class="text-center">{{ $value->status }}</td>
                                <td class="text-end">{{ number_format($payment,2) }}</td>
                                <td class="text-center">{{ $value->currency }}</td>
                                <td class="text-center">
                                    <div class="d-flex">    
                                        {{-- @if ( !$flag_comment ) --}}
                                            <div class="btn btn-primary __open_modal_comment" id="btn_add_modal_{{ $key.$value->id }}" data-bs-toggle="modal" data-bs-target="#messageModal" data-status="{{ ( $flag_comment ) ? 1 : 0 }}" data-id="{{ $key.$value->id }}" data-code="{{ $value->id }}" data-type="{{ $value->operation_type }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            </div>
                                        {{-- @endif --}}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <x-modals.reservations.comments />
@endsection