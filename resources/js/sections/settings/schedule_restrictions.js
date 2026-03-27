document.addEventListener("DOMContentLoaded", function() {
    if (document.querySelector('.table-rendering') != null) {
        components.actionTable($('.table-rendering'), 'fixedheader');
    }
    components.formReset();

    document.addEventListener('click', function (event) {
        if (event.target.closest('.btn-confirm-delete')) {
            event.preventDefault();
            const form = event.target.closest('.form-delete-restriction');
            Swal.fire({
                title: '¿Eliminar restricción?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    });
});
