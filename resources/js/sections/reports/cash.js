//VALIDAMOS DOM
document.addEventListener("DOMContentLoaded", function() {
    //VARIABLES
    const _formCashConciliation = document.getElementById('formCashConciliation');

    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'), 'fixedheader');
    }

    components.titleModalFilter();

    //CALENDARIO MODAL
    let picker = flatpickr("#date_conciliation", {
        mode: "single",
        dateFormat: "Y-m-d",
        enableTime: false,
    });

    document.addEventListener("click", components.debounce(function (event) {
        if (event.target && event.target.id === '__close_modal') {
            _formCashConciliation.reset();
        }

        //PERMITE CONCILIAR EL PAGO EN EFECTIVO
        if (event.target.classList.contains('cashConciliation')) {
            event.preventDefault();
            const { code } = event.target.dataset;
            if( code ){
                $("#addCashConciliationModal").modal('show');
                document.getElementById('codes_payment_conciliation').value = code;
            }else{
                Swal.fire(
                    '¡ERROR!',
                    'Nu cuenta con un pago para conciliar, favor de validar la reservación',
                    'error'
                );
            }
        };
    }, 300)); // 300ms de espera antes de ejecutar de nuevo

    document.addEventListener('submit', function(event) {
        if (event.target && event.target.id === 'formCashConciliation') {
            event.preventDefault();

            const _formData     = new FormData(_formCashConciliation);
            const _btnCashConciliation   = document.getElementById('CashConciliation');
            _btnCashConciliation.disabled = true;
            _btnCashConciliation.textContent = "procesando...";

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se realiza la conciliación.",
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                didOpen: () => {
                  Swal.showLoading();
                }
            });

            fetch('/action/cashConciliation', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: _formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }        
                return response.json();
            })
            .then(data => {                
                if( data.status  == "success" ){                        
                    $("#addCashConciliationModal").modal('hide');
                }

                _btnCashConciliation.disabled = false;
                _btnCashConciliation.textContent = "Guardar";

                Swal.fire({
                    icon: data.status,
                    html: data.message,
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                }).then(() => {
                    location.reload();
                });
            })
            .catch(error => {
                Swal.fire(
                '¡ERROR!',
                error.message || 'Ocurrió un error',
                'error'
                );
                _btnCashConciliation.disabled = false;
                _btnCashConciliation.textContent = "Guardar";
            });
        };
    }, true); // <- el `true` activa "capturing" y sí detecta el submit        
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