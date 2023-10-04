function initialize(div) {
  var input = document.getElementById(div);
  var autocomplete = new google.maps.places.Autocomplete(input);

  autocomplete.addListener('place_changed', function() {
      var place = autocomplete.getPlace();
      if(div == "bookingFromForm"){        
        var fromLat = document.getElementsByName("from_lat");
            fromLat[0].value = place.geometry.location.lat();

        var fromLng = document.getElementsByName("from_lng");
            fromLng[0].value = place.geometry.location.lng();
      }
      if(div == "bookingToForm"){
        var toLat = document.getElementsByName("to_lat");
            toLat[0].value = place.geometry.location.lat();

        var toLng = document.getElementsByName("to_lng");
            toLng[0].value = place.geometry.location.lng();
      }
  });
}

google.maps.event.addDomListener(window, 'load', initialize('bookingFromForm') );
google.maps.event.addDomListener(window, 'load', initialize('bookingToForm') );

document.addEventListener('DOMContentLoaded', function() {
  const fechaInput = document.getElementById('bookingPickupForm');
  const rangeCheckbox = document.getElementById('flexSwitchCheckDefault');
  let pickerInit = flatpickr("#bookingPickupForm", {    
    mode: "single",
    dateFormat: "Y-m-d H:i",
    enableTime: true,
    minDate: "today"
  });
  let pickerEnd = flatpickr("#bookingDepartureForm", {    
    mode: "single",
    dateFormat: "Y-m-d H:i",
    enableTime: true,
    minDate: "today"
  });

  // Función para actualizar el modo de Flatpickr
  function updatePickerMode() {
    var departureContainer = document.getElementById("departureContainer");
    if (rangeCheckbox.checked) {
      departureContainer.style.display = "block";
    }else{
      departureContainer.style.display = "none";
    }
  }
  rangeCheckbox.addEventListener('change', updatePickerMode);
});


function saveQuote(event){
  event.preventDefault();
  $("#btn_quote").prop('disabled', true);
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
      }
  });

  let frm_data = $("#bookingboxForm").serializeArray();

  $.ajax({
      url: '/tpv/quote',
      type: 'POST',
      data: frm_data,
      beforeSend: function() {        
        $("#loadContent").html('<div class="loading"><span class="loader"></span></div>');
      },
      success: function(resp) {

        $("html, body").animate({ scrollTop: $("#loadContent").offset().top }, 300);

        $("#loadContent").html(resp);
        $("#btn_quote").prop('disabled', false);
      },
  }).fail(function(xhr, status, error) {
      console.log(xhr);
      Swal.fire(
          '¡ERROR!',
          xhr.responseJSON.message,
          'error'
      )
      $("#loadContent").html("");
      $("#btn_quote").prop('disabled', false);
  });
}

function makeReservationButton(event){
  event.preventDefault();
  $("#btn_make_reservation").prop('disabled', true).text("Enviando...");
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
      }
  });

  let frm_data = $("#formReservation").serializeArray();

  $.ajax({
      url: '/tpv/create',
      type: 'POST',
      data: frm_data,      
      success: function(resp) {
        window.location.replace(`/reservations/detail/${resp.config.id}`);        
      },
  }).fail(function(xhr, status, error) {
      console.log(xhr);
      Swal.fire(
          '¡ERROR!',
          xhr.responseJSON.message,
          'error'
      );
      $("#btn_make_reservation").prop('disabled', false).text("Enviar");
  });
}

function setTotal(total){
  $("#formTotal").val(total);  
}

$(document).on("change", "#formSite", function() {
  var selectedValue = $(this).val();
  //$("#formTotal").attr("readonly", true);
  
  if(selectedValue == 2 || selectedValue == 4 || selectedValue == 5){
    //$("#formTotal").attr("readonly", false);
  }
  $("#formTotal").attr("readonly", false);
});