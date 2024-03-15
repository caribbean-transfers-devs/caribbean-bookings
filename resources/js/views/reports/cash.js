$('#reservations_table').DataTable({
    dom: 'Bfrtip',
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
    },
    paging: false,
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    ordering: true,
    "order": [[2, 'asc']] 
});

$(function() {
    const picker = new easepick.create({
        element: "#lookup_date",        
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        plugins: ['RangePlugin'],
        zIndex: 10
    })
});

function updateConfirmation(event, id, status){
    event.preventDefault();

    swal.fire({
        title: '¿Está seguro?',
        text: "Estás a punto de cambiar el estatus de conciliación",
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
                url: `/reports/cash/update-status`,
                type: 'PUT',
                data: { id, status},
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

function Search(){
    $("#btnSearch").text("Buscando....").attr("disabled", true);
    $("#formSearch").submit();
}