if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'), 'fixedheaderPagination');
}

const __btn_redunds = document.querySelectorAll('.__btn_redund');
const __close_modals = document.querySelectorAll('.__close_modal');
const __reservation_id = document.getElementById('reservation_id');
const __reservation_refund_id = document.getElementById("reservation_refund_id")
const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');

//ACCIONES
const __formPayment = document.getElementById('frm_new_payment');
const __addPaymentRefund = document.getElementById('btn_new_payment');

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
})
