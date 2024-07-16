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
    console.log(item);

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

$(function() {
    flatpickr("#created_at", {    
        mode: "single",
        dateFormat: "Y-m-d H:i",
        enableTime: true,
    });
});

document.getElementById('modifyCreatedAt')?.addEventListener('click', () => {
    const submitBtn = document.getElementById('modifyCreatedAt');
    const $alert = $('#alert_created_at');
    $alert.text('');
    $alert.hide();

    const created_at = $('#created_at').val();
    if( !created_at ) {
        $alert.show();
        $alert.text('Agrega una fecha');
        return;
    }

    const token = document.getElementsByName('_token')[0];

    let frm_data = [
        {name: 'created_at', value: created_at},
        {name: 'id', value: reservation_id},
        {name: '_token', value: token.value},
    ];
    let type_req = 'PUT';
    let url_req = '/punto-de-venta/edit-created-at';

    submitBtn.disabled = true;
    submitBtn.innerText = 'Cargando...';

    $.ajax({
        url: url_req,
        type: type_req,
        data: frm_data,
        success: function(resp) {
            if ( typeof resp === 'object' && 'success' in resp && resp.success ) {
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
                Swal.fire({
                    title: 'Oops!',
                    icon: 'error',
                    html: 'Ocurrió un error inesperado',
                    timer: 2500,
                });
                submitBtn.disabled = false;
                submitBtn.innerText = 'Modificar';
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        );
        submitBtn.disabled = false;
        submitBtn.innerText = 'Modificar';
    });
})

document.addEventListener('DOMContentLoaded', function() {

    const terminalSelect = document.getElementById('terminal');
    const total = document.getElementById('total');//TOTAL DE LA RESERVACION
    const sold_in_currency_select = document.getElementById('sold_in_currency');//MODENA DE LA RESERVACION
    const openPaymentModalBtn = document.getElementById('openPaymentModal');
    const formaDePagoSelect = document.getElementById('payment_method');
    const clipContainer = document.getElementById('clip_container');
    const tipoCambioSelect = document.getElementById('tipo_cambio_select');
    const addPaymentBtn = document.getElementById('addPayment');//
    const formComplet = document.getElementById('formComplet');
    const submitBtn = document.getElementById('submitBtn');

    //EVENTO CUANDO SE SELECCIONA UNA TERMINAL
    if (terminalSelect != null) {
        terminalSelect.addEventListener('change', () => {
            const number_of_rows = $('#payments_table tbody').children().length;
            if( number_of_rows === 0 ) return;
            $('#payments_table tbody').html('');
            $('#previous_total').text('0');
            $('.total_remaining').text( $('#total').text() );
            $('#payments_table').hide();
            // $('#sold_in_currency').prop('disabled', false);
            // $('#total').prop('disabled', false);
        });
    }

    //EVENTO AL ABRIR EL MODAL
    if (openPaymentModalBtn != null) {
        openPaymentModalBtn.addEventListener('click', (e) => {
            e.preventDefault();
            $('#payment_method').val('CASH').trigger('change');
            $('#reference_container').hide();
            $('#clip_container').hide();
            $('#paid_in_currency').val(sold_in_currency_select.innerText).prop('disabled', false);
            $('.total_remaining').text(total.innerText);
            $('.total-currency').text(sold_in_currency_select.innerText);
        });
    }

    if (formaDePagoSelect != null) {
        formaDePagoSelect.addEventListener('change', (e) => {
            if (e.target.value === 'CASH') {
                clipContainer.style.display = "none";
                $('#paid_in_currency').prop('disabled', false);
                $('#reference_container').hide();
                return;
            }
            clipContainer.style.display = "block";
            $('#paid_in_currency').val('MXN').prop('disabled', true).trigger('change');
            $('#reference_container').show();
        });       
    }

    if (tipoCambioSelect != null) {
        tipoCambioSelect?.addEventListener('change', (e) => {
            if( $(tipoCambioSelect).val() === '1' ) $('#tipo_cambio_container').show();
            else $('#tipo_cambio_container').hide();
        })   
    }

    const assignDeleteRowEvent = () => {
        $('#payments_table button').last().click(function (e) {
            e.preventDefault();
            const total_to_pay = Number($('#total_original').val());
            const payment = Number($(e.target).parent().parent().find('.payment').text());
            const origin_currency = $(e.target).parent().parent().find('.currency').text();
            const destination_currency = $('#sold_in_currency').text();
            const terminal = $('#terminal').val();
            const custom_currency_exchange = Number($(e.target).parent().parent().find('.custom_currency_exchange').val());
            const currency_exchange = currency_exchange_data.find(currency_exchange => (
                currency_exchange.origin == origin_currency &&
                currency_exchange.destination == destination_currency &&
                currency_exchange.terminal == terminal
            ));
            let total;
            if( custom_currency_exchange ) {
                total = payment * custom_currency_exchange;
            }
            else {
                if( currency_exchange.operation === 'multiplication' ) total = payment * currency_exchange.exchange_rate;
                else total = total = payment / currency_exchange.exchange_rate;
            }
            const previous_total = Number($('#previous_total').text());
            total = previous_total - total;
            total = total.toFixed(2);
            const total_remaining = (total_to_pay - total).toFixed(2);

            $('#previous_total').text(total);
            $('.total_remaining').text(total_remaining);
            if( total >= total_to_pay ) {
                $('.color-total-container').addClass('success');
                $('.color-total-container').removeClass('red');
            }else {
                $('.color-total-container').addClass('red');
                $('.color-total-container').removeClass('success');
            }
            $(e.target).parent().parent().remove();
            const number_of_rows = $('#payments_table tbody').children().length;
            if( number_of_rows === 0 ) {
                $('#payments_table').hide();
                // $('#sold_in_currency').prop('disabled', false);
                // $('#total').prop('disabled', false);
            }
        })        
    }

    if (addPaymentBtn != null) {
        addPaymentBtn.addEventListener('click', () => {
            const $alert = $('#addPaymentModal .alert-danger');
            const payment = Number($('#payment').val());
            const total_to_pay = Number($('#total_original').val());
            let custom_currency_exchange = 0;
            $alert.hide();
            if( !payment || payment <= 0 ) {
                $alert.text('Escribe una cantidad correcta');
                return $alert.show();
            }
            if( tipoCambioSelect && $(tipoCambioSelect).val() === '1' ) {
                if( Number($('#tipo_cambio').val()) <= 0 ) {
                    $alert.text('Escribe el tipo de cambio');
                    return $alert.show();
                }
                custom_currency_exchange = Number($('#tipo_cambio').val());
            }
            const payment_method = $('#payment_method').val();
            const reference = payment_method === 'CARD' ? $('#reference').val() : '';
            const clip_id = $('#clip_id').val();
            const origin_currency = $('#paid_in_currency').val();
            const destination_currency = $('#sold_in_currency').text();
            const terminal = $('#terminal').val();

            if( payment_method === 'CARD' && reference.length < 3 ) {
                $alert.text('Escribe la referencia de pago. Mínimo 4 caracteres');
                return $alert.show();
            }
            const currency_exchange = currency_exchange_data.find(currency_exchange => (
                currency_exchange.origin == origin_currency &&
                currency_exchange.destination == destination_currency &&
                currency_exchange.terminal == terminal
            ));
            if( !custom_currency_exchange && !currency_exchange ) {
                $alert.text(`Lo sentimos, no se encontró la conversión de moneda de ${origin_currency} -> ${destination_currency} para la Terminal ${terminal.replace('T', '')}. Quizá tengas que añadir este caso, o pedirle a algún administrador que lo haga`);
                return $alert.show();
            }

            let total;
            if( custom_currency_exchange ) {
                total = payment * custom_currency_exchange;
            }else {
                if( currency_exchange.operation === 'multiplication' ) total = payment * currency_exchange.exchange_rate;
                else total = total = payment / currency_exchange.exchange_rate;
            }

            const previous_total = Number($('#previous_total').text());
            total = previous_total + total;
            total = total.toFixed(2);
            const total_remaining = (total_to_pay - total).toFixed(2);
            $('#previous_total').text(total);
            $('.total_remaining').text(total_remaining);
            const number_of_rows = $('#payments_table tbody').children().length;
            
            const new_row = `
                <tr>
                    <td class="payment">${payment}</td>
                    <td class="currency">${origin_currency}</td>
                    <td class="reference">${reference || 'No aplica'}</td>
                    <td align="center">
                        <button class="btn btn-danger btn-sm">Eliminar</button>
                    </td>
                    <input type="hidden" class="hidden_reference" name="reference_${number_of_rows}" value="${reference}">
                    <input type="hidden" class="hidden_payment_method" name="payment_method_${number_of_rows}" value="${payment_method}">
                    <input type="hidden" class="hidden_clip_id" name="clip_id_${number_of_rows}" value="${clip_id}">
                    <input type="hidden" class="hidden_payment" name="payment_${number_of_rows}" value="${payment}">
                    <input type="hidden" class="hidden_currency" name="currency_${number_of_rows}" value="${origin_currency}">            
                    <input type="hidden" class="custom_currency_exchange" name="custom_currency_exchange_${number_of_rows}" value="${custom_currency_exchange}">
                </tr>
            `;
            
            $('#payments_table tbody').append(new_row);
            $('#payments_table').show();

            assignDeleteRowEvent();
            
            if( total >= total_to_pay ) {
                $('.color-total-container').addClass('success');
                $('.color-total-container').removeClass('red');
            }else {
                $('.color-total-container').addClass('red');
                $('.color-total-container').removeClass('success');
            }

            $('#sold_in_currency').prop('disabled', true);
            $('#total').prop('disabled', true);

            $('#reference').val("");
            $('#payment').val("");
            $('#addPaymentModal').modal('hide');
        })
    }

    formComplet.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const number_of_rows = $('#payments_table tbody').children().length;

        if( number_of_rows === 0 ) return Swal.fire({
            title: 'Faltan campos por rellenar',
            icon: 'warning',
            html: 'Tienes que agregar al menos 1 pago',
            timer: 5000,
        });

        submitBtn.disabled = true;
        submitBtn.innerText = 'Cargando...';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            }
        });

        $('#payments_table tbody').children().each(function(index) {
            $(this).find('.hidden_reference').attr('name', `reference_${index}`);
            $(this).find('.hidden_payment_method').attr('name', `payment_method_${index}`);
            $(this).find('.hidden_clip_id').attr('name', `clip_id_${index}`);
            $(this).find('.hidden_payment').attr('name', `payment_${index}`);
            $(this).find('.hidden_currency').attr('name', `currency_${index}`);
            $(this).find('.custom_currency_exchange').attr('name', `custom_currency_exchange_${index}`);
        });

        let frm_data = $("#formComplet").serializeArray();
        frm_data.push({name: 'number_of_payments', value: number_of_rows});
        frm_data.push({name: 'reservation_id', value: reservation_id});
        console.log(frm_data);

        $.ajax({
            url: '/punto-de-venta/capture/update',
            type: 'POST',
            data: frm_data,
            success: function(resp) {
                if ( typeof resp === 'object' && 'success' in resp && resp.success ) {
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
                    Swal.fire({
                        title: 'Oops!',
                        icon: 'error',
                        html: 'Ocurrió un error inesperado',
                        timer: 2500,
                    });
                    $('#sold_in_currency').prop('disabled', true);
                    $('#total').prop('disabled', true);
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Generar venta';
                }
            }
        }).fail(function(xhr, status, error) {
            Swal.fire(
                '¡ERROR!',
                xhr.responseJSON.message,
                'error'
            );
            $('#sold_in_currency').prop('disabled', true);
                    $('#total').prop('disabled', true);
            submitBtn.disabled = false;
            submitBtn.innerText = 'Generar venta';
        });
    });

});