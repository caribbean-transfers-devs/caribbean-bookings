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
const _actionType           = document.getElementById('actionType');
const _reservationID        = document.getElementById('reservationID');

//VALIDAMOS DOM
document.addEventListener("DOMContentLoaded", function() {
    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'), 'fixedheaderPaginationCheck');
    }

    components.titleModalFilter();
    components.formReset();
    components.renderCheckboxColumns('dataReceivable', 'columns');
    components.setValueSelectpicker();

    let pickerConciliation = flatpickr("#dateConciliation", {
        mode: "single",
        dateFormat: "Y-m-d",
        enableTime: false,
    });
    
    let pickerDeposit = flatpickr("#depositDate", {
        mode: "single",
        dateFormat: "Y-m-d",
        enableTime: false,
    });    

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

    document.addEventListener('change', components.debounce(function (event) {
        if (event.target && event.target.id === 'optionConciliation') {
            const boxMethodPayment              = document.getElementById('boxMethodPayment');
            const boxReferenceInvoiceAgency     = document.getElementById('boxReferenceInvoiceAgency');
            const boxPaymentCurrency            = document.getElementById('boxPaymentCurrency');
            const boxCommentConciliation        = document.getElementById('boxCommentConciliation');
            const boxdateConciliation           = document.getElementById('boxdateConciliation');
            const boxDepositDate                = document.getElementById('boxDepositDate');
            const boxReferencePayment           = document.getElementById('boxReferencePayment');

            const target = event.target;

            if( target.value == "" || target.value == 2 ){
                boxReferenceInvoiceAgency.classList.remove('d-none');
                boxPaymentCurrency.classList.remove('d-none');
                boxdateConciliation.classList.remove('d-none');

                boxMethodPayment.classList.add('d-none');
                boxCommentConciliation.classList.add('d-none');
                boxDepositDate.classList.add('d-none');
                boxReferencePayment.classList.add('d-none');
            }else{
                boxReferenceInvoiceAgency.classList.add('d-none');
                boxPaymentCurrency.classList.add('d-none');
                boxdateConciliation.classList.add('d-none');
                
                boxMethodPayment.classList.remove('d-none');
                boxCommentConciliation.classList.remove('d-none');
                boxDepositDate.classList.remove('d-none');
                boxReferencePayment.classList.remove('d-none');
            }
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo

    document.addEventListener("click", components.debounce(function (event) {
        if (event.target && event.target.id === 'checkboxSelected') {
            event.preventDefault();
            
            _actionType.value = "multiple";
        }
        
        if( event.target.classList.contains('__btn_conciliation_credit') ){
            event.preventDefault();
            const { reservation, code }  = event.target.dataset;            
                    
            _actionType.value = "single";
            if( code ){
                _reservationID.value = code;
            }else{
                _reservationID.value = reservation;
            }            
            $("#ConciliationReservesCreditModal").modal('show');
        }        

        if ( event.target.classList.contains('__show_reservation') ) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            refunds.reservation_id = target.dataset.reservation || 0,
                        
            $("#viewProofsModal").modal('show');
            document.getElementById("pills-general-tab").click();
        }

        if ( event.target.classList.contains('__close_modal') ) {
            event.preventDefault();

            const _processSelected = document.getElementById('processSelected');
            _processSelected.reset();
            _actionType.value = "";
            _reservationID.value = "";
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo

    document.addEventListener('submit', function(event) {
        if (event.target && event.target.id === 'processSelected') {
            event.preventDefault();    

            let selectedIds        = [];
            const checkboxes       = document.querySelectorAll('.row-check:checked');
        
            if( _actionType.value == "multiple" ){
                checkboxes.forEach(function (checkbox) {
                    selectedIds.push(checkbox.value); // Obtén los IDs de las filas seleccionadas
                });
            }else{
                selectedIds.push(_reservationID.value); // Obtén los IDs de las filas seleccionadas
            }
        
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

            const _processSelected = document.getElementById('processSelected');
            const _formData     = new FormData(_processSelected);
            _formData.append('codes', selectedIds);
        
            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se realiza concilian los pagos.",
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                didOpen: () => {
                    Swal.showLoading();
                }
            });
    
            fetch('/action/addCreditPayment', {
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
                console.log(data);
                if (data.status == "success") {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        confirmButtonText: 'Aceptar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        location.reload();
                    });
                }else{
                    Swal.fire(
                    '¡ERROR!',
                    data.message || 'Ocurrió un error',
                    'error'
                    );                    
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
    }, true); // <- el `true` activa "capturing" y sí detecta el submit
    
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
});