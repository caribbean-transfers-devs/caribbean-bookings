let refunds = {
    reservation_id: 0,
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
    fetchData: async function(url, containerId, params) {
        if (!containerId) return;
    
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(params),
            });
    
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
    
            const data = await response.text();
            containerId.innerHTML = data.trim();
        } catch (error) {
            console.error("Error en la petición:", error);
            container.innerHTML = '<p style="color:red;">Error al cargar datos.</p>'.trim();
        }
    },
    /**
     * 
     */
    getBasicInformationReservation: function() {
        // Elementos HTML
        const data       = document.getElementById("pills-general");

        // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
        data.innerHTML   = this.getLoader().trim();

        // Definir parámetros de la petición
        const _params    = {
            id: this.reservation_id,
        };

        this.fetchData('/action/getBasicInformationReservation', data, _params);
    },
    /**
     * 
     */
    getPhotosReservation: function() {
        // Elementos HTML
        const data       = document.getElementById("media-listing");

        // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
        data.innerHTML   = this.getLoader().trim();

        // Definir parámetros de la petición
        const _params    = {
            id: this.reservation_id,
        };
        this.fetchData('/action/getPhotosReservation', data, _params);
    },
    /**
     * 
     */
    getHistoryReservation: function() {
        // Elementos HTML
        const data       = document.getElementById("pills-history");

        // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
        data.innerHTML   = this.getLoader().trim();

        // Definir parámetros de la petición
        const _params    = {
            id: this.reservation_id,
        };
        this.fetchData('/action/getHistoryReservation', data, _params);
    },
    /**
     * 
     */
    getPaymentsReservation: function() {
        // Elementos HTML
        const data       = document.getElementById("pills-payments");

        // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
        data.innerHTML   = this.getLoader().trim();

        // Definir parámetros de la petición
        const _params    = {
            id: this.reservation_id,
        };
        this.fetchData('/action/getPaymentsReservation', data, _params);
    },
};

//DECLARACION DE VARIABLES
const __close_modals = document.querySelectorAll('.__close_modal');
const __reservation_id = document.getElementById('reservation_id');
const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');

//ACCIONES
const __formPayment = document.getElementById('frm_new_payment'); //FORMULARIO DEL PAGO
const __addPaymentCredit = document.getElementById('btn_new_payment'); //BOTON PARA PODER GUARDAR EL PAGO

//VALIDAMOS DOM
document.addEventListener("DOMContentLoaded", function() {
    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'), 'fixedheaderPaginationCheck');
    }

    components.titleModalFilter();
    components.formReset();
    components.renderCheckboxColumns('dataReceivable', 'columns');
    components.setValueSelectpicker();

    if( __close_modals.length > 0 ){
        __close_modals.forEach(__close_modal => {
            __close_modal.addEventListener('click', function(event){
                event.preventDefault();
                const __loading_container = document.getElementById('loading_container');
                const __form_container = document.getElementById('form_container');

                console.log(__box_comment_conciliation);
                
                __loading_container.classList.add('d-none');
                __form_container.classList.add('d-none');
            });
        });
    }

    document.addEventListener("click", components.debounce(function (event) {
        if( event.target.classList.contains('__btn_conciliation_credit') ){
            event.preventDefault();
            const { reservation } = event.target.dataset;

            $("#addPaymentsModal").modal('show');
            const __loading_container = document.getElementById('loading_container');
            const __form_container = document.getElementById('form_container');

            const __box_link_conciliation = document.querySelector('.box_link_conciliation');
            const __box_comment_conciliation = document.querySelector('.box_comment_conciliation');
            const __is_conciliated = document.getElementById('servicePaymentsConciliationModal');
            const __servicePaymentsCategory = document.getElementById('servicePaymentsCategory');            

            __loading_container.classList.remove('d-none');
            __loading_container.innerHTML = '<div class="spinner-grow align-self-center">';

            __box_link_conciliation.classList.add('d-none');
            __box_comment_conciliation.classList.remove('d-none');
            __is_conciliated.value = 1;
            __servicePaymentsCategory.value = "PAYOUT_CREDIT";

            setTimeout(() => {
                __loading_container.classList.add('d-none');
                __form_container.classList.remove('d-none');                
            }, 500);
            
            __reservation_id.value = reservation;
        }        

        if ( event.target.classList.contains('__show_reservation') ) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            refunds.reservation_id = target.dataset.reservation || 0,
                        
            $("#viewProofsModal").modal('show');
            document.getElementById("pills-general-tab").click();
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo

    
    // if( __is_conciliated != null ){
    //     changeIsConciliation(__is_conciliated);
    //     __is_conciliated.addEventListener('change', function(event){
    //         event.preventDefault();
    //         changeIsConciliation(this);
    //     });
    
    //     function changeIsConciliation(DOM){
    //         const __box_comment = document.querySelector('.box_comment');        
    //         if( DOM.value == 1 ){
    //             __box_comment.classList.remove('d-none');
    //         }else{
    //             __box_comment.classList.add('d-none');
    //         }
    //     }
    // }
    
    $("#servicePaymentsCurrencyModal").on('change', function(){
        let currency = $(this).val();
        let reservation_id = $("#reserv_id_pay").val();
        $.ajax({
            url: '/GetExchange/'+reservation_id+'?currency='+currency,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                $("#servicePaymentsExchangeModal").val(data.exchange_rate);
                $("#operation_pay").val(data.operation);
                $("#btn_new_payment").prop('disabled', false);
            },
        });
    });

    if( __addPaymentCredit ){
        __addPaymentCredit.addEventListener('click', function(event){
            event.preventDefault();
    
            Swal.fire({
                html: '¿Está seguro de aplicar el reembolso a la reservación?',
                icon: 'warning', 
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    let __params = components.serialize(__formPayment,'object');
                    
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se aplica reembolso de la reserva.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    console.log(__params);
                    
    
                    // fetch('/action/addPaymentRefund', {
                    //     method: 'POST',
                    //     headers: {
                    //         'Content-Type': 'application/json',
                    //         'X-CSRF-TOKEN': csrfToken
                    //     },            
                    //     body: JSON.stringify(__params)
                    // })
                    // .then(response => {
                    //     if (!response.ok) {
                    //         return response.json().then(err => { throw err; });
                    //     }
                    //     return response.json();
                    // })
                    // .then(data => {
                    //     if( data.status  == "success" ){                        
                    //         $("#addPaymentsModal").modal('hide');
                    //     }
                    //     Swal.fire({
                    //         icon: data.status,
                    //         html: data.message,
                    //         allowOutsideClick: false,
                    //     }).then(() => {
                    //         location.reload();
                    //     });
                    // })
                    // .catch(error => {
                    //     Swal.fire(
                    //         '¡ERROR!',
                    //         error.message || 'Ocurrió un error',
                    //         'error'
                    //     );
                    // });
                }
            });       
        })
    }
    
    document.getElementById('select-all').addEventListener('change', function () {
        const isChecked = this.checked;
        const checkboxes = document.querySelectorAll('.row-check');
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });
    
    // Lógica para sincronizar el checkbox "select-all" con las filas seleccionadas
    document.querySelector('#dataReceivable tbody').addEventListener('change', function (event) {
        if (event.target.classList.contains('row-check')) {
            const allCheckboxes = document.querySelectorAll('.row-check');
            const checkedCheckboxes = document.querySelectorAll('.row-check:checked');
            const allChecked = allCheckboxes.length === checkedCheckboxes.length;
            document.getElementById('select-all').checked = allChecked;
        }
    });
    
    document.getElementById('processSelected').addEventListener('click', function () {
        let selectedIds = [];
        const checkboxes = document.querySelectorAll('.row-check:checked');
    
        checkboxes.forEach(function (checkbox) {
            selectedIds.push(checkbox.value); // Obtén los IDs de las filas seleccionadas
        });
    
        if (selectedIds.length === 0) {
            components.proccessResponse({
                status: "error",
                message: "No hay reservas seleccionadas.",
                reload: false
            });
            return;
        }
    
        // Aquí puedes procesar los IDs seleccionados
        console.log('IDs seleccionados:', selectedIds);
    
        let __params = {ids: selectedIds};
        components.request_exec_ajax( _LOCAL_URL + "/payments/conciliation", 'POST', __params );
    
        // Ejemplo: enviar los datos mediante fetch
        // fetch('/process-selected', {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //     },
        //     body: JSON.stringify({ ids: selectedIds }),
        // })
        // .then(response => {
        //     if (response.ok) {
        //         return response.json();
        //     }
        //     throw new Error('Error al procesar los datos.');
        // })
        // .then(data => {
        //     alert('Datos procesados correctamente.');
        // })
        // .catch(error => {
        //     alert(error.message);
        // });
    });    
});