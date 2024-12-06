
let types_cancellations = {};
const __serviceDateForm = document.getElementById('serviceDateForm');
const __serviceDateRoundForm = document.getElementById('serviceDateRoundForm');

const __formConfirmation = document.getElementById('formConfirmation'); // DIV QUE TIENE EL FORMULARIO DE ENVIO DE CONFIRMACION
const __btnSendArrivalConfirmation = document.getElementById('btnSendArrivalConfirmation'); //BOTON PARA ENVIAR EL EMAIL DE CONFIRMATION

const __titleModal = document.getElementById('titleModal');
const __closeModalHeader = document.getElementById('closeModalHeader');
const __closeModalFooter = document.getElementById('closeModalFooter');

const __serviceType = document.getElementById('serviceTypeForm');

//DECLARAMOS VARIABLES, PARA VENTAS Y PAGOS
const __type = document.getElementById('type_form');
const __code = document.getElementById('sale_id');

const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');

if( document.querySelectorAll('.timeline-item').length > 0 ){
    document.querySelectorAll('.timeline-item').forEach(item => {
        const height = item.offsetHeight;
        item.style.setProperty('--timeline-height', `${height}px`);
    });
}

$(function() {
    $('#serviceSalesModal').on('hidden.bs.modal', function () {
        $("#frm_new_sale")[0].reset();
        $("#sale_id").val('');
        $("#type_form").val(1);
        $("#btn_new_sale").prop('disabled', false);
    });

    $('#servicePaymentsModal').on('hidden.bs.modal', function () {
        $("#frm_new_payment")[0].reset();
        $("#payment_id").val('');
        $("#type_form_pay").val(1);
        $("#btn_new_payment").prop('disabled', false);
    });
});

function typesCancellations() {
    const __types_cancellations = document.getElementById('types_cancellations');
    if( __types_cancellations != null ){
        let options = JSON.parse(__types_cancellations.value);
        if( options != null && options.length > 0 ){
            // Ordenar las opciones alfabéticamente por 'name_es'
            options.sort((a, b) => a.name_es.localeCompare(b.name_es));        
            options.forEach(option => {
                types_cancellations[option.id] = option.name_es;
            });
        }
    }
}
typesCancellations();

function initMap() {
    var from_lat = parseFloat($('#from_lat').val());
    var from_lng = parseFloat($('#from_lng').val());
    var to_lat = parseFloat($('#to_lat').val());
    var to_lng = parseFloat($('#to_lng').val());

    var location1 = { lat: from_lat, lng: from_lng };
    var location2 = { lat: to_lat, lng: to_lng };

    // Create a map centered at one of the locations
    var map = new google.maps.Map(document.getElementById('services_map'), {
        center: location1, 
        zoom: 10 
    });

    var marker1 = new google.maps.Marker({
        position: location1,
        map: map,
        title: 'Origen'
    });

    var marker2 = new google.maps.Marker({
        position: location2,
        map: map,
        title: 'Destino'
    });
}

function sendMail(code,mail,languague){
    var url = "https://api.caribbean-transfers.com/api/v1/reservation/send?code="+code+"&email="+mail+"&language="+languague+"&type=new";
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            swal.fire({
                title: 'Correo enviado',
                text: 'Se ha enviado el correo correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        },
        error: function (data) {
            swal.fire({
                title: 'Error',
                text: 'Ha ocurrido un error al enviar el correo',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
    
}

function sendInvitation(event, item_id, lang = 'en'){
    event.preventDefault();
    var url = "/reservations/payment-request";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: { item_id:item_id, lang:lang },
        success: function (data) {
            Swal.fire({
                title: '¡Éxito!',
                icon: 'success',
                html: 'Solicitúd de pago enviada. Será redirigido en <b></b>',
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
                location.reload();
            })
        },
        error: function (data) {
            swal.fire({
                title: 'Error',
                text: 'Ha ocurrido un error al enviar el invitación',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });    
}

//FUNCION PARA PODER CANCELAR LA RESERVACIÓN PADRE Y SUS ITEMS, SIEMPRE QUE NO TENGA ALGUN SERVICIO COMPLETED
function cancelReservation(id){
    swal.fire({
        html: '¿Está seguro de cancelar la reservación? <br> Esta acción no se puede revertir',
        inputLabel: "Selecciona el motivo de cancelación",
        input: "select",
        inputOptions: types_cancellations,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            let __params = {};
            __params.type = result.value;            
            components.request_exec_ajax( _LOCAL_URL + "/reservations/" + id, 'DELETE', __params );
        }
    });
}

function duplicatedReservation(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    swal.fire({
        title: '¿Está seguro de marcar como duplicado la reservación?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        console.log(result, id);
        if (result.isConfirmed) {
            var url = "/reservationsDuplicated/"+id;
            $.ajax({
                url: url,
                type: 'PUT',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Reservación duplicada',
                        text: 'Se ha marcado como duplicado la reservación correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al marcar la reservación',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

//ACCION PARA REMOVER UNA COMISION
const __remove_commission = document.querySelector('.__remove_commission');
if( __remove_commission != null ){
    __remove_commission.addEventListener('click', function(event){
        event.preventDefault();
        const { reservation } = this.dataset;
        console.log(reservation);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            }
        });
        swal.fire({
            title: '¿Está seguro que desea remover la comisión de esta reservación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "/reservation/removeCommission/"+reservation;
                $.ajax({
                    url: url,
                    type: 'PUT',
                    dataType: 'json',
                    success: function (data) {
                        swal.fire({
                            title: 'Comisión removida',
                            text: 'Se ha removido la comisión para esta reservación',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function (data) {
                        swal.fire({
                            title: 'Error',
                            text: 'Ha ocurrido un error al remover comisión',
                            icon: 'error',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            }
        });        
    })
}

function saveFollowUp(){
    $("#btn_new_followup").prop('disabled', true);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    let frm_data = $("#frm_new_followup").serializeArray();
    let type_req = 'POST';
    let url_req = '/reservationsfollowups';
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
                    location.reload();
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
        $("#btn_new_followup").prop('disabled', false);
    });
}

/* ===== Start Events Sales Settings ===== */
function saveSale(){
    $("#btn_new_sale").prop('disabled', true);
    let __params = components.serialize(document.getElementById('frm_new_sale'),'object');
    components.request_exec_ajax( _LOCAL_URL + ( __type.value == 1 ? "/sales" : "/sales/" + __code.value ), ( __type.value == 1 ? 'POST' : 'PUT' ), __params );
}

function getSale(id){
    $("#btn_new_sale").prop('disabled', true);
    $("#type_form").val(2);
    $("#sale_id").val(id);
    $.ajax({
        url: '/sales/'+id,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#new_sale_type_id").val(data.sale_type_id);
            $("#new_sale_description").val(data.description);
            $("#new_sale_total").val(data.total);
            $("#new_sale_quantity").val(data.quantity);
            $("#new_sale_agent_id").val(data.call_center_agent_id);
            $("#btn_new_sale").prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function deleteSale(id){
    swal.fire({
        html: '¿Está seguro de eliminar la venta? <br> Esta acción no se puede revertir',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            let __params = {};
            components.request_exec_ajax( _LOCAL_URL + "/sales/" + id, 'DELETE', __params );
        }
    });
}
/* ===== End Events Sales Settings ===== */

$("#btn_edit_res_details").on('click', function(){
    $("#btn_edit_res_details").prop('disabled', true);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    let frm_data = $("#frm_edit_details").serializeArray();
    let type_req ='PUT';
    let url_req = '/reservations/'+$("#reservation_id").val();
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
                    html: 'Datos de la reserva editados con éxito. Será redirigido en <b></b>',
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
                    location.reload();
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
        $("#btn_edit_res_details").prop('disabled', false);
    });
});

function serviceInfo(origin,destination,time,km){
    $("#origin_location").html(origin);
    $("#destination_location").html(destination);
    $("#destination_time").html(time);
    $("#destination_kms").html(km);
}

function itemInfo(item){
    console.log(item);
    const __service_type = document.querySelector('.serviceTypeForm');    

    $("#from_zone_id").val(item.from_zone);
    $("#to_zone_id").val(item.to_zone);

    $("#item_id_edit").val(item.reservations_item_id);
    $("#servicePaxForm").val(item.passengers);
    $("#destination_serv").val(item.destination_service_id);
    $("#serviceFromForm").val(item.from_name);
    $("#serviceToForm").val(item.to_name);
    $("#serviceFlightForm").val(item.flight_number);

    $("#from_lat_edit").val(item.from_lat);
    $("#from_lng_edit").val(item.from_lng);
    $("#to_lat_edit").val(item.to_lat);
    $("#to_lng_edit").val(item.to_lng);

    __serviceDateForm.value = item.op_one_pickup,
    __serviceDateForm.min = item.op_one_pickup;
    if(item.op_one_status != 'PENDING'){
        $("#serviceDateForm").prop('readonly', true);
    }

    if(item.op_two_status != 'PENDING'){
        $("#serviceDateRoundForm").prop('readonly', true);
    }

    if(item.is_round_trip == 1){
        __serviceDateRoundForm.value = item.op_two_pickup,
        __serviceDateRoundForm.min = item.op_one_pickup;
        $("#info_return").removeClass('d-none');
        __service_type.classList.add('d-none');
        __serviceType.removeAttribute('name');
    }else{
        __serviceDateRoundForm.value = "",
        __serviceDateRoundForm.min = "";
        $("#info_return").addClass('d-none');
        __service_type.classList.remove('d-none');
        __serviceType.setAttribute('name','serviceTypeForm');
    }
}

if( __serviceType != null ){
    __serviceType.addEventListener('change', function(event){
        event.preventDefault();        
        if (__serviceType.value == 1) {
            $("#info_return").removeClass('d-none');
        }
    })
}

//FUNCIONALIDAD DE CALENDARIO FORM
if( __serviceDateForm != null ){
    __serviceDateForm.addEventListener('input', function(event) {
        event.preventDefault();        
        __serviceDateRoundForm.min = this.value;
    });
}

/* ===== Start Events Payments Settings ===== */
$("#btn_new_payment").on('click', function(){
    $("#btn_new_payment").prop('disabled', true);
    let __params = components.serialize(document.getElementById('frm_new_payment'),'object');
    components.request_exec_ajax( _LOCAL_URL + ( __type_pay.value == 1 ? "/payments" : "/payments/" + __code_pay.value ), ( __type_pay.value == 1 ? 'POST' : 'PUT' ), __params );
});

function getPayment(id){
    $("#btn_new_payment").prop('disabled', true);
    $("#type_form_pay").val(2);
    $("#payment_id").val(id);
    $.ajax({
        url: '/payments/'+id,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#servicePaymentsTypeModal").val(data.payment_method);
            $("#servicePaymentsDescriptionModal").val(data.reference);
            $("#servicePaymentsTotalModal").val(data.total);
            $("#servicePaymentsCurrencyModal").val(data.currency);
            $("#servicePaymentsExchangeModal").val(data.exchange_rate);
            $("#btn_new_payment").prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function deletePayment(id){
    swal.fire({
        html: '¿Está seguro de eliminar el pago? <br> Esta acción no se puede revertir',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            let __params = {};
            components.request_exec_ajax( _LOCAL_URL + "/payments/" + id, 'DELETE', __params );
        }
    });
}
/* ===== End Events Payments Settings ===== */

$("#servicePaymentsCurrencyModal").on('change', function(){
    let currency = $(this).val();
    let reservation_id = $("#reserv_id_pay").val();
    $.ajax({
        url: '/GetExchange/'+reservation_id+'?currency='+currency,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#servicePaymentsExchangeModal").val(data.exchange_rate);
            $("#operation_pay").val(data.operation);
            $("#btn_new_payment").prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
});

$("#btn_edit_item").on('click', function(){
    $("#btn_edit_item").prop('disabled', true);
    let frm_data = $("#edit_reservation_service").serializeArray();
    let type_req ='PUT';
    let url_req = '/editreservitem/'+$("#item_id_edit").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
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
                    html: 'Datos del servicio editados con éxito. Será redirigido en <b></b>',
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
                    location.reload();
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
        $("#btn_edit_item").prop('disabled', false);
    });
});

function initialize(div) {
    var input = document.getElementById(div);
    var autocomplete = new google.maps.places.Autocomplete(input);
  
    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if(div == "serviceFromForm"){        
          var fromLat = document.getElementById("from_lat_edit");
              fromLat.value = place.geometry.location.lat();
  
          var fromLng = document.getElementById("from_lng_edit");
              fromLng.value = place.geometry.location.lng();
        }
        if(div == "serviceToForm"){
          var toLat = document.getElementById("to_lat_edit");
              toLat.value = place.geometry.location.lat();
  
          var toLng = document.getElementById("to_lng_edit");
              toLng.value = place.geometry.location.lng();
        }
    });
}

$(function() {
    google.maps.event.addDomListener(window, 'load', initialize('serviceFromForm') );
    google.maps.event.addDomListener(window, 'load', initialize('serviceToForm') );
    initMap();
});

function getContactPoints(item_id, destination_id){
    __formConfirmation.classList.remove('d-none');
    __btnSendArrivalConfirmation.classList.remove('d-none');
    __titleModal.innerHTML = "Confirmación de llegada";
    __closeModalFooter.innerHTML = "Cerrar";
    
    $("#arrival_confirmation_item_id").val(item_id);
    $("#terminal_id").empty().html('<option value="0">Cargando...</option>');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    $.ajax({
        url: '/reservations/confirmation/contact-points',
        type: 'POST',
        data: { destination_id: destination_id },
        success: function(resp) {
            //console.log(resp);
            let xHTML = '';
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {
                    const data = resp[key];
                    xHTML += `<option value="${data.id}">${data.name}</option>`;                    
                }
            }
            $("#terminal_id").empty().html(xHTML);           
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )        
    });
}

function sendArrivalConfirmation(){
    $("#btnSendArrivalConfirmation").prop('disabled', true);
    let frm_data = $("#formArrivalConfirmation").serializeArray();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    $.ajax({
        url: '/reservations/confirmation/arrival',
        type: 'POST',
        data: frm_data,
        success: function(resp) {
            if (resp.status == 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'La confirmación fue enviada.<b></b>',
                    timer: 2500
                })

                if( resp.hasOwnProperty('message') ){      
                    contentMessageConfirmation(resp.message);              
                }

                $("#btnSendArrivalConfirmation").prop('disabled', false);

                // window.onbeforeunload = null;
                // let timerInterval
                // Swal.fire({
                //     title: '¡Éxito!',
                //     icon: 'success',
                //     html: 'Confirmación enviada. Será redirigido en <b></b>',
                //     timer: 2500,
                //     timerProgressBar: true,
                //     didOpen: () => {
                //         Swal.showLoading()
                //         const b = Swal.getHtmlContainer().querySelector('b')
                //         timerInterval = setInterval(() => {
                //             b.textContent = (Swal.getTimerLeft() / 1000)
                //                 .toFixed(0)
                //         }, 100)
                //     },
                //     willClose: () => {
                //         clearInterval(timerInterval)
                //     }
                // }).then((result) => {
                //     location.reload();
                // })
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
        $("#btnSendArrivalConfirmation").prop('disabled', false);
    });
}

function sendDepartureConfirmation(event, item_id, destination_id, lang = 'en', type = 'departure'){
    event.preventDefault();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    $.ajax({
        url: '/reservations/confirmation/departure',
        type: 'POST',
        data: { item_id:item_id, destination_id: destination_id, lang:lang, type:type },
        success: function(resp) {
            if (resp.status == 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'La confirmación de regreso fue enviada.<b></b>',
                    timer: 2500
                });

                $("#arrivalConfirmationModal").modal('show');
                __titleModal.innerHTML = ( type == "departure" ? "Confirmación de salida" : ( type == "transfer-pickup" ? "Confirmación de recogida" : "Confirmación de regreso" ) );
                __closeModalFooter.innerHTML = "Cerrar";

                if( resp.hasOwnProperty('message') ){      
                    contentMessageConfirmation(resp.message);              
                }

                // window.onbeforeunload = null;
                // let timerInterval
                // Swal.fire({
                //     title: '¡Éxito!',
                //     icon: 'success',
                //     html: 'Confirmación de regreso enviada. Será redirigido en <b></b>',
                //     timer: 2500,
                //     timerProgressBar: true,
                //     didOpen: () => {
                //         Swal.showLoading()
                //         const b = Swal.getHtmlContainer().querySelector('b')
                //         timerInterval = setInterval(() => {
                //             b.textContent = (Swal.getTimerLeft() / 1000)
                //                 .toFixed(0)
                //         }, 100)
                //     },
                //     willClose: () => {
                //         clearInterval(timerInterval)
                //     }
                // }).then((result) => {
                //     location.reload();
                // })
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )        
    });
}

__closeModalHeader.addEventListener('click', function(){
    contentMessageConfirmation("");
    location.reload();
});

__closeModalFooter.addEventListener('click', function(){
    contentMessageConfirmation("");
    location.reload();
});

function contentMessageConfirmation(message){
    const __messageConfirmation = document.getElementById('messageConfirmation');
    if( message == "" ){
        __formConfirmation.classList.add('d-none');
        __btnSendArrivalConfirmation.classList.add('d-none');
        __titleModal.innerHTML = "";
        __closeModalFooter.innerHTML = "";        
    }
    __messageConfirmation.innerHTML = message;
}

function loadContent() {
    $('#media-listing').load('/reservations/upload/' + rez_id, function(response, status, xhr) {
        if (status == "error") {
            $('#media-listing').html('Error al cargar el contenido');
        }
    });
}

loadContent();

Dropzone.options.uploadForm = {
    maxFilesize: 5, // Tamaño máximo del archivo en MB
    acceptedFiles: 'image/*,.pdf', // Solo permitir imágenes y archivos PDF
    dictDefaultMessage: 'Arrastra el archivo aquí o haz clic para subirlo (Imágenes/PDF)...',
    addRemoveLinks: false,
    autoProcessQueue: true,
    uploadMultiple: false,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    init: function() {        
        this.on("success", function(file, response) {
            loadContent();
        });
        this.on("error", function(file, errorMessage) {
            console.log('Error al subir el archivo:', errorMessage);
        });
    }
};

$( document ).delegate( ".deleteMedia", "click", function(e) {
    e.preventDefault();
    let id = $(this).data("id");
    let name = $(this).data("name");
    swal.fire({
        title: '¿Está seguro de eliminar el documento?',
        text: "Esta acción no se puede revertir",        
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                }
            });
            $.ajax({
                url: '/reservations/upload/'+id,
                type: 'DELETE',
                data: { id:id, name:name },
                success: function(resp) {
                    swal.fire({
                        title: 'Documento eliminado',
                        text: 'El documento ha sido eliminado con éxito',
                        icon: 'success',
                    });
                    loadContent();
                }
            }).fail(function(xhr, status, error) {
                Swal.fire(
                    '¡ERROR!',
                    xhr.responseJSON.message,
                    'error'
                )        
            });
        }
    });    

});

//PERMITE CAMBIAR EL ESTATUS DE LOS SERVICIOS
function setStatus(event, type, status, item_id, rez_id){
    event.preventDefault();
    let __settings = {};
    __settings.html = '¿Está seguro de actualizar el estatus? <br> Esta acción no se puede revertir';
    if( status == "CANCELLED" ){
        __settings.inputLabel = "Selecciona el motivo de cancelación";
        __settings.input = "select";
        __settings.inputOptions = types_cancellations;
    }
    __settings.icon = "question";
    __settings.showCancelButton = true;
    __settings.confirmButtonText = "Aceptar";
    __settings.cancelButtonText = "Cancelar";

    swal.fire( __settings ).then((result) => {
        if(result.isConfirmed == true){
            let __params = {};
            __params.rez_id = rez_id;
            __params.item_id = item_id;
            __params.type = type;
            __params.status = status;
            __params.type_cancel = result.value;

            components.request_exec_ajax( _LOCAL_URL + "/operation/managment/update-status", 'PUT', __params );
        }
    });    
   
}

function updateConfirmation(event, id, type, status, rez_id){
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });                
            $.ajax({
                url: `/operation/confirmation/update-status`,
                type: 'PUT',
                data: { id, type, status, rez_id},
                beforeSend: function() {        
                    
                },
                success: function(resp) {
                    Swal.fire({
                        title: '¡Éxito!',
                        icon: 'success',
                        html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
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
                        location.reload();
                    });
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

const __unlocks = document.querySelectorAll('.unlock');
if (__unlocks.length > 0) {
    __unlocks.forEach(__unlocks => {
        __unlocks.addEventListener('click', function(event){
            event.preventDefault();
            const { id, type, rez_id } = this.dataset;
            swal.fire({
                title: '¿Está seguro de desbloquear este servicio del cierre de operación?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed == true){
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });                
                    $.ajax({
                        url: `/operation/unlock/service`,
                        type: 'PUT',
                        data: { id, type, rez_id},
                        beforeSend: function() {        
                            
                        },
                        success: function(resp) {
                            Swal.fire({
                                title: '¡Éxito!',
                                icon: 'success',
                                html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
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
                                location.reload();
                            });
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
        });
    });
}

function enableReservation(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    swal.fire({
        title: '¿Está seguro de activar la reservación?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        console.log(result, id);
        if (result.isConfirmed) {
            var url = "/reservationsEnable/"+id;
            $.ajax({
                url: url,
                type: 'PUT',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Reservación activada',
                        text: '¡Verifica los estatus de operación!',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al marcar la reservación',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

function copyPaymentLink(event, code, email, lang){
    event.preventDefault();

    let URL = `https://caribbean-transfers.com/easy-payment?code=${code}&email=${email}`;
    if(lang == "es"){
        URL = `https://caribbean-transfers.com/es/easy-payment?code=${code}&email=${email}`;
    }

    navigator.clipboard.writeText(URL).then(function() {

        Swal.fire({
            title: '¡Éxito!',
            icon: 'success',
            html: `Se ha copiado la URL (${lang}) al porta papeles`,
            timer: 1500,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading()
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                }, 100)
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        }).then((result) => {
            location.reload();
        })
    }).catch(function(error) {
        console.error('Error al copiar el texto al portapapeles: ', error);
    });    
}

function openCredit(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    swal.fire({
        title: '¿Está seguro de marcar como crédito abierto?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        console.log(result, id);
        if (result.isConfirmed) {
            var url = "/reservationsOpenCredit/"+id;
            $.ajax({
                url: url,
                type: 'PUT',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Reservación actualizada',
                        text: 'La reservación ha sido marcada como Crédito Abierto',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al marcar como Crédito Abierto',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

function enablePlusService(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    swal.fire({
        title: '¿Está seguro de activar el servicio plus?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        console.log(result, id);
        if (result.isConfirmed) {
            var url = "/reservationsEnablePlusService/"+id;
            $.ajax({
                url: url,
                type: 'PUT',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Reservación actualizada',
                        text: 'Se activo el servicio plus en la reservación',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al activar el servicio plus de la reservación',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

const __site = document.getElementById('serviceSiteReference');
if( __site != null ){
  actionSite(__site);
  __site.addEventListener('change', function(event){
    event.preventDefault();
    actionSite(__site);    
  });
}

function actionSite(__site){
  const __reference = document.getElementById('serviceClientReference');
  if( __site.value == "9" || __site.value == "14" || __site.value == "16" ){
    __reference.removeAttribute('readonly');
  }else{
    __reference.setAttribute('readonly', true);
  }
}
