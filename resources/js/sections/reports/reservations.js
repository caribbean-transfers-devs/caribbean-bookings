let bookings = {
    actionTableChart: function(table){
        const _settings = {};

        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'>>>
                        <'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;
        // _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
        //                 <'table-responsive'tr>
        //                 <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;
        _settings.deferRender = true;
        _settings.responsive = true;
        _settings.order = ['3'];
        _settings.paging = false; // Si no quieres paginación, puedes dejar esto en false
        _settings.oLanguage = {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",             
            "sInfo": "", // Oculta el número de registros mostrados
            "sInfoFiltered": "", // Oculta el texto filtrado
            "sSearch": '', // No muestra el campo de búsqueda
            "sSearchPlaceholder": "",
            "sLengthMenu": "", // Oculta el menú de cantidad de resultados por página
            "oPaginate": { 
                "sPrevious": '', // No muestra el botón de anterior
                "sNext": '' // No muestra el botón de siguiente
            },
        };

        table.DataTable( _settings );
    },    
};

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
    components.actionTable($('.table-rendering'));
    bookings.actionTableChart($('.table-chart'));
}
components.formReset();

// window.addEventListener('scroll', function() {
//     var table = document.getElementById('bookings');
//     var thead = table.querySelector('thead');
//     var offset = table.getBoundingClientRect().top;
    
//     if (window.scrollY > offset) {
//       thead.classList.add('fixed-header');
//     } else {
//       thead.classList.remove('fixed-header');
//     }
// });

// document.querySelector('.table-responsive').addEventListener('scroll', function() {
//     var table = document.getElementById('bookings');
//     var thead = table.querySelector('thead');
//     thead.style.transform = `translateX(-${this.scrollLeft}px)`;
// });

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

components.renderCheckboxColumns('bookings', 'columns');
components.setValueSelectpicker();