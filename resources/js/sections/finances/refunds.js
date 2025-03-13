if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'), 'fixedheaderPagination');
}

const __btn_redunds = document.querySelectorAll('.__btn_redund');
const __close_modals = document.querySelectorAll('.__close_modal');
const __reservation_id = document.getElementById('reservation_id');
const __reservation_refund_id = document.getElementById("reservation_refund_id")

const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');


document.addEventListener("DOMContentLoaded", function() {
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
    
    if( __btn_redunds.length > 0 ){
        __btn_redunds.forEach(__btn_redund => {
            __btn_redund.addEventListener('click', function(event){
                event.preventDefault();
                const { reservation, refund } = this.dataset;
                $("#addPaymentsModal").modal('show');
                const __loading_container = document.getElementById('loading_container');
                const __form_container = document.getElementById('form_container');
    
                __loading_container.classList.remove('d-none');
                __loading_container.innerHTML = '<div class="spinner-grow align-self-center">';
    
                setTimeout(() => {
                    __loading_container.classList.add('d-none');
                    __form_container.classList.remove('d-none');                
                }, 500);
                __reservation_id.value = reservation;
                __reservation_refund_id.value = refund;
            });
        });
    }

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
    
    $("#btn_new_payment").on('click', function(){
        $("#btn_new_payment").prop('disabled', true);
        let __params = components.serialize(document.getElementById('frm_new_payment'),'object');
        components.request_exec_ajax( _LOCAL_URL + ( __type_pay.value == 1 ? "/payments" : "/payments/" + __code_pay.value ), ( __type_pay.value == 1 ? 'POST' : 'PUT' ), __params );
    });    
})
