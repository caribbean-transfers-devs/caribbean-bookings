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
    components.actionTableChart($('.table-chart-general'), 'commissions');
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __title_modal = document.getElementById('filterModalLabel');
const __payment_infos = document.querySelectorAll('.__payment_info');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function () {
        __title_modal.innerHTML = this.dataset.title;
    });
}

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

components.renderCheckboxColumns('dataCommissions', 'columns');
components.setValueSelectpicker();