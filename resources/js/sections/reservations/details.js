// DECLARACIÓN DE VARIABLES
const __serviceDateForm                 = document.getElementById('serviceDateForm');
const __serviceDateRoundForm            = document.getElementById('serviceDateRoundForm');
const __formConfirmation                = document.getElementById('formConfirmation'); // DIV QUE TIENE EL FORMULARIO DE ENVIO DE CONFIRMACION
const __btnSendArrivalConfirmation      = document.getElementById('btnSendArrivalConfirmation'); //BOTON PARA ENVIAR EL EMAIL DE CONFIRMATION
const __titleModal                      = document.getElementById('titleModal');
const __closeModalHeader                = document.getElementById('closeModalHeader');
const __closeModalFooter                = document.getElementById('closeModalFooter');
const __serviceType                     = document.getElementById('serviceTypeForm');

//DECLARAMOS VARIABLES, PARA VENTAS Y PAGOS
const __type                            = document.getElementById('type_form');
const __code                            = document.getElementById('sale_id');
const __type_pay                        = document.getElementById('type_form_pay');
const __code_pay                        = document.getElementById('payment_id');
const deleteCommission                  = document.querySelector('.deleteCommission');

//DECLARAMOS VARIABLES PARA ACCIONES DE RESERVA
const sendMessageWhatsApp               = document.getElementById('sendMessageWhatsApp');
const enablePayArrival                  = document.getElementById('enablePayArrival');
const enablePlusService                 = document.getElementById('enablePlusService');
const markReservationOpenCredit         = document.getElementById('markReservationOpenCredit');
const reactivateReservation             = document.getElementById('reactivateReservation');
const refundRequest                     = document.getElementById('refundRequest');
const markReservationDuplicate          = document.getElementById('markReservationDuplicate');

//FUNCIONES ANONIMAS
let details = {
    /**
     * 
     * @param {*} item 
     */
    initMap: function(item) {
        const _from_name = document.getElementById('origin_location');
        const _to_name = document.getElementById('destination_location');
        const _distance_time = document.getElementById('destination_time');
        const _distance_km = document.getElementById('destination_kms');
    
        let from_lat = parseFloat(item.from_lat);
        let from_lng = parseFloat(item.from_lng);
        let to_lat = parseFloat(item.to_lat);
        let to_lng = parseFloat(item.to_lng);
    
        const origin = { lat: from_lat, lng: from_lng };
        const destination = { lat: to_lat, lng: to_lng };
    
        // Crear mapa
        const map = new google.maps.Map(document.getElementById('services_map'), {
            center: origin,
            zoom: 15,
            disableDefaultUI: false,
            mapTypeControl: false,
            scaleControl: false,
            zoomControl: true,
            streetViewControl: false,
            fullscreenControl: false,
        });
    
        // Crear marcadores
        new google.maps.Marker({ position: origin, map, title: 'Origen' });
        new google.maps.Marker({ position: destination, map, title: 'Destino' });
    
        // Mostrar nombres
        _from_name.innerText = item.from_name;
        _to_name.innerText = item.to_name;
    
        // Direcciones
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true
        });
    
        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, function(response, status) {
            if (status === google.maps.DirectionsStatus.OK) {
                directionsRenderer.setDirections(response);
    
                const route = response.routes[0].legs[0];
                // item.distance_time + '/' + 
                _distance_time.innerText = route.duration.text;
                // item.distance_km + '/' + 
                _distance_km.innerText = route.distance.text;
            } else {
                console.error('Error obteniendo la ruta: ', status);
            }
        });
    },
    /**
     * 
     * @param {*} div 
     */
    initialize: function (div) {
        const _input = document.getElementById(div);
        if (!_input) {
            console.warn('No se encontró el input con ID: ' + div);
            return;
        }
        
        const autocomplete = new google.maps.places.Autocomplete(_input);
      
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                console.warn('El lugar no tiene geometría (lat/lng):', place);
                return;
            }
    
            if (div === "serviceFromForm") {
                const latInput = document.getElementById("from_lat_edit");
                const lngInput = document.getElementById("from_lng_edit");
                if (latInput && lngInput) {
                    latInput.value = place.geometry.location.lat();
                    lngInput.value = place.geometry.location.lng();
                }
            }
    
            if (div === "serviceToForm") {
                const latInput = document.getElementById("to_lat_edit");
                const lngInput = document.getElementById("to_lng_edit");
                if (latInput && lngInput) {
                    latInput.value = place.geometry.location.lat();
                    lngInput.value = place.geometry.location.lng();
                }
            }
        });
    },
    /**
     * 
     * @param {*} item 
     */
    itemInfo: function (item){
        const __service_type = document.querySelector('.serviceTypeForm');    
        $("#from_zone_id").val(item.from_zone);
        $("#to_zone_id").val(item.to_zone);
    
        $("#item_id_edit").val(item.reservations_item_id);
        $("#servicePaxForm").val(item.passengers);
        $("#destination_serv").val(item.destination_service_id);
        $("#serviceFromForm").val(item.from_name);
        $("#serviceToForm").val(item.to_name);
        $("#serviceFlightForm").val(item.flight_number);
    
        $("#from_lat_edit").val(item.from_lat);
        $("#from_lng_edit").val(item.from_lng);
        $("#to_lat_edit").val(item.to_lat);
        $("#to_lng_edit").val(item.to_lng);
    
        __serviceDateForm.value = item.op_one_pickup,
        __serviceDateForm.min = item.op_one_pickup;
        if(item.op_one_status != 'PENDING'){
            $("#serviceDateForm").prop('readonly', true);
        }
    
        if(item.op_two_status != 'PENDING'){
            $("#serviceDateRoundForm").prop('readonly', true);
        }
    
        if(item.is_round_trip == 1){
            __serviceDateRoundForm.value = item.op_two_pickup,
            __serviceDateRoundForm.min = item.op_one_pickup;
            $("#info_return").removeClass('d-none');
            __service_type.classList.add('d-none');
            __serviceType.removeAttribute('name');
        }else{
            __serviceDateRoundForm.value = "",
            __serviceDateRoundForm.min = "";
            $("#info_return").addClass('d-none');
            __service_type.classList.remove('d-none');
            __serviceType.setAttribute('name','serviceTypeForm');
        }
    },

    /**
     * 
     * @param {*} params 
     * @returns 
     */
    updateServiceStatus: async function (params) {
        try {
            const response = await fetch(_LOCAL_URL + "/action/updateServiceStatus", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) throw new Error("Error en la petición");

            const result = await response.json();
            if (result.status !== "success") {
                throw new Error(result.message || "No se pudo actualizar el estatus");
            }

            return true; // Si todo bien, continúa
        } catch (error) {
            Swal.fire("Error", error.message || "Ocurrió un error", "error");
            return false;
        }
    },

    /**
     * 
     * @param {*} files 
     * @param {*} id 
     * @param {*} status 
     * @returns 
     */
    uploadImages: async function(files, id, status = "CANCELLATION") {
        const uploadedImages = [];
        for (const file of files) {
            // Obtener el token CSRF desde el meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData();
            formData.append("folder", id);
            formData.append("type_media", ( status == "CANCELLED" ? "CANCELLATION" : status ));
            formData.append("file", file);

            try {
                const response = await fetch(_LOCAL_URL + "/reservations/upload", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': csrfToken  // Incluir el token en los headers
                    },                
                    body: formData
                });

                // console.log(response);
                if (response.ok) {
                    const data = await response.json();
                    uploadedImages.push(data.imageUrl); // Suponiendo que el servidor responde con `{ "imageUrl": "URL de la imagen" }`
                } else {
                    throw new Error("Error al subir una imagen.");
                }
            } catch (error) {
                console.error("Error subiendo imagen:", error);
                return [];
            }
        }
        return uploadedImages;
    }    
}

//DECLARAMOS VARIABLES PARA ITEMS DE SERVICIOS

//ACCION PARA REMOVER UNA COMISION
if( deleteCommission ){
    deleteCommission.addEventListener('click', function(event){
        event.preventDefault();
        const { code } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de eliminar la comisión de la reservación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC            
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se elimina la comisión de la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/deleteCommission', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC                        
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });
    })
}

//ENVIAMOS RESERVA POR WHATSAPP
if( sendMessageWhatsApp ){
    sendMessageWhatsApp.addEventListener('click', function(event){
        event.preventDefault();
        const { code } = this.dataset;

        Swal.fire({
            title: "Procesando solicitud...",
            text: "Por favor, espera mientras se envia la reserva por WhatsApp.",
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/action/sendMessageWhatsApp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },            
            body: JSON.stringify({ reservation_id: code })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            window.open(data.link, '_blank');
            // Swal.fire({
            //     icon: data.status,
            //     html: data.message,
            //     allowOutsideClick: false,
            //     allowEscapeKey: false, // Esta línea evita que se cierre con ESC
            // }).then(() => {
            //     location.reload();
            // });
        })
        .catch(error => {
            Swal.fire(
                '¡ERROR!',
                error.message || 'Ocurrió un error',
                'error'
            );
        });
    });
}

//HABILIDATAMOS PAGO A LA LLEGADA DE UNA RESERVA
if( enablePayArrival ){
    enablePayArrival.addEventListener('click', function(event){
        event.preventDefault();
        const { code } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de marcar la reservación como pago a la llegada?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC            
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se marca como pago a la llegada la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/enablePayArrival', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });
    });
}

//HABILIDATAMOS SERVICIO PLUS DE UNA RESERVA
if( enablePlusService ){
    enablePlusService.addEventListener('click', function(event){
        event.preventDefault();
        const { code } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de activar el servicio plus de la reservación?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se activa el servicio plus de la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/enablePlusService', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });       
    });
}

//MARCAMOS COMO CREDITO ABIERTO UNA RESERVA
if( markReservationOpenCredit ){
    markReservationOpenCredit.addEventListener('click', function(event){
        event.preventDefault();
        const { code, status } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de la marcar como crédito abierto la reservación?',
            icon: 'warning',    
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se marca como crédito abierto la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/markReservationOpenCredit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code, status: status })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });        
    })
}

//REACTIVAMOS UNA RESERVA
if( reactivateReservation ){
    reactivateReservation.addEventListener('click', function(event){
        event.preventDefault();
        const { code, status, pay_at_arrival } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de reactivar la reservación?',
            icon: 'warning',    
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se reactiva la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/reactivateReservation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code, status: status })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });        
    })
}

//SOLITAMOS REEMBOLSO DE UNA RESERVA
if( refundRequest ){
    refundRequest.addEventListener('click', function(event){
        event.preventDefault();
        const { code } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de la solicitud del reembolso de la reservación?',
            icon: 'warning',
            input: 'textarea',
            inputLabel: "Ingresa un comentario indicando los detalles del reembolso",
            inputPlaceholder: 'Ingresa un comentario indicando si el reembolso es total, o si es parcial de cuanto porcentaje, en caso de ser un "round trip" indicar si aplica el reembolso solo para llegada o salida o ambos...',
            inputAttributes: {
                required: true
            },            
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
            preConfirm: (comment) => {
                if (!comment.trim()) {
                    Swal.showValidationMessage('Debe ingresar un comentario.');
                    return false;
                }
                return comment.trim();
            }            
        }).then((result) => {
            if (result.isConfirmed) {
                let message = result.value;
                
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se envia la solicud de reembolso de la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/refundRequest', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code, message: message })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });
    });
}

//MARCAMOS COMO DUPLICADA UNA RESERVA
if( markReservationDuplicate ){
    markReservationDuplicate.addEventListener('click', function(event){
        event.preventDefault();
        const { code, status } = this.dataset;

        Swal.fire({
            html: '¿Está seguro de la marcar como duplicada la reservación?',
            icon: 'warning',    
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Procesando solicitud...",
                    text: "Por favor, espera mientras se marca como duplicada la reserva.",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/action/markReservationDuplicate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },            
                    body: JSON.stringify({ reservation_id: code, status: status })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        html: data.message,
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire(
                        '¡ERROR!',
                        error.message || 'Ocurrió un error',
                        'error'
                    );
                });
            }
        });        
    })
}

//VALIDAMOS DOM
/*
    se dispara cuando el documento HTML ha sido completamente cargado y parseado, 
    sin esperar a que se carguen los estilos, imágenes u otros recursos externos.
 */
document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("click", components.debounce(function (event) {
        components.typesCancellations();
        
        //PERMITE CALIFICAR LA RESERVACION
        if (event.target.classList.contains('enabledLike')) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            const _params    = {
                reservation_id: target.dataset.reservation || "",
                status: target.dataset.status || "",
            };
            
            Swal.fire({
                html: '¿Está seguro de la calificación de la reservación?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se actualiza el estatus de calificación de la reservación.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
    
                    fetch('/action/enabledLike', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(_params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            '¡ERROR!',
                            error.message || 'Ocurrió un error',
                            'error'
                        );
                    });
                }
            });            
        }

        //PERMITE ELIMINAR ITEM DE LA RESERVACION
        if (event.target.classList.contains('deleteItem')) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            const _params    = {
                item_id: target.dataset.item || ""
            };
            
            Swal.fire({
                html: '¿Está seguro de la eliminar el item de la reservación?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se elimina el item de la reservación.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
    
                    fetch('/action/deleteItem', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(_params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            '¡ERROR!',
                            error.message || 'Ocurrió un error',
                            'error'
                        );
                    });
                }
            });            
        }        

        //ACTUALIZA EL ESTATUS DEL SERVICIO
        if (event.target.classList.contains('serviceStatusUpdate')) {
            event.preventDefault();
            
            // Obtener datos del elemento clickeado
            const { reservation, item, service, status, type } = event.target.dataset;
       
            (async () => {                
                // Crear un contenedor para Dropzone y el select
                const dropzoneContainer = document.createElement("div");
                let HTML = "";

                if ( status == "CANCELLED" || status == "NOSHOW" ){
                    HTML = `
                        <label for="cancelReason">Selecciona el motivo de cancelación:</label>
                        <select id="cancelReason" class="swal2-input">
                            <option value="">Seleccione una opción</option>
                            ${Object.entries(__typesCancellations).map(([key, value]) => `<option value="${key}">${value}</option>`).join('')}
                        </select>
                        <label for="attachPicture">Debes adjuntar al menos una imagen:</label>
                        <div id="dropzoneService" class="dropzone"></div>            
                    `;
                }
                let selectedFiles = []; // Array para almacenar las imágenes seleccionadas
                dropzoneContainer.classList.add('box_cancelation');
                dropzoneContainer.innerHTML = `
                    <p>${ status == "CANCELLED" || status == "NOSHOW" ? '¿Está seguro de cancelar la reservación? <br>  Esta acción no se puede revertir' : '¿Está seguro de actualizar el estatus? <br> Esta acción no se puede revertir' }</p>
                    ${HTML}
                `;
        
                const { isConfirmed, value } = await swal.fire({
                    html: dropzoneContainer,
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar",
                    allowOutsideClick: false,
                    allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                    didOpen: () => {
                        if (document.getElementById('dropzoneService')) {
                            // Inicializar Dropzone
                            new Dropzone("#dropzoneService", {
                                url: "/reservations/upload", // No se enviarán archivos aquí, solo los almacenaremos en memoria
                                maxFilesize: 5, // Tamaño máximo del archivo en MB
                                maxFiles: 5,
                                acceptedFiles: "image/*",
                                dictDefaultMessage: "Arrastra el archivo aquí o haz clic para subirlo (Imágenes/PDF)...",
                                addRemoveLinks: true,
                                dictRemoveFile: "Eliminar imagen",
                                autoProcessQueue: false,
                                init: function () {
                                    this.on("addedfile", file => selectedFiles.push(file));
                                    this.on("removedfile", file => {
                                        selectedFiles = selectedFiles.filter(f => f !== file);
                                    });
                                }
                            });
                        }
                    },
                    preConfirm: () => {
                        if (status == "CANCELLED" || status == "NOSHOW") {
                            const reason = document.getElementById("cancelReason").value;
                            const dropzone = Dropzone.forElement("#dropzoneService");
                
                            if (!reason) {
                                Swal.showValidationMessage("Debes seleccionar un motivo de cancelación.");
                                return false;
                            }
                            if (dropzone.files.length === 0) {
                                Swal.showValidationMessage("Debes subir al menos una imagen.");
                                return false;
                            }
        
                            return { reason, images: dropzone.files };
                        } else {
                            return value;
                        }
                    }            
                });
        
                if (isConfirmed) {
                    const params = {
                        item_id: item,
                        service: service,
                        status: status,
                        type: type,
                        type_cancel: (status == "CANCELLED" || status == "NOSHOW") ? value.reason : value
                    };

                    Swal.fire({
                        title: "Actualizando estatus...",
                        text: "Por favor, espera mientras se actualiza el estatus.", //Realiza la function de HTML en el Swal
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });

                    const statusUpdated = await details.updateServiceStatus(params);

                    if (statusUpdated) {
                        if (status == "CANCELLED" || status == "NOSHOW") {
                            Swal.fire({
                                title: "Subiendo imágenes...",
                                text: "Por favor, espera mientras se suben todas las imagenes", //Realiza la function de HTML en el Swal
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => Swal.showLoading()
                            });

                            try {
                                const uploadedImages = await details.uploadImages(value.images, reservation, status);

                                if (uploadedImages.length === value.images.length) {
                                    Swal.fire("Éxito", "Cancelación confirmada y archivos subidos.", "success").then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire("Error", "Algunas imágenes no se pudieron subir. Intenta de nuevo.", "error");
                                }
                            } catch (err) {
                                Swal.fire("Error", "Ocurrió un problema al subir las imágenes.", "error");
                            }
                        } else {
                            Swal.fire("Éxito", "Estatus actualizado correctamente.", "success").then(() => {
                                window.location.reload();
                            });
                        }
                    }         
                }
            })();            
        }

        //ENVIAR CONFIRMACIÓN DE LLEGADA
        if (event.target.classList.contains('arrivalConfirmation')) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target = event.target;
            const id = target.dataset.id;

            document.getElementById('arrival_confirmation_item_id').value = id;
            __formConfirmation.classList.remove('d-none');
            __btnSendArrivalConfirmation.classList.remove('d-none');
            __titleModal.innerHTML = "Confirmación de llegada";
            __closeModalFooter.innerHTML = "Cerrar";        
        }        

        //ACTUALIZA LA CONFIRMACIÓN DEL SERVICIO
        if (event.target.classList.contains('confirmService')) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            const _params    = {
                item_id: target.dataset.item || "",
                service: target.dataset.service || "",
                status: target.dataset.status || "",
                type: target.dataset.type || "",
            };
            
            Swal.fire({
                html: '¿Está seguro de actualizar el estatus de confirmación del servicio?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se actualiza el estatus de confirmación.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
    
                    fetch('/action/confirmService', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(_params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            '¡ERROR!',
                            error.message || 'Ocurrió un error',
                            'error'
                        );
                    });
                }
            });            
        }

        //ACTUALIZA PARA DESBLOQUEAR SERVICIO DEL CIERRE DE OPERACIÓN
        if (event.target.classList.contains('updateServiceUnlock')) {
            event.preventDefault();

            // Definir parámetros de la petición
            const target     = event.target;
            const _params    = {
                item_id: target.dataset.item || "",
                service: target.dataset.service || "",
                type: target.dataset.type || "",
            };
            
            Swal.fire({
                html: '¿Está seguro de desbloquear este servicio del cierre de operación?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC                
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se desbloque el servicio de la operación.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
    
                    fetch('/action/updateServiceUnlock', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },            
                        body: JSON.stringify(_params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            '¡ERROR!',
                            error.message || 'Ocurrió un error',
                            'error'
                        );
                    });
                }
            });            
        }

        //VENTAS, AGREGAR Y EDITAR
        if (event.target && event.target.id === 'btn_new_sale') {
            event.preventDefault();

            const _btnNewSale = document.getElementById('btn_new_sale');
            _btnNewSale.disabled = true;
            _btnNewSale.textContent = "Enviando...";     
    
            Swal.fire({
                html: '¿Está seguro de agregar la venta?',
                icon: 'warning',    
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC      
            }).then((result) => {
                if (result.isConfirmed) {
                    let __params = components.serialize(document.getElementById('frm_new_sale'),'object');
                    let __url = _LOCAL_URL + ( __type.value == 1 ? "/sales" : "/sales/" + __code.value );
    
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se agrega la venta.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
        
                    fetch(__url, {
                        method: ( __type.value == 1 ? 'POST' : 'PUT' ),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },            
                        body: JSON.stringify(__params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        _btnNewSale.disabled = false;
                        _btnNewSale.textContent = "Guardar";                        
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            '¡ERROR!',
                            error.message || 'Ocurrió un error',
                            'error'
                        );
                        _btnNewSale.disabled = false;
                        _btnNewSale.textContent = "Guardar";
                    });
                }else{
                    _btnNewSale.disabled = false;
                    _btnNewSale.textContent = "Guardar";
                }
            });
        }

        //PAGOS, AGREGAR Y EDITAR
        if (event.target && event.target.id === 'btn_new_payment') {
            event.preventDefault();

            const _btnNewPayment = document.getElementById('btn_new_payment');
            _btnNewPayment.disabled = true;
            _btnNewPayment.textContent = "Enviando...";        
    
            Swal.fire({
                html: '¿Está seguro de agregar el pago?',
                icon: 'warning',    
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC      
            }).then((result) => {
                if (result.isConfirmed) {
                    let __params = components.serialize(document.getElementById('frm_new_payment'),'object');
                    let __url = _LOCAL_URL + ( __type_pay.value == 1 ? "/payments" : "/payments/" + __code_pay.value );
    
                    Swal.fire({
                        title: "Procesando solicitud...",
                        text: "Por favor, espera mientras se agrega el pago.",
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
        
                    fetch(__url, {
                        method: ( __type_pay.value == 1 ? 'POST' : 'PUT' ),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },            
                        body: JSON.stringify(__params)
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        _btnNewPayment.disabled = false;
                        _btnNewPayment.textContent = "Guardar";                        
                        Swal.fire({
                            icon: data.status,
                            html: data.message,
                            allowOutsideClick: false,
                            allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        Swal.fire(
                            '¡ERROR!',
                            error.message || 'Ocurrió un error',
                            'error'
                        );
                        _btnNewPayment.disabled = false;
                        _btnNewPayment.textContent = "Guardar";
                    });
                }else{
                    _btnNewPayment.disabled = false;
                    _btnNewPayment.textContent = "Guardar";
                }
            });            
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo
});

/*
    se dispara después de que todos los recursos (imágenes, hojas de estilo, etc.) se han cargado.
 */
window.addEventListener('load', function () {
    // google.maps.event.addDomListener(window, 'load', details.initialize('serviceFromForm') );
    // google.maps.event.addDomListener(window, 'load', details.initialize('serviceToForm') );
    if (typeof google !== 'undefined' && google.maps && google.maps.places) {
        if (document.getElementById('serviceFromForm')) {
            details.initialize('serviceFromForm');
        } else {
            console.warn("No se encontró el input con ID 'serviceFromForm'");
        }

        if (document.getElementById('serviceToForm')) {
            details.initialize('serviceToForm');
        } else {
            console.warn("No se encontró el input con ID 'serviceToForm'");
        }
    } else {
        console.error("La API de Google Maps no está cargada aún.");
    }
});

$(function() {
    $('#serviceSalesModal').on('hidden.bs.modal', function () {
        $("#frm_new_sale")[0].reset();
        $("#sale_id").val('');
        $("#type_form").val(1);
        $("#btn_new_sale").prop('disabled', false);
    });

    $('#servicePaymentsModal').on('hidden.bs.modal', function () {
        $("#frm_new_payment")[0].reset();
        $("#payment_id").val('');
        $("#type_form_pay").val(1);
        $("#btn_new_payment").prop('disabled', false);
    });
});

function sendMail(code,mail,languague){
    var url = "https://api.caribbean-transfers.com/api/v1/reservation/send?code="+code+"&email="+mail+"&language="+languague+"&type=new";
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            swal.fire({
                title: 'Correo enviado',
                text: 'Se ha enviado el correo correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        },
        error: function (data) {
            swal.fire({
                title: 'Error',
                text: 'Ha ocurrido un error al enviar el correo',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });
}

function sendInvitation(event, item_id, lang = 'en'){
    event.preventDefault();
    var url = "/reservations/payment-request";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); 
    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: { item_id:item_id, lang:lang },
        success: function (data) {
            Swal.fire({
                title: '¡Éxito!',
                icon: 'success',
                html: 'Solicitúd de pago enviada. Será redirigido en <b></b>',
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
        },
        error: function (data) {
            swal.fire({
                title: 'Error',
                text: 'Ha ocurrido un error al enviar el invitación',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        }
    });    
}

function saveFollowUp(){
    $("#btn_new_followup").prop('disabled', true);
    let __params = components.serialize(document.getElementById('frm_new_followup'),'object');
    components.request_exec_ajax( _LOCAL_URL + "/reservationsfollowups", 'POST', __params );
}

/* ===== Start Events Sales Settings ===== */
function getSale(id){
    $("#btn_new_sale").prop('disabled', true);
    $("#type_form").val(2);
    $("#sale_id").val(id);
    $.ajax({
        url: '/sales/'+id,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#new_sale_type_id").val(data.sale_type_id);
            $("#new_sale_description").val(data.description);
            $("#new_sale_total").val(data.total);
            $("#new_sale_quantity").val(data.quantity);
            $("#new_sale_agent_id").val(data.call_center_agent_id);
            $("#btn_new_sale").prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function deleteSale(id){
    swal.fire({
        html: '¿Está seguro de eliminar la venta? <br> Esta acción no se puede revertir',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.value) {
            let __params = {};
            components.request_exec_ajax( _LOCAL_URL + "/sales/" + id, 'DELETE', __params );
        }
    });
}
/* ===== End Events Sales Settings ===== */

$("#btn_edit_res_details").on('click', function(){
    $("#btn_edit_res_details").prop('disabled', true);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    let frm_data = $("#frm_edit_details").serializeArray();
    let type_req ='PUT';
    let url_req = '/reservations/'+$("#reservation_id").val();
    $.ajax({
        url: url_req,
        type: type_req,
        data: frm_data,
        success: function(resp) {
            if (resp.success == 1) {
                window.onbeforeunload = null;
                let timerInterval
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Datos de la reserva editados con éxito. Será redirigido en <b></b>',
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
            } else {
                console.log(resp);
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
        $("#btn_edit_res_details").prop('disabled', false);
    });
});

if( __serviceType != null ){
    __serviceType.addEventListener('change', function(event){
        event.preventDefault();        
        if (__serviceType.value == 1) {
            $("#info_return").removeClass('d-none');
        }
    })
}

//FUNCIONALIDAD DE CALENDARIO FORM
if( __serviceDateForm != null ){
    __serviceDateForm.addEventListener('input', function(event) {
        event.preventDefault();        
        __serviceDateRoundForm.min = this.value;
    });
}

/* ===== Start Events Payments Settings ===== */
function getPayment(id){
    $("#btn_new_payment").prop('disabled', true);
    $("#type_form_pay").val(2);
    $("#payment_id").val(id);
    $.ajax({
        url: '/payments/'+id,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#servicePaymentsTypeModal").val(data.payment_method);
            $("#servicePaymentsDescriptionModal").val(data.reference);
            $("#servicePaymentsTotalModal").val(data.total);
            $("#servicePaymentsCurrencyModalPayment").val(data.currency);
            $("#servicePaymentsExchangeModal").val(data.exchange_rate);
            $("#servicePaymentsConciliationModal").val(data.is_conciliated);
            $("#servicePaymentsMessageConciliationModal").val(data.conciliation_comment);
            $("#btn_new_payment").prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function deletePayment(id){
    swal.fire({
        html: '¿Está seguro de eliminar el pago? <br> Esta acción no se puede revertir',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
    }).then((result) => {
        if (result.value) {
            let __params = {};
            components.request_exec_ajax( _LOCAL_URL + "/payments/" + id, 'DELETE', __params );
        }
    });
}
/* ===== End Events Payments Settings ===== */

$("#servicePaymentsCurrencyModalPayment").on('change', function(){
    let currency = $(this).val();
    let reservation_id = $("#reserv_id_pay").val();
    $.ajax({
        url: '/GetExchange/'+reservation_id+'?currency='+currency,
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#servicePaymentsExchangeModal").val(data.exchange_rate);
            $("#operation_pay").val(data.operation);
            $("#btn_new_payment").prop('disabled', false);
        },
        error: function (data) {
            console.log(data);
        }
    });
});

$("#btn_edit_item").on('click', function(){
    $("#btn_edit_item").prop('disabled', true);
    let frm_data = $("#edit_reservation_service").serializeArray();
    let type_req ='PUT';
    let url_req = '/editreservitem/'+$("#item_id_edit").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });
    $.ajax({
        url: url_req,
        type: type_req,
        data: frm_data,
        success: function(resp) {
            if (resp.success == 1) {
                window.onbeforeunload = null;
                let timerInterval
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Datos del servicio editados con éxito. Será redirigido en <b></b>',
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
            } else {
                console.log(resp);
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
        $("#btn_edit_item").prop('disabled', false);
    });
});

function sendArrivalConfirmation() {
    const btn = document.getElementById("btnSendArrivalConfirmation");
    const form = document.getElementById("formArrivalConfirmation");

    btn.disabled = true;

    const formData = new FormData(form);

    Swal.fire({
        title: "Procesando...",
        text: "Por favor, espera mientras se envia la confirmación.", //Realiza la function de HTML en el Swal
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });    

    fetch('/reservations/confirmation/arrival', {
        headers: {
            'X-CSRF-TOKEN': csrfToken // Agregar el token en los headers
        },
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) 
    .then(resp => {
        if (resp.status === 'success') {
            Swal.fire({
                title: '¡Éxito!',
                icon: 'success',
                html: 'La confirmación fue enviada.<b></b>',
                timer: 2500
            }).then(() => {
                btn.disabled = false;
            });

            if (resp.hasOwnProperty('message')) {
                contentMessageConfirmation(resp.message);
            }
        } else {
            throw new Error(resp.message || 'Error desconocido');
        }
    })
    .catch(error => {
        Swal.fire('¡ERROR!', error.message, 'error');
        btn.disabled = false;
    });
}

function sendDepartureConfirmation(event, item_id, destination_id, lang = 'en', type = 'departure', is_round_trip = '0'){
    event.preventDefault();
    $.ajax({
        url: '/reservations/confirmation/departure',
        type: 'POST',
        data: { item_id:item_id, destination_id: destination_id, lang:lang, type:type, is_round_trip:is_round_trip },
        success: function(resp) {
            if (resp.status == 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'La confirmación de regreso fue enviada.<b></b>',
                    timer: 2500
                });

                $("#arrivalConfirmationModal").modal('show');
                __titleModal.innerHTML = ( type == "departure" ? "Confirmación de salida" : ( type == "transfer-pickup" ? "Confirmación de recogida" : "Confirmación de regreso" ) );
                __closeModalFooter.innerHTML = "Cerrar";

                if( resp.hasOwnProperty('message') ){      
                    contentMessageConfirmation(resp.message);              
                }
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )        
    });
}

__closeModalHeader.addEventListener('click', function(){
    contentMessageConfirmation("");
    location.reload();
});

__closeModalFooter.addEventListener('click', function(){
    contentMessageConfirmation("");
    location.reload();
});

function contentMessageConfirmation(message){
    const __messageConfirmation = document.getElementById('messageConfirmation');
    if( message == "" ){
        __formConfirmation.classList.add('d-none');
        __btnSendArrivalConfirmation.classList.add('d-none');
        __titleModal.innerHTML = "";
        __closeModalFooter.innerHTML = "";        
    }
    __messageConfirmation.innerHTML = message;
}

function loadContent() {
    $('#media-listing').load('/reservations/upload/' + rez_id, function(response, status, xhr) {
        if (status == "error") {
            $('#media-listing').html('Error al cargar el contenido');
        }
    });
}

loadContent();

Dropzone.options.uploadForm = {
    maxFilesize: 5, // Tamaño máximo del archivo en MB
    acceptedFiles: 'image/*,.pdf', // Solo permitir imágenes y archivos PDF
    dictDefaultMessage: 'Arrastra el archivo aquí o haz clic para subirlo (Imágenes/PDF)...',
    addRemoveLinks: false,
    autoProcessQueue: false, // Desactivar procesamiento automático para usar SweetAlert
    uploadMultiple: false,
    init: function() {
        const dropzone = this;
        let selectedOption = null; // Variable para almacenar la opción seleccionada

        // Interceptar el evento "addedfile"
        this.on("addedfile", function(file) {
            Swal.fire({
                html: '¿Está seguro de agregar una imagen?',
                icon: 'question',
                inputLabel: 'Selecciona la categoría de la imagen:',
                input: 'select',
                inputOptions: {
                    'GENERAL': 'General',
                    'NOSHOW': 'No se presentó',
                    'CANCELLATION': 'Cancelación',
                    'OPERATION': 'Operación',
                    'REFUND': 'Reembolso'
                },
                inputPlaceholder: 'Seleccione una opción',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false,
                allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                preConfirm: (value) => {
                    if (!value) {
                        Swal.showValidationMessage('Debes seleccionar una categoría de imagen.');
                        return false;
                    }
                    return value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Subiendo imágen...",
                        text: "Por favor, espera mientras se cargan la imágen.", //Realiza la function de HTML en el Swal
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Guardar la opción seleccionada
                    selectedOption = result.value;
                    // Si el usuario confirma, enviar el archivo
                    // Procesar el archivo
                    dropzone.processFile(file);
                    // console.log('Opción seleccionada:', result.value);
                } else {
                    // Si el usuario cancela, eliminar el archivo
                    dropzone.removeFile(file);
                }
            });
        });

        // Añadir el valor seleccionado a los datos enviados
        this.on("sending", function(file, xhr, formData) {
            if (selectedOption) {
                formData.append("type_media", selectedOption); // Agregar la opción seleccionada
            }
        });

        this.on("success", function(file, response) {
            components.removeLoadScreen();
            // Limpiar el área de Dropzone
            this.removeAllFiles(true); // 'true' evita que se activen eventos adicionales, y elimina el archivo
            loadContent();
            location.reload();
        });
        this.on("error", function(file, errorMessage) {
            this.removeAllFiles(true); // 'true' evita que se activen eventos adicionales, y elimina el archivo
            components.proccessResponse(errorMessage);
        });
    }
};

//PERMITE ELIMINAR UNA IMAGEN
$( document ).delegate( ".deleteMedia", "click", function(e) {
    e.preventDefault();
    let id = $(this).data("id");
    let name = $(this).data("name");
    swal.fire({
        html: "¿Está seguro de eliminar el documento? <br> Esta acción no se puede revertir",        
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        allowOutsideClick: false,
        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/reservations/upload/'+id,
                type: 'DELETE',
                data: { id:id, name:name },
                beforeSend: function(){
                    Swal.fire({
                        title: "Confirmando eliminación...",
                        text: "Procesando la eliminación de la imagen.", //Realiza la function de HTML en el Swal
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(resp) {
                    swal.fire({
                        title: 'Documento eliminado',
                        text: 'El documento ha sido eliminado con éxito',
                        icon: 'success',
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC                        
                    });
                    loadContent();
                }
            });
        }
    });
});

function copyPaymentLink(event, code, email, lang){
    event.preventDefault();

    let URL = `https://caribbean-transfers.com/easy-payment?code=${code}&email=${email}`;
    if(lang == "es"){
        URL = `https://caribbean-transfers.com/es/easy-payment?code=${code}&email=${email}`;
    }

    navigator.clipboard.writeText(URL).then(function() {

        Swal.fire({
            title: '¡Éxito!',
            icon: 'success',
            html: `Se ha copiado la URL (${lang}) al porta papeles`,
            timer: 1500,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading()
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                }, 100)
            },
            willClose: () => {
                clearInterval(timerInterval)
            }
        }).then((result) => {
            location.reload();
        })
    }).catch(function(error) {
        console.error('Error al copiar el texto al portapapeles: ', error);
    });    
}

const __site = document.getElementById('serviceSiteReference');
if( __site ){
  actionSite(__site);
  __site.addEventListener('change', function(event){
    event.preventDefault();
    actionSite(__site);    
  });
}

function actionSite(__site){
  const __reference = document.getElementById('serviceClientReference');
  const selectedOption = __site.options[__site.selectedIndex];

  if( selectedOption.getAttribute('data-type') == "AGENCY" ){
    __reference.removeAttribute('readonly');
  }else{
    __reference.setAttribute('readonly', true);
  }
}

// Seleccionamos los enlaces con clase .pdf-lightbox
if( document.querySelectorAll(".pdf-lightbox") != null ){
    document.querySelectorAll(".pdf-lightbox").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault(); // Evita que el enlace se abra normalmente

            // Obtiene la URL del PDF desde el href
            let pdfUrl = this.getAttribute("href");

            // Inserta la URL en el iframe
            document.getElementById("pdf-frame").setAttribute("src", pdfUrl);

            // Muestra el modal
            document.getElementById("pdf-lightbox-modal").style.display = "block";
        });
    });

    // Cerrar el modal al hacer clic en la "X"
    // document.querySelector(".pdf-lightbox-close").addEventListener("click", function () {
    //     document.getElementById("pdf-lightbox-modal").style.display = "none";
    //     document.getElementById("pdf-frame").setAttribute("src", ""); // Limpia el iframe
    // });

    // Cerrar el modal si se hace clic fuera del contenido
    // document.getElementById("pdf-lightbox-modal").addEventListener("click", function (event) {
    //     if (event.target === this) {
    //         this.style.display = "none";
    //         document.getElementById("pdf-frame").setAttribute("src", "");
    //     }
    // });
}