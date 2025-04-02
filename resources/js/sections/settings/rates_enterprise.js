//DECLARACION DE VARIABLES
const _enterprise       = document.getElementById('enterpriseID');
const _destination      = document.getElementById('destinationID');
const _zoneOne          = document.getElementById('rateZoneOneId');
const _zoneTwo          = document.getElementById('rateZoneTwoId');
const _service          = document.getElementById('rateServicesID');
const _btnQuoteRate     = document.getElementById('btnGetRates');
const _container        = document.getElementById('rates-container');

//FUNCIONES ANONIMAS
let rates = {
    getInputs: function(destinationID){
        // Configurar beforeSend
        _btnQuoteRate.setAttribute('disabled', true);
        _zoneOne.innerHTML = '<option>Buscando...</option>';
        _zoneTwo.innerHTML = '<option>Buscando...</option>';
        _service.innerHTML = '<option>Buscando...</option>';

        fetch(`/config/rates/enterprise/destination/${destinationID}/get`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(resp => {
            _btnQuoteRate.removeAttribute('disabled');
            
            for (const key in resp) {
                if (resp.hasOwnProperty(key)) {
                    const data = resp[key];    
                    if (key == "zones") {
                        let xHTML = ``;
                        data.forEach(item => {
                            xHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        _zoneOne.innerHTML = xHTML;
                        _zoneTwo.innerHTML = xHTML;
                    }
        
                    if (key == "services") {
                        let xHTML = `<option value="0">[TODOS]</option>`;
                        data.forEach(item => {
                            xHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        _service.innerHTML = xHTML;
                    }
                }
            }
        })
        .catch(error => {
            _btnQuoteRate.removeAttribute('disabled');
            console.error('Hubo un problema con la operación fetch:', error);
        });
    }
};

if( _destination != null ){
    _destination.addEventListener('change', function(){
        if(this.value == 0){
            _zoneOne.innerHTML  = '<option value="0">Zona de origen</option>';
            _zoneTwo.innerHTML  = '<option value="0">Zona de destino</option>';
            _service.innerHTML    = '<option value="0">[TODOS]</option>';
            return false;
        }
        rates.getInputs(this.value);
    })
}

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

        if( _enterprise.value == 0 ){
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

        fetch('/config/rates/enterprise/get', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // 👈 Asegura el tipo de contenido
                'X-CSRF-TOKEN': csrfToken
            },            
            body: JSON.stringify({ 
                enterprise_id: _enterprise.value, 
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
        const _form         = document.getElementById("newPriceForm");
        const _formData     = new FormData(_form);
        const _btnAddRate   = document.getElementById('btn_add_rate');
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

        fetch('/config/rates/enterprise/new', {
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
            _update.disabled = true;
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

        fetch('/config/rates/enterprise/update', {
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
                html: 'Tarifas actualizadas con éxito.',
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
    Swal.fire({
        title: '¿Está seguro de eliminar la tarifa?',
        text: 'Esta acción no se puede revertir',
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

            fetch('/config/rates/enterprise/delete', {
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
                    title: 'Tarifa eliminada',
                    icon: 'success',
                    html: 'La tarifa ha sido eliminada con éxito.',
                    allowOutsideClick: false,
                }).then(() => {
                    Swal.close(); // Cierra el Swal al recibir respuesta
                    _btnQuoteRate.click();
                });
            })
            .catch(error => {
                Swal.fire(
                    '¡ERROR!',
                    error.message || 'Ocurrió un error inesperado',
                    'error'
                );
                if (button) {
                    _button.disabled = false;
                    _button.textContent = "Eliminar";
                }                
            });
        }
    });
}