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
    components.actionTable($('.table-rendering'), 'fixedheaderPagination');
    components.actionTableChart($('.table-chart-general'), 'general');
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __title_modal = document.getElementById('filterModalLabel');
const __btn_conciliations = document.querySelectorAll('.__btn_conciliation');
const __close_modals = document.querySelectorAll('.__close_modal');
const __type_pay = document.getElementById('type_form_pay');
const __code_pay = document.getElementById('payment_id');
const __is_conciliated = document.getElementById('servicePaymentsConciliationModal');
const __payment_infos = document.querySelectorAll('.__payment_info');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function () {
        __title_modal.innerHTML = this.dataset.title;
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
            const { reservation } = this.dataset;
            $("#addPaymentsModal").modal('show');
            const __loading_container = document.getElementById('loading_container');
            const __form_container = document.getElementById('form_container');

            __loading_container.classList.remove('d-none');
            __loading_container.innerHTML = '<div class="spinner-grow align-self-center">';

            setTimeout(() => {
                __loading_container.classList.add('d-none');
                __form_container.classList.remove('d-none');                
            }, 500);
            $("#reserv_id_pay").val(reservation);          
        });
    });
}

if( __is_conciliated != null ){
    changeIsConciliation(__is_conciliated);
    __is_conciliated.addEventListener('change', function(event){
        event.preventDefault();
        changeIsConciliation(this);
    });

    function changeIsConciliation(DOM){
        const __box_comment = document.querySelector('.box_comment');        
        if( DOM.value == 1 ){
            __box_comment.classList.remove('d-none');
        }else{
            __box_comment.classList.add('d-none');
        }
    }
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

if( __payment_infos.length > 0 ){
    __payment_infos.forEach(__payment_info => {
        __payment_info.addEventListener('click', function(event){
            event.preventDefault();
            const { reservation } = this.dataset;
            $("#reservationPaymentsModal").modal('show');
            const __container = document.getElementById('containerReservationPayments');
            const __footer = document.getElementById('footerReservationPayments');
            $.ajax({
                url: '/reservation/payments/' + reservation,
                type: 'GET',
                async: true,
                beforeSend: function() {
                    __container.innerHTML = '<tr align="center"><td colspan="5"><div class="spinner-grow align-self-center"></tr></td>';
                    __footer.innerHTML = '';
                },
                success: function(resp) {
                    let __tr = "";
                    let __tr_footer = "";
                    let __total = 0;
                    if( resp.length > 0 ){
                        resp.forEach(element => {
                            __tr +=  '<tr>' +
                                        '<td class="text-center">'+ element.total +'</td>' +
                                        '<td class="text-center">'+ ( element.exchange_rate > 1 ? ( element.total / element.exchange_rate ) : element.total ) +'</td>' +
                                        '<td class="text-center">'+ element.currency +'</td>' +
                                        '<td class="text-center">'+ element.exchange_rate +'</td>' +
                                        '<td class="text-center">'+ element.payment_method +'</td>' +
                                        '<td class="text-center">'+ element.reference +'</td>' +
                                    '</tr>';
                            __total += parseFloat(( element.exchange_rate > 1 ? ( element.total / element.exchange_rate ) : element.total ));
                        });

                        __tr_footer = '<tr>' +
                                        '<td class="text-center">0</td>' +
                                        '<td class="text-center">'+ __total +'</td>' +
                                        '<td class="text-center"></td>' +
                                        '<td class="text-center"></td>' +
                                        '<td class="text-center"></td>' +
                                        '<td class="text-center"></td>' +
                                      '</tr>';
                    }else{
                        __tr =  '<tr align="center">' +
                                    '<td colspan="5">no hay pagos</td>' +
                                '</tr>';                         
                    }

                    __container.innerHTML = __tr;
                    __footer.innerHTML = __tr_footer;
                }
            });        
        });        
    });
}

components.renderCheckboxColumns('dataReceivable', 'columns');
components.setValueSelectpicker();