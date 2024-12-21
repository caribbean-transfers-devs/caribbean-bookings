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
    components.actionTable($('.table-rendering'), 'fixedheader');
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __btn_conciliation_paypal = document.querySelector('.__btn_conciliation_paypal');
const __title_modal = document.getElementById('filterModalLabel');
const __btn_conciliations = document.querySelectorAll('.__btn_conciliation');
const __close_modals = document.querySelectorAll('.__close_modal');
const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function () {
        __title_modal.innerHTML = this.dataset.title;
    });
}

if( __btn_conciliation_paypal != null ){
    __btn_conciliation_paypal.addEventListener('click', function(event){
        event.preventDefault();
        swal.fire({
            title: '¿Esta seguro de conciliar los pagos de PayPal?',
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
                $.ajax({
                    type: "GET",
                    url: _LOCAL_URL + "/bot/conciliation/paypal",
                    data: { startDate, endDate }, // Envío de fechas
                    dataType: "json",
                    beforeSend: function(){
                        components.loadScreen();
                    },
                    success: function(response) {
                        // Manejar la respuesta exitosa del servidor
                        Swal.fire({
                            icon: response.status,
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                });
            }
        });
    });
}

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

if( __btn_conciliations.length > 0 ){
    __btn_conciliations.forEach(__btn_conciliation => {
        __btn_conciliation.addEventListener('click', function(event){
            event.preventDefault();
            const { reservation, payment, currency } = this.dataset;
            $("#addPaymentsModal").modal('show');
            const __loading_container = document.getElementById('loading_container');
            const __form_container = document.getElementById('form_container');
            $.ajax({
                url: '/payments/' + payment,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    __loading_container.classList.remove('d-none');
                    __loading_container.innerHTML = '<div class="spinner-grow align-self-center">';
                },                
                success: function (data) {
                    __loading_container.classList.add('d-none');
                    __form_container.classList.remove('d-none');
                    $("#type_form_pay").val(2);
                    $("#reserv_id_pay").val(reservation);
                    $("#payment_id").val(payment);                
                    $("#servicePaymentsTypeModal").val(data.payment_method);
                    $("#servicePaymentsDescriptionModal").val(data.reference);
                    $("#servicePaymentsTotalModal").val(data.total);
                    $("#servicePaymentsCurrencyModal").val(data.currency);
                    $("#servicePaymentsExchangeModal").val(data.exchange_rate);
                    $("#servicePaymentsConciliationModal").val(data.is_conciliated);
                    $("#servicePaymentsMessageConciliationModal").val(data.conciliation_comment);
                    $("#btn_new_payment").prop('disabled', false);
                },
            });            
        });
    });
}

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

$("#btn_new_payment").on('click', function(){
    $("#btn_new_payment").prop('disabled', true);
    let __params = components.serialize(document.getElementById('frm_new_payment'),'object');
    components.request_exec_ajax( _LOCAL_URL + ( __type_pay.value == 1 ? "/payments" : "/payments/" + __code_pay.value ), ( __type_pay.value == 1 ? 'POST' : 'PUT' ), __params );
});

components.renderCheckboxColumns('dataConciliation', 'columns');
components.setValueSelectpicker();
