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

function cancelReservation(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    }); 
    swal.fire({
        title: '¿Está seguro de cancelar la reservación?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            var url = "/reservations/"+id;
            $.ajax({
                url: url,
                type: 'DELETE',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Reservación cancelada',
                        text: 'Se ha cancelado la reservación correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al cancelar la reservación',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
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

function saveSale(){
    $("#btn_new_sale").prop('disabled', true);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    let frm_data = $("#frm_new_sale").serializeArray();
    let type = $("#type_form").val();
    let type_req = type == 1 ? 'POST' : 'PUT';
    let url_req = type == 1 ? '/sales' : '/sales/'+$("#sale_id").val();
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
                    html: 'Venta guardada con éxito. Será redirigido en <b></b>',
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
        $("#btn_new_sale").prop('disabled', false);
    });
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    swal.fire({
        title: '¿Está seguro de eliminar la venta?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/sales/'+id,
                type: 'DELETE',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Venta eliminada',
                        text: 'Se ha eliminado la venta correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al eliminar la venta',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

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
    $("#from_zone_id").val(item.from_zone);
    $("#to_zone_id").val(item.to_zone);

    $("#item_id_edit").val(item.reservations_item_id);
    $("#servicePaxForm").val(item.passengers);
    $("#destination_serv").val(item.destination_service_id);
    $("#serviceFromForm").val(item.from_name);
    $("#serviceToForm").val(item.to_name);
    $("#serviceDateForm").val(item.op_one_pickup);
    $("#serviceFlightForm").val(item.flight_number);

    $("#from_lat_edit").val(item.from_lat);
    $("#from_lng_edit").val(item.from_lng);
    $("#to_lat_edit").val(item.to_lat);
    $("#to_lng_edit").val(item.to_lng);


    if(item.op_one_status != 'PENDING'){
        $("#serviceDateForm").prop('readonly', true);
    }
    if(item.op_two_status != 'PENDING'){
        $("#serviceDateRoundForm").prop('readonly', true);
    }
    if(item.is_round_trip == 1){
        $("#serviceDateRoundForm").val(item.op_two_pickup);
        $("#info_return").removeClass('d-none');
    }else{
        $("#serviceDateRoundForm").val('');
        $("#info_return").addClass('d-none');
    }
}

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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    swal.fire({
        title: '¿Está seguro de eliminar el pago?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/payments/'+id,
                type: 'DELETE',
                dataType: 'json',
                success: function (data) {
                    swal.fire({
                        title: 'Pago eliminado',
                        text: 'Se ha eliminado el pago correctamente',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function (data) {
                    swal.fire({
                        title: 'Error',
                        text: 'Ha ocurrido un error al eliminar el pago',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        }
    });
}

$("#btn_new_payment").on('click', function(){
    $("#btn_new_payment").prop('disabled', true);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    let frm_data = $("#frm_new_payment").serializeArray();
    let type = $("#type_form_pay").val();
    let type_req = type == 1 ? 'POST' : 'PUT';
    let url_req = type == 1 ? '/payments' : '/payments/'+$("#payment_id").val();
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
                    html: 'Pago guardado con éxito. Será redirigido en <b></b>',
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
        $("#btn_new_payment").prop('disabled', false);
    });
});

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
                window.onbeforeunload = null;
                let timerInterval
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Confirmación enviada. Será redirigido en <b></b>',
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
    //console.log(item_id);
    //console.log(destination_id);
    //$("#arrival_confirmation_item_id").val(item_id);
    //$("#terminal_id").empty().html('<option value="0">Cargando...</option>');
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
                window.onbeforeunload = null;
                let timerInterval
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Confirmación de regreso enviada. Será redirigido en <b></b>',
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