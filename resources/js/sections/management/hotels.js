//DECLARACION DE VARIABLES
const _destination      = document.getElementById('destinationID');
const _zone             = document.getElementById('zoneId');
const _serviceFromForm  = document.getElementById('serviceFromForm');
const _address          = document.getElementById('from_address');
const _lat              = document.getElementById('from_lat');
const _lng              = document.getElementById('from_lng');
const _btnAdd           = document.getElementById('btnAdd');

//FUNCIONES ANONIMAS
let hotels = {
    /**
     * 
     * @param {*} destinationID 
     */
    getInputs: function(destinationID){
        // Configurar beforeSend
        _btnAdd.setAttribute('disabled', true);
        _zone.innerHTML = '<option>Buscando...</option>';

        fetch(`/config/rates/enterprise/destination/${destinationID}/get`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(resp => {
            _btnAdd.removeAttribute('disabled');
            
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {
                    const data = resp[key];
                    if(  data.length > 0  ){
                        if (key == "zones") {
                            let xHTML = ``;
                            data.forEach(item => {
                                xHTML += `<option value="${item.id}">${item.name}</option>`;
                            });
                            _zone.innerHTML = xHTML;
                        }
                    }else{
                        Swal.fire(
                            '¡ERROR!',
                            'Ocurrió un error',
                            'error'
                        );
                    }
                }
            }
        })
        .catch(error => {
            _btnAdd.removeAttribute('disabled');
            console.error('Hubo un problema con la operación fetch:', error);
        });
    },
    /**
     * 
     * @param {*} lat 
     * @param {*} lng 
     */
    initMap: function(lat, lng) {
        let from_lat = parseFloat(lat);
        let from_lng = parseFloat(lng);
    
        var location1 = { lat: from_lat, lng: from_lng };
    
        // Create a map centered at one of the locations
        var map = new google.maps.Map(document.getElementById('services_map'), {
            center: location1, 
            zoom: 15
        });
    
        var marker1 = new google.maps.Marker({
            position: location1,
            map: map,
            title: 'Origen'
        });
    },
    /**
     * 
     * @param {*} div 
     */
    initialize: function(div) {
        const _input = document.getElementById(div);
        if (!_input) {
            console.warn('No se encontró el input con ID: ' + div);
            return;
        }
        
        const autocomplete = new google.maps.places.Autocomplete(_input);
      
        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                console.warn('El lugar no tiene geometría (lat/lng):', place);
                return;
            }
            
            _input.value    = place.name;
            _address.value  = place.formatted_address;
            _lat.value      = place.geometry.location.lat();
            _lng.value      = place.geometry.location.lng();
        });
    }     
};

//VALIDAMOS DOM
/*
    se dispara cuando el documento HTML ha sido completamente cargado y parseado, 
    sin esperar a que se carguen los estilos, imágenes u otros recursos externos.
 */
document.addEventListener("DOMContentLoaded", function() {
    components.actionTable($('.table-bookings'), 'fixedheaderPagination');
    components.titleModalFilter();

    document.addEventListener('change', function (event) {
        if (event.target && event.target.id === 'destinationID') {
            if(event.target.value == 0){
                _zone.innerHTML  = '<option value="0">Zona</option>';
                return false;
            }
            hotels.getInputs(event.target.value);
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target && event.target.id === 'btnAdd') {
            event.preventDefault();

            if( _destination.value == 0 ){
                Swal.fire({
                    icon: "error",
                    html: "Debe seleccionar un destino...",
                    allowOutsideClick: false,          
                });
                return false;
            }

            if( _zone.value == 0 ){
                Swal.fire({
                    icon: "error",
                    html: "Debe seleccionar una zona...",
                    allowOutsideClick: false,          
                });
                return false;
            }

            if( serviceFromForm.value == "" ){
                Swal.fire({
                    icon: "error",
                    html: "Por favor, ingresa el nombre del hotel...",
                    allowOutsideClick: false,          
                });
                return false;
            }

            const _form             = document.getElementById("hotelAdd");
            const _formData         = new FormData(_form);
            _btnAdd.disabled    = true;
            _btnAdd.textContent = "Procesando...";

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se guarda el hotel.",
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/management/hotel/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },            
                body: _formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Hotel agregado con exito con éxito.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then(() => {
                    Swal.close(); // Cierra el Swal al recibir respuesta
                    location.reload();                    
                });
            })
            .catch(error => {
                Swal.fire(
                    '¡ERROR!',
                    error.message || 'Ocurrió un error',
                    'error'
                );
                _btnAdd.disabled = false;
                _btnAdd.textContent = "Agregar Hotel";
            });            
        }

        if (event.target && event.target.classList.contains('viewMap')){
            event.preventDefault();
            $("#serviceMapModal").modal('show');
            hotels.initMap(event.target.dataset.lat, event.target.dataset.lng);
        }
    })
    
    /*
        se dispara después de que todos los recursos (imágenes, hojas de estilo, etc.) se han cargado.
    */
    window.addEventListener('load', function () {
        // google.maps.event.addDomListener(window, 'load', hotels.initialize('serviceFromForm') );
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            if (document.getElementById('serviceFromForm')) {
                hotels.initialize('serviceFromForm');
            } else {
                console.warn("No se encontró el input con ID 'serviceFromForm'");
            }
        } else {
            console.error("La API de Google Maps no está cargada aún.");
        }
    });
});