//VALIDAMOS DOM
document.addEventListener("DOMContentLoaded", function() {
    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'), 'fixedheader');        
    }
        
    components.titleModalFilter();
    components.formReset();

    filters.calendarFilter(__lookup_date, { mode: "single", defaultDate: __lookup_date.value ?? 'today', minDate: null });

    document.addEventListener("click", components.debounce(function (event) {
        //ACTUALIZA LA CONFIRMACIÓN DEL SERVICIO
        if (event.target.classList.contains('confirmService')) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            const _params    = {
                item_id: target.dataset.item || "",
                service: target.dataset.service || "",
                status: target.dataset.status || "",
                type: target.dataset.type || "",
            };
            
            Swal.fire({
                html: '¿Está seguro de actualizar el estatus de confirmación del servicio?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se actualiza el estatus de confirmación.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
    
                    fetch('/action/confirmService', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(_params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
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
                    });
                }
            });            
        }    
    }, 300)); // 300ms de espera antes de ejecutar de nuevo
});