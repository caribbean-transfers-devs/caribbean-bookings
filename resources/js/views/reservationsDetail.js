$(function() {
    
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