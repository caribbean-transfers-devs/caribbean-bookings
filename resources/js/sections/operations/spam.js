let spam = {
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

        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                        <'table-responsive'tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;                        
        _settings.deferRender = true;
        _settings.responsive = true;
        _settings.buttons =  _buttons;
        _settings.paging = false;
        _settings.oLanguage = {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": components.getTranslation("table.search") + "...",
            "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
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

if ( document.getElementById('lookup_date2') != null ) {
    const picker2 = new easepick.create({
        element: "#lookup_date2",
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
    spam.actionTable($('.table-rendering'));
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __export = document.querySelector('.__btn_export'); //* ===== BUTTON TO EXPORT ===== */
const __title_modal = document.getElementById('filterModalLabel');
const __title_modal2 = document.getElementById('filterModalExportLabel');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function () {
        __title_modal.innerHTML = this.dataset.title;
    });
}

//ACCION PARA EXPORTAR
if( __export != null ){
    __export.addEventListener('click', function () {
        __title_modal2.innerHTML = this.dataset.title;
    });
}

function updateSpam(event, id, type, update){
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
            var button = $('button[data-id="'+ id +'"]');        
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });                
            $.ajax({
                url: `/operation/spam/update-status`,
                type: 'PUT',
                data: { id, type},
                beforeSend: function() {        
                    
                },
                success: function(resp) {
                    button.removeClass().addClass('btn dropdown-toggle ' + update).text(type);
                    Swal.fire({
                        icon: "success",
                        title: '¡Éxito!',
                        html: 'Servicio actualizado con éxito.',
                        showConfirmButton: false,
                        timer: 1500
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

if( document.getElementById('generateExcel') != null ){
    document.getElementById('generateExcel').addEventListener('click', function() {
        let date = document.getElementById('lookup_date2').value;
        let language = document.getElementById('language').value;
        let url = '/operation/spam/exportExcel?date=' + date + '&language=' + language;

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            },
        })
        .then(response => response.blob())
        .then(blob => {
            $("#filterModalExport").modal('hide');
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'spam_'+ date +'.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => console.error('Error:', error));
    });
}