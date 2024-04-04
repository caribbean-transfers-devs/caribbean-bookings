$(function() {
    $('#vendors').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        }
    });
});

// actionType = 'create' | 'edit'
const openVendorModal = (actionType = 'create', vendor = null) => {
    if( actionType === 'create' ) {
        $('#title').text('Crear vendedor');
        $('#submitVendorBtn').text('Crear');
        
        $('#id').val('');
        $('#name').val('');
        $('#email').val('');
        $('#phone').val('');
        $('#status').val('1');
    }
    else {
        $('#title').text('Editar vendedor');
        $('#submitVendorBtn').text('Editar');

        $('#id').val(vendor.id);
        $('#name').val(vendor.name);
        $('#email').val(vendor.email);
        $('#phone').val(vendor.phone);
        $('#status').val(vendor.status);
    }

    $('#vendorModal').modal('show');
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