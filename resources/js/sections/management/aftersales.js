$(document).ready(function () {
    const selectElement = $('#spam-selec-date');
          selectElement.find('option:eq(1)').prop('selected', true);

        getSpamByDate({ target: selectElement[0] });        
});

function getSpamByDate(event) {
    getSpamRequest(event.target.value, 'PENDING');
}

function getSpamRequest(date, status, partial = 0){
    $.ajax({
        url: '/management/spam/get',
        type: 'POST',
        data: { date, status, partial},
        dataType: 'text',
        beforeSend: function() {
            if(partial == 0){
                $("#spam-general-container").empty().append('<div class="loaderItem"></div>');
            }else{
                $("#spam-items-content").empty().append('<div class="loaderItem"></div>');
            }
        },
        success: function (data) {
            if(partial == 0){
                $("#spam-general-container").empty().append(data);
            }else{
                $("#spam-items-content").empty().append(data);
            }            
        }
    }).fail(function(xhr, status, error) {
        console.log(xhr);
    });
}

window.getSpamByStatus = function(status, date){
    getSpamRequest(date, status, 1);
}

window.spamOnenModal = function(event, ritID, rezID, status = 'PENDING'){
    $("#viewSpamDetailModal").modal("show");
    getSpamClientDetails(ritID);
    getSpamHistory(rezID);
    $("#spam_rez_id").val(rezID);
    $("#spam_item_id").val(ritID);

    resetSpamForm();
    selectSpamTabs();

    $("#spamRememberDisplay").addClass("follow_up_hide");
    $('#forSpamNewStatus').val(status);
}

function getSpamClientDetails(id){
    $.ajax({
        url: '/management/spam/get/basic-information',
        type: 'POST',
        data: { id},
        dataType: 'text',
        beforeSend: function() {
            $("#spamResumeInformationContainer").empty().append('<div class="loaderItem"></div>');
        },
        success: function (data) {
            $("#spamResumeInformationContainer").empty().append(data);
        }
    }).fail(function(xhr, status, error) {
        console.log(xhr);
    });
}

function getSpamHistory(id){
    $.ajax({
        url: '/management/spam/history/get',
        type: 'POST',
        data: { id},
        dataType: 'text',
        beforeSend: function() {
            $("#spamHistory").empty().append('<div class="loaderItem"></div>');
        },
        success: function (data) {
            $("#spamHistory").empty().append(data);
        }
    }).fail(function(xhr, status, error) {
        console.log(xhr);
    });
}

window.saveSpamComment = function(event){
    event.preventDefault();
    let frm_data = $("#formSpamAddComment").serializeArray();
    $.ajax({
        url: '/management/spam/history/add',
        type: 'POST',
        data: frm_data,
        beforeSend: function() {        
            $("#btnSaveSpamComment").prop('disabled', true).text("Agregando seguimiento...");
        },
        success: function(resp) {
            Swal.fire({
                icon: "success",
                title: '¡Éxito!',
                html: 'Seguimiento agregado...',
                showConfirmButton: false,
                timer: 1500
            }).then((result) => {
                $("#btnSaveSpamComment").prop('disabled', false).text("Guardar");
                $("#viewSpamDetailModal").modal("hide");
                const selectElement = $('#spam-selec-date');
                    selectElement.find('option:eq(1)').prop('selected', true);
                    getSpamByDate({ target: selectElement[0] });                
            });                  
        }
    }).fail(function(xhr, status, error) {
        $("#btnSaveSpamComment").prop('disabled', false).text("Guardar");

        console.log(xhr);
        Swal.fire({
            title: '¡Error!',
            text: `[${xhr.responseJSON.error.code}]: ${xhr.responseJSON.error.message.join(' ')}`,
            icon: 'error',
            showConfirmButton: false,
            timer: 2500
        })        
    });
}

function resetSpamForm() {
    $('#formSpamAddComment')[0].reset();
    $('#btnSaveSpamComment').prop('disabled', false);
}

function selectSpamTabs(){
    $('#pills-tab .nav-link').removeClass('active').attr('aria-selected', 'false');
    $('#spamResumeInformationContainer-tab').addClass('active').attr('aria-selected', 'true');

    $('.tab-content .tab-pane').removeClass('show active');
    $('#spamResumeInformationContainer').addClass('show active');
}

$('#spamRememberCheck').on('change', function () {
    if ($(this).prop('checked')) {
        $("#spamRememberDisplay").removeClass("follow_up_hide");
    } else {
        $("#spamRememberDisplay").addClass("follow_up_hide");
    }
});

$(document).ready(function () {
    getPendingRequest();
    if( document.getElementById('quotation-general-container') ){
        getQuotationRequest();
    }    
});

function getPendingRequest(){
    $.ajax({
        url: '/management/pending/get',
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

function getQuotationRequest(){
    $.ajax({
        url: '/management/quotation/get',
        type: 'POST',
        data: { date: document.getElementById('lookup_date').value },
        dataType: 'text',
        beforeSend: function() {
            $("#quotation-general-container").empty().append('<div class="loaderItem"></div>');
        },
        success: function (data) {
            $("#quotation-general-container").empty().append(data);
        }
    }).fail(function(xhr, status, error) {
        console.log(xhr);
    });
}