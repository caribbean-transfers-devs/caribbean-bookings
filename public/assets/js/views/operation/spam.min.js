$('.table').DataTable({
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
    },
    paging: false,
    ordering: true,
    "order": [[2, 'asc']] 
});

$(function() {
    const picker = new easepick.create({
        element: "#lookup_date",        
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10
    })
});

function updateSpam(event, id, type, update){
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

            var button = $('button[data-id="'+ id +'"]');
            

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });                
            $.ajax({
                url: `/operation/spam/update-status`,
                type: 'PUT',
                data: { id, type},
                beforeSend: function() {        
                    
                },
                success: function(resp) {
                    button.removeClass().addClass('btn dropdown-toggle ' + update).text(type);
                    Swal.fire({
                        icon: "success",
                        title: '¡Éxito!',
                        html: 'Servicio actualizado con éxito.',
                        showConfirmButton: false,
                        timer: 1500
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