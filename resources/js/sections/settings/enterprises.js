let enterprises = {
    loadContent: function(){
        $('#media-listing').load('/enterprises/upload/' + enterprise_id, function(response, status, xhr) {
            if (status == "error") {
                $('#media-listing').html('Error al cargar el contenido');
            }
        });
    },
    initializeDropzone: function(){
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
                        title: "Subiendo imágen...",
                        text: "Por favor, espera mientras se cargan la imágen.", //Realiza la function de HTML en el Swal
                        allowOutsideClick: false,
                        allowEscapeKey: false, // Esta línea evita que se cierre con ESC
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Si el usuario confirma, enviar el archivo
                    // Procesar el archivo
                    dropzone.processFile(file);

                    // Si el usuario cancela, eliminar el archivo
                    // dropzone.removeFile(file);
                });

                // Añadir el valor seleccionado a los datos enviados
                this.on("sending", function(file, xhr, formData) {
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
    }
}

//VALIDAMOS DOM
/*
    se dispara cuando el documento HTML ha sido completamente cargado y parseado, 
    sin esperar a que se carguen los estilos, imágenes u otros recursos externos.
 */
document.addEventListener("DOMContentLoaded", function() {
    if( document.querySelector('.table-rendering') != null ){
        components.actionTable($('.table-rendering'));
    }

    if(typeof enterprise_id !== 'undefined'){
        enterprises.loadContent();
    }    

    if (typeof Dropzone !== 'undefined') {
        enterprises.initializeDropzone(); // Ya está cargado, inicializa
    } else {
        console.warn("Dropzone no está cargado. Intentando cargarlo dinámicamente...");
    }    


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
                url: '/enterprises/upload/'+id,
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
});