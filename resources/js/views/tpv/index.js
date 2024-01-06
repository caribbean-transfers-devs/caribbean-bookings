var from_autocomplete = document.getElementById('aff-input-from');
var to_autocomplete = document.getElementById('aff-input-to');

let setup = {        
  lang: 'es',
  currency: 'USD',
  deeplink: '/resultados',
  serviceType: 'OW',
  pax: 1,
  items: {
      from: {
          name: '',
          latitude: '',
          longitude: '',
          pickupDate: '',
          pickupTime: '00:00',
      },
      to: {
          name: '',
          latitude: '',
          longitude: '',
          pickupDate: '',
          pickupTime: '00:00',
      },
  },
  setLang: function(lang){
    setup.lang = lang;    
  },
  loadingMessage: function(item){

    const loader = document.getElementById(item);
    loader.innerHTML = '';
    
    const div = document.createElement('div');
    div.classList.add("loader");
    const image = document.createElement('img');
    image.width = 35;
    image.height = 35;
    image.src = '/assets/img/loader.gif';
    
    div.appendChild(image);
    loader.appendChild(div);

  },
  autocomplete: function(keyword, element){
    let size = keyword.length;
      if(size < 3) return false;
      setup.loadingMessage(element);

      fetch(`/tpv/autocomplete/${keyword}`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          }
      }).then((response) => {
          return response.json()
      }).then((data) => {
          this.makeItems(data,element);
      }).catch((error) => {
          console.error('Error:', error);
      });
  },
  makeItems: function(data, element){    

    const finalElement = document.getElementById(element);
          finalElement.innerHTML = '';

    for (let key in data) {
      if (data.hasOwnProperty(key)) {

        if(data[key].type === "DEFAULT"){

            const itemDiv = document.createElement('div');
                  itemDiv.textContent = data[key].name;
                  itemDiv.className = 'default';

            const span = document.createElement('span');
                  span.textContent = data[key].address;

                  itemDiv.appendChild(span);
                  itemDiv.addEventListener('click', function() { 
                    setup.setItem(element, data[key]);
                  });

            finalElement.appendChild(itemDiv);
        }

        if(data[key].type === "GCP"){
            const itemNameDiv = document.createElement('div');      
                  itemNameDiv.className = 'GCP';            

            const itemInformation = document.createElement('div');
                  itemInformation.textContent = data[key].name;

                  const span = document.createElement('span');
                    span.textContent = data[key].address;


                  itemInformation.appendChild(span);
                  itemInformation.addEventListener('click', function() { 
                    setup.setItem(element, data[key]);
                  });

                const itemButton = document.createElement('button');
                      itemButton.textContent = 'ADD';
                      itemButton.type = 'button';
                      itemButton.addEventListener('click', function() {                         
                        setup.saveHotel(element, data[key]);
                      });
                            
                itemNameDiv.appendChild(itemInformation);
                itemNameDiv.appendChild(itemButton);

                finalElement.appendChild(itemNameDiv);

        }


      }
    }
  },
  setItem(element, data = {}){
    const finalElement = document.getElementById(element);
    finalElement.innerHTML = '';

    if(element === "aff-input-from-elements"){
        const initInput = document.getElementById('aff-input-from');
        initInput.value = data.name;
        setup.items.from.name = data.name;
        setup.items.from.latitude = data.geo.lat;
        setup.items.from.longitude = data.geo.lng;
        
        var fromLat = document.getElementsByName("from_lat");
            fromLat[0].value = data.geo.lat;
        var fromLng = document.getElementsByName("from_lng");
            fromLng[0].value = data.geo.lng;

    }

    if(element === "aff-input-to-elements"){
        const initInput = document.getElementById('aff-input-to');
        initInput.value = data.name;
        setup.items.to.name = data.name;
        setup.items.to.latitude = data.geo.lat;
        setup.items.to.longitude = data.geo.lng;
        
        var toLat = document.getElementsByName("to_lat");
            toLat[0].value = data.geo.lat;

        var toLng = document.getElementsByName("to_lng");
            toLng[0].value = data.geo.lng;
    }
  },
  saveHotel: function(element, data){      
      let item = {
        name: data.name,
        address: data.address,
        start: {
          lat: data.geo.lat,
          lng: data.geo.lng,
        },
      };

      $.ajax({
        url: 'https://api.caribbean-transfers.com/api/v1/hotels/add',
        type: 'POST',
        data: item,
        beforeSend: function() {
          setup.loadingMessage(element);
        },
        success: function(resp) {
          setup.setItem(element, data);          
          alert("Hotel agregado con éxito...");
          const finalElement = document.getElementById(element);
          finalElement.innerHTML = '';          
        },
    }).fail(function(xhr, status, error) {
      console.log(error);
    });
  },
};


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
  $("#formTotal").attr("readonly", false);
}



function affDelayAutocomplete(callback, ms) {
  var timer = 0;
  return function () {
      var context = this,
          args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
          callback.apply(context, args);
      }, ms || 0);
  };
}



from_autocomplete.addEventListener('keydown', affDelayAutocomplete(function (e) {
  setup.autocomplete( e.target.value, 'aff-input-from-elements');
}, 500));
from_autocomplete.addEventListener('focus', (e) => {
  setup.autocomplete( e.target.value, 'aff-input-from-elements');
});
to_autocomplete.addEventListener('keydown', affDelayAutocomplete(function (e) {        
  setup.autocomplete( e.target.value, 'aff-input-to-elements');
}, 500));
to_autocomplete.addEventListener('focus', (e) => {
  setup.autocomplete( e.target.value, 'aff-input-to-elements');
});


$(document).on("change", "#formSite", function() {
  var selectedValue = $(this).val();
  //$("#formTotal").attr("readonly", true);
  
  if(selectedValue == 2 || selectedValue == 4 || selectedValue == 5){
    //$("#formTotal").attr("readonly", false);
  }
  $("#formTotal").attr("readonly", false);
});