let stripe = {
    getLoader: function() {
        return '<span class="container-loader"><i class="fa-solid fa-spinner fa-spin-pulse"></i></span>';
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
        if (event.target && event.target.id === 'conciliationActionBtn') {
            event.preventDefault();
            console.log("hola");
            

            const selectedOption = document.getElementById('conciliationSelect').value;
            console.log(selectedOption);
            
            if (!selectedOption || selectedOption == "null") {
                alert('Por favor seleccione una opción para conciliar');
                return;
            }
            
            // Aquí puedes agregar la lógica para cada tipo de conciliación
            switch(selectedOption) {
                case 'pending':
                    console.log('Conciliando pendientes de pago');
                    // Abrir modal específico o ejecutar acción
                    break;
                case 'charged':
                    console.log('Conciliando cobrados no pagados');
                    break;
                case 'disputed':
                    console.log('Conciliando disputas/reembolsos');
                    break;
            }            
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
                            throw new Error('Error en la respuesta del servidor');
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Ocurrió un problema al procesar la solicitud',
                        });
                    });
                }
            });
        }        
    }, 300)); // 300ms de espera antes de ejecutar de nuevo
});

// //PAYPAL
// if( __btn_conciliation_paypal != null ){
//     __btn_conciliation_paypal.addEventListener('click', function(event){
//         event.preventDefault();
//         swal.fire({
//             title: '¿Esta seguro de conciliar los pagos de PayPal?',
//             html: `
//                 <div class="w-100 d-flex justify-content-between gap-3">
//                     <div class="w-50">
//                         <label for="startDate">Fecha Inicio:</label>
//                         <input id="startDate" type="date" class="form-control">
//                     </div>
//                     <div class="w-50">
//                         <label for="endDate">Fecha Fin:</label>
//                         <input id="endDate" type="date" class="form-control">
//                     </div>
//                 </div>
//             `,            
//             icon: 'info',
//             showCancelButton: true,
//             confirmButtonText: 'Aceptar',
//             cancelButtonText: 'Cancelar',
//             preConfirm: () => {
//                 const startDate = document.getElementById('startDate').value;
//                 const endDate = document.getElementById('endDate').value;
        
//                 if (!startDate || !endDate) {
//                     Swal.showValidationMessage('Por favor seleccione un rango de fechas válido.');
//                     return false;
//                 }
        
//                 if (new Date(startDate) > new Date(endDate)) {
//                     Swal.showValidationMessage('La fecha de inicio no puede ser mayor que la fecha de fin.');
//                     return false;
//                 }
        
//                 return { startDate, endDate };
//             }
//         }).then((result) => {
//             if(result.isConfirmed == true){
//                 const { startDate, endDate } = result.value;
//                 $.ajax({
//                     type: "GET",
//                     url: _LOCAL_URL + "/bot/conciliation/paypal",
//                     data: { startDate, endDate }, // Envío de fechas
//                     dataType: "json",
//                     beforeSend: function(){
//                         components.loadScreen();
//                     },
//                     success: function(response) {
//                         // Manejar la respuesta exitosa del servidor
//                         Swal.fire({
//                             icon: response.status,
//                             text: response.message,
//                             showConfirmButton: false,
//                             timer: 1500,
//                         });
//                     }
//                 });
//             }
//         });
//     });
// }

