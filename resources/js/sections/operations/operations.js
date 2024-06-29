//SETUP 
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
  
      if(element === "from_name_elements"){
          const initInput = document.getElementById('from_name');
          initInput.value = data.name;
          setup.items.from.name = data.name;
          setup.items.from.latitude = data.geo.lat;
          setup.items.from.longitude = data.geo.lng;
          
          var fromLat = document.getElementsByName("from_lat");
              fromLat[0].value = data.geo.lat;
          var fromLng = document.getElementsByName("from_lng");
              fromLng[0].value = data.geo.lng;
  
      }
  
      if(element === "to_name_elements"){
          const initInput = document.getElementById('to_name');
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

//DECLARACION DE VARIABLES
const from_autocomplete = document.getElementById('from_name');
const to_autocomplete = document.getElementById('to_name');

const sold_in_currency_select = document.getElementById('sold_in_currency');
const currency_span = document.getElementById('currency_span');
// const total = document.getElementById('total');
const form = document.getElementById('posForm');
const submitBtn = document.getElementById('submitBtn');

//FUNCIONALIDAD DEL AUTOCOMPLET
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
    setup.autocomplete( e.target.value, 'from_name_elements');
}, 500));
from_autocomplete.addEventListener('focus', (e) => {
    setup.autocomplete( e.target.value, 'from_name_elements');
});
to_autocomplete.addEventListener('keydown', affDelayAutocomplete(function (e) {
    setup.autocomplete( e.target.value, 'to_name_elements');
}, 500));
to_autocomplete.addEventListener('focus', (e) => {
    setup.autocomplete( e.target.value, 'to_name_elements');
});

//FUNCIONALIDAD DE CALENDARIO PARA AGREGAR SERVICIO
let pickerInit = flatpickr("#departure_date", {    
    mode: "single",
    dateFormat: "Y-m-d H:i",
    enableTime: true,
    minDate: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000)
});

sold_in_currency_select.addEventListener('change', (e) => {
    currency_span.innerText = `(${e.target.value})`;
});

form.addEventListener('submit', function (event) {
    event.preventDefault();

    // submitBtn.disabled = true;
    // submitBtn.innerText = 'Cargando...';

    $('#sold_in_currency').prop('disabled', false);
    $('#total').prop('disabled', false);

    let _params = components.serialize(this,'object');
    console.log(_params);

    $.ajax({
        type: "POST", // Método HTTP de la solicitud
        url: _LOCAL_URL + "/operation/capture/service",
        data: JSON.stringify(_params), // Datos a enviar al servidor
        dataType: "json", // Tipo de datos que se espera en la respuesta del servidor
        contentType: 'application/json; charset=utf-8',
        beforeSend: function(){
            components.loadScreen();
        },
        success: function(resp) {
            console.log(resp);
            if ( typeof resp === 'object' && 'success' in resp && resp.success ) {
                $("#operationModal").modal('hide');
                socket.emit("addServiceServer", resp);
            } else {
                Swal.fire({
                    title: 'Oops!',
                    icon: 'error',
                    html: 'Ocurrió un error inesperado',
                    timer: 2500,
                });
                $('#sold_in_currency').prop('disabled', true);
                $('#total').prop('disabled', true);
                // submitBtn.disabled = false;
                // submitBtn.innerText = 'guardar';
            }
        }
    });
});

var inactivityTime = (5 * 60000); // 30 segundos en milisegundos
var timeoutId;

function resetTimer() {
    clearTimeout(timeoutId);          
    timeoutId = setTimeout(refreshPage, inactivityTime);
}

function refreshPage() {
    location.reload();
}
    
document.addEventListener('mousemove', resetTimer);
document.addEventListener('keydown', resetTimer);
        
resetTimer(); 
