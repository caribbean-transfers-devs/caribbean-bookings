@php
    use App\Traits\RoleTrait;
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
                                <''tr>
                                <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;
                _settings.deferRender = true;
                _settings.responsive = true;
                _settings.buttons =  _buttons;
                // _settings.scrollY = "800px";
                // _settings.scrollCollapse = true;
                _settings.paging = false;
                _settings.oLanguage = {
                    "sProcessing": "Procesando...",
                    "sZeroRecords": "No se encontraron resultados",             
                    "sInfo": "Mostrando _TOTAL_ registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": components.getTranslation("table.search") + "...",
                    "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
                    "oPaginate": { 
                        "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                        "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
                    },
                };

                table.DataTable( _settings );
                // new $.fn.dataTable.FixedHeader(render);
            },

            bsTooltip: function() {
                var bsTooltip = document.querySelectorAll('.bs-tooltip')
                for (let index = 0; index < bsTooltip.length; index++) {
                    var tooltip = new bootstrap.Tooltip(bsTooltip[index])
                }
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
            },

            setPreassignment: function(_operation){
                let alert_type = 'btn-success';
                switch (_operation) {
                    case 'ARRIVAL':
                        alert_type = 'btn-success';
                        break;
                    case 'DEPARTURE':
                        alert_type = 'btn-primary';
                        break;
                    case 'TRANSFER':
                        alert_type = 'btn-info';
                        break;
                    default:
                        alert_type = 'btn-success';
                        break;
                }
                return alert_type;                
            },

            isTime: function(hora) {
                // Expresión regular para validar formato HH:MM
                const regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
                return regex.test(hora);
            },

            obtenerHoraActual: function() {
                const ahora = new Date();
                const horas = String(ahora.getHours()).padStart(2, '0');
                const minutos = String(ahora.getMinutes()).padStart(2, '0');
                return `${horas}:${minutos}`;
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
        }
        managment.bsPopover();
        managment.bsTooltip();
        components.formReset();//RESETEA LOS VALORES DE UN FORMULARIO, EN UN MODAL
        
        //DECLARACION DE VARIABLES
        const __add_preassignments = document.querySelectorAll('.add_preassignment'); //* ===== BUTTONS PRE ASSIGNMENT ===== */
        const __vehicles = document.querySelectorAll('.vehicles'); //* ===== SELECT VEHICLES ===== */
        const __drivers = document.querySelectorAll('.drivers'); //* ===== SELECT DRIVERS ===== */
        const __open_modal_comments = document.querySelectorAll('.__open_modal_comment');
        const __title_modal = document.getElementById('filterModalLabel');
        const __button_form = document.getElementById('formComment'); //* ===== BUTTON FORM ===== */
        const __btn_preassignment = document.getElementById('btn_preassignment') //* ===== BUTTON PRE ASSIGNMENT GENERAL ===== */
        const __btn_addservice = document.getElementById('btn_addservice') //* ===== BUTTON PRE ASSIGNMENT GENERAL ===== */

        const __btn_update_status_operations = document.querySelectorAll('.btn_update_status_operation');
        const __btn_update_status_bookings = document.querySelectorAll('.btn_update_status_booking');

        //DEFINIMOS EL SERVIDOR SOCKET QUE ESCUCHARA LAS PETICIONES
        console.log(_LOCAL_URL);
        // const socket = io( (_LOCAL_URL == 'http://127.0.0.1:8000' ) ? 'http://localhost:3000': 'https://socket-production-bed1.up.railway.app' );
        // const socket = io('http://localhost:4000');
        const socket = io('https://socket-caribbean-transfers.up.railway.app');
        socket.on('connection');

        if ( __btn_addservice != null ) {
            __btn_addservice.addEventListener('click', function(event) {
                event.preventDefault();
                $("#operationModal").modal('show');
            });
        }

        if( __btn_preassignment != null ){
            __btn_preassignment.addEventListener('click', function() {
                swal.fire({
                    text: '¿Está seguro de pre-asignar los servicios?',
                    icon: 'warning',
                    inputLabel: "Selecciona la fecha que pre-asignara",
                    input: "date",
                    inputValue: document.getElementById('lookup_date').value,
                    inputValidator: (result) => {
                        return !result && "Selecciona un fecha";
                    },
                    didOpen: () => {
                        const today = (new Date()).toISOString();
                        Swal.getInput().min = today.split("T")[0];
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if(result.isConfirmed == true){
                        $.ajax({
                            type: "POST",
                            url: _LOCAL_URL + "/operation/preassignments",
                            data: JSON.stringify({ date: result.value }), // Datos a enviar al servidor                            
                            dataType: "json",
                            contentType: 'application/json; charset=utf-8',   
                            beforeSend: function(){
                                components.loadScreen();
                            },
                            success: function(response) {
                                // Manejar la respuesta exitosa del servidor
                                Swal.fire({
                                    icon: 'success',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            }
                        });
                    }
                });               
            });
        }

        if (__add_preassignments.length > 0) {
            __add_preassignments.forEach(__add_preassignment => {
                __add_preassignment.addEventListener('click', function(event) {
                    event.preventDefault();                    
                    const { id, code, operation, service } = this.dataset;
                    const __date = document.getElementById('lookup_date');
                    swal.fire({
                        text: '¿Está seguro de pre-asignar el servicio ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if(result.isConfirmed == true){
                            $.ajax({
                                url: _LOCAL_URL + "/operation/preassignment",
                                type: 'PUT',
                                data: { date : __date.value, id : id, code : code, operation : operation },
                                beforeSend: function() {
                                    components.loadScreen();
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: "success",
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 1500,
                                        willClose: () => {
                                            socket.emit("addPreassignmentServer", response.data);
                                        }
                                    });                            
                                }
                            });
                        }
                    });
                });
            });
        }

        if (__vehicles.length > 0) {
            __vehicles.forEach(__vehicle => {
                __vehicle.addEventListener('change', function(event) {
                    event.preventDefault();                    
                    const { id, item, code, operation } = this.dataset;
                    swal.fire({
                        inputLabel: "Ingresa el costo operativo",
                        inputPlaceholder: "Ingresa el costo operativo",
                        input: "text",
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar',
                        // showLoaderOnConfirm: true,
                        preConfirm: async (login) => {
                            try {
                                if (login == "") {
                                    return Swal.showValidationMessage(`
                                        "Por favor, ingresa el costo operativo"
                                    `);
                                }
                            } catch (error) {
                                Swal.showValidationMessage(`
                                    Request failed: ${error}
                                `);
                            }
                        },
                        // allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if(result.isConfirmed == true){
                            $.ajax({
                                url: `/operation/vehicle/set`,
                                type: 'PUT',
                                data: { item : item, vehicle_id : __vehicle.value, reservation_item_id : code, operation : operation, value : result.value },
                                beforeSend: function() {
                                    components.loadScreen();
                                },
                                success: function(resp) {
                                    if( resp.success ){
                                        Swal.fire({
                                            icon: "success",
                                            text: resp.message,
                                            showConfirmButton: false,
                                            timer: 1500,
                                            willClose: () => {
                                                socket.emit("setVehicleReservationServer", resp.data);
                                            }
                                        });
                                    }                            
                                }
                            });
                        }
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
                        beforeSend: function() {
                            components.loadScreen();
                        },
                        success: function(resp) {
                            if( resp.success ){
                                Swal.fire({
                                    icon: "success",
                                    text: resp.message,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    willClose: () => {
                                        socket.emit("setDriverReservationServer", resp.data);
                                    }
                                });                                
                            }
                        }
                    });
                });
            });
        }

        if (__btn_update_status_operations.length > 0) {
            __btn_update_status_operations.forEach(__btn_update_status_operation => {
                __btn_update_status_operation.addEventListener('click', function(event) {
                    event.preventDefault();
                    let _settings = {};
                    const { operation, status, item, booking, key } = this.dataset;
                    console.log(operation, status, item, booking, key);
                    _settings.text = "¿Está seguro de actualizar el estatus de operación?";
                    _settings.icon = 'warning';
                    _settings.showCancelButton = true;
                    _settings.confirmButtonText = 'Aceptar';
                    _settings.cancelButtonText = 'Cancelar';
                    if (status == "OK") {
                        _settings.inputLabel = "Ingresa la hora de abordaje";
                        _settings.input = "time";
                        _settings.inputValue = managment.obtenerHoraActual();
                        _settings.inputValidator = (result) => {
                            return !result && "Selecciona un horario";
                        }
                    }
                    swal.fire(_settings).then((result) => {
                        if(result.isConfirmed == true){
                            $.ajax({
                                url: `/operation/status/operation`,
                                type: 'PUT',
                                data: { id: key, rez_id: booking, item_id: item, type: operation, status: status, time: ( managment.isTime(result.value) ? result.value : "" ) },
                                beforeSend: function() {
                                    components.loadScreen();
                                },
                                success: function(resp) {
                                    Swal.fire({
                                        icon: 'success',
                                        text: 'Servicio actualizado con éxito.',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        willClose: () => {
                                            socket.emit("updateStatusOperationServer", resp.data);
                                        }
                                    });
                                }
                            });
                        }
                    });
                });
            });
        }

        if (__btn_update_status_bookings.length > 0) {
            __btn_update_status_bookings.forEach(__btn_update_status_booking => {
                __btn_update_status_booking.addEventListener('click', function(event) {
                    event.preventDefault();
                    const { operation, status, item, booking, key } = this.dataset;
                    console.log(operation, status, item, booking, key);
                    swal.fire({
                        text: "¿Está seguro de actualizar el estatus de reservación?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if(result.isConfirmed == true){
                            const __vehicle = document.getElementById('vehicle_id_' + key);
                            const __driver = document.getElementById('driver_id_' + key);
                            console.log(__vehicle, __driver);
                            
                            if ( ( __vehicle.value == 0 && __driver.value == 0 ) || ( __vehicle.value == 0 ) || ( __driver.value == 0 ) ) {
                                Swal.fire({
                                    text: 'Valida la seleccion de unidad y conductor.',
                                    icon: 'error',
                                    showConfirmButton: false,
                                    timer: 1500,
                                });                        
                            }else{
                                $.ajax({
                                    url: `/operation/status/booking`,
                                    type: 'PUT',
                                    data: { id: key, rez_id: booking, item_id: item, type: operation, status: status },
                                    beforeSend: function() {
                                        components.loadScreen();
                                    },
                                    success: function(resp) {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            icon: 'success',
                                            html: 'Servicio actualizado con éxito.',
                                            showConfirmButton: false,
                                            timer: 1500,
                                            willClose: () => {
                                                socket.emit("updateStatusBookingServer", resp.data);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });
                });
            });
        }

        //ACCION PARA ABRIR MODAL PARA AÑADIR UN COMENTARIO
        if( __open_modal_comments.length > 0 ){
            __open_modal_comments.forEach(__open_modal_comment => {
                __open_modal_comment.addEventListener('click', function(){

                    //DECLARACION DE VARIABLES
                    const __modal = document.getElementById('messageModal');
                    const __title_modal = document.getElementById('messageModalLabel');
                    const __form_label = __modal.querySelector('.form-label');

                    //SETEAMOS VALORES EN EL MODAL
                    __title_modal.innerHTML = ( this.dataset.status == 0 ? "Agregar comentario" : "Editar comentario" );
                    __form_label.innerHTML = ( this.dataset.status == 0 ? "Ingresa el comentario" : "Editar el comentario" );
                    document.getElementById('id_item').value = this.dataset.id;
                    document.getElementById('code_item').value = this.dataset.code;
                    document.getElementById('type_item').value = this.dataset.type;

                    if (this.dataset.status == 1) {
                        $.ajax({
                            url: `/operation/comment/get`,
                            type: 'GET',
                            data: { item_id: this.dataset.code, type: this.dataset.type },
                            // beforeSend: function() {
                            //     components.loadScreen();
                            // },
                            success: function(resp) {
                                document.getElementById('comment_item').value = resp.message;
                                $(__modal).modal('show');
                            }
                        });
                    }else{
                        $(__modal).modal('show');
                    }
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
        
        //EVENTOS SOCKET IO, ESCUCHAN DE LADO DEL CLIENTE
        socket.on("addPreassignmentClient", function(data){
            console.log("asignacion");
            // console.log(data);
            //DECLARACION DE VARIABLES
            const __btn_preassignment = document.getElementById('btn_preassignment_' + data.item);
            if( __btn_preassignment != null ){
                const __Row = ( __btn_preassignment != null ? components.closest(__btn_preassignment, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(1)') : "" );
                console.log(__btn_preassignment, __Row, __Cell);
                __btn_preassignment.classList.remove('btn-primary');
                __btn_preassignment.classList.add(managment.setPreassignment(data.operation));
                __btn_preassignment.innerHTML = data.value;
                // __Cell.innerHTML = '<button type="button" class="btn btn-'+ managment.setPreassignment(data.operation) +' text-uppercase">'+ data.value +'</button>';
            }

            Snackbar.show({
                text: data.message,
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });

        socket.on("setVehicleReservationClient", function(data){
            console.log("nueva asignación de unidad");
            // console.log(data);
            //DECLARACION DE VARIABLES
            const __select_vehicle = document.getElementById('vehicle_id_' + data.item);
            if( __select_vehicle != null ){
                const __Row = ( __select_vehicle != null ? components.closest(__select_vehicle, 'tr') : null );
                const __CellVehicle = ( __Row != null ? __Row.querySelector('td:nth-child(10)') : null );
                const __CellCost = ( __Row != null ? __Row.querySelector('td:nth-child(14)') : null );
                // console.log(__select_vehicle, __Row, __CellVehicle, __CellCost);
                __select_vehicle.value = data.value;
                __CellCost.innerHTML = data.cost;
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
            // console.log(data);
            //DECLARACION DE VARIABLES
            const __select_driver = document.getElementById('driver_id_' + data.item);
            if( __select_driver != null ){
                const __Row = ( __select_driver != null ? components.closest(__select_driver, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(11)') : "" );
                // console.log(__select_driver, __Row, __Cell);
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
                const __CellStatus = ( __Row != null ? __Row.querySelector('td:nth-child(12)') : "" );
                const __CellTime = ( __Row != null ? __Row.querySelector('td:nth-child(13)') : "" );
                console.log(__status_operation, __Row, __CellStatus);
                __status_operation.classList.remove('btn-secondary', 'btn-success', 'btn-warning', 'btn-danger');
                __status_operation.classList.add(managment.setStatus(data.value));
                __status_operation.querySelector('span').innerText = data.value;
                __CellTime.innerHTML = data.time;
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
            // console.log(data);
            //DECLARACION DE VARIABLES
            const __status_booking = document.getElementById('optionsBooking' + data.item);
            if( __status_booking != null ){
                const __Row = ( __status_booking != null ? components.closest(__status_booking, 'tr') : null );
                const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(15)') : "" );
                // console.log(__status_booking, __Row, __Cell);
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
            // console.log(data);
            //DECLARACION DE VARIABLES
            const __btn_comment = document.getElementById('btn_add_modal_' + data.item);
            if( __btn_comment != null ){
                const __Row = ( __btn_comment != null ? components.closest(__btn_comment, 'tr') : null );
                const __indicators = ( __Row != null ? __Row.querySelector('td:nth-child(2)') : "" );
                const __btn_open_modal_comment = ( __Row != null ? __Row.querySelector('td:nth-child(21)') : "" );
                const __comment_new = document.getElementById('comment_new_' + data.item);
                // console.log(__btn_comment);
                // console.log(__Row);
                console.log(__indicators);
                // console.log(__btn_open_modal_comment);
                __btn_comment.dataset.status = data.status;
                __comment_new.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square bs-popover" data-bs-container="body" data-bs-trigger="hover" data-bs-content="'+ data.value +'"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>';
            }
            managment.bsPopover();

            Snackbar.show({
                text: data.message,
                duration: 5000, 
                pos: 'top-right',
                actionTextColor: '#fff',
                backgroundColor: '#2196f3'
            });
        });

        socket.on("addServiceClient", function(data){
            console.log("nuevo servicio");
            // console.log(data);
            //DECLARACION DE VARIABLES
            const __btn_comment = document.getElementById('btn_add_modal_' + data.item);
            if( data.success ){
                if( data.today ){
                    Swal.fire({
                        text: data.message,
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Confirmar recargar pagina",
                        denyButtonText: "Cancelar"
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            location.reload();
                        } else if (result.isDenied) {
                        }
                    });
                }else{
                    Snackbar.show({
                        text: data.message,
                        duration: 5000, 
                        pos: 'top-right',
                        actionTextColor: '#fff',
                        backgroundColor: '#2196f3'
                    });
                }
            }
        });        
    </script>
@endpush

@section('content')
    @php
        // dd($items);
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
                            @csrf
                            <input type="hidden" id="lookup_date_next" value="{{ $nexDate }}" required>
                            <div class="col-12 col-sm-4 mb-3 mb-lg-0">
                                <label class="form-label" for="lookup_date">Fecha de creación</label>
                                <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $date }}">
                            </div>
                            <div class="col-12 col-sm-2 align-self-end">
                                <button type="submit" class="btn btn-primary btn-lg btn-filter w-100">Filtrar</button>
                            </div>
                            <div class="col-12 col-sm-2 align-self-end">
                                <button type="button" class="btn btn-primary btn-lg btn-filter w-100" id="btn_addservice">Agregar nuevo servicio</button>
                            </div>
                            <div class="col-12 col-sm-2 align-self-end">
                                <button type="button" class="btn btn-primary btn-lg btn-filter w-100" id="btn_preassignment">Pre-asignación</button>
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
                        <th class="text-center">HORA OPERACIÓN</th>
                        <th class="text-center">COSTO OPERATIVO</th>
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
                                //DECLARAMOS VARIABLES DE IDENTIFICADORES
                                    //SABER SI SON ARRIVAL, DEPARTURE O TRANSFER, MEDIANTE UN COLOR DE FONDO
                                    $background_color = "background-color: #".( $value->final_service_type == 'ARRIVAL' ? "ddf5f0" : ( $value->final_service_type == 'TRANSFER' ? "d9edfc" : "dbe0f9" ) ).";";
                                    //SABER EL NIVEL DE CUT OFF
                                    $cut_off_zone = ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || ( $value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0 ) ? $value->zone_one_cut_off : $value->zone_two_cut_off );

                                $payment = ( $value->total_sales - $value->total_payments );
                                if($payment < 0) $payment = 0;

                                //PREASIGNACION
                                $flag_preassignment = ( ( ($value->final_service_type == 'ARRIVAL') || ($value->final_service_type == 'TRANSFER') ) && $value->op_one_preassignment != "" ? true : ( $value->final_service_type == 'DEPARTURE' && ( ($value->is_round_trip == 1 && $value->op_two_preassignment != "") || ($value->is_round_trip == 0 && $value->op_one_preassignment != "") ) ? true : false ) );
                                $preassignment = ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || ( $value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0 ) ? $value->op_one_preassignment : $value->op_two_preassignment );
                                //ESTATUS
                                $status_operation = ( ($value->final_service_type == 'ARRIVAL') || ($value->final_service_type == 'TRANSFER') || ($value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0) ? $value->op_one_status_operation : $value->op_two_status_operation );
                                $time_operation = ( ($value->final_service_type == 'ARRIVAL') || ($value->final_service_type == 'TRANSFER') || ($value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0) ? $value->op_one_time_operation : $value->op_two_time_operation );
                                $cost_operation = ( ($value->final_service_type == 'ARRIVAL') || ($value->final_service_type == 'TRANSFER') || ($value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0) ? $value->op_one_operating_cost : $value->op_two_operating_cost );
                                $status_booking = ( ($value->final_service_type == 'ARRIVAL') || ($value->final_service_type == 'TRANSFER') || ($value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0) ? $value->op_one_status : $value->op_two_status );

                                $operation_pickup = (($value->operation_type == 'arrival')? $value->op_one_pickup : $value->op_two_pickup );
                                $operation_from = (($value->operation_type == 'arrival')? $value->from_name.((!empty($value->flight_number))? ' ('.$value->flight_number.')' :'')  : $value->to_name );
                                $operation_to = (($value->operation_type == 'arrival')? $value->to_name : $value->from_name );
                                //COMENTARIO
                                $flag_comment = ( ( ($value->final_service_type == 'ARRIVAL') || ($value->final_service_type == 'TRANSFER') ) && $value->op_one_comments != "" ? true : ( $value->final_service_type == 'DEPARTURE' && ( ($value->is_round_trip == 1 && $value->op_two_comments != "") || ($value->is_round_trip == 0 && $value->op_one_comments != "") ) ? true : false ) );
                                $comment = ( $value->final_service_type == 'ARRIVAL' || $value->final_service_type == 'TRANSFER' || ( $value->final_service_type == 'DEPARTURE' && $value->is_round_trip == 0 ) ? $value->op_one_comments : $value->op_two_comments );

                                switch ($status_operation) {
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

                                switch ($status_booking) {
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
                            <tr class="item-{{ $key.$value->id }}" id="item-{{ $key.$value->id }}" data-code="{{ $value->id }}" data-operation="{{ $value->final_service_type }}" style="{{ $background_color }}">
                                <td>
                                    @if ( $flag_preassignment )
                                        <button type="button" class="btn btn-<?=( $value->final_service_type == 'ARRIVAL' ? 'success' : ( $value->final_service_type == 'DEPARTURE' ? 'primary' : 'info' ) )?> btn_operations text-uppercase">{{ $preassignment }}</button>
                                    @else
                                        <button type="button" class="btn btn-primary text-uppercase add_preassignment btn_operations" id="btn_preassignment_{{ $key.$value->id }}" data-id="{{ $key.$value->id }}" data-code="{{ $value->id }}" data-operation="{{ $value->final_service_type }}">ADD</button>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex w-100">
                                        <div class="comment-default">
                                            @if ( !empty($value->messages) )                                                
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square bs-popover" data-bs-container="body" data-bs-trigger="hover" data-bs-content="{{ $value->messages }}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            @endif
                                        </div>
                                        <div class="comment_new" id="comment_new_{{ $key.$value->id }}">
                                            @if ( $flag_comment )
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square bs-popover" data-bs-container="body" data-bs-trigger="hover" data-bs-content="{{ $comment }}"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ date("H:i", strtotime($operation_pickup)) }}</td>                                    
                                <td>
                                    <span>{{ $value->client_first_name }} {{ $value->client_last_name }}</span>
                                    @if(!empty($value->reference))
                                        [{{ $value->reference }}]
                                    @endif
                                </td>
                                <td>{{ $value->final_service_type }}</td>
                                <td class="text-center">{{ $value->passengers }}</td>
                                <td style="{{ ( $cut_off_zone >= 3 ? 'background-color:#e2a03f;color:#fff;' : '' ) }}">{{ $operation_from }}</td>
                                <td>{{ $operation_to }}</td>
                                <td>{{ $value->site_name }}</td>
                                <td>
                                    <select class="form-control vehicles selectpicker" data-live-search="true" name="vehicle_id" id="vehicle_id_{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" data-code="{{ $value->id }}" data-operation="{{ $value->final_service_type }}">
                                        <option value="0">Selecciona un vehículo</option>
                                        @if ( isset($vehicles) && count($vehicles) >= 1 )
                                            @foreach ($vehicles as $vehicle)
                                                <option {{ ( isset($value->vehicle_id) && $value->vehicle_id == $vehicle->id ) ? 'selected' : '' }} value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->destination_service->name }} - {{ $vehicle->enterprise->names }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control drivers selectpicker" data-live-search="true" name="driver_id" id="driver_id_{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" data-code="{{ $value->id }}">
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
                                            <span>{{ $status_operation }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="optionsOperation{{ $key.$value->id }}">
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-status="PENDING" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-status="E" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> E</a>
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-status="C" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> C</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_operation" data-operation="{{ $value->final_service_type }}" data-status="OK" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Ok</a>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation }}</td>
                                <td class="text-center">{{ $cost_operation }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button id="optionsBooking{{ $key.$value->id }}" data-item="{{ $key.$value->id }}" type="button" class="btn btn-{{ $label2 }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span>{{ $status_booking }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="optionsBooking{{ $key.$value->id }}">
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-status="PENDING" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Pendiente</a>
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-status="COMPLETED" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Completado</a>
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-status="NOSHOW'" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> No show</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item btn_update_status_booking" data-operation="{{ $value->final_service_type }}" data-status="CANCELLED" data-item="{{ $value->id }}" data-booking="{{ $value->reservation_id }}" data-key="{{ $key.$value->id }}"><i class="flaticon-home-fill-1 mr-1"></i> Cancelado</a>
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
                                <td class="text-center" style="{{ ( $value->status == "PENDIENTE" ? 'background-color:#e7515a;color:#fff;' : '' ) }}">{{ $value->status }}</td>
                                <td class="text-end" style="{{ ( $value->status == "PENDIENTE" ? 'background-color:#e7515a;color:#fff;' : '' ) }}">{{ number_format($payment,2) }}</td>
                                <td class="text-center">{{ $value->currency }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-3">
                                        <div class="btn btn-primary btn_operations __open_modal_comment bs-tooltip" title="{{ ( $flag_comment ) ? 'Modificar comentario' : 'Agregar comentario' }}" id="btn_add_modal_{{ $key.$value->id }}" data-status="{{ ( $flag_comment ) ? 1 : 0 }}" data-id="{{ $key.$value->id }}" data-code="{{ $value->id }}" data-type="{{ $value->final_service_type }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                        </div>
                                        <div class="btn btn-primary btn_operations extract_whatsapp bs-tooltip" title="Enviar información por whatsApp" id="extract_whatsapp{{ $key.$value->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"></polygon></svg>
                                        </div>
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
    <x-modals.reservations.operation :websites="$websites" :zones="$zones" :services="$services" />
@endsection