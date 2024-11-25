let confirmation = {
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
        // console.log(buttons);

        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-12 col-lg-8 d-flex flex-column flex-sm-row justify-content-sm-start justify-content-center'l<'dt--pages-count align-self-center'i><'dt-action-buttons align-self-center ms-3 ms-lg-3'B>><'col-12 col-sm-12 col-lg-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                        <'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;        
        _settings.deferRender = true;
        _settings.responsive = true;
        _settings.buttons =  _buttons;
        _settings.order = [];
        _settings.paging = false;

        // _settings.fixedHeader = true; // Activar encabezados fijos
        // _settings.scrollX = true;

        _settings.scrollX = true;
        // _settings.scrollY = '2000px';  // Habilitar scroll vertical
        // _settings.scrollCollapse = true;  // Colapsar el scroll cuando no haya suficientes filas
        _settings.fixedHeader = true;  // Fijar el header  // Habilitar el header fijo

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

        let __table = table.DataTable( _settings );

        // Ajustar encabezado fijo al scroll dentro del contenedor
        // new $.fn.dataTable.FixedHeader(__table, {
        //     header: true, // Habilita encabezado fijo
        //     footer: false // Opcional: deshabilitar footer fijo si no lo necesitas
        // });

        // Corrige el ancho al inicializar
        table.on('init', function () {
            __table.columns.adjust().draw();
        });
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
    });   
}

if( document.querySelector('.table-rendering') != null ){
    confirmation.actionTable($('.table-rendering'));
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __title_modal = document.getElementById('filterModalLabel');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function () {
        __title_modal.innerHTML = this.dataset.title;
    });
}

function updateConfirmation(event, id, type, status, rez_id){
    event.preventDefault();

    swal.fire({
        title: '¿Está seguro de actualizar el estatus?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if(result.isConfirmed == true){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });                
            $.ajax({
                url: `/operation/confirmation/update-status`,
                type: 'PUT',
                data: { id, type, status, rez_id},
                beforeSend: function() {        
                    
                },
                success: function(resp) {
                    Swal.fire({
                        title: '¡Éxito!',
                        icon: 'success',
                        html: 'Servicio actualizado con éxito. Será redirigido en <b></b>',
                        timer: 2500,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                            const b = Swal.getHtmlContainer().querySelector('b')
                            timerInterval = setInterval(() => {
                                b.textContent = (Swal.getTimerLeft() / 1000)
                                    .toFixed(0)
                            }, 100)
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                        }
                    }).then((result) => {
                        location.reload();
                    });
                }
            }).fail(function(xhr, status, error) {
                    console.log(xhr);
                    Swal.fire(
                        '¡ERROR!',
                        xhr.responseJSON.message,
                        'error'
                    );
            });

        }
    });

   
}