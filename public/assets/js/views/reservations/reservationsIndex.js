$(function() {
    $('#reservations_table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        },
        paging: false,
        order: []
    });

    const picker = new easepick.create({
        element: "#lookup_date",        
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10,
        plugins: ['RangePlugin'],
    })

});

function Search(){
    $("#btnSearch").text("Buscando....").attr("disabled", true);
    $("#formSearch").submit();
}

