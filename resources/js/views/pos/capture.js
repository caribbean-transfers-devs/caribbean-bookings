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

    openPaymentModalBtn.addEventListener('click', (e) => e.preventDefault());

    sold_in_currency_select.addEventListener('change', (e) => {
        currency_span.innerText = `(${e.target.value})`;
        $('.total-currency').text(e.target.value);
    })

    formaDePagoSelect.addEventListener('change', (e) => {
        if (e.target.value === 'CARD') clipContainer.style.display = "block";
        else clipContainer.style.display = "none";
    });

    terminalSelect.addEventListener('change', () => {
        const number_of_rows = $('#payments_table tbody').children().length;
        if( number_of_rows === 0 ) return;

        $('#payments_table tbody').html('');
        $('#previous_total').text('0');
        $('#total_remaining').text( $('#total').val() );

        $('#payments_table').hide();
        $('#sold_in_currency').prop('disabled', false);
        $('#total').prop('disabled', false);
    });

    total.addEventListener('input', (e) => {
        const value = Number(e.target.value);

        if( value > 0 ) $('.payment-section').show();
        else $('.payment-section').hide();

        $('#total_remaining').text(value);
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

            const currency_exchange = currency_exchange_data.find(currency_exchange => (
                currency_exchange.origin == origin_currency &&
                currency_exchange.destination == destination_currency &&
                currency_exchange.terminal == terminal
            ));

            let total;
            if( currency_exchange.operation === 'multiplication' ) total = payment * currency_exchange.exchange_rate;
            else total = total = payment / currency_exchange.exchange_rate;

            const previous_total = Number($('#previous_total').text());
            total = previous_total - total;
            total = total.toFixed(2);

            $('#previous_total').text(total);
            $('#total_remaining').text(total_to_pay - total);

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
        $alert.hide();

        if( !payment || payment <= 0 ) {
            $alert.text('Escribe una cantidad correcta');
            return $alert.show();
        }
        
        const payment_method = $('#payment_method').val();
        const clip_id = $('#clip_id').val();
        const origin_currency = $('#paid_in_currency').val();
        const destination_currency = $('#sold_in_currency').val();
        const terminal = $('#terminal').val();

        const currency_exchange = currency_exchange_data.find(currency_exchange => (
            currency_exchange.origin == origin_currency &&
            currency_exchange.destination == destination_currency &&
            currency_exchange.terminal == terminal
        ));

        if( !currency_exchange ) {
            $alert.text(`Lo sentimos, no se encontró la conversión de moneda de ${origin_currency} -> ${destination_currency} para la Terminal ${terminal.replace('T', '')}. Quizá tengas que añadir este caso, o pedirle a algún administrador que lo haga`);
            return $alert.show();
        }

        let total;
        if( currency_exchange.operation === 'multiplication' ) total = payment * currency_exchange.exchange_rate;
        else total = total = payment / currency_exchange.exchange_rate;

        const previous_total = Number($('#previous_total').text());
        total = previous_total + total;
        total = total.toFixed(2);

        $('#previous_total').text(total);
        $('#total_remaining').text(total_to_pay - total);

        const number_of_rows = $('#payments_table tbody').children().length;
        
        const new_row = `
            <tr>
                <td class="payment">${payment}</td>
                <td class="currency">${origin_currency}</td>
                <td align="center">
                    <button class="btn btn-danger btn-sm">Eliminar</button>
                </td>

                <input type="hidden" name="payment_method_${number_of_rows}" value="${payment_method}">
                <input type="hidden" name="clip_id_${number_of_rows}" value="${clip_id}">
                <input type="hidden" name="payment_${number_of_rows}" value="${payment}">
                <input type="hidden" name="currency_${number_of_rows}" value="${origin_currency}">
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