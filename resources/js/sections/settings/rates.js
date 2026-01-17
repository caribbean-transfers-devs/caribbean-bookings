//DECLARACION DE VARIABLES
const _destination      = document.getElementById('destinationID');
const _zoneOne          = document.getElementById('rateZoneOneId');
const _zoneTwo          = document.getElementById('rateZoneTwoId');
const _service          = document.getElementById('rateServicesID');
const _group            = document.getElementById('rateGroupID');
const _btnQuoteRate     = document.getElementById('btnGetRates');
const _container        = document.getElementById('rates-container');

//FUNCIONES ANONIMAS
let rates = {
    getInputs: function(destinationID){
        // Configurar beforeSend
        // _btnQuoteRate.setAttribute('disabled', true);
        _zoneOne.innerHTML = '<option>Buscando...</option>';
        _zoneTwo.innerHTML = '<option>Buscando...</option>';
        _service.innerHTML = '<option>Buscando...</option>';

        fetch(`/config/rates/destination/${destinationID}/get`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(resp => {
            // _btnQuoteRate.removeAttribute('disabled');
            console.log(resp);
            
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {
                    const data = resp[key];    
                    if (key == "zones") {
                        let xHTML = `<option value="">Selecciona una zona</option>`;
                        data.forEach(item => {
                            xHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        _zoneOne.innerHTML = ( xHTML != "" ? xHTML : `<option value="">Sin resultados</option>` );
                        _zoneTwo.innerHTML = ( xHTML != "" ? xHTML : `<option value="">Sin resultados</option>` );

                        if( _zoneOne.dataset.code ){
                            _zoneOne.value = _zoneOne.dataset.code;
                        }

                        if( _zoneTwo.dataset.code ){
                            _zoneTwo.value = _zoneTwo.dataset.code;
                        }
                    }
        
                    if (key == "services") {
                        let xHTML = `<option value="">[TODOS]</option>`;
                        data.forEach(item => {
                            xHTML += `<option data-type="${item.price_type}" value="${item.id}">${item.name}</option>`;
                        });
                        _service.innerHTML = xHTML;

                        if( _service.dataset.code ){
                            _service.value = _service.dataset.code;
                        }

                        rates.changeService(_service);                        
                    }
                }
            }
        })
        .catch(error => {
            // _btnQuoteRate.removeAttribute('disabled');
            console.error('Hubo un problema con la operación fetch:', error);
        });
    },
    changeDestination: function(value){
        if (value == 0) {
            _zoneOne.innerHTML    = '<option value="">Zona de origen</option>';
            _zoneTwo.innerHTML    = '<option value="">Zona de destino</option>';
            _service.innerHTML    = '<option value="">[Vehículo]</option>';
            _group.value = "";
            return false;            
        }
        rates.getInputs(value);
    },
    changeService: function(target) {
        const selectElement = target;
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const optionType = selectedOption.dataset.type ? selectedOption.dataset.type.trim().toLowerCase() : null;
        const single = document.querySelector('.single_');
        const multiple = document.querySelector('.multiple_');
        const costOperative = document.querySelector('.costOperative');
        const destinationServiceType = document.getElementById('destinationServiceType');
        if(destinationServiceType){
            destinationServiceType.value = optionType;
        }
        
        // Asegurarnos de que los elementos existen antes de manipularlos
        if (!single || !multiple) return;

        // Resetear clases primero
        single.classList.add('d-none');
        multiple.classList.add('d-none');
        (costOperative ? costOperative.classList.add('d-none') : '' );

        // Validar el tipo de opción
        if (optionType === "vehicle" || optionType === "shared") {
            single.classList.remove('d-none');
            (costOperative ? costOperative.classList.remove('d-none') : '' );
        } 
        else if (optionType === "passenger") {  // Asegúrate que coincide exactamente con tu HTML
            multiple.classList.remove('d-none');
            (costOperative ? costOperative.classList.remove('d-none') : '' );
        }
        
        console.log("Tipo seleccionado:", optionType); // Para depuración
    }
};

if (_destination) {
    rates.changeDestination(_destination.value);
}

document.addEventListener('change', function (event) {
    if (event.target && event.target.id === 'destinationID') {
        rates.changeDestination(event.target.value)
    }

    if (event.target && event.target.id === 'rateServicesID') {
        rates.changeService(event.target);
    }
});

document.querySelectorAll('.item-actions button, .item-actions a').forEach(el => {
    el.addEventListener('click', e => {
        if(el.href) window.location = el.href;
    });
});

document.addEventListener('click', function (event) {
    if (event.target && event.target.id === 'btnGetRates') {
        event.preventDefault();

        if( _destination.value == 0 || _zoneOne.value == 0 || _zoneTwo.value == 0 ){
            Swal.fire({
                icon: "error",
                html: "Debe seleccionar todos los inputs...",
                allowOutsideClick: false,          
            });
            return false;
        }

        if( _group.value == 0 ){
            Swal.fire({
                icon: "error",
                html: "Selecciona una empresa...",
                allowOutsideClick: false,          
            });
            return false;            
        }        

        Swal.fire({
            title: "Procesando solicitud...",
            text: "Por favor, espera mientras se cargan las tarifas.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        _container.innerHTML = '<div class="spinner-container"><div class="spinner-border text-dark me-2" role="status"><span class="visually-hidden">Loading...</span></div></div>';

        fetch('/config/rates/get', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // 👈 Asegura el tipo de contenido
                'X-CSRF-TOKEN': csrfToken
            },            
            body: JSON.stringify({ 
                rate_group: _group.value, 
                destination_id: _destination.value, 
                from_id: _zoneOne.value, 
                to_id: _zoneTwo.value, 
                service_id: _service.value 
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.text();
        })
        .then(data => {
            Swal.close(); // Cierra el Swal al recibir respuesta
            _container.innerHTML = data;
        })
        .catch(error => {
            Swal.fire(
                '¡ERROR!',
                error.message || 'Ocurrió un error',
                'error'
            );
            _container.innerHTML = '';
        });
    }

    if (event.target && event.target.id === 'btn_add_rate') {
        event.preventDefault();
        const _form          = document.getElementById("newPriceForm");
        const _formData      = new FormData(_form);
        const _btnAddRate    = document.getElementById('btn_add_rate');
        _btnAddRate.disabled = true;
        _btnAddRate.textContent = "Enviando...";

        Swal.fire({
            title: "Procesando solicitud...",
            text: "Por favor, espera mientras se guarda la tarifa.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/config/rates/new', {
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
                html: 'Tarifa guardada con éxito.',
                allowOutsideClick: false,
            }).then(() => {
                Swal.close(); // Cierra el Swal al recibir respuesta
                _btnQuoteRate.click();
            });
        })
        .catch(error => {
            Swal.fire(
                '¡ERROR!',
                error.message || 'Ocurrió un error',
                'error'
            );
            _btnAddRate.disabled = false;
            _btnAddRate.textContent = "Agregar Tarifa";
        });
    }

    if (event.target.classList.contains('btnUpdateRates')) {
        event.preventDefault();
        const _form             = document.getElementById("editPriceForm");
        const _formData         = new FormData(_form);
        _formData.append('_method', 'PUT');
        const _btnUpdateRates   = document.querySelectorAll(".btnUpdateRates");
        _btnUpdateRates.forEach(_update => {
            _update.disabled    = true;
            _update.textContent = "Actualizando...";
        });

        Swal.fire({
            title: "Procesando solicitud...",
            text: "Por favor, espera mientras se actualizan las tarifas.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/config/rates/update', {
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
                text: 'Tarifas actualizadas con éxito.',
                allowOutsideClick: false,
            }).then(() => {
                Swal.close(); // Cierra el Swal al recibir respuesta
                _btnQuoteRate.click();
            });
        })
        .catch(error => {
            Swal.fire(
                '¡ERROR!',
                error.message || 'Ocurrió un error',
                'error'
            );
            _btnUpdateRates.forEach(_update => {
                _update.disabled = false;
                _update.textContent = "Actualizar Tarifas";
            });
        });        
    }    
});

function deleteItem(id){
    swal.fire({
        // title: '¿Está seguro de eliminar la tarifa?',
        text: '¿Está seguro de eliminar la tarifa?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed) {
            const _button = document.querySelector(`[data-id="${id}"]`);
            if (_button) {
                _button.disabled = true;
                _button.textContent = "Eliminando...";
            }

            Swal.fire({
                title: "Procesando solicitud...",
                text: "Por favor, espera mientras se elimina la tarifa.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });            

            fetch('/config/rates/delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    // title: 'Tarifa eliminada',
                    icon: 'success',
                    text: 'La tarifa se ha eliminado con éxito.',
                    allowOutsideClick: false,
                }).then(() => {
                    Swal.close(); // Cierra el Swal al recibir respuesta
                    window.location.reload();
                    // _btnQuoteRate.click();
                });
            })
            .catch(error => {
                Swal.fire(
                    '¡ERROR!',
                    error.message || 'Ocurrió un error inesperado',
                    'error'
                );
                if (_button) {
                    _button.disabled = false;
                    _button.textContent = "Eliminar";
                }                
            });
        }
    });
}