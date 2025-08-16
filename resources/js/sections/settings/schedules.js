let schedules = {

    calendarFilter: function(selector, options = {}){
        const defaultConfig = {
            mode: "single",
            locale: "es", // Idioma dinámico
            enableTime: false, // Activamos o Desactivamos la selección de hora
            // noCalendar: true, // Ocultamos la selección de fecha
            dateFormat: "Y-m-d", // Formato por defecto
            altInput: true, // Input visual más amigable
            altFormat: "j F Y", // Formato más legible
            allowInput: true,
            // altFormat: "h:i K", // Formato 12h con AM/PM
            defaultDate: "today",
            minDate: "today",
            plugins: [] // Aseguramos que sea un array
        };
    
        let config = { ...defaultConfig, ...options };
    
        const fp = flatpickr(selector, config);
        return fp;
    },        

}
if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}
components.formReset();

const __date            = document.getElementById('lookup_date');
const __date_schedule   = document.getElementById('date_schedule');
const __check_in        = document.getElementById('check_in_time');
const __check_out       = document.getElementById('check_out_time');
const __end_check_out   = document.getElementById('end_check_out_time');

const options_check_in = document.querySelectorAll('.check_in_time');
const options_check_out = document.querySelectorAll('.end_check_out_time');

document.addEventListener("DOMContentLoaded", function() {
    if( __date_schedule ){
        schedules.calendarFilter(__date_schedule, { mode: "single", defaultDate: __date_schedule.value ?? 'today', minDate: null });
    }

    if( __check_in ){
        schedules.calendarFilter(__check_in, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: __check_in.value ?? '00', minDate: null });
    }

    if( __check_out ){
        schedules.calendarFilter(__check_out, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: __check_out.value ?? '00', minDate: null });
    }

    if( __end_check_out ){
        schedules.calendarFilter(__end_check_out, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: __end_check_out.value ?? '', minDate: null });
    }

    if( options_check_out.length ){
        options_check_out.forEach(item => {
           schedules.calendarFilter(item, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: item.value ?? '', minDate: null }); 
        });
    }    

    if( options_check_in.length ){
        options_check_in.forEach(item => {
           schedules.calendarFilter(item, { enableTime: true, noCalendar: true, dateFormat: "H:i", altFormat: "h:i K", defaultDate: item.value ?? '', minDate: null }); 
        });
    }

    document.addEventListener('click', function (event) {
        //PERMITE AGREGAR NUEVOS OPERADORES EN CASO DE QUE NO ESTEN EN LOS HORARIOS
        if (event.target.classList.contains('updateDriver')) {
            event.preventDefault();        

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se crean los nuevos horarios.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/schedules/update/schedules', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    date: __date.value
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Nuevos horarios generados correctamente.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }                    
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

        //NOS PERMITE GENERAR NUEVAMENTE EL REGISTRO DEL OPERADOR SI YA LO CERRE 
        if (event.target.classList.contains('reloadSchedules')) {
            event.preventDefault();        

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se crean los nuevos horarios.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/schedules/reload/schedules', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                // body: JSON.stringify({
                //     code: code,
                //     status: status
                // })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Estatus actualizado con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }                    
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

        //PERMITE AGREGAR LA PREASIGNACIÓN
        if (event.target.classList.contains('creatingSchedules')) {
            event.preventDefault();

            const fechaActual = new Date();

            swal.fire({
                text: '¿Está seguro de cargar los operadores, para asignación de horarios y unidad ?',
                icon: 'warning',
                inputLabel: "Selecciona la fecha que pre-asignara",
                input: "date",
                inputValue: fechaActual,
                inputValidator: (result) => {
                    return !result && "Selecciona un fecha";
                },
                didOpen: () => {
                    const today = (new Date()).toISOString();
                    Swal.getInput().min = today.split("T")[0];
                },
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if(result.isConfirmed == true){
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se crean los operadores, para asignación de horarios y unidad.",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('/set/schedules', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            date: result.value
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: data.status,
                            text: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }                    
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

        if (event.target.classList.contains('statusSchedule')) {
            event.preventDefault();
            
            const target = event.target;
            const { code, status } = target.dataset;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se actualiza el estatus.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/schedules/status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    status: status
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Estatus actualizado con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }                    
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
    })
    
    document.addEventListener('change', function (event) {
        if (event.target.classList.contains('check_in_time')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se actualizan horario.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/schedules/timeCheckIn', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Hora actualizada con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }                    
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

        if (event.target.classList.contains('end_check_out_time')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se actualizan horario.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/schedules/timeCheckout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Hora actualizada con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }
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

        if (event.target.classList.contains('schedule_unit')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se asigna el conductor.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/schedules/set/unit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value,
                    date: __date.value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Estatus de conductor actualizado con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }                    
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

        if (event.target.classList.contains('schedule_status_unit')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se actualizan el estatus de la unidad.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/schedules/unit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Estatus de unidad actualizada con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }                    
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

        if (event.target.classList.contains('schedule_driver')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se asigna el conductor.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/schedules/driver', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Estatus de conductor actualizado con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }                    
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
        
        if (event.target.classList.contains('schedule_status_driver')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se actualizan el estatus del conductor.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/schedules/status/driver', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Estatus de conductor actualizado con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }
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

        if (event.target.classList.contains('schedule_comments')) {
            const target = event.target;
            const code = target.dataset.code;
            const value = target.value;

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se actualizan las observaciones.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/schedules/comments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    code: code,
                    value: value                    
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    text: 'Observaciones actualizado con exito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                    }                    
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
});