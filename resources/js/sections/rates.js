$(function() {
    $('#btnGetRates').on('click', function (e) {
        var destinationID = $("#destinationID").find("option:selected").val();
        var rateZoneOneId = $("#rateZoneOneId").find("option:selected").val();
        var rateZoneTwoId = $("#rateZoneTwoId").find("option:selected").val();
        var rateServicesID = $("#rateServicesID").find("option:selected").val();
        var rateGroupID = $("#rateGroupID").find("option:selected").val();

        if(destinationID == 0 || rateZoneOneId == 0 || rateZoneTwoId == 0  || rateGroupID == 0){
            Swal.fire(
                '¡ERROR!',
                'Debe seleccionar todos los inputs',
                'error'
            );
            return false;
        }


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        $.ajax({
            url: `/config/rates/get`,
            type: 'POST',
            data: { destination_id: destinationID, from_id: rateZoneOneId, to_id: rateZoneTwoId, service_id: rateServicesID, rate_group: rateGroupID },
            dataType: 'html',
            beforeSend: function() {
                $("#rates-container").empty().html(`<div class="spinner-container"><div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div></div>`);
            },
            success: function(resp) {
                $("#rates-container").empty().html(resp);                      
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

$(document).ready(function () {
    $('#destinationID').on('change', function () {
        var destinationID = $(this).val();
        if(destinationID == 0){
            $("#rateZoneOneId").html('<option value="0">Zona de origen</option>');
            $("#rateZoneTwoId").html('<option value="0">Zona de destino</option>');
            $("#rateServicesID").html('<option value="0">[TODOS]</option>');
            return false;
        }
        getInputs(destinationID);        
    });
});

function getInputs(destinationID){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: `/config/rates/destination/${destinationID}/get`,
        type: 'GET',
        beforeSend: function() {        
            $("#rateZoneOneId").html('<option>Buscando...</option>');
            $("#rateZoneTwoId").html('<option>Buscando...</option>');
            $("#rateServicesID").html('<option>Buscando...</option>');
        },
        success: function(resp) {
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {
                    const data = resp[key];

                    if(key == "zones"){
                        let xHTML = ``;
                        data.forEach(item => {
                            xHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $("#rateZoneOneId").html(xHTML);
                        $("#rateZoneTwoId").html(xHTML);
                    }

                    if(key == "services"){
                        let xHTML = `<option value="0">[TODOS]</option>`;
                        data.forEach(item => {
                            xHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $("#rateServicesID").html(xHTML);
                    }

                }
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
}

$(document).on('click', '#btn_add_rate', function (e) {
    e.preventDefault();

    let frm_data = $("#newPriceForm").serializeArray();
    $.ajax({
        url: '/config/rates/new',
        type: 'POST',
        data: frm_data,
        beforeSend: function() {        
            $("#btn_add_rate").prop('disabled', true).text("Enviando...");
        },
        success: function(resp) {

            Swal.fire({
                title: '¡Éxito!',
                icon: 'success',
                html: 'Tarifa guardada con éxito. Será redirigido en <b></b>',
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
                $("#btnGetRates").click();
            })         
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
        $("#btn_add_rate").prop('disabled', false).text("Agregar Tarifa");
    });

});

function deleteItem(id){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    swal.fire({
        title: '¿Está seguro de eliminar la tarifa?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: '/config/rates/delete',
                type: 'DELETE',
                data: { id: id},
                dataType: 'json',
                beforeSend: function() {
                    $('[data-id="'+id+'"]').prop('disabled', true).text("Eliminando...");
                },
                success: function (data) {
                    swal.fire({
                        title: 'Tarifa eliminada',
                        text: 'La tarifa ha sido eliminada con éxito',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        $("#btnGetRates").click();
                    });
                }
            }).fail(function(xhr, status, error) {
                $('[data-id="'+id+'"]').prop('disabled', true).text("Eliminar");
                Swal.fire(
                    '¡ERROR!',
                    xhr.responseJSON.message,
                    'error'
                );
            });
        }
    });
}

$(document).on('click', '.btnUpdateRates', function(e){
    e.preventDefault();

    let frm_data = $("#editPriceForm").serializeArray();
    $.ajax({
        url: '/config/rates/update',
        type: 'PUT',
        data: frm_data,
        beforeSend: function() {        
            $(".btnUpdateRates").prop('disabled', true).text("Actualizando...");
        },
        success: function(resp) {

            Swal.fire({
                title: '¡Éxito!',
                icon: 'success',
                html: 'Tarifas actualizadas con éxito. Será redirigido en <b></b>',
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
                $("#btnGetRates").click();
            }) 
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
        $(".btnUpdateRates").prop('disabled', true).text("Actualizar Tarifas");
    });

});