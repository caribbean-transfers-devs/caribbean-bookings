$('.table').DataTable({
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
    },
    paging: false,
    //ordering: false
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

function setStatus(event, type, status, item_id, rez_id){
    event.preventDefault();
    var clickedRow = event.target.closest('tr');
    var statusCell = clickedRow.querySelector('td:nth-child(4)');
    //statusCell.textContent = status;

    let alert_type = 'btn-secondary';
    switch (status) {
        case 'PENDING':
            alert_type = 'btn-secondary';
            break;
        case 'COMPLETED':
            alert_type = 'btn-success';
            break; 
        case 'NOSHOW':
            alert_type = 'btn-warning';
            break;
        case 'CANCELLED':
            alert_type = 'btn-danger';
            break;  
        default:
            alert_type = 'btn-secondary';
            break;
    }    

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
                url: `/operation/managment/update-status`,
                type: 'PUT',
                data: { rez_id:rez_id, item_id:item_id, type:type, status:status },
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
                        statusCell.innerHTML = `<span class="badge ${alert_type} rounded-pill">${status}</span>`;                        
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

function Search(){
    $("#btnSearch").text("Buscando....").attr("disabled", true);
    $("#formSearch").submit();
}