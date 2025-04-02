document.addEventListener("DOMContentLoaded", function() {
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
        components.actionTable($('.table-rendering'), 'fixedheader');
    }
    components.formReset();

    components.titleModalFilter();
    components.renderCheckboxColumns('dataBookings', 'columns');
    components.setValueSelectpicker();    
});