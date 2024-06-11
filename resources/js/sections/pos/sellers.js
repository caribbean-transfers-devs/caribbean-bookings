if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}
components.formReset();

//DECLARACION DE VARIABLES
const __create = document.querySelector('.__btn_create'); //* ===== BUTTON TO CREATE ===== */
const __update = document.querySelector('.__btn_update'); //* ===== BUTTON TO UPDATE ===== */
const __title_modal = document.getElementById('vendorModalLabel');

//ACCION PARA CREAR
if( __create != null ){
    __create.addEventListener('click', function (event) {
        event.preventDefault();
        __title_modal.innerHTML = this.dataset.title;
    });
}

//ACCION PARA ACTUALIZAR
if( __update != null ){
    __update.addEventListener('click', function (event) {
        event.preventDefault();
        const { title, id, name, email, phone, status } = this.dataset;
        console.log( id, name, email, phone, status );
        __title_modal.innerHTML = title;
        document.getElementById('id').value = id.replace(/'/g, "");
        document.getElementById('name').value = name.replace(/'/g, "");
        document.getElementById('email').value = email.replace(/'/g, "");
        document.getElementById('phone').value = phone.replace(/'/g, "");
        document.getElementById('status').value = status.replace(/'/g, "");
    });
}

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
        url: $('#id').val() === '' ? '/punto-de-venta/vendors/create' : '/punto-de-venta/vendors/edit',
        type: $('#id').val() === '' ? 'POST' : 'PUT',
        data: vendor,
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
                            b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
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
                submitBtn.disabled = false;
                submitBtn.innerText = $('#id').val() === '' ? 'Crear' : 'Editar';
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        );
        submitBtn.disabled = false;
        submitBtn.innerText = $('#id').val() === '' ? 'Crear' : 'Editar';
    });
}

$('.toogle-status').click(function () {
    Swal.fire({
        title: 'Cargando...',
        icon: 'info',
        allowOutsideClick: false
    })
    Swal.showLoading();

    const data = $(this).find('form').serializeArray();

    $.ajax({
        url: '/punto-de-venta/vendors/edit',
        type: 'PUT',
        data,
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
                            b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
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
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        );
    });
})

$('.delete-vendor').click(function () {
    Swal.fire({
        title: 'Cargando...',
        icon: 'info',
        allowOutsideClick: false
    })
    Swal.showLoading();

    const data = $(this).find('form').serializeArray();

    $.ajax({
        url: '/punto-de-venta/vendors/delete',
        type: 'DELETE',
        data,
        success: function(resp) {
            if ( typeof resp === 'object' && 'success' in resp && resp.success ) {
                window.onbeforeunload = null;
                let timerInterval
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Se eliminó el vendedor con éxito. Será redirigido en <b></b>',
                    timer: 2500,
                    timerProgressBar: true,
                    didOpen: () => {
                        Swal.showLoading()
                        const b = Swal.getHtmlContainer().querySelector('b')
                        timerInterval = setInterval(() => {
                            b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
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
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        );
    });
})