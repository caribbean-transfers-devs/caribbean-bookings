$('.table_').DataTable({
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
    },
    paging: false,
    //ordering: false
});

$(function() {

    let current_date = op_current_date;
    let storage_date = localStorage.getItem("op_date");
    if (storage_date !== null) {
        current_date = storage_date;
    }
    $("#lookup_date").val(current_date);
    $("#op_date_label").empty().text(current_date);

    const picker = new easepick.create({
        element: "#lookup_date",        
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10
    })
});

function setStatus(event, type, status, item_id, rez_id){
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
                url: `/operation/managment/update-status`,
                type: 'PUT',
                data: { rez_id:rez_id, item_id:item_id, type:type, status:status },
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
                    })

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

function Search(){
    let date_ = $("#lookup_date").val();
    if(date_ === ""){
        alert("Por favor, selecciona una fecha...");
        return false;
    }
    localStorage.setItem("op_date", date_);
    $("#btnSearch").text("Buscando....").attr("disabled", true);
    $("#filterModal").modal("hide");
    fetchData();
}

function fetchData(){
    let current_date = op_current_date;
    let storage_date = localStorage.getItem("op_date");
    if (storage_date !== null) {
        current_date = storage_date;
    }
    $("#op_date_label").empty().text(current_date);

    if ($.fn.DataTable.isDataTable('.table')) {
        $('.table').DataTable().destroy();
    }
    
    $('.table').DataTable({
        paging: false,
        "ajax": {
            "url": "/operation/managment/fetch",
            "data": { date:current_date },
            "beforeSend": function(xhr, settings) {
                $("#btnSearch").text("Buscar").attr("disabled", false);
            },
            "dataSrc": function (json) {
                if(json.op_data.status === "OPEN"){
                    $("#op_label_current").empty().html(`<i class="align-middle fas fa-fw fa-lock-open"></i> Operación abierta`);
                    $("#op_buttons").empty().html(`
                        <button class="btn btn-pill btn-warning" title="Cierre parcial" onclick="setBlockByType('PARTIAL')"><i class="align-middle fas fa-fw fa-unlock"></i></button>
                        <button class="btn btn-pill btn-danger" title="Cierre total" onclick="setBlockByType('TOTAL')"><i class="align-middle fas fa-fw fa-lock"></i></button>
                    `);
                }
                if(json.op_data.status === "PARTIAL"){
                    $("#op_label_current").empty().html(`<i class="align-middle fas fa-fw fa-unlock"></i> Operación cerrada parcialmente`);
                    $("#op_buttons").empty().html(`<button class="btn btn-pill btn-danger" title="Cierre total" onclick="setBlockByType('TOTAL')"><i class="align-middle fas fa-fw fa-lock"></i></button>`);
                }
                if(json.op_data.status === "TOTAL"){
                    $("#op_label_current").empty().html(`<i class="align-middle fas fa-fw fa-lock"></i> Operación cerrada totalmente`);
                    $("#op_buttons").empty();
                }                
                return json.items;
            },
            "method":'POST',
            "headers": {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        "columns": [
            { "data": "op_pickup" },
            { "data": "site" },
            { "data": "type" },
            { 
                "data": null,
                "render": function(data, type, row){
                    let label = '';
                    switch (data.type) {
                        case 'PENDING':
                            label = 'btn-secondary'
                        break;
                        case 'COMPLETED':
                            label = 'btn-success'
                        break;
                        case 'NOSHOW':
                            label = 'btn-warning'
                        break;
                        case 'CANCELLED':
                            label = 'btn-danger'
                        break;
                        default:
                            label = 'btn-secondary';
                        break;
                    }
                    return `<span class="badge ${label} rounded-pill">${data.type}</span>`;
                }
            },
            { "data": "code" },
            { "data": "client_name" },
            { "data": "service_name" },
            { "data": "passengers" },
            { "data": "op_from_name" },
            { "data": "op_to_name" },
            { "data": "payment_status" },
            { "data": "payment" },
            { "data": "currency" },
            { 
                "data": null,
                "render": function(data, type, row){
                    return `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="align-middle fas fa-fw fa-clipboard"></i> 
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" onclick="">Pendiente</a>
                                <a class="dropdown-item" href="#" onclick="">Completado</a>
                                <a class="dropdown-item" href="#" onclick="">No show</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="">Cancelado</a>                                                                
                            </div>
                        </div>
                    `;
                }
            }
        ]
    });
}

function setBlockByType(type){

    let current_date = op_current_date;
    let storage_date = localStorage.getItem("op_date");
    if (storage_date !== null) {
        current_date = storage_date;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: '/operation/managment/create-lock',
        type: 'POST',
        data: { type:type, date:current_date } ,
        success: function(resp) {
            console.log(resp);
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
    });
}

$(document).ready(function() {
    fetchData();
});