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
const __reservation_id2 = document.getElementById('reservation_id2');
const __reservation_refund_id = document.getElementById("reservation_refund_id");
const __reservation_refund_id2 = document.getElementById("reservation_refund_id2");
const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');

//ACCIONES
const __formPayment = document.getElementById('frm_new_payment'); //FORMULARIO DEL PAGO
const __formRefundNotApplicable = document.getElementById('formRefundNotApplicable'); //FORMULARIO DEL DECLINACION REEMBOLSO
const __addPaymentRefund = document.getElementById('btn_new_payment'); //BOTON PARA PODER GUARDAR EL PAGO
const __refundNotApplicable = document.getElementById('refundNotApplicable'); //BOTON PARA DECLINAR UN REEMBOLSO

//VALIDAMOS DOM
document.addEventListener("DOMContentLoaded", function() {
    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'), 'fixedheaderPagination');
    }

    components.titleModalFilter();
    components.formReset();
    components.renderCheckboxColumns('dataRefunds', 'columns');
    components.setValueSelectpicker();    

    if( __close_modals.length > 0 ){
        __close_modals.forEach(__close_modal => {
            __close_modal.addEventListener('click', function(event){
                event.preventDefault();
                const __loading_container = document.getElementById('loading_container');
                const __form_container = document.getElementById('form_container');
                __loading_container.classList.add('d-none');
                __form_container.classList.add('d-none');
            });
        });
    }
    
    document.addEventListener("click", components.debounce(function (event) {
        if( event.target.classList.contains('__btn_refund') ){
            event.preventDefault();
            const { reservation, refund, type } = event.target.dataset;

            if( type == "APPLY_REFUND" ){
                $("#addPaymentsModal").modal('show');
                const __loading_container = document.getElementById('loading_container');
                const __form_container = document.getElementById('form_container');
                const __servicePaymentsCategory = document.getElementById('servicePaymentsCategory');
    
                __loading_container.classList.remove('d-none');
                __loading_container.innerHTML = '<div class="spinner-grow align-self-center">';
                __servicePaymentsCategory.value = "REFUND";
    
                setTimeout(() => {
                    __loading_container.classList.add('d-none');
                    __form_container.classList.remove('d-none');                
                }, 500);
            }else{
                $("#refundNotApplicableModal").modal('show');
            }

            __reservation_id.value = reservation;
            __reservation_refund_id.value = refund;
            __reservation_id2.value = reservation;
            __reservation_refund_id2.value = refund;    
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

    $("#servicePaymentsCurrencyModal").on('change', function(){
        let currency = $(this).val();
        let reservation_id = $("#reservation_id").val();
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

    if( __addPaymentRefund ){
        __addPaymentRefund.addEventListener('click', function(event){
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
    
                    fetch('/action/addPaymentRefund', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(__params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if( data.status  == "success" ){                        
                            $("#addPaymentsModal").modal('hide');
                        }
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
        })
    }

    if( __refundNotApplicable ){
        __refundNotApplicable.addEventListener('click', function(event){
            event.preventDefault();
    
            Swal.fire({
                html: '¿Está seguro de declinar el reembolso a la reservación?',
                icon: 'warning', 
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    let __params = components.serialize(__formRefundNotApplicable,'object');
                    
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se declina el reembolso de la reserva.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
    
                    fetch('/action/refundNotApplicable', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(__params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if( data.status  == "success" ){                        
                            $("#refundNotApplicableModal").modal('hide');
                        }
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
        })
    }    
})
