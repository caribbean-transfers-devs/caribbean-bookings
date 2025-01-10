$(document).ready(function () {    
    getPendingRequest();       
});

function getPendingRequest(){
    $.ajax({
        url: '/operation/pending/get',
        type: 'POST',
        data: {},
        dataType: 'text',
        beforeSend: function() {
            $("#pending-general-container").empty().append('<div class="loaderItem"></div>');
        },
        success: function (data) {
            $("#pending-general-container").empty().append(data);         
        }
    }).fail(function(xhr, status, error) {
        console.log(xhr);
    });
}
