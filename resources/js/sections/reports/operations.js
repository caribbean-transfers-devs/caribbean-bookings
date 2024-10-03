let operations = {
    /**
     * ===== Render Table Settings ===== *
     * @param {*} table //tabla a renderizar
    */
    actionTable: function(table){
        let buttons = [];
        const _settings = {},
            _buttons = table.data('button');

        if( _buttons != undefined && _buttons.length > 0 ){        
            _buttons.forEach(_btn => {
                if( _btn.hasOwnProperty('url') ){
                    _btn.action = function(e, dt, node, config){
                        window.location.href = _btn.url;
                    }
                };
                buttons.push(_btn);
            });
        }

        // _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
        //                 <'table-responsive'tr>
        //                 <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;
        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt--pages-count align-self-center'i><'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                        <'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;
        _settings.deferRender = true;
        _settings.responsive = true;
        _settings.buttons =  _buttons;
        _settings.order = [];
        _settings.paging = false;
        _settings.oLanguage = {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",             
            "sInfo": "Mostrando _TOTAL_ registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": components.getTranslation("table.search") + "...",
            "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
            "oPaginate": { 
                "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
            },
        };

        table.DataTable( _settings );
    },
}

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
    operations.actionTable($('.table-rendering'));    
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
                    }else{
                        __tr =  '<tr align="center">' +
                                    '<td colspan="5">no hay pagos</td>' +
                                '</tr>';                         
                    }

                    __tr_footer = '<tr>' +
                                    '<td class="text-center">0</td>' +
                                    '<td class="text-center">'+ __total +'</td>' +
                                    '<td class="text-center"></td>' +
                                    '<td class="text-center"></td>' +
                                    '<td class="text-center"></td>' +
                                    '<td class="text-center"></td>' +
                                '</tr>';

                    __container.innerHTML = __tr;
                    __footer.innerHTML = __tr_footer;
                }
            });        
        });        
    });
}

components.renderCheckboxColumns('bookings', 'columns');
components.setValueSelectpicker();