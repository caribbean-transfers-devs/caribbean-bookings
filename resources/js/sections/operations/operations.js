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
    /**
     * ===== Render Table Settings ===== *
     * @param {*} table //tabla a renderizar
    */
    actionTable: function(table, param = ""){
        let buttons = [];
        const _settings = {},
            _buttons = table.data('button');

        if( _buttons != undefined && _buttons.length > 0 ){
            _buttons.forEach(_btn => {
                if( _btn.hasOwnProperty('url') ){
                    _btn.action = function(e, dt, node, config){
                        window.location.href = _btn.url;
                    }
                };
                buttons.push(_btn);
            });
        }

        // _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
        //                 <''tr>
        //                 <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count mb-sm-0 mb-3'i><'dt--pagination'p>>`;
        _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-8 d-flex justify-content-sm-start justify-content-center'l<'dt--pages-count align-self-center'i><'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-4 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                        <''tr>
                        <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pagination'p>>`;
        _settings.deferRender = true;
        _settings.responsive = true;
        _settings.buttons =  _buttons;
        _settings.order = [[ 2, "asc" ]];
        // _settings.order = [];
        _settings.paging = false;
        _settings.oLanguage = {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",             
            "sInfo": "Mostrando _TOTAL_ registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": components.getTranslation("table.search") + "...",
            "sLengthMenu": components.getTranslation("table.results") + " :  _MENU_",
            "oPaginate": { 
                "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', 
                "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' 
            },
        };

        table.DataTable( _settings );
    },
    bsTooltip: function() {
        var bsTooltip = document.querySelectorAll('.bs-tooltip')
        for (let index = 0; index < bsTooltip.length; index++) {
            var tooltip = new bootstrap.Tooltip(bsTooltip[index])
        }
    },
    bsPopover: function() {
        var bsPopover = document.querySelectorAll('.bs-popover');
        for (let index = 0; index < bsPopover.length; index++) {
            var popover = new bootstrap.Popover(bsPopover[index])
        }
    },
    setStatus: function(_status){
        let alert_type = 'btn-secondary';
        switch (_status) {
            case 'PENDING':
                alert_type = 'btn-secondary';
                break;
            case 'COMPLETED':
            case 'OK':
                alert_type = 'btn-success';
                break;
            case 'NOSHOW':
            case 'C':
                alert_type = 'btn-warning';
                break;
            case 'CANCELLED':
                alert_type = 'btn-danger';
                break;
            case 'E':
                alert_type = 'btn-info';
                break;                        
            default:
                alert_type = 'btn-secondary';
                break;
        }
        return alert_type;
    },
    setPreassignment: function(_operation){
        let alert_type = 'btn-success';
        switch (_operation) {
            case 'ARRIVAL':
                alert_type = 'btn-success';
                break;
            case 'DEPARTURE':
                alert_type = 'btn-primary';
                break;
            case 'TRANSFER':
                alert_type = 'btn-info';
                break;
            default:
                alert_type = 'btn-success';
                break;
        }
        return alert_type;                
    },
    isTime: function(hora) {
        // Expresión regular para validar formato HH:MM
        const regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
        return regex.test(hora);
    },
    obtenerHoraActual: function() {
        const ahora = new Date();
        const horas = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        return `${horas}:${minutos}`;
    }
};

if( document.querySelector('.table-rendering') != null ){
    setup.actionTable($('.table-rendering'));
}
setup.bsPopover();
setup.bsTooltip();
components.setValueSelectpicker();
components.formReset();//RESETEA LOS VALORES DE UN FORMULARIO, EN UN MODAL

//CONFIGURACION DE DROPZONE
Dropzone.options.uploadForm = {
    maxFilesize: 5, // Tamaño máximo del archivo en MB
    acceptedFiles: 'image/*,.pdf', // Solo permitir imágenes y archivos PDF
    dictDefaultMessage: 'Arrastra el archivo aquí o haz clic para subirlo (Imágenes/PDF)...',
    addRemoveLinks: false,
    autoProcessQueue: true,
    uploadMultiple: false,
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    init: function() {        
        this.on("success", function(file, response) {
            console.log(response);
            if (response.hasOwnProperty('success') && response.success) {
                Swal.fire({
                    icon: 'success',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    willClose: () => {
                        socket.emit("uploadBookingServer", response.data);
                    }
                })
            }
        });
        this.on("error", function(file, errorMessage) {
            console.log('Error al subir el archivo:', errorMessage);
        });
    }
};

//DECLARACION DE VARIABLES
const from_autocomplete = document.getElementById('from_name'); //* ===== INPUT AUTOCOMPLETE ORIGIN ===== */
const to_autocomplete = document.getElementById('to_name'); //* ===== INPUT AUTOCOMPLETE DESTINATION ===== */
const sold_in_currency_select = document.getElementById('sold_in_currency'); //* ===== SELECT CURRENCY ===== */
const currency_span = document.getElementById('currency_span'); //* ===== LABEL CURRENCY QUOTATION ===== */
const form = document.getElementById('posForm'); //* ===== FORM CREATE SERVICE ===== */
const submitBtn = document.getElementById('submitBtn'); //* ===== BUTTON CREATE SERVICE ===== */

const __add_preassignments = document.querySelectorAll('.add_preassignment'); //* ===== BUTTONS PRE ASSIGNMENT ===== */
const __vehicles = document.querySelectorAll('.vehicles'); //* ===== SELECT VEHICLES ===== */
const __drivers = document.querySelectorAll('.drivers'); //* ===== SELECT DRIVERS ===== */

const __open_modal_customers = document.querySelectorAll('.__open_modal_customer');
const __open_modal_comments = document.querySelectorAll('.__open_modal_comment');
const __title_modal = document.getElementById('filterModalLabel');
const __button_form = document.getElementById('formComment'); //* ===== BUTTON FORM ===== */
const __btn_preassignment = document.getElementById('btn_preassignment') //* ===== BUTTON PRE ASSIGNMENT GENERAL ===== */
// const __btn_addservice = document.getElementById('btn_addservice') //* ===== BUTTON PRE ASSIGNMENT GENERAL ===== */
const __btn_close_operation = document.getElementById('btn_close_operation') //* ===== BUTTON PRE ASSIGNMENT GENERAL ===== */

const __btn_update_status_operations = document.querySelectorAll('.btn_update_status_operation');
const __btn_update_status_bookings = document.querySelectorAll('.btn_update_status_booking');

const __copy_whatsapp = document.querySelector('.copy_whatsapp');
const __copy_history = document.querySelector('.copy_history');
const __copy_data_customer = document.querySelector('.copy_data_customer');

//DEFINIMOS EL SERVIDOR SOCKET QUE ESCUCHARA LAS PETICIONES
const socket = io( (window.location.hostname == '127.0.0.1' ) ? 'http://localhost:4000': 'https://socket-caribbean-transfers.up.railway.app' );
console.log(socket);
socket.on('connection');

if ( document.getElementById('lookup_date') != null ) {
    const picker = new easepick.create({
        element: "#lookup_date",
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10,
    });   
}
function history(){
    setup.bsTooltip();
    const __open_modal_historys = document.querySelectorAll('.__open_modal_history');
    if( __open_modal_historys.length > 0 ){
        __open_modal_historys.forEach(__open_modal_history => {
            __open_modal_history.addEventListener('click', function(){
      
                //DECLARACION DE VARIABLES
                const __type = this.dataset.type;
                const __comment = ( this.dataset.comment != undefined ? this.dataset.comment : '' );
                const __modal = document.getElementById('historyModal');
    
                if (__type == "history") {
                    $.ajax({
                        url: `/operation/history/get`,
                        type: 'GET',
                        data: { code: this.dataset.code },
                        success: function(resp) {
                            if ( resp.success ) {
                                const content = document.getElementById('wrapper_history');
                                content.innerHTML = resp.message;
                                $(__modal).modal('show');
                            }
                        }
                    });
                }else{
                    const content = document.getElementById('wrapper_history');
                    content.innerHTML = __comment;
                    $(__modal).modal('show');
                }
            });
        });
    }    
}

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

//FUNCIONALIDAD DE CAMBIO DE MONEDA
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

//FUNCIONADA PARA EXTRACION DE INFORMACION DE DATOS PARA ENVIAR POR WHATSAPP
$('#zero-config').on('click', '.extract_whatsapp', function() {
    // Obtener la fila en la que se encuentra el botón
    var fila = $(this).closest('tr');

    // Extraer la información de las celdas de la fila
    if( fila.find('td').eq(0).find('button').text() == "ADD" ){
      var identificator = "NO DEFINIDO";
    }else{
      var identificator = fila.find('td').eq(0).find('button').text();
    }    
    var hora = fila.find('td').eq(2).text();
    var cliente = fila.find('td').eq(3).find('span').text();
    var tipo_servicio = fila.find('td').eq(4).text();
    var pax = fila.find('td').eq(5).text();
    var origin = fila.find('td').eq(6).text();
    var destination = fila.find('td').eq(7).text();
    var agency = fila.find('td').eq(8).text();
    var vehicle = ( fila.find('td').eq(9).find('button').length > 0 ? fila.find('td').eq(9).find('button .filter-option .filter-option-inner .filter-option-inner-inner').text() : fila.find('td').eq(9).text() );
    var driver = ( fila.find('td').eq(10).find('button').length > 0 ? fila.find('td').eq(10).find('button .filter-option .filter-option-inner .filter-option-inner-inner').text() : fila.find('td').eq(10).text() );
    var status_operation = fila.find('td').eq(11).find('.btn-group button span').text();
    var time_operation = fila.find('td').eq(12).text();
    var status_booking = fila.find('td').eq(14).find('.btn-group button span').text();
    if( fila.find('td').eq(15).find('a') ){
      var code = fila.find('td').eq(15).find('a').text();
    }else{
      var code = fila.find('td').eq(15).text();
    }
    var unit = fila.find('td').eq(16).text();
    var payment = fila.find('td').eq(17).text();
    var total = fila.find('td').eq(18).text();
    var currency = fila.find('td').eq(19).text();

    // Mostrar la información (puedes realizar otras acciones aquí)
    // console.log('Número:', identificator);
    // console.log('Hora:', hora);
    // console.log('Cliente:', cliente);
    // console.log('Tipo de servicio:', tipo_servicio);
    // console.log('Pax:', pax);
    // console.log('Origen:', origin);
    // console.log('Destino:', destination);
    // console.log('Agencia:', agency);
    // console.log('Unidad:', vehicle);
    // console.log('Conductor:', driver);
    // console.log('Estatus de operación:', status_operation);
    // console.log('Hora de operación:', time_operation);
    // console.log('Estatus de reservación:', status_booking);
    // console.log('Código:', code);
    // console.log('Vehículo:', unit);
    // console.log('Pago:', payment);
    // console.log('Total:', total + ' ' + currency);

    // let message = 'Número: ' + identificator + '</p> \n ' +
    //               'Código: ' + code + '</p> \n ' +
    //               'Hora: ' + hora + '</p> \n ' +
    //               'Cliente: ' + cliente + '</p> \n ' +
    //               'Tipo de servicio: ' + tipo_servicio + '</p> \n ' +
    //               'Pax: ' + pax + '</p> \n ' +
    //               'Origen: ' + origin + '</p> \n ' +
    //               'Destino: ' + destination + '</p> \n ' +
    //               'Agencia: ' + agency + '</p> \n ' +
    //               'Unidad: ' + vehicle + '</p> \n ' +
    //               'Conductor: ' + driver + '</p> \n ' +
    //               'Estatus de operación: ' + status_operation + '</p> \n ' +
    //               'Hora de operación: ' + time_operation + '</p> \n ' +
    //               'Estatus de reservación: ' + status_booking + '</p> \n ' +                  
    //               'Vehículo: ' + unit + '</p> \n ' +
    //               'Pago: ' + payment + '</p> \n ' +
    //               'Total: ' + total + ' ' + currency;

   let message =  '<p class="m-0">Número: ' + identificator + '</p> \n ' +
                  '<p class="m-0">Código: ' + code + '</p> \n ' +
                  '<p class="m-0">Hora: ' + hora + '</p> \n ' +
                  '<p class="m-0">Cliente: ' + cliente + '</p> \n ' +
                  '<p class="m-0">Tipo de servicio: ' + tipo_servicio + '</p> \n ' +
                  '<p class="m-0">Pax: ' + pax + '</p> \n ' +
                  '<p class="m-0">Origen: ' + origin + '</p> \n ' +
                  '<p class="m-0">Destino: ' + destination + '</p> \n ' +
                  '<p class="m-0">Agencia: ' + agency + '</p> \n ' +
                  '<p class="m-0">Unidad: ' + vehicle + '</p> \n ' +
                  '<p class="m-0">Conductor: ' + driver + '</p> \n ' +
                  '<p class="m-0">Estatus de operación: ' + status_operation + '</p> \n ' +
                  '<p class="m-0">Hora de operación: ' + time_operation + '</p> \n ' +
                  '<p class="m-0">Estatus de reservación: ' + status_booking + '</p> \n ' +                  
                  '<p class="m-0">Vehículo: ' + unit + '</p> \n ' +
                  '<p class="m-0">Pago: ' + payment + '</p> \n ' +
                  '<p class="m-0">Total: ' + total + ' ' + currency;    

    document.getElementById('wrapper_whatsApp').innerHTML = message;

    // let text = "https://api.whatsapp.com/send?phone=5219982127069&text=" + decodeURIComponent(message);
    // window.location.href = text;
});

//ABRE EL MODAL PARA PODER AGREGAR UN NUEVO SERVICIO
// if ( __btn_addservice != null ) {
//   __btn_addservice.addEventListener('click', function(event) {
//       event.preventDefault();
//       $("#operationModal").modal('show');
//   });
// }

if( __btn_preassignment != null ){
  __btn_preassignment.addEventListener('click', function() {
      swal.fire({
          text: '¿Está seguro de pre-asignar los servicios?',
          icon: 'warning',
          inputLabel: "Selecciona la fecha que pre-asignara",
          input: "date",
          inputValue: document.getElementById('lookup_date').value,
          inputValidator: (result) => {
              return !result && "Selecciona un fecha";
          },
          didOpen: () => {
              const today = (new Date()).toISOString();
              Swal.getInput().min = today.split("T")[0];
          },
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar'
      }).then((result) => {
          if(result.isConfirmed == true){
              $.ajax({
                  type: "POST",
                  url: _LOCAL_URL + "/operation/preassignments",
                  data: JSON.stringify({ date: result.value }), // Datos a enviar al servidor                            
                  dataType: "json",
                  contentType: 'application/json; charset=utf-8',   
                  beforeSend: function(){
                      components.loadScreen();
                  },
                  success: function(response) {
                      // Manejar la respuesta exitosa del servidor
                      Swal.fire({
                          icon: 'success',
                          text: response.message,
                          showConfirmButton: false,
                          timer: 1500,
                      });
                  }
              });
          }
      });               
  });
}

if( __btn_close_operation != null ){
    __btn_close_operation.addEventListener('click', function() {
        swal.fire({
            text: '¿Está seguro que desea cerrar la operación',
            icon: 'warning',
            inputLabel: "Selecciona la fecha de operación que desea cerrar",
            input: "date",
            inputValue: document.getElementById('lookup_date').value,
            inputValidator: (result) => {
                return !result && "Selecciona un fecha";
            },
            didOpen: () => {
                const today = (new Date()).toISOString();
                Swal.getInput().min = today.split("T")[0];
            },
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if(result.isConfirmed == true){
                $.ajax({
                    type: "POST",
                    url: _LOCAL_URL + "/operation/closeOperation",
                    data: JSON.stringify({ date: result.value }), // Datos a enviar al servidor                            
                    dataType: "json",
                    contentType: 'application/json; charset=utf-8',   
                    beforeSend: function(){
                        components.loadScreen();
                    },
                    success: function(response) {
                        // Manejar la respuesta exitosa del servidor
                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            willClose: () => {
                                socket.emit("addPreassignmentServer", response.data);
                            }                            
                        });
                    }
                });
            }
        });               
    });
}

if (__add_preassignments.length > 0) {
  __add_preassignments.forEach(__add_preassignment => {
      __add_preassignment.addEventListener('click', function(event) {
          event.preventDefault();                    
          const { id, reservation, item, operation, service, type } = this.dataset;
          const __date = document.getElementById('lookup_date');
          swal.fire({
              text: '¿Está seguro de pre-asignar el servicio ?',
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Aceptar',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if(result.isConfirmed == true){
                $.ajax({
                    url: _LOCAL_URL + "/operation/preassignment",
                    type: 'PUT',
                    data: { date : __date.value, id : id, reservation : reservation, reservation_item : item, operation : operation, service : service, type : type },
                    beforeSend: function() {
                        components.loadScreen();
                    },
                    success: function(response) {
                        // Manejar la respuesta exitosa del servidor
                        Swal.fire({
                            icon: "success",
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                            willClose: () => {
                                socket.emit("addPreassignmentServer", response.data);
                            }
                        });
                    }
                });
              }
          });
      });
  });
}

if (__vehicles.length > 0) {
  __vehicles.forEach(__vehicle => {
      __vehicle.addEventListener('change', function(event) {
          event.preventDefault();                    
          const { id, reservation, item, operation, service, type } = this.dataset;
          swal.fire({
              inputLabel: "Ingresa el costo operativo",
              inputPlaceholder: "Ingresa el costo operativo",
              input: "text",
              icon: 'info',
              showCancelButton: true,
              confirmButtonText: 'Aceptar',
              cancelButtonText: 'Cancelar',
              // showLoaderOnConfirm: true,
              preConfirm: async (login) => {
                  try {
                      if (login == "") {
                          return Swal.showValidationMessage(`
                              "Por favor, ingresa el costo operativo"
                          `);
                      }
                  } catch (error) {
                      Swal.showValidationMessage(`
                          Request failed: ${error}
                      `);
                  }
              },
              // allowOutsideClick: () => !Swal.isLoading()
          }).then((result) => {
              if(result.isConfirmed == true){
                  $.ajax({
                      url: `/operation/vehicle/set`,
                      type: 'PUT',
                      data: { id : id, reservation : reservation, reservation_item : item, operation : operation, service : service, vehicle_id : __vehicle.value, type : type, operating_cost : result.value },
                      beforeSend: function() {
                          components.loadScreen();
                      },
                      success: function(resp) {
                          if( resp.success ){
                              Swal.fire({
                                  icon: "success",
                                  text: resp.message,
                                  showConfirmButton: false,
                                  timer: 1500,
                                  willClose: () => {
                                      socket.emit("setVehicleReservationServer", resp.data);
                                  }
                              });
                          }                            
                      }
                  });
              }
          });
      });
  });
}

if (__drivers.length > 0) {
  __drivers.forEach(__driver => {
      __driver.addEventListener('change', function() {
          const { id, reservation, item, operation, service, type } = this.dataset;
          $.ajax({
              url: `/operation/driver/set`,
              type: 'PUT',
              data: { id : id, reservation : reservation, reservation_item : item, operation : operation, service : service, driver_id : __driver.value, type : type },
              beforeSend: function() {
                  components.loadScreen();
              },
              success: function(resp) {
                  if( resp.success ){
                      Swal.fire({
                          icon: "success",
                          text: resp.message,
                          showConfirmButton: false,
                          timer: 1500,
                          willClose: () => {
                              socket.emit("setDriverReservationServer", resp.data);
                          }
                      });                                
                  }
              }
          });
      });
  });
}

if (__btn_update_status_operations.length > 0) {
  __btn_update_status_operations.forEach(__btn_update_status_operation => {
      __btn_update_status_operation.addEventListener('click', function(event) {
          event.preventDefault();
          let _settings = {};
          const { operation, service, type, status, item, booking, key } = this.dataset;
          console.log(operation, service, status, item, booking, key);
          _settings.text = "¿Está seguro de actualizar el estatus de operación?";
          _settings.icon = 'warning';
          _settings.showCancelButton = true;
          _settings.confirmButtonText = 'Aceptar';
          _settings.cancelButtonText = 'Cancelar';
          if (status == "OK") {
              _settings.inputLabel = "Ingresa la hora de abordaje";
              _settings.input = "time";
              _settings.inputValue = setup.obtenerHoraActual();
              _settings.inputValidator = (result) => {
                  return !result && "Selecciona un horario";
              }
          }
          swal.fire(_settings).then((result) => {
              if(result.isConfirmed == true){
                  $.ajax({
                      url: `/operation/status/operation`,
                      type: 'PUT',
                      data: { id: key, rez_id: booking, item_id: item, operation: operation, service: service, type: type, status: status, time: ( setup.isTime(result.value) ? result.value : "" ) },
                      beforeSend: function() {
                          components.loadScreen();
                      },
                      success: function(resp) {
                          Swal.fire({
                              icon: 'success',
                              text: 'Servicio actualizado con éxito.',
                              showConfirmButton: false,
                              timer: 1500,
                              willClose: () => {
                                  socket.emit("updateStatusOperationServer", resp.data);
                              }
                          });
                      }
                  });
              }
          });
      });
  });
}

if (__btn_update_status_bookings.length > 0) {
  __btn_update_status_bookings.forEach(__btn_update_status_booking => {
      __btn_update_status_booking.addEventListener('click', function(event) {
          event.preventDefault();
          const { operation, service, type, status, item, booking, key } = this.dataset;
          swal.fire({
              text: "¿Está seguro de actualizar el estatus de reservación?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Aceptar',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if(result.isConfirmed == true){
                  const __vehicle = document.getElementById('vehicle_id_' + key);
                  const __driver = document.getElementById('driver_id_' + key);
                  console.log(__vehicle, __driver);
                  
                  if ( ( __vehicle.value == 0 && __driver.value == 0 ) || ( __vehicle.value == 0 ) || ( __driver.value == 0 ) ) {
                      Swal.fire({
                          text: 'Valida la seleccion de unidad y conductor.',
                          icon: 'error',
                          showConfirmButton: false,
                          timer: 1500,
                      });                        
                  }else{
                      $.ajax({
                          url: `/operation/status/booking`,
                          type: 'PUT',
                          data: { id: key, rez_id: booking, item_id: item, operation: operation, service: service, type: type, status: status },
                          beforeSend: function() {
                              components.loadScreen();
                          },
                          success: function(resp) {
                              Swal.fire({
                                  title: '¡Éxito!',
                                  icon: 'success',
                                  html: 'Servicio actualizado con éxito.',
                                  showConfirmButton: false,
                                  timer: 1500,
                                  willClose: () => {
                                      socket.emit("updateStatusBookingServer", resp.data);
                                  }
                              });
                          }
                      });
                  }
              }
          });
      });
  });
}

history();

if( __open_modal_customers.length > 0 ){
    __open_modal_customers.forEach(__open_modal_customer => {
        __open_modal_customer.addEventListener('click', function(){
  
            //DECLARACION DE VARIABLES
            const __modal = document.getElementById('customerDataModal');
  
            $.ajax({
                url: `/operation/data/customer/get`,
                type: 'GET',
                data: { code: this.dataset.code },
                success: function(resp) {
                    if ( resp.success ) {
                        let message =  '<p class="m-0">Nombre: ' + resp.data.client_first_name + ' ' + resp.data.client_last_name + '</p> \n ' +
                        '<p class="m-0">Correo: ' + resp.data.client_email + '</p> \n ' +
                        '<p class="m-0">Teléfono: ' + resp.data.client_phone + '</p> \n ';

                        const content = document.getElementById('wrapper_data_customer');
                        content.innerHTML = message;
                        $(__modal).modal('show');
                    }                                        
                }
            });
        });
    });
}

//ACCION PARA ABRIR MODAL PARA AÑADIR UN COMENTARIO
if( __open_modal_comments.length > 0 ){
  __open_modal_comments.forEach(__open_modal_comment => {
      __open_modal_comment.addEventListener('click', function(){

          //DECLARACION DE VARIABLES
          const __modal = document.getElementById('messageModal');
          const __title_modal = document.getElementById('messageModalLabel');
          const __form_label = __modal.querySelector('.form-label');

          //SETEAMOS VALORES EN EL MODAL
        //   __title_modal.innerHTML = ( this.dataset.status == 0 ? "Agregar comentario" : "Editar comentario" );
          __form_label.innerHTML = ( this.dataset.status == 0 ? "Ingresa el comentario" : "Editar el comentario" );
          document.getElementById('id_item').value = this.dataset.id;
          document.getElementById('code_item').value = this.dataset.code;
          document.getElementById('operation_item').value = this.dataset.operation;
          document.getElementById('type_item').value = this.dataset.type;

          document.getElementById('id').value = this.dataset.id;
          document.getElementById('reservation_id').value = this.dataset.reservation;
          document.getElementById('reservation_item').value = this.dataset.code;

          if (this.dataset.status == 1) {
              $.ajax({
                  url: `/operation/comment/get`,
                  type: 'GET',
                  data: { item_id: this.dataset.code, operation: this.dataset.operation, type: this.dataset.type },
                  // beforeSend: function() {
                  //     components.loadScreen();
                  // },
                  success: function(resp) {
                      document.getElementById('comment_item').value = resp.message;
                      $(__modal).modal('show');
                  }
              });
          }else{
              $(__modal).modal('show');
          }
      });
  });
}

//ACCION DE FORMULARIO
__button_form.addEventListener('submit', function (event) {
  event.preventDefault();
  let _params = components.serialize(this,'object');
  if( _params != null ){
      $.ajax({
          type: "POST", // Método HTTP de la solicitud
          url: _LOCAL_URL + "/operation/comment/add", // Ruta del archivo PHP que manejará la solicitud
          data: JSON.stringify(_params), // Datos a enviar al servidor
          dataType: "json", // Tipo de datos que se espera en la respuesta del servidor
          contentType: 'application/json; charset=utf-8',
          beforeSend: function(){
              components.loadScreen();
          },
          success: function(response) {
              // Manejar la respuesta exitosa del servidor
              $("#messageModal").modal('hide');
              Swal.fire({
                  icon: 'success',
                  text: response.message,
                  showConfirmButton: false,
                  timer: 1500,
                  willClose: () => {
                      socket.emit("addCommentServer", response.data);
                  }
              })
          }
      });
  }else{
      event.stopPropagation();
      components.sweetAlert({"status": "error", "message": "No se definieron parametros"});
  }
});

if ( __copy_whatsapp != null ) {
    __copy_whatsapp.addEventListener('click', function(){
        // Obtiene el div por su ID
        var div = document.getElementById('wrapper_whatsApp');
        // console.log(div);
        // Obtiene el contenido del div y elimina los espacios
        // var contenido = div.textContent.replace(/\s+/g, '');
        var contenido = div.textContent;
        // Usa la API del portapapeles para copiar el contenido
        navigator.clipboard.writeText(contenido).then(function() {
            // Notifica al usuario que el contenido se ha copiado
            // alert('Contenido copiado: ' + contenido);
        }, function(err) {
            // Notifica al usuario en caso de error
            console.error('No se pudo copiar el contenido: ', err);
        });
    });
}

if ( __copy_history != null ) {
    __copy_history.addEventListener('click', function(){
        // Obtiene el div por su ID
        var div = document.getElementById('wrapper_history');
        // console.log(div);
        // Obtiene el contenido del div y elimina los espacios
        // var contenido = div.textContent.replace(/\s+/g, '');
        var contenido = div.textContent;
        // Usa la API del portapapeles para copiar el contenido
        navigator.clipboard.writeText(contenido).then(function() {
            // Notifica al usuario que el contenido se ha copiado
            // alert('Contenido copiado: ' + contenido);
        }, function(err) {
            // Notifica al usuario en caso de error
            console.error('No se pudo copiar el contenido: ', err);
        });
    });    
}

if ( __copy_data_customer != null ) {
    __copy_data_customer.addEventListener('click', function(){
        // Obtiene el div por su ID
        var div = document.getElementById('wrapper_data_customer');
        // console.log(div);
        // Obtiene el contenido del div y elimina los espacios
        // var contenido = div.textContent.replace(/\s+/g, '');
        var contenido = div.textContent;
        // Usa la API del portapapeles para copiar el contenido
        navigator.clipboard.writeText(contenido).then(function() {
            // Notifica al usuario que el contenido se ha copiado
            // alert('Contenido copiado: ' + contenido);
        }, function(err) {
            // Notifica al usuario en caso de error
            console.error('No se pudo copiar el contenido: ', err);
        });
    });    
}

//FUNCIONALIDAD QUE RECARGA LA PAGINA, CUANDO ESTA DETACTA INACTIVIDAD POR 5 MINUTOS
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

window.addEventListener('scroll', function() {
  var table = document.getElementById('zero-config');
  var thead = table.querySelector('thead');
  var offset = table.getBoundingClientRect().top;

  if (window.scrollY > offset) {
    thead.classList.add('fixed-header');
  } else {
    thead.classList.remove('fixed-header');
  }
});

//BOTONES 
if( document.getElementById('btn_dowload_operation') != null ){
    document.getElementById('btn_dowload_operation').addEventListener('click', function() {
        let date = document.getElementById('lookup_date').value;
        let url = '/operation/board/exportExcel?date=' + date ;
        // console.log(url);
        
        components.loadScreen();

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            },
        })
        .then(response => response.blob())
        .then(blob => {
            components.removeLoadScreen();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'spam_'+ date +'.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            components.removeLoadScreen();
            console.error('Error:', error);
        });
    });
}

if( document.getElementById('btn_dowload_operation_comission') != null ){
    document.getElementById('btn_dowload_operation_comission').addEventListener('click', function() {
        let date = document.getElementById('lookup_date').value;
        let url = '/operation/board/exportExcelCommission?date=' + date ;
        components.loadScreen();

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            },
        })
        .then(response => response.blob())
        .then(blob => {
            components.removeLoadScreen();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'spam_'+ date +'.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            components.removeLoadScreen();
            console.error('Error:', error);
        });
    });
}

//EVENTOS SOCKET IO, ESCUCHAN DE LADO DEL CLIENTE
socket.on("addPreassignmentClient", function(data){
    console.log("asignacion");
    console.log(data);
    //DECLARACION DE VARIABLES
    const __btn_preassignment = document.getElementById('btn_preassignment_' + data.item);
    if( __btn_preassignment != null ){
        const __Row = ( __btn_preassignment != null ? components.closest(__btn_preassignment, 'tr') : null );
        const __Cell = ( __Row != null ? __Row.querySelector('td:nth-child(1)') : "" );
        console.log(__btn_preassignment, __Row, __Cell);
        __btn_preassignment.classList.remove('btn-danger');
        __btn_preassignment.classList.add(setup.setPreassignment(data.operation));
        __btn_preassignment.innerHTML = data.value;
    }

    Snackbar.show({
        text: data.message,
        duration: 5000, 
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("setVehicleReservationClient", function(data){
    console.log("nueva asignación de unidad");
    console.log(data);
    //DECLARACION DE CONSTANTES
    const __Row = document.getElementById('item-' + data.item);//ROW ES EL TR, DONDE ESTAMOS TRABAJANDO CON EL SELECT DEL VEHICULO
    const __CellVehicle = ( __Row != null ? __Row.querySelector('td:nth-child(10)') : null );//ES LA CELDA DONDE SETEAMOS O IMPRIMIMOS EL VALOR DE VEHICULO
    const __select_vehicle = document.getElementById('vehicle_id_' + data.item);
    const __CellCost = ( __Row != null ? __Row.querySelector('td:nth-child(14)') : null );//ES LA CELDA DONDE SETEAMOS O IMPRIMIMOS EL COSTO OPERATIVO
    
    ( __CellVehicle != null ?  __CellVehicle.dataset.order = data.value : "" );
    ( __CellVehicle != null ?  __CellVehicle.dataset.name = data.name : "" );
    ( __select_vehicle == null && __CellVehicle != null ?  __CellVehicle.innerHTML = data.name : "" );
    
    ( __select_vehicle != null ? __select_vehicle.value = data.value : "" );
    ( __select_vehicle != null ? $('#vehicle_id_' + data.item).selectpicker('val', data.value) : "" );
    
    ( __CellCost != null ? __CellCost.innerHTML = data.cost : "" );
    
    console.log(__select_vehicle, __Row, __CellVehicle, __CellCost);
    Snackbar.show({ 
        text: data.message, 
        duration: 5000, 
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("setDriverReservationClient", function(data){
    console.log("nueva asignación de conductor");
    console.log(data);
    //DECLARACION DE CONSTANTES
    const __Row = document.getElementById('item-' + data.item);//ROW ES EL TR, DONDE ESTAMOS TRABAJANDO CON EL SELECT DEL CONDUCTOR
    const __CellDriver = ( __Row != null ? __Row.querySelector('td:nth-child(11)') : "" );
    const __select_driver = document.getElementById('driver_id_' + data.item);
        
    ( __CellDriver != null ?  __CellDriver.dataset.order = data.value : "" );
    ( __CellDriver != null ?  __CellDriver.dataset.name = data.name : "" );
    ( __select_driver == null && __CellDriver != null ?  __CellDriver.innerHTML = data.name : "" );

    ( __select_driver != null ? __select_driver.value = data.value : "" );
    ( __select_driver != null ? $('#driver_id_' + data.item).selectpicker('val', data.value) : "" );

    console.log(__select_driver, __Row, __CellDriver);
    Snackbar.show({ 
        text: data.message, 
        duration: 5000, 
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("updateStatusOperationClient", function(data){
    console.log("operación");
    console.log(data);
    //DECLARACION DE CONSTANTES
    const __Row = document.getElementById('item-' + data.item);//ROW ES EL TR, DONDE ESTAMOS TRABAJANDO CON EL SELECT DEL ESTATUS DE OPERACIÓN
    const __CellStatusOperation = ( __Row != null ? __Row.querySelector('td:nth-child(12)') : "" );
    const __status_operation = document.getElementById('optionsOperation' + data.item);
    const __CellTime = ( __Row != null ? __Row.querySelector('td:nth-child(13)') : "" );
            
    ( __status_operation != null ? __status_operation.classList.remove('btn-secondary', 'btn-success', 'btn-warning', 'btn-danger') : "" );
    ( __status_operation != null ? __status_operation.classList.add(setup.setStatus(data.value)) : "" );
    ( __status_operation != null ? __status_operation.querySelector('span').innerText = data.value : "" );

    ( __CellTime != null ? __CellTime.innerHTML = data.time : "" );

    console.log(__status_operation, __Row, __CellStatusOperation, __CellTime);
    Snackbar.show({ 
        text: data.message,
        duration: 5000, 
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("updateStatusBookingClient", function(data){
    console.log("reservación");
    console.log(data);
    //DECLARACION DE VARIABLES
    const __Row = document.getElementById('item-' + data.item);//ROW ES EL TR, DONDE ESTAMOS TRABAJANDO CON EL SELECT DEL ESTATUS DE RESERVACIÓN
    const __CellStatusBooking = ( __Row != null ? __Row.querySelector('td:nth-child(15)') : "" );
    const __status_booking = document.getElementById('optionsBooking' + data.item);
        
    ( __status_booking != null ? __status_booking.classList.remove('btn-secondary', 'btn-success', 'btn-warning', 'btn-danger') : "" );
    ( __status_booking != null ? __status_booking.classList.add(setup.setStatus(data.value)) : "" );
    ( __status_booking != null ? __status_booking.querySelector('span').innerText = data.value : "" );

    console.log(__status_booking, __Row, __CellStatusBooking);
    Snackbar.show({
        text: data.message,
        duration: 5000, 
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("addCommentClient", function(data){
    console.log("comentario");
    console.log(data);

    //DECLARACION DE VARIABLES
    const __btn_comment = document.getElementById('btn_add_modal_' + data.item);
    ( __btn_comment != null ?  __btn_comment.dataset.status = data.status : "" );
    const __comment_new = document.getElementById('comment_new_' + data.item);
    __comment_new.innerHTML = '<div class="btn btn-primary btn_operations __open_modal_history bs-tooltip" title="Ver mensaje de operaciones" data-type="comment" data-comment="'+ data.value +'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg></div>'

    history();
    Snackbar.show({
        text: data.message,
        duration: 5000,
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("uploadBookingClient", function(data){
    console.log("upload");
    console.log(data);

    //DECLARACION DE VARIABLES
    const __comment_new = document.getElementById('upload_new_' + data.item);
    __comment_new.innerHTML = '<div class="btn btn-primary btn_operations bs-tooltip" title="Esta reservación tiene imagenes"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></div>'

    setup.bsTooltip();
    Snackbar.show({
        text: data.message,
        duration: 5000,
        pos: 'top-right',
        actionTextColor: '#fff',
        backgroundColor: '#2196f3'
    });
});

socket.on("addServiceClient", function(data){
    console.log("nuevo servicio");
    console.log(data);
    //DECLARACION DE VARIABLES
    const __btn_comment = document.getElementById('btn_add_modal_' + data.item);
    const __permission = document.getElementById('permission_reps');
    if (__permission == null) {
        if( data.success ){
            if( data.today ){
                Swal.fire({
                    text: data.message,
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: "Confirmar recargar pagina",
                    denyButtonText: "Cancelar"
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        location.reload();
                    } else if (result.isDenied) {
                    }
                });
            }else{
                Snackbar.show({
                    text: data.message,
                    duration: 5000, 
                    pos: 'top-right',
                    actionTextColor: '#fff',
                    backgroundColor: '#2196f3'
                });
            }
        }   
    }
});