document.addEventListener("DOMContentLoaded", function() {
    // Intervalo en milisegundos (2.5 minutos = 150,000 ms)
    const intervalo = 150000;
    // Configuración
    const config = {
        intervaloReload: 150000, // 2.5 minutos en milisegundos
        tiempoEsperaRespuesta: 30000, // 30 segundos para esperar respuesta
        mostrarNotificacionAntes: 60000 // 1 minuto antes de recargar
    };

    // Variables de estado
    let intervaloPrincipal;
    let temporizadorConfirmacion;
    let recargaPendiente = false;

    // Elementos de la interfaz
    let notificacion;
    let contadorElement;

    function inicializarAutoRecarga() {
        console.log(`Iniciando auto-recarga cada ${config.intervaloReload/60000} minutos`);
        
        intervaloPrincipal = setInterval(() => {
            solicitarConfirmacionRecarga();
        }, config.intervaloReload);
    }

    function crearNotificacion() {
        // Crear elementos HTML para la notificación
        notificacion = document.createElement('div');
        notificacion.style.position = 'fixed';
        notificacion.style.bottom = '20px';
        notificacion.style.right = '20px';
        notificacion.style.backgroundColor = '#f8f9fa';
        notificacion.style.border = '1px solid #dee2e6';
        notificacion.style.borderRadius = '5px';
        notificacion.style.padding = '15px';
        notificacion.style.boxShadow = '0 0 10px rgba(0,0,0,0.1)';
        notificacion.style.zIndex = '1000';
        notificacion.style.maxWidth = '300px';
        
        contadorElement = document.createElement('div');
        contadorElement.style.marginBottom = '10px';
        contadorElement.style.fontWeight = 'bold';
        
        const mensaje = document.createElement('p');
        mensaje.textContent = 'La página se recargará automáticamente para mantener los datos actualizados. ¿Desea continuar?';
        mensaje.style.marginBottom = '15px';
        
        const btnAceptar = document.createElement('button');
        btnAceptar.textContent = 'Aceptar';
        btnAceptar.style.marginRight = '10px';
        btnAceptar.style.padding = '5px 10px';
        btnAceptar.style.backgroundColor = '#28a745';
        btnAceptar.style.color = 'white';
        btnAceptar.style.border = 'none';
        btnAceptar.style.borderRadius = '3px';
        btnAceptar.style.cursor = 'pointer';
        
        const btnCancelar = document.createElement('button');
        btnCancelar.textContent = 'Cancelar';
        btnCancelar.style.padding = '5px 10px';
        btnCancelar.style.backgroundColor = '#dc3545';
        btnCancelar.style.color = 'white';
        btnCancelar.style.border = 'none';
        btnCancelar.style.borderRadius = '3px';
        btnCancelar.style.cursor = 'pointer';
        
        // Agregar elementos al contenedor
        notificacion.appendChild(contadorElement);
        notificacion.appendChild(mensaje);
        notificacion.appendChild(btnAceptar);
        notificacion.appendChild(btnCancelar);
        
        // Agregar al documento
        document.body.appendChild(notificacion);
        
        // Event listeners
        btnAceptar.addEventListener('click', () => {
            clearTimeout(temporizadorConfirmacion);
            recargarPagina();
        });
        
        btnCancelar.addEventListener('click', () => {
            clearTimeout(temporizadorConfirmacion);
            ocultarNotificacion();
            recargaPendiente = false;
        });
    }

    function actualizarContador(segundos) {
        if (contadorElement) {
            contadorElement.textContent = `La página se recargará en ${segundos} segundos...`;
        }
    }
    
    function ocultarNotificacion() {
        if (notificacion && notificacion.parentNode) {
            notificacion.parentNode.removeChild(notificacion);
        }
    }
    
    function solicitarConfirmacionRecarga() {
        if (recargaPendiente) return;
        
        recargaPendiente = true;
        crearNotificacion();
        
        let segundosRestantes = config.tiempoEsperaRespuesta / 1000;
        actualizarContador(segundosRestantes);
        
        temporizadorConfirmacion = setInterval(() => {
            segundosRestantes -= 1;
            actualizarContador(segundosRestantes);
            
            if (segundosRestantes <= 0) {
                clearInterval(temporizadorConfirmacion);
                recargarPagina();
            }
        }, 1000);
    }
    
    function recargarPagina() {
        console.log('Recargando página...', new Date().toLocaleTimeString());
        ocultarNotificacion();
        window.location.reload();
    }    
    
    if ( document.getElementById('lookup_date') != null ) {
        const picker = new easepick.create({
            element: "#lookup_date",
            css: [
                'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
            ],
            zIndex: 10,
        });
    }

    components.actionTable($('.table-bookings'), 'fixedheader');
    components.actionTable($('.table-arrivals'), 'fixedheader');
    components.actionTable($('.table-departures'), 'fixedheader');    

    components.titleModalFilter();
    components.formReset();
    components.renderCheckboxColumns('dataBookings', 'columns');
    components.setValueSelectpicker();

    // Filtro rápido: Canceladas / Duplicadas
    const btnFilterCD = document.getElementById('btn_filter_cancelled_duplicated');
    if (btnFilterCD) {
        btnFilterCD.addEventListener('click', function () {
            $('#reservation_status').selectpicker('val', ['CANCELLED', 'DUPLICATED']);
            document.getElementById('formSearch').submit();
        });
    }

    // Selección múltiple para eliminación de reservas
    const selectAllBookings = document.getElementById('select-all-bookings');
    const btnDeleteSelected = document.getElementById('btn_delete_selected');

    function updateDeleteButton() {
        if (!btnDeleteSelected) return;
        const checked = document.querySelectorAll('.row-check-booking:checked');
        btnDeleteSelected.classList.toggle('d-none', checked.length === 0);
    }

    if (selectAllBookings) {
        selectAllBookings.addEventListener('change', function () {
            document.querySelectorAll('.row-check-booking').forEach(cb => {
                cb.checked = this.checked;
            });
            updateDeleteButton();
        });
    }

    const bookingsTbody = document.querySelector('#dataBookings tbody');
    if (bookingsTbody) {
        bookingsTbody.addEventListener('change', function (event) {
            if (event.target.classList.contains('row-check-booking')) {
                if (selectAllBookings) {
                    const all = document.querySelectorAll('.row-check-booking');
                    const checked = document.querySelectorAll('.row-check-booking:checked');
                    selectAllBookings.checked = all.length === checked.length;
                }
                updateDeleteButton();
            }
        });
    }

    // Modal de eliminación múltiple
    const confirmDeleteInput  = document.getElementById('confirm-delete-input');
    const confirmBulkDeleteBtn = document.getElementById('confirm-bulk-delete-btn');
    const bulkDeleteModal     = document.getElementById('bulkDeleteReservationsModal')
                                    ? new bootstrap.Modal(document.getElementById('bulkDeleteReservationsModal'))
                                    : null;

    if (btnDeleteSelected && bulkDeleteModal) {
        btnDeleteSelected.addEventListener('click', function () {
            const checkedBoxes = document.querySelectorAll('.row-check-booking:checked');
            const tbody = document.getElementById('bulk-delete-table-body');
            const countEl = document.getElementById('bulk-delete-count');

            tbody.innerHTML = '';

            checkedBoxes.forEach(cb => {
                const row = cb.closest('tr');
                const tds = row.querySelectorAll('td');

                // Índices: [0]=checkbox [1]=ID [4]=Código [7]=Fecha [12]=Estatus [13]=Cliente [19]=Total [21]=Moneda
                const id       = tds[1]  ? tds[1].textContent.trim()  : '';
                const codigoParts = tds[4] ? Array.from(tds[4].querySelectorAll('p')).map(p => p.textContent.trim()) : [];
                const codigo = codigoParts.length > 0 ? codigoParts.join('<br>') : (tds[4] ? tds[4].textContent.trim() : '');
                const cliente  = tds[13] ? tds[13].textContent.trim() : '';
                const fecha    = tds[7]  ? tds[7].textContent.trim()  : '';
                const estatus  = tds[12] ? tds[12].querySelector('button')?.textContent.trim() ?? tds[12].textContent.trim() : '';
                const total    = tds[19] ? tds[19].textContent.trim() : '';
                const moneda   = tds[21] ? tds[21].textContent.trim() : '';

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">${id}</td>
                    <td class="text-center">${codigo}</td>
                    <td class="text-center">${cliente}</td>
                    <td class="text-center">${fecha}</td>
                    <td class="text-center">${estatus}</td>
                    <td class="text-center">${total} ${moneda}</td>
                `;
                tbody.appendChild(tr);
            });

            countEl.textContent = checkedBoxes.length;

            if (confirmDeleteInput) {
                confirmDeleteInput.value = '';
            }
            if (confirmBulkDeleteBtn) {
                confirmBulkDeleteBtn.disabled = true;
            }

            bulkDeleteModal.show();
        });
    }

    if (confirmDeleteInput && confirmBulkDeleteBtn) {
        confirmDeleteInput.addEventListener('input', function () {
            confirmBulkDeleteBtn.disabled = this.value.trim() !== 'eliminar';
        });
    }

    if (confirmBulkDeleteBtn) {
        confirmBulkDeleteBtn.addEventListener('click', async function () {
            const checkedBoxes = document.querySelectorAll('.row-check-booking:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);

            if (ids.length === 0) return;

            confirmBulkDeleteBtn.disabled = true;
            confirmBulkDeleteBtn.textContent = 'Procesando...';

            try {
                let res = await fetch('/reservations/delete-reservations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ ids }),
                });
                res = await res.json();

                bulkDeleteModal.hide();

                let html = '';

                if (res.deleted && res.deleted.length > 0) {
                    html += `<p class="text-success fw-bold">${res.deleted.length} reserva(s) eliminada(s) correctamente:</p>`;
                    html += `<p>${res.deleted.map(id => '#' + id).join(', ')}</p>`;
                }

                if (res.failed && res.failed.length > 0) {
                    html += `<p class="text-danger fw-bold mt-2">❌ ${res.failed.length} reserva(s) no pudieron eliminarse:</p>`;
                    html += '<table class="table table-sm table-bordered mt-1"><thead><tr><th>ID</th><th>Motivo</th></tr></thead><tbody>';
                    res.failed.forEach(f => {
                        html += `<tr><td>#${f.id}</td><td>${f.reason}</td></tr>`;
                    });
                    html += '</tbody></table>';
                }

                Swal.fire({
                    icon: res.status,
                    title: res.deleted?.length > 0 ? 'Proceso completado' : 'Sin cambios',
                    html: html,
                    allowOutsideClick: false,
                }).then(() => {
                    if (res.deleted && res.deleted.length > 0) location.reload();
                });

            } catch (e) {
                bulkDeleteModal.hide();
                Swal.fire('¡ERROR!', e.message || 'Ocurrió un error', 'error');
            }
        });
    }
    
    // Iniciar cuando el DOM esté listo
    inicializarAutoRecarga();
    
    // Opcional: Detener la recarga automática si es necesario
    window.detenerAutoRecarga = function() {
        clearInterval(intervaloPrincipal);
        clearTimeout(temporizadorConfirmacion);
        ocultarNotificacion();
        recargaPendiente = false;
        console.log('Auto-recarga detenida');
    };

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

    document.querySelectorAll('.delete-reservation').forEach(element => {
        element.addEventListener("click", (event) => {
        
            const reservation_id = event.currentTarget.getAttribute('data-id');

            Swal.fire({
                title: 'Escribe "eliminar", para borrar la reservación',
                input: "text",
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: true,
                confirmButtonText: "Eliminar",
                showLoaderOnConfirm: true,
                preConfirm: async (input) => {
                    if(input !== 'eliminar') {
                        Swal.showValidationMessage('Escribe "eliminar", para borrar la reservación');
                        return false;
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then(async (result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: "Procesando solicitud...",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    let res = await fetch(`/reservations/delete-reservation/${reservation_id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                    })
                    res = await res.json();
                    
                    Swal.fire({
                        icon: res.status,
                        html: res.message,
                        allowOutsideClick: false,
                    }).then(() => {
                        if(res.status === 'success') location.reload();
                    });
                } catch(e) {
                    Swal.fire(
                        '¡ERROR!',
                        e.message || 'Ocurrió un error',
                        'error'
                    );
                }
            });

        });
    });
});