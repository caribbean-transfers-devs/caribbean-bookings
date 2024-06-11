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

if ( document.getElementById('lookup_date2') != null ) {
    const picker2 = new easepick.create({
        element: "#lookup_date2",
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10,
    });
}

if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
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