if ( document.getElementById('lookup_date') != null ) {
    const picker = new easepick.create({
        element: "#lookup_date",
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10,
        plugins: ['RangePlugin'],
    });   
}

if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __title_modal = document.getElementById('filterModalLabel');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function () {
        __title_modal.innerHTML = this.dataset.title;
    });
}

function setStatus(event, type, status, item_id, rez_id){
    event.preventDefault();
    var clickedRow = event.target.closest('tr');
    var statusCell = clickedRow.querySelector('td:nth-child(4)');
    //statusCell.textContent = status;

    let alert_type = 'btn-secondary';
    switch (status) {
        case 'PENDING':
            alert_type = 'secondary';
            break;
        case 'COMPLETED':
            alert_type = 'success';
            break; 
        case 'NOSHOW':
            alert_type = 'warning';
            break;
        case 'CANCELLED':
            alert_type = 'danger';
            break;  
        default:
            alert_type = 'secondary';
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
                        statusCell.innerHTML = `<span class="badge badge-light-${alert_type} mb-2 me-4">${status}</span>`;
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

var inactivityTime = (5 * 60000); // 30 segundos en milisegundos
var timeoutId;

function resetTimer() {
    clearTimeout(timeoutId);          
    timeoutId = setTimeout(refreshPage, inactivityTime);
}

function refreshPage() {
    location.reload();
}
    
document.addEventListener('mousemove', resetTimer);
document.addEventListener('keydown', resetTimer);
        
resetTimer();  