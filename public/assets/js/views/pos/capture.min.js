var from_autocomplete = document.getElementById('from_name');
var to_autocomplete = document.getElementById('to_name');

document.addEventListener('DOMContentLoaded', function() {
    const formaDePagoSelect = document.getElementById('payment_method');
    const clipContainer = document.getElementById('clip_container');
    const terminalSelect = document.getElementById('terminal');
    const form = document.getElementById('posForm');
    const submitBtn = document.getElementById('submitBtn');
    const sold_in_currency_select = document.getElementById('sold_in_currency');
    const currency_span = document.getElementById('currency_span');
    const addPaymentBtn = document.getElementById('addPayment');
    const total = document.getElementById('total');
    const openPaymentModalBtn = document.getElementById('openPaymentModal');
    const isRoundTripSelect = document.getElementById('is_round_trip');
    const tipoCambioSelect = document.getElementById('tipo_cambio_select');

    openPaymentModalBtn.addEventListener('click', (e) => {
        e.preventDefault();

        $('#payment_method').val('CASH').trigger('change');
        $('#reference_container').hide();
        $('#clip_container').hide();
        $('#paid_in_currency').val( $(sold_in_currency_select).val() ).prop('disabled', false);
    });

    sold_in_currency_select.addEventListener('change', (e) => {
        currency_span.innerText = `(${e.target.value})`;
        $('.total-currency').text(e.target.value);
        $('#paid_in_currency').val(e.target.value).trigger('change');
    })

    tipoCambioSelect?.addEventListener('change', (e) => {
        if( $(tipoCambioSelect).val() === '1' ) $('#tipo_cambio_container').show();
        else $('#tipo_cambio_container').hide();
    })

    isRoundTripSelect.addEventListener('change', (e) => {
        if( $(isRoundTripSelect).val() === '1' ) $('#departure_date_container').show();
        else $('#departure_date_container').hide();
    })

    formaDePagoSelect.addEventListener('change', (e) => {
        if (e.target.value === 'CASH') {
            clipContainer.style.display = "none";
            $('#paid_in_currency').prop('disabled', false);
            $('#reference_container').hide();
            return;
        }

        clipContainer.style.display = "block";
        $('#paid_in_currency').val('MXN').prop('disabled', true).trigger('change');
        $('#reference_container').show();
    });

    terminalSelect.addEventListener('change', () => {
        const number_of_rows = $('#payments_table tbody').children().length;
        if( number_of_rows === 0 ) return;

        $('#payments_table tbody').html('');
        $('#previous_total').text('0');
        $('.total_remaining').text( $('#total').val() );

        $('#payments_table').hide();
        $('#sold_in_currency').prop('disabled', false);
        $('#total').prop('disabled', false);
    });

    total.addEventListener('input', (e) => {
        const value = Number(e.target.value);

        if( value > 0 ) $('.payment-section').show();
        else $('.payment-section').hide();

        $('.total_remaining').text(value);
    })

    createVendor.addEventListener('click', (e) => {
        e.preventDefault();

        $('#title').text('Crear vendedor');
        $('#submitVendorBtn').text('Crear');
        
        $('#id').val('');
        $('#name').val('');
        $('#email').val('');
        $('#phone').val('');
        $('#status').val('1');

        $('#vendorModal').modal('show');
    });
    
    const assignDeleteRowEvent = () => {
        $('#payments_table button').last().click(function (e) {
            e.preventDefault();

            const total_to_pay = Number($('#total').val());
            const payment = Number($(e.target).parent().parent().find('.payment').text());
            const origin_currency = $(e.target).parent().parent().find('.currency').text();
            const destination_currency = $('#sold_in_currency').val();
            const terminal = $('#terminal').val();
            const custom_currency_exchange = Number($(e.target).parent().parent().find('.custom_currency_exchange').val());

            const currency_exchange = currency_exchange_data.find(currency_exchange => (
                currency_exchange.origin == origin_currency &&
                currency_exchange.destination == destination_currency &&
                currency_exchange.terminal == terminal
            ));

            let total;

            if( custom_currency_exchange ) {
                total = payment * custom_currency_exchange;
            }
            else {
                if( currency_exchange.operation === 'multiplication' ) total = payment * currency_exchange.exchange_rate;
                else total = total = payment / currency_exchange.exchange_rate;
            }

            const previous_total = Number($('#previous_total').text());
            total = previous_total - total;
            total = total.toFixed(2);
            const total_remaining = (total_to_pay - total).toFixed(2);

            $('#previous_total').text(total);
            $('.total_remaining').text(total_remaining);

            if( total >= total_to_pay ) {
                $('.color-total-container').addClass('success');
                $('.color-total-container').removeClass('red');
            }
            else {
                $('.color-total-container').addClass('red');
                $('.color-total-container').removeClass('success');
            }

            $(e.target).parent().parent().remove();

            const number_of_rows = $('#payments_table tbody').children().length;

            if( number_of_rows === 0 ) {
                $('#payments_table').hide();
                $('#sold_in_currency').prop('disabled', false);
                $('#total').prop('disabled', false);
            }
        })
        

    }

    addPaymentBtn.addEventListener('click', () => {
        const $alert = $('#addPaymentModal .alert-danger');
        const payment = Number($('#payment').val());
        const total_to_pay = Number($('#total').val());
        let custom_currency_exchange = 0;
        $alert.hide();

        if( !payment || payment <= 0 ) {
            $alert.text('Escribe una cantidad correcta');
            return $alert.show();
        }

        if( tipoCambioSelect && $(tipoCambioSelect).val() === '1' ) {
            if( Number($('#tipo_cambio').val()) <= 0 ) {
                $alert.text('Escribe el tipo de cambio');
                return $alert.show();
            }
            custom_currency_exchange = Number($('#tipo_cambio').val());
        }
        
        const payment_method = $('#payment_method').val();
        const reference = payment_method === 'CARD' ? $('#reference').val() : '';
        const clip_id = $('#clip_id').val();
        const origin_currency = $('#paid_in_currency').val();
        const destination_currency = $('#sold_in_currency').val();
        const terminal = $('#terminal').val();

        if( payment_method === 'CARD' && reference.length < 3 ) {
            $alert.text('Escribe la referencia de pago. Mínimo 4 caracteres');
            return $alert.show();
        }

        const currency_exchange = currency_exchange_data.find(currency_exchange => (
            currency_exchange.origin == origin_currency &&
            currency_exchange.destination == destination_currency &&
            currency_exchange.terminal == terminal
        ));

        if( !custom_currency_exchange && !currency_exchange ) {
            $alert.text(`Lo sentimos, no se encontró la conversión de moneda de ${origin_currency} -> ${destination_currency} para la Terminal ${terminal.replace('T', '')}. Quizá tengas que añadir este caso, o pedirle a algún administrador que lo haga`);
            return $alert.show();
        }

        let total;
        if( custom_currency_exchange ) {
            total = payment * custom_currency_exchange;
        }
        else {
            if( currency_exchange.operation === 'multiplication' ) total = payment * currency_exchange.exchange_rate;
            else total = total = payment / currency_exchange.exchange_rate;
        }

        const previous_total = Number($('#previous_total').text());
        total = previous_total + total;
        total = total.toFixed(2);
        const total_remaining = (total_to_pay - total).toFixed(2);

        $('#previous_total').text(total);
        $('.total_remaining').text(total_remaining);

        const number_of_rows = $('#payments_table tbody').children().length;
        
        const new_row = `
            <tr>
                <td class="payment">${payment}</td>
                <td class="currency">${origin_currency}</td>
                <td class="reference">${reference || 'No aplica'}</td>
                <td align="center">
                    <button class="btn btn-danger btn-sm">Eliminar</button>
                </td>

                <input type="hidden" name="reference_${number_of_rows}" value="${reference}">
                <input type="hidden" name="payment_method_${number_of_rows}" value="${payment_method}">
                <input type="hidden" name="clip_id_${number_of_rows}" value="${clip_id}">
                <input type="hidden" name="payment_${number_of_rows}" value="${payment}">
                <input type="hidden" name="currency_${number_of_rows}" value="${origin_currency}">
                
                <input type="hidden" class="custom_currency_exchange" name="custom_currency_exchange_${number_of_rows}" value="${custom_currency_exchange}">
            </tr>
        `;
        
        $('#payments_table tbody').append(new_row);
        $('#payments_table').show();

        assignDeleteRowEvent();
        
        if( total >= total_to_pay ) {
            $('.color-total-container').addClass('success');
            $('.color-total-container').removeClass('red');
        }
        else {
            $('.color-total-container').addClass('red');
            $('.color-total-container').removeClass('success');
        }

        $('#sold_in_currency').prop('disabled', true);
        $('#total').prop('disabled', true);

        $('#reference').val("");
        $('#payment').val("");
        $('#addPaymentModal').modal('hide');
    })

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const number_of_rows = $('#payments_table tbody').children().length;

        if( number_of_rows === 0 ) return Swal.fire({
            title: 'Faltan campos por rellenar',
            icon: 'warning',
            html: 'Tienes que agregar al menos 1 pago',
            timer: 5000,
        });

        submitBtn.disabled = true;
        submitBtn.innerText = 'Cargando...';

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            }
        });

        $('#sold_in_currency').prop('disabled', false);
        $('#total').prop('disabled', false);

        let frm_data = $("#posForm").serializeArray();
        let type_req = 'POST';
        let url_req = '/punto-de-venta/capture/create';

        frm_data.push({name: 'number_of_payments', value: number_of_rows});

        $.ajax({
            url: url_req,
            type: type_req,
            data: frm_data,
            success: function(resp) {
                if ( typeof resp === 'object' && 'success' in resp && resp.success ) {
                    window.onbeforeunload = null;
                    let timerInterval
                    Swal.fire({
                        title: '¡Éxito!',
                        icon: 'success',
                        html: 'Datos guardados con éxito. Será redirigido en <b></b>',
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
                    Swal.fire({
                        title: 'Oops!',
                        icon: 'error',
                        html: 'Ocurrió un error inesperado',
                        timer: 2500,
                    });
                    $('#sold_in_currency').prop('disabled', true);
                    $('#total').prop('disabled', true);
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Generar venta';
                }
            }
        }).fail(function(xhr, status, error) {
            Swal.fire(
                '¡ERROR!',
                xhr.responseJSON.message,
                'error'
            );
            $('#sold_in_currency').prop('disabled', true);
                    $('#total').prop('disabled', true);
            submitBtn.disabled = false;
            submitBtn.innerText = 'Generar venta';
        });

    });
});

const fetchVendor = () => {
    if( !$("#vendorForm")[0].checkValidity() ) {
        $("#vendorForm")[0].reportValidity();
        return;
    }

    const submitBtn = document.getElementById('submitVendorBtn');
    const vendor = $("#vendorForm").serializeArray();

    submitBtn.disabled = true;
    submitBtn.innerText = 'Cargando...';

    $.ajax({
        url: '/punto-de-venta/vendors/create',
        type: 'POST',
        data: vendor,
        success: function(resp) {
            if ( typeof resp === 'object' && 'success' in resp && resp.success ) {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Crear';
                $('#vendorModal').modal('hide');

                $('#vendor_id').append(`
                    <option value="${resp.vendor.id}">${resp.vendor.name}</option>
                `);

                $('#vendor_id').val(resp.vendor.id);
                $('#vendor_id').trigger('change');

                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Se creó el vendedor con éxito, ya lo tienes disponible en el select de vendedores',
                    timer: 2500,
                });
            } else {
                Swal.fire({
                    title: 'Oops!',
                    icon: 'error',
                    html: 'Ocurrió un error inesperado',
                    timer: 2500,
                });
                submitBtn.disabled = false;
                submitBtn.innerText = 'Crear';
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        );
        submitBtn.disabled = false;
        submitBtn.innerText = 'Crear';
    });
}

$(function() {
    let pickerInit = flatpickr("#departure_date", {    
        mode: "single",
        dateFormat: "Y-m-d H:i",
        enableTime: true,
        minDate: "today"
    });
});

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