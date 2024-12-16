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

components.setValueSelectpicker();
