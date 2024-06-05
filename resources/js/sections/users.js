// $(function() {
//     $('#active_users,#inactive_users,#tbl_whitelist').DataTable({
//         language: {
//             url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
//         }
//     });
// });

if( document.querySelector('.table-rendering') != null ){
    components.actionTable($('.table-rendering'));
}

function ChangePass(id){
    $("#password").val('');
    $("#confirm_pass").val('');
    $("#pass_id").val(id);
}

$("#chgPassBtn").on('click', () => {
    $("#chgPassBtn").prop('disabled', true);
    $("#chgPassBtn").html('<i class="fas fa-spinner fa-pulse"></i>');
    let frm_data = $("#frm_chg_pass").serialize();
    let id = $("#pass_id").val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
        }
    });       
    $.ajax({
        url: '/ChangePass/'+id,
        type: 'PUT',
        data: frm_data,
        success: function(resp) {
            if(resp.success == 1){
                let timerInterval
                Swal.fire({
                    title: '¡Éxito!',
                    icon: 'success',
                    html: 'Contraseña cambiada con éxito. Será redirigido en <b></b>',
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
                    window.location.href = '/users'                        
                })
            }else{
                console.log(resp);
            }
        }
    }).fail(function(xhr, status, error) {
        Swal.fire(
            '¡ERROR!',
            xhr.responseJSON.message,
            'error'
        )
        $("#chgPassBtn").html('Cambiar Contraseña');
        $("#chgPassBtn").prop('disabled', false);
    });        
})

function chgStatus(id,status){
    let msg = status == 1 ? 'activa' : 'desactiva';
    Swal.fire({
        title: '¿Está seguro?',
        text: "¿Desea "+msg+" este usuario?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '¡Si, '+msg+'r!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                }
            });       
            $.ajax({
                url: '/ChangeStatus/'+id,
                type: 'PUT',
                data: {status:status},
                success: function(resp) {
                    if(resp.success == 1){
                        let timerInterval
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: 'success',
                            html: 'Usuario '+msg+'do con éxito. Será redirigido en <b></b>',
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
                    }else{
                        console.log(resp);
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
    })
}

function StoreIP(){
    let ip = $("#valid_ip").val();
    if(ip == ''){
        Swal.fire(
            '¡ERROR!',
            'Debe ingresar una IP válida',
            'error'
        )
    }else{
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
            }
        });       
        $.ajax({
            url: '/StoreIP',
            type: 'POST',
            data: {ip:ip},
            success: function(resp) {
                if(resp.success == 1){
                    let timerInterval
                    Swal.fire({
                        title: '¡Éxito!',
                        icon: 'success',
                        html: 'IP agregada con éxito. Será redirigido en <b></b>',
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
                }else{
                    console.log(resp);
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
}

function DelIP(id){
    Swal.fire({
        title: '¿Está seguro?',
        text: "¿Desea eliminar esta IP de la lista blanca?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '¡Si, eliminar!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').attr('value')
                }
            });       
            $.ajax({
                url: '/DeleteIPs/'+id,
                type: 'DELETE',
                success: function(resp) {
                    if(resp.success == 1){
                        let timerInterval
                        Swal.fire({
                            title: '¡Éxito!',
                            icon: 'success',
                            html: 'IP eliminada con éxito. Será redirigido en <b></b>',
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
                    }else{
                        console.log(resp);
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
    })
}