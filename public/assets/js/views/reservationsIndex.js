$(function() {
    $('#reservations_table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        }
    });

    flatpickr("#lookup_date", {
        locale: "es",
        mode: "range",
        dateFormat: "Y-m-d"
    });
});

function Search(){
    var input;
    input = document.getElementById("lookup_date");
    
    location.href = "/reservations?lookup_date=" + input.value;
}

