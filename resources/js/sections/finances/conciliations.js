let stripe = {
    getLoader: function() {
        return '<span class="container-loader"><i class="fa-solid fa-spinner fa-spin-pulse"></i></span>';
    },
    cleanNumber: function(value) {
        if (!value) return 0;
        return Number(value.toString().replace(/[^0-9.-]+/g, ""));
    },
    formatCurrency: function(value, asNumber = false) {
        let cleanedValue = this.cleanNumber(value);
        return asNumber ? cleanedValue.toFixed(2) : 
            new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(cleanedValue);
    },    
    /**
     * 
     * @param {*} url 
     * @param {*} containerId 
     * @param {*} params 
     * @returns 
     */
    fetchData: async function(url, _params) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(_params),
            });
    
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
    
            return await response.text();
        } catch (error) {
            console.error("Error en la petición:", error);
        }
    },
}

//DECLARACION DE VARIABLES
const __btn_conciliation_paypal = document.querySelector('.__btn_conciliation_paypal');
const __btn_conciliation_stripe = document.querySelector('.__btn_conciliation_stripe');

//VALIDAMOS DOM
document.addEventListener("DOMContentLoaded", function() {
    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'), 'fixedheader');
    }

    components.titleModalFilter();
    components.formReset();
    components.renderCheckboxColumns('dataStripe', 'columns');
    components.setValueSelectpicker();

    document.addEventListener("click", components.debounce(async function (event) {
        const formatDateFromTimestamp = (timestamp) => {
            // Detectar si el timestamp viene en segundos o milisegundos
            if (timestamp.toString().length === 10) {
                timestamp *= 1000; // convertir segundos -> ms
            }

            const date = new Date(timestamp);
            return date.toISOString().split("T")[0]; // "YYYY-MM-DD"
        }
        const formatMoney = (amount) => (new Intl.NumberFormat("es-MX", {
            style: "currency",
            currency: "MXN",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount))

        if (event.target && event.target.id === 'conciliationActionBtn') {
            event.preventDefault();            

            const selectedOption = document.getElementById('conciliationSelect').value;
            
            if (!selectedOption || selectedOption == "null") {
                alert('Por favor ingrese un código para conciliar');
                return;
            }

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se consulta la información del pago.",
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Aquí puedes agregar la lógica para cada tipo de conciliación
            // switch(selectedOption) {
            //     case 'pending':
            //         console.log('Conciliando pendientes de pago');
            //         // Abrir modal específico o ejecutar acción
            //         break;
            //     case 'charged':
            //         console.log('Conciliando cobrados no pagados');
            //         break;
            //     case 'disputed':
            //         console.log('Conciliando disputas/reembolsos');
            //         break;
            // } 
            fetch('/stripe/payouts/' + selectedOption, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }        
                return response.json();
            })
            .then(data => {
                // Swal.close();      
                // console.log(data);
                if( data.data.status == "paid" && data.data.reconciliation_status == "completed" ){
                    Swal.fire({
                        text: "Este pago es una transferencia " + ( data.data.automatic ? "automática" : "manual" ) + " por "+ stripe.formatCurrency(data.data.amount) +" " + data.data.currency.toUpperCase() + " a una cuenta de " + data.data.destination.bank_name +" (últimos 4 dígitos: " + data.data.destination.last4 + "), generada desde fondos de pagos con "+ ( data.data.source_type == "card" ? "tarjeta" : "otros" ) +", esta completado con fecha de llegada al banco: " + data.data.arrival_date,
                        icon: 'success',
                        confirmButtonText: 'Aceptar para conciliar stripe vs bancos (sistema)',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            console.log(JSON.stringify({ reference: selectedOption, version: data.data.version }));
                            
                            Swal.fire({
                                title: "Procesando solicitud...",
                                text: "Por favor, espera mientras se consulta concilia el pago.",
                                allowOutsideClick: false,
                                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            fetch('/stripeInternal/payouts', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ reference: selectedOption, version: data.data.version })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => { throw err; });
                                }        
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    text: data.message,
                                    icon: data.status,
                                    confirmButtonText: 'Aceptar',
                                    allowOutsideClick: false
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
                }else{
                    Swal.fire({
                        text: "Este pago por "+ stripe.formatCurrency(data.data.amount) +" " + data.data.currency.toUpperCase() + " a una cuenta de " + data.data.destination.bank_name +" (últimos 4 dígitos: " + data.data.destination.last4 + "), generada desde fondos de pagos con "+ ( data.data.source_type == "card" ? "tarjeta" : "otros" ) +", se encuentra pendiente con fecha de llegada al banco: " + data.data.arrival_date,
                        icon: 'warning',
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // El usuario hizo click en "Aceptar"
                            Swal.close();
                            document.getElementById('conciliationSelect').value = "";
                        }
                    });
                }
            })
            .catch(error => {
                Swal.fire(
                '¡ERROR!',
                error.message || 'Ocurrió un error',
                'error'
                );
            });                       
        }

        if ( event.target.classList.contains('chargeInformationStripe') ) {
            event.preventDefault();

            // Obtener datos del elemento clickeado
            const { reference } = event.target.dataset;
            const _params       = {
                reference: reference
            };            
            
            $("#chargeStripeModal").modal('show');
            
            // Elementos HTML
            const elements = {
                container: document.getElementById("bodyChargeStripe"),
            };

            // Validar que los elementos existen antes de usarlos
            if (!elements.container) {
                console.error("Error: No se encontraron los elementos del DOM necesarios.");
                return;
            }
                        
            // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
            Object.values(elements).forEach(el => el.innerHTML = stripe.getLoader().trim());

            try {
                const data = await stripe.fetchData("/action/getChargesStripe", _params);                
                
                // Validar que los elementos existen antes de modificar el contenido
                await Promise.all([
                    elements.container.innerHTML = data.trim(),
                ]);
            } catch (error) {
                console.error("Error al obtener datos:", error);
                Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar datos.</p>');
            }
        }

        if (event.target && event.target.id === 'generateStripeAutomaticConciliationData') {
            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se pre-concilian todos los pagos de stripe. Esto puede tardar de 1 - 2 minutos",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/stripeInternal/stripeTemporalSemiAutomaticConciliation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }        
                return response.json();
            })
            .then(data => {
                Swal.close();

                if(data.stripe_payments.length === 0) {
                    Swal.fire({
                        title: "Listo!",
                        text: "Por el momento todos los pagos de stripe han sido conciliados",
                        icon: "success"
                    });
                    return;
                }

                $('#generateStripeAutomaticConciliationDataModal').modal('show');

                let tr = '';
                data.stripe_payments.forEach(stripePayment => {
                    tr += `<tr>`;
                        tr += `<td class="text-left">${stripePayment.code}</td>`;
                        tr += `<td class="text-left">${stripePayment.object.destination.bank_name}</td>`;
                        tr += `<td class="text-right">${formatMoney(stripePayment.object.amount)}</td>`;
                        tr += `<td class="text-left">${formatDateFromTimestamp(stripePayment.object.arrival_date)}</td>`;
                        tr += `<td class="text-left">`;
                            tr += `<button class="btn btn-primary d-flex align-items-center _effect--ripple waves-effect waves-light open-payment-detail" data-payout-id="${stripePayment.code}">`;
                                tr += `<i class="fa-solid fa-eye"></i>`;
                            tr += `</button>`;
                        tr += `</td>`;
                    tr += `</tr>`;
                });
                $("#generateStripeAutomaticConciliationDataModal_tbody").html(tr);

                $('.open-payment-detail').click(function() {
                    let tr = '';
                    data.conciliations.filter(conciliation => conciliation.referencia_deposito_banco === $(this).data('payout-id')).forEach(item => {
                        tr += `<tr>`;
                            tr += `<td class="text-center">${item.reservation_id}</td>`;
                            tr += `<td class="text-center">${item.fecha}</td>`;
                            tr += `<td class="text-center">${item.sitio}</td>`;
                            tr += `<td class="text-center">${item.codigo.split(",").map(c => `<p class="mb-1">${c}</p>`).join('')}</td>`;
                            tr += `<td class="text-center"><button class="btn btn-${item.estatus === 'CONFIRMED' ? 'success' : 'warning'}">${item.estatus}</button></td>`;
                            tr += `<td class="text-center">${item.cliente}</td>`;
                            tr += `<td class="text-center">${item.servicio}</td>`;
                            tr += `<td class="text-center">${item.pax}</td>`;
                            tr += `<td class="text-center">${item.destino}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.importe_venta)}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.importe_cobrado)}</td>`;
                            tr += `<td class="text-center">${item.moneda}</td>`;
                            tr += `<td class="text-center">${item.metodo_pago}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.importe_pesos)}</td>`;
                            tr += `<td class="text-center">${item.id_stripe ? `<a href="javascript:void(0)" class="chargeInformationStripe" data-reference="${item.id_stripe}">${item.id_stripe}</a>` : ''}</td>`;
                            tr += `<td class="text-center">${item.fecha_cobro_stripe ?? 'SIN FECHA DE COBRO'}</td>`;
                            tr += `<td class="text-center" style="color:#fff;background-color:#${item.estatus_cobro_stripe === 'COBRADO' ? '00ab55' : 'e7515a'};">${item.estatus_cobro_stripe}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.total_cobrado_stripe)}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.comision_stripe)}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.total_depositar_stripe)}</td>`;
                            tr += `<td class="text-center">${item.fecha_depositada_banco ?? 'SIN FECHA DE PAGO'}</td>`;
                            tr += `<td class="text-center" style="color:#fff;background-color:#${item.estatus_deposito_banco === 'DEPOSITADO' ? '00ab55' : 'e7515a'};">${item.estatus_deposito_banco}</td>`;
                            tr += `<td class="text-right">${formatMoney(item.total_depositado_banco)}</td>`;
                            tr += `<td class="text-center">${item.referencia_deposito_banco}</td>`;
                            tr += `<td class="text-center">${item.banco}</td>`;
                            tr += `<td class="text-center">${item.tiene_reembolso ? '<button class="btn btn-success">Sí</button>' : ''}</td>`;
                            tr += `<td class="text-center">${item.tiene_disputa ? '<button class="btn btn-success">Sí</button>' : ''}</td>`;
                        tr += `</tr>`;
                    });

                    $("#paymentsFromStripeAutomaticConciliationDataModal_tbody").html(tr);
                    $('#paymentsFromStripeAutomaticConciliationDataModal').modal('show');
                });

                return (new Promise((resolve) => {
                    $('#confirm_conciliation').click(function() {
                        Swal.fire({
                            title: "Procesando solicitud...",
                            text: "Se está conciliando todos los pagos de stripe mostrados anteriormente",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $('#generateStripeAutomaticConciliationDataModal').modal('hide');
                        resolve(data);
                    });
                }))
            })
            .then((dataToSend) => {
                if(!dataToSend) return;
                return fetch('/stripeInternal/stripeTemporalConfirmAutomaticConciliation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(dataToSend)
                })
            })
            .then(response => {
                if(!response) return;
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }        
                return response.json();
            })
            .then(response => {
                if(!response) return;
                Swal.fire({
                    title: "Listo!",
                    text: "Todos los pagos mostrados anteriormente han sido conciliados. Ahora puedes filtrar los pagos para ver los resultados finales",
                    icon: "success"
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

        const btnConciliationStripe = event.target.closest('.btnConciliationStripe');
        if ( btnConciliationStripe ) {
            event.preventDefault();            

            swal.fire({
                title: '¿Esta seguro de conciliar los pagos de Stripe?',
                html: `
                    <div class="w-100 d-flex justify-content-between gap-3">
                        <div class="w-50">
                            <label for="startDate">Fecha Inicio:</label>
                            <input id="startDate" type="date" class="form-control">
                        </div>
                        <div class="w-50">
                            <label for="endDate">Fecha Fin:</label>
                            <input id="endDate" type="date" class="form-control">
                        </div>
                    </div>
                `,            
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                didOpen: () => {
                    // Obtener fechas (ejemplo: primer día del mes actual y fecha actual)
                    const today = new Date();
                    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    
                    // Formatear fechas como YYYY-MM-DD
                    const formatDate = (date) => date.toISOString().split('T')[0];

                    // Inicializar Flatpickr después de que el modal se abre
                    flatpickr("#startDate", {
                        mode: "single",
                        dateFormat: "Y-m-d",
                        enableTime: false,
                        defaultDate: formatDate(firstDayOfMonth), // Fecha inicial: primer día del mes
                        locale: "es" // Opcional: para español
                    });
                    
                    flatpickr("#endDate", {
                        mode: "single",
                        dateFormat: "Y-m-d",
                        enableTime: false,
                        defaultDate: formatDate(today), // Fecha final: hoy
                        locale: "es" // Opcional: para español
                    });
                },                
                preConfirm: () => {
                    const startDate = document.getElementById('startDate').value;
                    const endDate = document.getElementById('endDate').value;
            
                    if (!startDate || !endDate) {
                        Swal.showValidationMessage('Por favor seleccione un rango de fechas válido.');
                        return false;
                    }
            
                    if (new Date(startDate) > new Date(endDate)) {
                        Swal.showValidationMessage('La fecha de inicio no puede ser mayor que la fecha de fin.');
                        return false;
                    }
            
                    return { startDate, endDate };
                }
            }).then((result) => {
                if(result.isConfirmed == true){
                    const { startDate, endDate } = result.value;

                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se concilian los pagos.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                        Swal.showLoading();
                        }
                    });

                    fetch(`${_LOCAL_URL}/bot/conciliation/stripe?startDate=${startDate}&endDate=${endDate}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        },
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
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC                            
                        }).then(() => {
                            window.location.reload(); // Recargar después de cerrar
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
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

